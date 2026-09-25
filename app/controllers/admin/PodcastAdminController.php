<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Auth;
use App\Core\MediaProcessor;
use App\Core\Validator;
use App\Models\Podcast;

class PodcastAdminController extends AdminController
{
    private Podcast $model;
    private ?string $mediaWarning = null;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Podcast();
    }

    public function index(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $filtros = [
            'estado' => $this->param('estado'),
            'busqueda' => $this->param('busqueda'),
        ];
        if (Auth::hasRole('redactor')) {
            $filtros['usuario_id'] = Auth::id();
        }
        $resultados = $this->model->paginateAdmin($filtros, max(1, (int) ($_GET['page'] ?? 1)), ITEMS_PER_PAGE);
        $titlePage = 'Podcasts';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('podcasts/index', compact('titlePage', 'resultados', 'filtros', 'flash'));
    }

    public function create(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $podcast = [
            'titulo' => '', 'slug' => '', 'descripcion' => '', 'tipo' => 'embed',
            'url_embed' => '', 'archivo_audio' => '', 'portada' => '',
            'duracion_segundos' => null, 'tamano_bytes' => null,
            'fecha_publicacion' => date('Y-m-d'), 'estado' => 'borrador',
        ];
        $titlePage = 'Nuevo Podcast';
        $this->view('podcasts/form', compact('titlePage', 'podcast'));
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
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['titulo'], 'podcasts');
        $this->processFiles($data, true);
        $id = $this->model->create($data);
        $this->log('crear', 'podcast', $id, 'Podcast "' . $data['titulo'] . '" creado');
        if ($this->mediaWarning) {
            $this->flash('warning', 'Podcast creado. ' . $this->mediaWarning);
        } else {
            $this->flash('success', 'Podcast creado correctamente.');
        }
        $this->redirect(BASE_URL . '/admin/podcasts/' . $id . '/editar');
    }

    public function edit(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $id = (int) ($_GET['id'] ?? 0);
        $podcast = $this->model->find($id);
        if (!$podcast) {
            $this->flash('danger', 'Podcast no encontrado.');
            $this->redirect(BASE_URL . '/admin/podcasts');
        }
        $this->assertOwner($podcast);
        $titlePage = 'Editar Podcast';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('podcasts/form', compact('titlePage', 'podcast', 'flash'));
    }

    public function update(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $existing = $this->model->find($id);
        if (!$existing) {
            $this->flash('danger', 'Podcast no encontrado.');
            $this->redirect(BASE_URL . '/admin/podcasts');
        }
        $this->assertOwner($existing);
        $data = $this->formData($existing);
        $this->validateData($data, false);
        if (Auth::hasRole('redactor')) {
            $data['estado'] = 'borrador';
        }
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['titulo'], 'podcasts', $id);
        $this->processFiles($data, false);
        $this->model->update($id, $data);
        $this->log('editar', 'podcast', $id, 'Podcast "' . $data['titulo'] . '" editado');
        if ($this->mediaWarning) {
            $this->flash('warning', 'Podcast actualizado. ' . $this->mediaWarning);
        } else {
            $this->flash('success', 'Podcast actualizado correctamente.');
        }
        $this->redirect(BASE_URL . '/admin/podcasts/' . $id . '/editar');
    }

    public function delete(): void
    {
        $this->requireAnyRole(['admin', 'editor', 'redactor']);
        $this->validateCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $podcast = $this->model->find($id);
        if (!$podcast) {
            $this->flash('danger', 'Podcast no encontrado.');
            $this->redirect(BASE_URL . '/admin/podcasts');
        }
        if (Auth::hasRole('redactor') && ((int) $podcast['usuario_id'] !== Auth::id() || $podcast['estado'] !== 'borrador')) {
            $this->forbidden();
        }
        if (!Auth::hasRole('admin', 'editor', 'redactor')) {
            $this->forbidden();
        }
        $this->model->delete($id);
        $this->log('eliminar', 'podcast', $id, 'Podcast "' . $podcast['titulo'] . '" eliminado');
        $this->flash('success', 'Podcast eliminado.');
        $this->redirect(BASE_URL . '/admin/podcasts');
    }

    private function formData(array $existing): array
    {
        return [
            'titulo' => $this->post('titulo'),
            'slug' => $this->post('slug'),
            'descripcion' => $this->post('descripcion'),
            'tipo' => $this->post('tipo', 'embed'),
            'url_embed' => $this->post('url_embed'),
            'archivo_audio' => $this->post('archivo_audio_actual', (string) ($existing['archivo_audio'] ?? '')),
            'portada' => $this->post('portada_actual', (string) ($existing['portada'] ?? '')),
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
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Errores: ' . $validator->errorsAsString()];
            $this->redirect(BASE_URL . ($isNew ? '/admin/podcasts/nuevo' : '/admin/podcasts/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
        if ($data['tipo'] === 'embed' && trim($data['url_embed']) === '') {
            $this->flash('danger', 'Debe indicar el enlace embed del podcast.');
            $this->redirect(BASE_URL . ($isNew ? '/admin/podcasts/nuevo' : '/admin/podcasts/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
        if ($data['tipo'] === 'archivo' && trim((string) $data['archivo_audio']) === '' && empty($_FILES['archivo_audio']['tmp_name'])) {
            $this->flash('danger', 'Debe seleccionar un archivo de audio.');
            $this->redirect(BASE_URL . ($isNew ? '/admin/podcasts/nuevo' : '/admin/podcasts/' . (int) ($_GET['id'] ?? 0) . '/editar'));
        }
    }

    private function processFiles(array &$data, bool $isNew): void
    {
        $media = new MediaProcessor();
        if (!empty($_FILES['portada']['tmp_name']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
            $rutas = $media->processImage($_FILES['portada'], 'podcasts/portadas', 'portada');
            if ($rutas) {
                $data['portada'] = $rutas['ruta_webp'];
            }
        }
        if ($data['tipo'] === 'archivo' && !empty($_FILES['archivo_audio']['tmp_name']) && $_FILES['archivo_audio']['error'] === UPLOAD_ERR_OK) {
            $ruta = $media->processAudio($_FILES['archivo_audio'], 'podcasts/audio', 'audio');
            if ($ruta) {
                $data['archivo_audio'] = $ruta;
                $data['tamano_bytes'] = null;
                if (!$media->hasFfmpeg()) {
                    $this->mediaWarning = 'ffmpeg no está disponible: se guardó el audio original sin transcode.';
                }
            } else {
                $this->flash('warning', 'No se pudo procesar el audio: ' . implode(', ', $media->errors()));
            }
        }
    }

    private function assertOwner(array $podcast): void
    {
        if (Auth::hasRole('redactor') && (int) $podcast['usuario_id'] !== Auth::id()) {
            $this->forbidden();
        }
    }
}
