<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Core\MediaProcessor;
use App\Core\Auth;
use App\Models\Boletin;

class BoletinAdminController extends AdminController {
    
    private Boletin $boletinModel;
    
    public function __construct() {
        parent::__construct();
        $this->boletinModel = new Boletin();
    }
    
    public function index(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Boletines NTEP';
        $filtros = [];
        if (Auth::hasRole('redactor')) {
            $filtros['usuario_id'] = Auth::id();
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $resultados = $this->boletinModel->paginateAdmin($filtros, $page, ITEMS_PER_PAGE);
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('boletines/index', compact('titlePage', 'resultados', 'flash'));
    }
    
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Nuevo Boletín';
        $boletin = [
            'numero_boletin' => '', 'resumen' => '', 'foto_portada' => '',
            'archivo_pdf' => '', 'fecha_publicacion' => date('Y-m-d'), 'estado' => 'borrador',
        ];
        
        $this->view('boletines/form', compact('titlePage', 'boletin'));
    }
    
    public function store(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $data = [
            'numero_boletin'    => $this->post('numero_boletin'),
            'resumen'           => $this->post('resumen'),
            'temas'             => $this->post('temas'),
            'foto_portada'      => $this->post('foto_portada'),
            'archivo_pdf'       => $this->post('archivo_pdf'),
            'fecha_publicacion' => $this->post('fecha_publicacion', date('Y-m-d')),
            'estado'            => $this->post('estado', 'borrador'),
            'usuario_id'        => Auth::id(),
        ];
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'numero_boletin' => 'requerido|max:50',
            'estado' => 'enum:borrador,publicado,archivado',
            'fecha_publicacion' => 'requerido|fecha',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/boletines/nuevo');
        }
        
        // Procesar portada
        if (!empty($_FILES['foto_portada']['tmp_name']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutas = $media->processImage($_FILES['foto_portada'], 'boletines', 'portada');
            if ($rutas) $data['foto_portada'] = $rutas['ruta_original'];
        }
        
        // Procesar PDF
        if (!empty($_FILES['archivo_pdf']['tmp_name']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutaPdf = $media->processPdf($_FILES['archivo_pdf'], 'boletines', 'ntep');
            if ($rutaPdf) $data['archivo_pdf'] = $rutaPdf;
        }
        
        $id = $this->boletinModel->create($data);
        $this->log('crear', 'boletin', $id, 'Boletín "' . $data['numero_boletin'] . '" creado');
        $this->flash('success', 'Boletín creado correctamente.');
        $this->redirect(BASE_URL . '/admin/boletines/' . $id . '/editar');
    }
    
    public function edit(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $id = (int) ($_GET['id'] ?? 0);
        $boletin = $this->boletinModel->find($id);
        
        if (!$boletin) {
            $this->flash('danger', 'Boletín no encontrado.');
            $this->redirect(BASE_URL . '/admin/boletines');
        }
        if (Auth::hasRole('redactor') && (int) $boletin['usuario_id'] !== Auth::id()) {
            $this->forbidden();
        }
        
        $titlePage = 'Editar Boletín';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('boletines/form', compact('titlePage', 'boletin', 'flash'));
    }
    
    public function update(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();

        $id = (int) ($_GET['id'] ?? 0);
        $existing = $this->boletinModel->find($id);
        if (!$existing) {
            $this->flash('danger', 'Boletín no encontrado.');
            $this->redirect(BASE_URL . '/admin/boletines');
        }
        if (Auth::hasRole('redactor') && (int) $existing['usuario_id'] !== Auth::id()) {
            $this->forbidden();
        }
        
        $data = [
            'numero_boletin'    => $this->post('numero_boletin'),
            'resumen'           => $this->post('resumen'),
            'temas'             => $this->post('temas', (string) ($existing['temas'] ?? '')),
            'foto_portada'      => $this->post('foto_portada_actual', (string) $existing['foto_portada']),
            'archivo_pdf'       => $this->post('archivo_pdf_actual', (string) $existing['archivo_pdf']),
            'fecha_publicacion' => $this->post('fecha_publicacion', date('Y-m-d')),
            'estado'            => $this->post('estado', (string) ($existing['estado'] ?? 'borrador')),
        ];
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'numero_boletin' => 'requerido|max:50',
            'estado' => 'enum:borrador,publicado,archivado',
            'fecha_publicacion' => 'requerido|fecha',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/boletines/' . $id . '/editar');
        }
        
        if (!empty($_FILES['foto_portada']['tmp_name']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutas = $media->processImage($_FILES['foto_portada'], 'boletines', 'portada');
            if ($rutas) $data['foto_portada'] = $rutas['ruta_original'];
        }
        
        if (!empty($_FILES['archivo_pdf']['tmp_name']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
            $media = new MediaProcessor();
            $rutaPdf = $media->processPdf($_FILES['archivo_pdf'], 'boletines', 'ntep');
            if ($rutaPdf) $data['archivo_pdf'] = $rutaPdf;
        }
        
        $this->boletinModel->update($id, $data);
        $this->log('editar', 'boletin', $id, 'Boletín "' . $data['numero_boletin'] . '" editado');
        $this->flash('success', 'Boletín actualizado.');
        $this->redirect(BASE_URL . '/admin/boletines/' . $id . '/editar');
    }
    
    public function delete(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $boletin = $this->boletinModel->find($id);
        
        if (!$boletin) {
            $this->flash('danger', 'Boletín no encontrado.');
            $this->redirect(BASE_URL . '/admin/boletines');
        }
        if (Auth::hasRole('redactor') && ((int) $boletin['usuario_id'] !== Auth::id() || $boletin['estado'] !== 'borrador')) {
            $this->forbidden();
        }
        
        $this->boletinModel->delete($id);
        $this->log('eliminar', 'boletin', $id, 'Boletín "' . $boletin['numero_boletin'] . '" eliminado');
        $this->flash('success', 'Boletín eliminado.');
        $this->redirect(BASE_URL . '/admin/boletines');
    }
}