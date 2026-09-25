<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Pagina;
use App\Models\Configuracion;
use App\Core\Validator;

class PaginaController extends Controller
{
    public function sobreNosotros(): void
    {
        $this->renderPage('sobre-nosotros');
    }

    public function alianzas(): void
    {
        $this->renderPage('alianzas');
    }

    public function contacto(): void
    {
        $this->renderPage('contacto');
    }

    private function renderPage(string $slug): void
    {
        $pagina = (new Pagina())->findPublicBySlug($slug);
        if (!$pagina) {
            $this->render404();
            return;
        }
        $config = (new Configuracion())->allKeyValue();
        $seo = [
            'title' => $pagina['meta_titulo'] ?: ($pagina['titulo'] . ' | ' . SITE_NAME),
            'description' => $pagina['meta_descripcion'] ?: mb_substr(strip_tags((string) $pagina['contenido']), 0, 155),
            'og_type' => 'website',
            'og_url' => BASE_URL . '/' . rawurlencode($slug),
            'og_image' => $pagina['imagen'] ?? '',
        ];
        $this->view($slug, compact('pagina', 'seo') + ['siteConfig' => $config]);
    }

    public function enviarContacto(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect(BASE_URL . '/contacto?error=1&msg=csrfe');
        }

        $nombre = trim($this->post('nombre'));
        $email = trim($this->post('email'));
        $asunto = trim($this->post('asunto'));
        $mensaje = trim($this->post('mensaje'));

        if ($nombre === '' || $email === '' || $asunto === '' || $mensaje === '') {
            $this->redirect(BASE_URL . '/contacto?error=1&msg=complete');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect(BASE_URL . '/contacto?error=1&msg=email');
        }

        if (!is_dir(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0775, true);
        }
        $safeEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        $entry = date('Y-m-d H:i:s') . ' | Contacto: ' . $nombre
            . ' | ' . $safeEmail . ' | ' . $asunto . ' | '
            . substr($mensaje, 0, 200) . PHP_EOL;
        file_put_contents(LOGS_PATH . '/contactos.txt', $entry, FILE_APPEND | LOCK_EX);
        $this->redirect(BASE_URL . '/contacto?exito=1');
    }
}
