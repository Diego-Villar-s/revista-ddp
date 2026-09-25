<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Models\Pagina;

class PaginaAdminController extends AdminController
{
    private Pagina $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Pagina();
    }

    public function index(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $titlePage = 'Páginas';
        $paginas = $this->model->all('titulo ASC');
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('paginas/index', compact('titlePage', 'paginas', 'flash'));
    }

    public function create(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $pagina = [
            'slug' => '', 'titulo' => '', 'contenido' => '', 'imagen' => '',
            'meta_titulo' => '', 'meta_descripcion' => '', 'activo' => 1,
        ];
        $titlePage = 'Nueva Página';
        $this->view('paginas/form', compact('titlePage', 'pagina'));
    }

    public function store(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $data = $this->formData([]);
        $this->validateData($data, true);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['titulo'], 'paginas');
        $id = $this->model->create($data);
        $this->log('crear', 'pagina', $id, 'Página "' . $data['titulo'] . '" creada');
        $this->flash('success', 'Página creada correctamente.');
        $this->redirect(BASE_URL . '/admin/paginas');
    }

    public function edit(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $id = (int) ($_GET['id'] ?? 0);
        $pagina = $this->model->find($id);
        if (!$pagina) {
            $this->flash('danger', 'Página no encontrada.');
            $this->redirect(BASE_URL . '/admin/paginas');
        }
        $titlePage = 'Editar Página';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('paginas/form', compact('titlePage', 'pagina', 'flash'));
    }

    public function update(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $existing = $this->model->find($id);
        if (!$existing) {
            $this->flash('danger', 'Página no encontrada.');
            $this->redirect(BASE_URL . '/admin/paginas');
        }
        $data = $this->formData($existing);
        $this->validateData($data, false);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['titulo'], 'paginas', $id);
        $this->model->update($id, $data);
        $this->log('editar', 'pagina', $id, 'Página "' . $data['titulo'] . '" editada');
        $this->flash('success', 'Página actualizada correctamente.');
        $this->redirect(BASE_URL . '/admin/paginas');
    }

    public function delete(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $pagina = $this->model->find($id);
        if (!$pagina) {
            $this->flash('danger', 'Página no encontrada.');
            $this->redirect(BASE_URL . '/admin/paginas');
        }
        $this->model->delete($id);
        $this->log('eliminar', 'pagina', $id, 'Página "' . $pagina['titulo'] . '" eliminada');
        $this->flash('success', 'Página eliminada.');
        $this->redirect(BASE_URL . '/admin/paginas');
    }

    private function formData(array $existing): array
    {
        return [
            'slug' => $this->post('slug'),
            'titulo' => $this->post('titulo'),
            'contenido' => Validator::sanitizeHtml($this->postRaw('contenido')),
            'imagen' => $this->post('imagen_actual', (string) ($existing['imagen'] ?? '')),
            'meta_titulo' => $this->post('meta_titulo'),
            'meta_descripcion' => $this->post('meta_descripcion'),
            'activo' => !empty($_POST['activo']) ? 1 : 0,
        ];
    }

    private function validateData(array $data, bool $isNew): void
    {
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'slug' => 'requerido|slug',
            'titulo' => 'requerido|max:180',
            'meta_descripcion' => 'max:320',
        ]);
        if (!$valid) {
            $this->flash('danger', 'Errores: ' . $validator->errorsAsString());
            $this->redirect(BASE_URL . ($isNew ? '/admin/paginas/nuevo' : '/admin/paginas/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
    }
}
