<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Core\MediaProcessor;
use App\Core\Auth;
use App\Models\Noticia;

class NoticiaAdminController extends AdminController {
    
    private Noticia $noticiaModel;
    
    public function __construct() {
        parent::__construct();
        $this->noticiaModel = new Noticia();
    }
    
    /**
     * Listado admin de noticias
     */
    public function index(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Noticias';
        $filtros = ['busqueda' => $this->param('busqueda')];
        if (Auth::hasRole('redactor')) {
            $filtros['usuario_id'] = Auth::id();
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $resultados = $this->noticiaModel->paginateAdmin($filtros, $page, ITEMS_PER_PAGE);
        
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('noticias/index', compact('titlePage', 'resultados', 'filtros', 'flash'));
    }
    
    /**
     * Formulario de creación
     */
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Nueva Noticia';
        $noticia = [
            'titulo' => '', 'slug' => '', 'foto' => '', 'link_externo' => '',
            'fecha_publicacion' => date('Y-m-d'), 'estado' => 'borrador',
        ];
        
        $this->view('noticias/form', compact('titlePage', 'noticia'));
    }
    
    /**
     * Guarda una nueva noticia
     */
    public function store(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $data = [
            'titulo'            => $this->post('titulo'),
            'slug'              => $this->generateSlug($this->post('slug') ?: $this->post('titulo')),
            'foto'              => $this->post('foto'),
            'link_externo'      => $this->post('link_externo'),
            'fecha_publicacion' => $this->post('fecha_publicacion', date('Y-m-d')),
            'estado'            => $this->post('estado', 'borrador'),
            'usuario_id'        => Auth::id(),
        ];
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'titulo' => 'requerido|max:255',
            'slug'   => 'requerido|slug',
            'estado' => 'enum:borrador,publicado,archivado',
            'fecha_publicacion' => 'requerido|fecha',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/noticias/nuevo');
        }
        
        $data['slug'] = $this->uniqueSlug($data['slug'], 'noticias');
        
        // Procesar foto
        if (!empty($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutas = $media->processImage($_FILES['foto'], 'reportajes', 'noticia');
            if ($rutas) {
                $data['foto'] = $rutas['ruta_original'];
            }
        }
        
        $id = $this->noticiaModel->create($data);
        $this->log('crear', 'noticia', $id, 'Noticia "' . $data['titulo'] . '" creada');
        $this->flash('success', 'Noticia creada correctamente.');
        $this->redirect(BASE_URL . '/admin/noticias/' . $id . '/editar');
    }
    
    /**
     * Formulario de edición
     */
    public function edit(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $id = (int) ($_GET['id'] ?? 0);
        $noticia = $this->noticiaModel->find($id);
        
        if (!$noticia) {
            $this->flash('danger', 'Noticia no encontrada.');
            $this->redirect(BASE_URL . '/admin/noticias');
        }
        
        if (Auth::hasRole('redactor') && (int) $noticia['usuario_id'] !== Auth::id()) {
            $this->flash('danger', 'No tiene permisos para editar esta noticia.');
            $this->redirect(BASE_URL . '/admin/noticias');
        }

        $titlePage = 'Editar Noticia';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('noticias/form', compact('titlePage', 'noticia', 'flash'));
    }
    
    /**
     * Actualiza una noticia
     */
    public function update(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();

        $id = (int) ($_GET['id'] ?? 0);
        $existing = $this->noticiaModel->find($id);
        if (!$existing) {
            $this->flash('danger', 'Noticia no encontrada.');
            $this->redirect(BASE_URL . '/admin/noticias');
        }
        if (Auth::hasRole('redactor') && (int) $existing['usuario_id'] !== Auth::id()) {
            $this->forbidden();
        }
        
        $data = [
            'titulo'            => $this->post('titulo'),
            'slug'              => $this->post('slug') ?: $this->generateSlug($this->post('titulo')),
            'foto'              => $this->post('foto_actual', (string) $existing['foto']),
            'link_externo'      => $this->post('link_externo'),
            'fecha_publicacion' => $this->post('fecha_publicacion', date('Y-m-d')),
            'estado'            => $this->post('estado', (string) ($existing['estado'] ?? 'borrador')),
        ];
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'titulo' => 'requerido|max:255',
            'slug'   => 'requerido|slug',
            'estado' => 'enum:borrador,publicado,archivado',
            'fecha_publicacion' => 'requerido|fecha',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/noticias/' . $id . '/editar');
        }
        
        $data['slug'] = $this->uniqueSlug($data['slug'], 'noticias', $id);
        
        // Foto opcionalmente se reemplaza
        if (!empty($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutas = $media->processImage($_FILES['foto'], 'reportajes', 'noticia');
            if ($rutas) {
                $data['foto'] = $rutas['ruta_original'];
            }
        }
        
        $this->noticiaModel->update($id, $data);
        $this->log('editar', 'noticia', $id, 'Noticia "' . $data['titulo'] . '" editada');
        $this->flash('success', 'Noticia actualizada.');
        $this->redirect(BASE_URL . '/admin/noticias/' . $id . '/editar');
    }
    
    /**
     * Elimina una noticia (solo admin/editor)
     */
    public function delete(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $noticia = $this->noticiaModel->find($id);
        
        if (!$noticia) {
            $this->flash('danger', 'Noticia no encontrada.');
            $this->redirect(BASE_URL . '/admin/noticias');
        }
        
        if (Auth::hasRole('redactor') && (int) $noticia['usuario_id'] !== Auth::id()) {
            $this->forbidden();
        }

        $this->noticiaModel->delete($id);
        $this->log('eliminar', 'noticia', $id, 'Noticia "' . $noticia['titulo'] . '" eliminada');
        $this->flash('success', 'Noticia eliminada.');
        $this->redirect(BASE_URL . '/admin/noticias');
    }
}