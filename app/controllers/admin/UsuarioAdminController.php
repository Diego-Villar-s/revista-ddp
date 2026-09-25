<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Core\Auth;
use App\Models\Usuario;

class UsuarioAdminController extends AdminController {
    
    private Usuario $usuarioModel;
    
    public function __construct() {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }
    
    public function index(): void {
        // Solo admin puede gestionar usuarios
        $this->requireRole('admin');
        
        $titlePage = 'Usuarios';
        $usuarios = $this->usuarioModel->getAllConDetalles();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('usuarios/index', compact('titlePage', 'usuarios', 'flash'));
    }
    
    public function create(): void {
        $this->requireRole('admin');
        
        $titlePage = 'Nuevo Usuario';
        $usuario = [
            'nombres' => '', 'ap_paterno' => '', 'ap_materno' => '', 'email' => '',
            'rol' => 'redactor', 'activo' => 1,
        ];
        
        $this->view('usuarios/form', compact('titlePage', 'usuario'));
    }
    
    public function store(): void {
        $this->requireRole('admin');
        $this->validateCsrf();
        
        $data = [
            'nombres'       => $this->post('nombres'),
            'ap_paterno'    => $this->post('ap_paterno'),
            'ap_materno'    => $this->post('ap_materno'),
            'email'         => $this->post('email'),
            'password_hash' => '',
            'rol'           => $this->post('rol', 'redactor'),
            'activo'        => !empty($_POST['activo']) ? 1 : 0,
        ];
        
        $password = (string) ($_POST['password'] ?? '');
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'nombres'    => 'requerido|max:100',
            'ap_paterno' => 'requerido|max:60',
            'email'      => 'requerido|email',
            'rol'        => 'enum:admin,editor,redactor',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/usuarios/nuevo');
        }
        
        // Validar password
        if (strlen($password) < 8) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'La contraseña debe tener al menos 8 caracteres.'];
            $this->redirect(BASE_URL . '/admin/usuarios/nuevo');
        }
        
        if ($this->usuarioModel->emailExists($data['email'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ya existe un usuario con ese correo.'];
            $this->redirect(BASE_URL . '/admin/usuarios/nuevo');
        }
        
        $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        
        $id = $this->usuarioModel->create($data);
        $this->log('crear', 'usuario', $id, 'Usuario "' . $data['email'] . '" creado con rol ' . $data['rol']);
        $this->flash('success', 'Usuario creado correctamente.');
        $this->redirect(BASE_URL . '/admin/usuarios');
    }
    
    public function edit(): void {
        $this->requireRole('admin');
        
        $id = (int) ($_GET['id'] ?? 0);
        $usuario = $this->usuarioModel->find($id);
        
        if (!$usuario) {
            $this->flash('danger', 'Usuario no encontrado.');
            $this->redirect(BASE_URL . '/admin/usuarios');
        }
        
        $titlePage = 'Editar Usuario';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        
        $this->view('usuarios/form', compact('titlePage', 'usuario', 'flash'));
    }
    
    public function update(): void {
        $this->requireRole('admin');
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        
        $data = [
            'nombres'    => $this->post('nombres'),
            'ap_paterno' => $this->post('ap_paterno'),
            'ap_materno' => $this->post('ap_materno'),
            'email'      => $this->post('email'),
            'rol'        => $this->post('rol', 'redactor'),
            'activo'     => !empty($_POST['activo']) ? 1 : 0,
        ];
        
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'nombres'    => 'requerido|max:100',
            'ap_paterno' => 'requerido|max:60',
            'email'      => 'requerido|email',
            'rol'        => 'enum:admin,editor,redactor',
        ]);
        
        if (!$valid) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . '/admin/usuarios/' . $id . '/editar');
        }
        
        if ($this->usuarioModel->emailExists($data['email'], $id)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ya existe otro usuario con ese correo.'];
            $this->redirect(BASE_URL . '/admin/usuarios/' . $id . '/editar');
        }
        
        // Actualizar password si se proporcionó una nueva
        if (!empty($_POST['password'])) {
            $newPassword = (string) $_POST['password'];
            if (strlen($newPassword) < 8) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'La contraseña debe tener al menos 8 caracteres.'];
                $this->redirect(BASE_URL . '/admin/usuarios/' . $id . '/editar');
            }
            $data['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        
        if ($id === 1 && (($data['rol'] ?? '') !== 'admin' || empty($data['activo']))) {
            $this->forbidden();
        }
        
        $this->usuarioModel->update($id, $data);
        $this->log('editar', 'usuario', $id, 'Usuario "' . $data['email'] . '" editado');
        $this->flash('success', 'Usuario actualizado.');
        $this->redirect(BASE_URL . '/admin/usuarios');
    }
    
    public function delete(): void {
        $this->requireRole('admin');
        $this->validateCsrf();
        
        $id = (int) ($_GET['id'] ?? 0);
        $usuario = $this->usuarioModel->find($id);
        
        if (!$usuario) {
            $this->flash('danger', 'Usuario no encontrado.');
            $this->redirect(BASE_URL . '/admin/usuarios');
        }
        
        // No permitir eliminar al usuario admin principal (id 1)
        if ($id === 1) {
            $this->flash('danger', 'No se puede eliminar el usuario administrador principal.');
            $this->redirect(BASE_URL . '/admin/usuarios');
        }
        
        $this->usuarioModel->delete($id);
        $this->log('eliminar', 'usuario', $id, 'Usuario "' . $usuario['email'] . '" eliminado');
        $this->flash('success', 'Usuario eliminado.');
        $this->redirect(BASE_URL . '/admin/usuarios');
    }
}