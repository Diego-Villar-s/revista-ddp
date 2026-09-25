<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Models\Especial;

class EspecialAdminController extends AdminController
{
    private Especial $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Especial();
    }

    public function index(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $filtros = [
            'busqueda' => $this->param('busqueda'),
            'activo' => $this->param('activo'),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $resultados = $this->model->paginateAdmin($filtros, $page, ITEMS_PER_PAGE);
        $titlePage = 'Especiales';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('especiales/index', compact('titlePage', 'resultados', 'filtros', 'flash'));
    }

    public function create(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $especial = [
            'titulo_completo' => '',
            'palabra_resaltada' => '',
            'titulo_leet' => '',
            'url_video' => '',
            'activo' => 1,
        ];
        $titlePage = 'Nuevo especial';
        $this->view('especiales/form', compact('titlePage', 'especial'));
    }

    public function store(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $data = $this->formData();
        $this->validateData($data, true);
        $id = $this->model->create($data);
        $this->log('crear', 'especial', $id, 'Especial "' . $data['titulo_completo'] . '" creado');
        $this->flash('success', 'Especial creado correctamente.');
        $this->redirect(BASE_URL . '/admin/especiales/' . $id . '/editar');
    }

    public function edit(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $id = (int) ($_GET['id'] ?? 0);
        $especial = $this->model->find($id);
        if (!$especial) {
            $this->flash('danger', 'Especial no encontrado.');
            $this->redirect(BASE_URL . '/admin/especiales');
        }
        $titlePage = 'Editar especial';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('especiales/form', compact('titlePage', 'especial', 'flash'));
    }

    public function update(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $existing = $this->model->find($id);
        if (!$existing) {
            $this->flash('danger', 'Especial no encontrado.');
            $this->redirect(BASE_URL . '/admin/especiales');
        }
        $data = $this->formData($existing);
        $this->validateData($data, false);
        $this->model->update($id, $data);
        $this->log('editar', 'especial', $id, 'Especial "' . $data['titulo_completo'] . '" editado');
        $this->flash('success', 'Especial actualizado correctamente.');
        $this->redirect(BASE_URL . '/admin/especiales/' . $id . '/editar');
    }

    public function delete(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $especial = $this->model->find($id);
        if (!$especial) {
            $this->flash('danger', 'Especial no encontrado.');
            $this->redirect(BASE_URL . '/admin/especiales');
        }
        $this->model->delete($id);
        $this->log('eliminar', 'especial', $id, 'Especial "' . $especial['titulo_completo'] . '" eliminado');
        $this->flash('success', 'Especial eliminado.');
        $this->redirect(BASE_URL . '/admin/especiales');
    }

    private function formData(array $existing = []): array
    {
        return [
            'titulo_completo' => $this->post('titulo_completo', (string) ($existing['titulo_completo'] ?? '')),
            'palabra_resaltada' => $this->post('palabra_resaltada', (string) ($existing['palabra_resaltada'] ?? '')),
            'titulo_leet' => $this->post('titulo_leet', (string) ($existing['titulo_leet'] ?? '')),
            'url_video' => $this->post('url_video', (string) ($existing['url_video'] ?? '')),
            'activo' => $this->post('activo', '0') === '1' ? 1 : 0,
        ];
    }

    private function validateData(array $data, bool $isNew): void
    {
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'titulo_completo' => 'requerido|max:255',
            'palabra_resaltada' => 'max:255',
            'titulo_leet' => 'requerido|max:255',
            'url_video' => 'requerido|url|max:500',
        ]);
        if (!$valid) {
            $this->flash('danger', 'Errores: ' . $validator->errorsAsString());
            $this->redirect(BASE_URL . ($isNew ? '/admin/especiales/nuevo' : '/admin/especiales/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
        if (ddpVideoEmbedUrl($data['url_video']) === '') {
            $this->flash('danger', 'La URL debe ser un enlace válido de YouTube o Vimeo.');
            $this->redirect(BASE_URL . ($isNew ? '/admin/especiales/nuevo' : '/admin/especiales/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
    }
}
