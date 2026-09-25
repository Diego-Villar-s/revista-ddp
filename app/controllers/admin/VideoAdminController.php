<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Auth;
use App\Core\MediaProcessor;
use App\Core\Validator;
use App\Models\Video;

class VideoAdminController extends AdminController
{
    private Video $model;
    private ?string $mediaWarning = null;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Video();
    }

    public function index(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $filtros = ['estado' => $this->param('estado'), 'busqueda' => $this->param('busqueda')];
        if (Auth::hasRole('redactor')) {
            $filtros['usuario_id'] = Auth::id();
        }
        $resultados = $this->model->paginateAdmin($filtros, max(1, (int) ($_GET['page'] ?? 1)), ITEMS_PER_PAGE);
        $titlePage = 'Videos';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('videos/index', compact('titlePage', 'resultados', 'filtros', 'flash'));
    }

    public function create(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $video = [
            'titulo' => '', 'slug' => '', 'descripcion' => '', 'tipo' => 'embed',
            'url_embed' => '', 'archivo_video' => '', 'poster' => '',
            'duracion_segundos' => null, 'tamano_bytes' => null,
            'fecha_publicacion' => date('Y-m-d'), 'estado' => 'borrador',
        ];
        $titlePage = 'Nuevo Video';
        $this->view('videos/form', compact('titlePage', 'video'));
    }

    public function store(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        $data = $this->formData([]);
        $this->validateData($data, true);
        $data['usuario_id'] = Auth::id();
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['titulo'], 'videos');
        $this->processFiles($data);
        $id = $this->model->create($data);
        $this->log('crear', 'video', $id, 'Video "' . $data['titulo'] . '" creado');
        if ($this->mediaWarning) {
            $this->flash('warning', 'Video creado. ' . $this->mediaWarning);
        } else {
            $this->flash('success', 'Video creado correctamente.');
        }
        $this->redirect(BASE_URL . '/admin/videos/' . $id . '/editar');
    }

    public function edit(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $id = (int) ($_GET['id'] ?? 0);
        $video = $this->model->find($id);
        if (!$video) {
            $this->flash('danger', 'Video no encontrado.');
            $this->redirect(BASE_URL . '/admin/videos');
        }
        $this->assertOwner($video);
        $titlePage = 'Editar Video';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('videos/form', compact('titlePage', 'video', 'flash'));
    }

    public function update(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $existing = $this->model->find($id);
        if (!$existing) {
            $this->flash('danger', 'Video no encontrado.');
            $this->redirect(BASE_URL . '/admin/videos');
        }
        $this->assertOwner($existing);
        $data = $this->formData($existing);
        $this->validateData($data, false);
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['titulo'], 'videos', $id);
        $this->processFiles($data);
        $this->model->update($id, $data);
        $this->log('editar', 'video', $id, 'Video "' . $data['titulo'] . '" editado');
        if ($this->mediaWarning) {
            $this->flash('warning', 'Video actualizado. ' . $this->mediaWarning);
        } else {
            $this->flash('success', 'Video actualizado correctamente.');
        }
        $this->redirect(BASE_URL . '/admin/videos/' . $id . '/editar');
    }

    public function delete(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $video = $this->model->find($id);
        if (!$video) {
            $this->flash('danger', 'Video no encontrado.');
            $this->redirect(BASE_URL . '/admin/videos');
        }
        if (Auth::hasRole('redactor') && ((int) $video['usuario_id'] !== Auth::id() || $video['estado'] !== 'borrador')) {
            $this->forbidden();
        }
        $this->model->delete($id);
        $this->log('eliminar', 'video', $id, 'Video "' . $video['titulo'] . '" eliminado');
        $this->flash('success', 'Video eliminado.');
        $this->redirect(BASE_URL . '/admin/videos');
    }

    private function formData(array $existing): array
    {
        return [
            'titulo' => $this->post('titulo'),
            'slug' => $this->post('slug'),
            'descripcion' => $this->post('descripcion'),
            'tipo' => $this->post('tipo', 'embed'),
            'url_embed' => $this->post('url_embed'),
            'archivo_video' => $this->post('archivo_video_actual', (string) ($existing['archivo_video'] ?? '')),
            'poster' => $this->post('poster_actual', (string) ($existing['poster'] ?? '')),
            'fecha_publicacion' => $this->post('fecha_publicacion', date('Y-m-d')),
            'estado' => $this->post('estado', 'borrador'),
        ];
    }

    private function validateData(array $data, bool $isNew): void
    {
        $validator = new Validator();
        $valid = $validator->validate($data, [
            'titulo' => 'requerido|max:255',
            'slug' => 'requerido|slug',
            'tipo' => 'enum:embed,archivo',
            'url_embed' => 'url',
            'fecha_publicacion' => 'requerido|fecha',
        ]);
        if (!$valid) {
            $this->flash('danger', 'Errores: ' . $validator->errorsAsString());
            $this->redirect(BASE_URL . ($isNew ? '/admin/videos/nuevo' : '/admin/videos/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
        if ($data['tipo'] === 'embed' && trim($data['url_embed']) === '') {
            $this->flash('danger', 'Debe indicar el enlace embed del video.');
            $this->redirect(BASE_URL . ($isNew ? '/admin/videos/nuevo' : '/admin/videos/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
        if ($data['tipo'] === 'archivo' && trim((string) $data['archivo_video']) === '' && empty($_FILES['archivo_video']['tmp_name'])) {
            $this->flash('danger', 'Debe seleccionar un archivo de video.');
            $this->redirect(BASE_URL . ($isNew ? '/admin/videos/nuevo' : '/admin/videos/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
    }

    private function processFiles(array &$data): void
    {
        $media = new MediaProcessor();
        if ($data['tipo'] === 'archivo' && !empty($_FILES['archivo_video']['tmp_name']) && $_FILES['archivo_video']['error'] === UPLOAD_ERR_OK) {
            $result = $media->processVideo($_FILES['archivo_video'], 'videos/archivo', 'video');
            if ($result) {
                $data['archivo_video'] = $result['ruta_video'];
                if (!empty($result['poster'])) {
                    $data['poster'] = $result['poster'];
                }
                if (!$media->hasFfmpeg()) {
                    $this->mediaWarning = 'ffmpeg no está disponible: se guardó el video original sin transcode.';
                }
            } else {
                $this->flash('warning', 'Procesamiento de video: ' . implode(', ', $media->errors()));
            }
        }
        if (!empty($_FILES['poster']['tmp_name']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $rutas = $media->processImage($_FILES['poster'], 'videos/poster', 'poster');
            if ($rutas) {
                $data['poster'] = $rutas['ruta_webp'];
            }
        }
    }

    private function assertOwner(array $video): void
    {
        if (Auth::hasRole('redactor') && (int) $video['usuario_id'] !== Auth::id()) {
            $this->forbidden();
        }
    }
}
