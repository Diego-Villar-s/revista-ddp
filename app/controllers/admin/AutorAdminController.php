<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Models\Autor;

class AutorAdminController extends AdminController {
    
    private Autor $autorModel;
    
    public function __construct() {
        parent::__construct();
        $this->autorModel = new Autor();
    }
    
    public function index(): void {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        
        $titlePage = 'Autores';
        $autores = $this->autorModel->getAllWithCount();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('autores/index', compact('titlePage', 'autores', 'flash'));
    }
    
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor']);
        
        $titlePage = 'Nuevo Autor';
        $autor = ['nombres' => '', 'ap_paterno' => '', 'ap_materno' => '', 'nickname' => '', 'es_nickname' => 0];
        
        $this->view('autores/form', compact('titlePage', 'autor'));
    }
    
    public function store(): void {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        
        $data = [
            'nombres'     => $this->post('nombres'),
            'ap_paterno'  => $this->post('ap_paterno'),
            'ap_materno'  => $this->post('ap_materno'),
            'nickname'    => $this->post('nickname'),
            'es_nickname' => !empty($_POST['es_nickname']) ? 1 : 0,
        ];
        
        $validator = new Validator();
        $valid = $validator->validate($data, ['nombres' => 'requerido|max:120']);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/autores/nuevo');
        }
        
        $id = $this->autorModel->create($data);
        $this->log('crear', 'autor', $id, 'Autor "' . $data['nombres'] . '" creado');
        $this->flash('success', 'Autor creado correctamente.');
        $this->redirect(BASE_URL . '/admin/autores');
    }
    
    public function edit(): void {
        $this->requireAnyRole(['admin', 'editor']);
        $id = (int) ($_GET['id'] ?? 0);
        $autor = $this->autorModel->find($id);
        
        if (!$autor) {
            $this->flash('danger', 'Autor no encontrado.');
            $this->redirect(BASE_URL . '/admin/autores');
        }
        
        $titlePage = 'Editar Autor';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('autores/form', compact('titlePage', 'autor', 'flash'));
    }
    
    public function update(): void {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        
        $data = [
            'nombres'     => $this->post('nombres'),
            'ap_paterno'  => $this->post('ap_paterno'),
            'ap_materno'  => $this->post('ap_materno'),
            'nickname'    => $this->post('nickname'),
            'es_nickname' => !empty($_POST['es_nickname']) ? 1 : 0,
        ];
        
        $validator = new Validator();
        $valid = $validator->validate($data, ['nombres' => 'requerido|max:120']);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/autores/' . $id . '/editar');
        }
        
        $this->autorModel->update($id, $data);
        $this->log('editar', 'autor', $id, 'Autor "' . $data['nombres'] . '" editado');
        $this->flash('success', 'Autor actualizado.');
        $this->redirect(BASE_URL . '/admin/autores');
    }
    
    public function delete(): void {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $autor = $this->autorModel->find($id);
        
        if (!$autor) {
            $this->flash('danger', 'Autor no encontrado.');
            $this->redirect(BASE_URL . '/admin/autores');
        }
        
        $this->autorModel->delete($id);
        $this->log('eliminar', 'autor', $id, 'Autor "' . $autor['nombres'] . '" eliminado');
        $this->flash('success', 'Autor eliminado.');
        $this->redirect(BASE_URL . '/admin/autores');
    }
}