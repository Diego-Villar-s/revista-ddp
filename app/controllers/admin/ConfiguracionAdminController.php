<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Validator;
use App\Models\Configuracion;

class ConfiguracionAdminController extends AdminController
{
    public function index(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $titlePage = 'Configuración del sitio';
        $config = (new Configuracion())->allKeyValue();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('configuracion/index', compact('titlePage', 'config', 'flash'));
    }

    public function update(): void
    {
        $this->requireAnyRole(['admin', 'editor']);
        $this->validateCsrf();
        $model = new Configuracion();
        $values = [
            'site_description' => [$this->post('site_description'), 'text'],
            'institutional_text' => [$this->post('institutional_text'), 'text'],
            'about_cta' => [$this->post('about_cta'), 'text'],
            'contact_email' => [$this->post('contact_email'), 'email'],
            'social_facebook' => [$this->post('social_facebook'), 'url'],
            'social_tiktok' => [$this->post('social_tiktok'), 'url'],
            'social_instagram' => [$this->post('social_instagram'), 'url'],
            'youtube_embed' => [$this->post('youtube_embed'), 'url'],
            'youtube_title' => [$this->post('youtube_title'), 'text'],
        ];
        $validator = new Validator();
        $validationData = array_map(static fn (array $item): string => $item[0], $values);
        if (!$validator->validate($validationData, [
            'contact_email' => 'email',
            'social_facebook' => 'url',
            'social_tiktok' => 'url',
            'social_instagram' => 'url',
            'youtube_embed' => 'url',
        ])) {
            $this->flash('danger', 'Revisa los valores: ' . $validator->errorsAsString());
            $this->redirect(BASE_URL . '/admin/configuracion');
        }
        foreach ($values as $key => [$value, $type]) {
            $model->save($key, $value, $type);
        }
        $this->log('editar', 'configuracion', null, 'Configuración pública actualizada');
        $this->flash('success', 'Configuración actualizada.');
        $this->redirect(BASE_URL . '/admin/configuracion');
    }
}
