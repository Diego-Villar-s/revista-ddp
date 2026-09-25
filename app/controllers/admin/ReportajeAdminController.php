<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Core\MediaProcessor;
use App\Core\Auth;
use App\Models\Reportaje;
use App\Models\Autor;
use App\Core\Database;

class ReportajeAdminController extends AdminController {
    
    private Reportaje $reportajeModel;
    
    public function __construct() {
        parent::__construct();
        $this->reportajeModel = new Reportaje();
    }
    
    /**
     * Listado de reportajes con filtros
     */
    public function index(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Reportajes';
        
        // Filtros
        $filtros = [
            'estado'    => $this->param('estado'),
            'autor_id'  => $this->param('autor_id'),
            'destacado' => $this->param('destacado'),
            'desde'     => $this->param('desde'),
            'hasta'     => $this->param('hasta'),
            'busqueda'  => $this->param('busqueda'),
        ];
        
        // Los redactores solo ven sus propios reportajes
        if (Auth::hasRole('redactor')) {
            $filtros['usuario_id'] = Auth::id();
        }
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $resultados = $this->reportajeModel->paginateAdmin($filtros, $page, REPORT_ITEMS_PER_PAGE);
        
        // Datos para los filtros
        $autores = (new Autor())->getOptions();
        
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('reportajes/index', compact(
            'titlePage', 'resultados', 'filtros', 'autores', 'flash'
        ));
    }
    
    /**
     * Formulario de creación
     */
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Nuevo Reportaje';
        $reportaje = [
            'titulo' => '', 'slug' => '', 'resumen_corto' => '', 'desarrollo' => '', 'video_embed' => '',
            'foto_principal' => '', 'alt_foto_principal' => '', 'pdf_adjunto' => '',
            'fecha_publicacion' => date('Y-m-d'), 'estado' => 'borrador',
            'es_destacado' => 0, 'meta_titulo' => '', 'meta_descripcion' => '',
            'autor_id' => null,
        ];
        $autores = (new Autor())->getOptions();
        $historial = [];
        $fotos = [];
        $esRedactor = Auth::hasRole('redactor');
        $modo = 'create';
        
        $this->view('reportajes/form', compact(
            'titlePage', 'reportaje', 'autores', 'historial', 'fotos', 'esRedactor', 'modo' 
        ));
    }
    
    /**
     * Guarda un nuevo reportaje
     */
    public function store(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $data = $this->collectFormData();
        
        // Validar datos
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'titulo'      => 'requerido|max:255',
            'slug'        => 'requerido|slug',
            'resumen_corto' => 'max:500',
            'alt_foto_principal' => 'requerido',
            'fecha_publicacion' => 'requerido|fecha',
            'estado' => 'enum:borrador,publicado,archivado',
            'video_embed' => 'url',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores de validación: ' . $validator->errorsAsString()];
            $_SESSION['form_data'] = $data;
            $this->redirect(BASE_URL . '/admin/reportajes/nuevo');
            return;
        }
        
        // Slug único
        $data['slug'] = $this->uniqueSlug($data['slug'], 'reportajes');
        
        // El redactor no puede publicar ni marcar destacados aunque manipule el POST.
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
            $data['es_destacado'] = 0;
        }
        $data['usuario_id'] = Auth::id();

        // Procesar foto principal si se subió
        if (!empty($_FILES['foto_principal']['tmp_name']) && $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutas = $media->processImage($_FILES['foto_principal'], 'reportajes');
            
            if ($rutas) {
                $data['foto_principal'] = $rutas['ruta_original'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'No se pudo procesar la imagen: ' . implode(', ', $media->errors())];
            }
        }
        
        // Procesar PDF adjunto si se subió
        if (!empty($_FILES['pdf_adjunto']['tmp_name']) && $_FILES['pdf_adjunto']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutaPdf = $media->processPdf($_FILES['pdf_adjunto'], 'reportajes');
            if ($rutaPdf) {
                $data['pdf_adjunto'] = $rutaPdf;
            }
        }
        
        // Autor por defecto: "Redacción" si no se seleccionó
        if (empty($data['autor_id'])) {
            $autorModel = new Autor();
            $redaccion = $autorModel->findBy('nickname', 'Redacción DDP');
            $data['autor_id'] = $redaccion ? $redaccion['id'] : null;
        }
        
        $id = $this->reportajeModel->create($data);
        
        // Registrar log
        $this->log('crear', 'reportaje', $id, 'Reportaje "' . $data['titulo'] . '" creado');
        
        $this->flash('success', 'Reportaje creado correctamente.');
        $this->redirect(BASE_URL . '/admin/reportajes/' . $id . '/editar');
    }
    
    /**
     * Formulario de edición
     */
    public function edit(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $id = (int) ($_GET['id'] ?? 0);
        $reportaje = $this->reportajeModel->findWithRelations($id);
        
        if (!$reportaje) {
            $this->flash('danger', 'Reportaje no encontrado.');
            $this->redirect(BASE_URL . '/admin/reportajes');
        }
        
        // Redactor solo edita sus propios reportajes
        if (Auth::hasRole('redactor') && $reportaje['usuario_id'] != Auth::id()) {
            $this->flash('danger', 'No tiene permisos para editar este reportaje.');
            $this->redirect(BASE_URL . '/admin/reportajes');
        }
        
        // Restaurar datos del formulario en caso de error de validación
        if (!empty($_SESSION['form_data'])) {
            $reportaje = array_merge($reportaje, $_SESSION['form_data']);
            unset($_SESSION['form_data']);
        }
        
        $titlePage = 'Editar Reportaje';
        $autores = (new Autor())->getOptions();
        $historial = (new \App\Models\LogActividad())->getHistorial('reportaje', $id);
        $fotos = $this->reportajeModel->getFotos($id);
        $esRedactor = Auth::hasRole('redactor');
        $modo = 'edit';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('reportajes/form', compact(
            'titlePage', 'reportaje', 'autores', 'historial', 'fotos', 'esRedactor', 'flash', 'modo'
        ));
    }
    
    /**
     * Actualiza un reportaje
     */
    public function update(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $reportaje = $this->reportajeModel->find($id);
        
        if (!$reportaje) {
            $this->flash('danger', 'Reportaje no encontrado.');
            $this->redirect(BASE_URL . '/admin/reportajes');
        }
        
        // Redactor solo su propios reportajes
        if (Auth::hasRole('redactor') && $reportaje['usuario_id'] != Auth::id()) {
            $this->flash('danger', 'No tiene permisos para editar este reportaje.');
            $this->redirect(BASE_URL . '/admin/reportajes');
        }
        
        // Redactor no puede publicar
        $data = $this->collectFormData();
        if (Auth::hasRole('redactor')) {
            if ($data['estado'] === 'publicado') {
                $this->flash('warning', 'Los redactores no pueden publicar. El reportaje quedó en borrador.');
            }
            $data['estado'] = 'borrador';
            $data['es_destacado'] = 0;
        }
        
        // En borrador sin fecha, usar hoy
        if ($data['fecha_publicacion'] === '' || $data['fecha_publicacion'] === null) {
            $data['fecha_publicacion'] = date('Y-m-d');
        }
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'titulo'      => 'requerido|max:255',
            'slug'        => 'requerido|slug',
            'resumen_corto' => 'max:500',
            'alt_foto_principal' => 'requerido',
            'fecha_publicacion' => 'requerido|fecha',
            'estado' => 'enum:borrador,publicado,archivado',
            'video_embed' => 'url',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores de validación: ' . $validator->errorsAsString()];
            $_SESSION['form_data'] = $data;
            $this->redirect(BASE_URL . '/admin/reportajes/' . $id . '/editar');
            return;
        }
        
        // Slug único excluyendo este reportaje
        $data['slug'] = $this->uniqueSlug($data['slug'], 'reportajes', $id);
        
        // Procesar nueva foto principal si se subió
        if (!empty($_FILES['foto_principal']['tmp_name']) && $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutas = $media->processImage($_FILES['foto_principal'], 'reportajes');
            if ($rutas) {
                $data['foto_principal'] = $rutas['ruta_original'];
            }
        }
        
        // Procesar nuevo PDF si se subió
        if (!empty($_FILES['pdf_adjunto']['tmp_name']) && $_FILES['pdf_adjunto']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutaPdf = $media->processPdf($_FILES['pdf_adjunto'], 'reportajes');
            if ($rutaPdf) {
                $data['pdf_adjunto'] = $rutaPdf;
            }
        }
        
        // Autor por defecto: "Redacción"
        if (empty($data['autor_id'])) {
            $autorModel = new Autor();
            $redaccion = $autorModel->findBy('nickname', 'Redacción DDP');
            $data['autor_id'] = $redaccion ? $redaccion['id'] : null;
        }
        
        $this->reportajeModel->update($id, $data);
        
        // Registrar log con estado previo
        $detalles = 'Reportaje "' . $data['titulo'] . '" editado';
        if ($reportaje['estado'] !== $data['estado']) {
            $detalles .= ' (estado: ' . $reportaje['estado'] . ' -> ' . $data['estado'] . ')';
        }
        $this->log('editar', 'reportaje', $id, $detalles);
        
        $this->flash('success', 'Reportaje actualizado correctamente.');
        $this->redirect(BASE_URL . '/admin/reportajes/' . $id . '/editar');
    }
    
    /**
     * Elimina un reportaje (solo admin/editor, o redactor de sus propios borradores)
     */
    public function delete(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $reportaje = $this->reportajeModel->find($id);
        
        if (!$reportaje) {
            $this->flash('danger', 'Reportaje no encontrado.');
            $this->redirect(BASE_URL . '/admin/reportajes');
        }
        
        // Solo admin/editor pueden eliminar; un redactor solo sus borradores propios
        if (Auth::hasRole('admin', 'editor')) {
            $allowed = true;
        } else if (Auth::hasRole('redactor') && $reportaje['usuario_id'] == Auth::id() && $reportaje['estado'] === 'borrador') {
            $allowed = true;
        } else {
            $this->flash('danger', 'No tiene permisos para eliminar este reportaje.');
            $this->redirect(BASE_URL . '/admin/reportajes');
        }
        
        // Eliminar fotos de la galería
        $fotos = $this->reportajeModel->getFotos($id);
        foreach ($fotos as $foto) {
            $this->reportajeModel->deleteFoto($foto['id']);
        }
        
        $this->reportajeModel->delete($id);
        $this->log('eliminar', 'reportaje', $id, 'Reportaje "' . $reportaje['titulo'] . '" eliminado');
        
        $this->flash('success', 'Reportaje eliminado.');
        $this->redirect(BASE_URL . '/admin/reportajes');
    }
    
    /**
     * Toggle de destacado con aviso
     */
    public function toggleDestacado(): void {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $reportaje = $this->reportajeModel->find($id);
        
        if (!$reportaje) {
            $this->json(['ok' => false, 'message' => 'Reportaje no encontrado.']);
        }
        
        $nuevoValor = $reportaje['es_destacado'] ? 0 : 1;
        
        // Aviso si ya hay varios destacados
        $mensaje = '';
        if ($nuevoValor == 1) {
            $destacados = $this->reportajeModel->countWhere("es_destacado = 1 AND id != {$id}");
            if ($destacados >= 3) {
                $mensaje = 'Aviso: ya hay ' . $destacados . ' reportajes destacados activos.';
            }
        }
        
        $this->reportajeModel->update($id, ['es_destacado' => $nuevoValor]);
        $this->log('destacado', 'reportaje', $id, $nuevoValor ? 'Marcado como destacado' : 'Quitado de destacados');
        
        $this->json([
            'ok'      => true,
            'destacado' => (bool) $nuevoValor,
            'mensaje' => $mensaje,
        ]);
    }
    
    /**
     * Vista previa (devuelve HTML del reportaje renderizado)
     * Usado por el modal de vista previa antes de publicar
     */
    public function preview(): void {
        $this->validateCsrf();
        
        $data = $this->collectFormData();
        
        // Renderizar contenido en HTML
        $html = '<h1 class="h1">' . htmlspecialchars($data['titulo']) . '</h1>';
        $html .= '<p class="text-muted">Autor: ' . htmlspecialchars((string) ($data['autor_id'])) . '</p>';
        $html .= '<img src="' . htmlspecialchars(ddpImgUrl((string) ($data['foto_principal'] ?? '')), ENT_QUOTES, 'UTF-8') . '" class="img-fluid mb-3">';
        $html .= ($data['desarrollo'] ?? '');
        
        $this->json(['ok' => true, 'html' => $html]);
    }
    
    /**
     * Sube una foto a la galería del reportaje (vía AJAX)
     */
    public function subirFoto(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();

        $reportajeId = (int) ($_POST['reportaje_id'] ?? 0);
        $reportaje = $this->reportajeModel->find($reportajeId);
        if (!$reportaje) {
            $this->json(['ok' => false, 'message' => 'Reportaje no encontrado.']);
        }
        if (Auth::hasRole('redactor') && (int) $reportaje['usuario_id'] !== Auth::id()) {
            $this->json(['ok' => false, 'message' => 'No tiene permisos para modificar este reportaje.']);
        }
        $descripcion = Validator::sanitize($_POST['descripcion'] ?? '');
        $orden = (int) ($_POST['orden'] ?? 99);
        
        if (!isset($_FILES['foto_galeria'])) {
            $this->json(['ok' => false, 'message' => 'No se recibió archivo.']);
        }
        
        $media = new MediaProcessor();
        $rutas = $media->processImage($_FILES['foto_galeria'], 'reportajes', 'galeria');
        
        if (!$rutas) {
            $this->json(['ok' => false, 'message' => implode(', ', $media->errors())]);
        }
        
        $fotoId = $this->reportajeModel->addFoto($reportajeId, $rutas, $orden, $descripcion);
        $this->log('subir_foto', 'reportaje_foto', $fotoId, 'Foto añadida a galería del reportaje #' . $reportajeId);
        
        $this->json([
            'ok'    => true,
            'id'    => $fotoId,
            'imagen' => ddpImgUrl($rutas['ruta_thumb']),
        ]);
    }
    
    /**
     * Elimina una foto de la galería
     */
    public function eliminarFoto(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $fotoId = (int) ($_GET['fotoId'] ?? 0);
        
        $db = Database::getInstance();
        $foto = $db->selectOne("SELECT * FROM reportajes_fotos WHERE id = ?", [$fotoId]);
        
        if (!$foto) {
            $this->json(['ok' => false, 'message' => 'Foto no encontrada.']);
        }
        if (Auth::hasRole('redactor')) {
            $reportaje = $this->reportajeModel->find((int) $foto['reportaje_id']);
            if (!$reportaje || (int) $reportaje['usuario_id'] !== Auth::id()) {
                $this->json(['ok' => false, 'message' => 'No tiene permisos para modificar esta foto.']);
            }
        }
        
        $this->reportajeModel->deleteFoto($fotoId);
        $this->log('eliminar_foto', 'reportaje_foto', $fotoId, 'Foto eliminada del reportaje #' . $foto['reportaje_id']);
        
        $this->json(['ok' => true]);
    }
    
    /**
     * Recopila y normaliza los datos del formulario
     */
    private function collectFormData(): array {
        $esRedactor = Auth::hasRole('redactor');
        
        return [
            'titulo'              => $this->post('titulo'),
            'slug'                => $this->post('slug', $this->slugFromPost()),
            'resumen_corto'       => $this->post('resumen_corto'),
            'desarrollo'          => Validator::sanitizeHtml($_POST['desarrollo'] ?? ''),
            'video_embed'         => $this->post('video_embed'),
            'foto_principal'      => $this->post('foto_principal_actual'),
            'alt_foto_principal'  => $this->post('alt_foto_principal'),
            'pdf_adjunto'         => $this->post('pdf_adjunto_actual'),
            'fecha_publicacion'   => $this->post('fecha_publicacion', date('Y-m-d')),
            'estado'              => $this->post('estado', 'borrador'),
            'es_destacado'        => isset($_POST['es_destacado']) && $_POST['es_destacado'] ? 1 : 0,
            'meta_titulo'         => $this->post('meta_titulo'),
            'meta_descripcion'    => $this->post('meta_descripcion'),
            'autor_id'            => !empty($_POST['autor_id']) ? (int) $_POST['autor_id'] : null,
        ];
    }
    
    /**
     * Genera slug desde el título si no se llenó el campo slug
     */
    private function slugFromPost(): string {
        $slug = $this->post('slug');
        if ($slug !== '') return $slug;
        
        $titulo = $this->post('titulo');
        $slug = strtolower(trim($titulo));
        $slug = str_replace(['á','é','í','ó','ú','ü','ñ','Á','É','Í','Ó','Ú','Ñ'], ['a','e','i','o','u','u','n','a','e','i','o','u','n'], $slug);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}