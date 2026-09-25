<?php
namespace App\Core;

/**
 * Base de controladores públicos.
 */
class Controller
{
    protected function view(string $view, array $data = []): void
    {
        require_once APP_PATH . '/views/public/partials/helpers.php';
        $this->ensureCsrfToken();

        if (!isset($data['siteConfig'])) {
            try {
                $data['siteConfig'] = (new \App\Models\Configuracion())->allKeyValue();
            } catch (\Throwable $exception) {
                error_log('No se pudo cargar la configuración pública: ' . $exception->getMessage());
                $data['siteConfig'] = [];
            }
        }

        extract($data, EXTR_SKIP);
        $viewPath = APP_PATH . '/views/public/' . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(500);
            echo 'Vista no encontrada: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();
        require APP_PATH . '/views/public/layout.php';
    }

    protected function standalone(string $view, array $data = []): void
    {
        $this->ensureCsrfToken();
        extract($data, EXTR_SKIP);
        $viewPath = APP_PATH . '/views/admin/' . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(500);
            echo 'Vista no encontrada: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }
        require $viewPath;
    }

    protected function render404(): void
    {
        http_response_code(404);
        $this->view('404', [
            'seo' => [
                'title' => 'Página no encontrada | ' . SITE_NAME,
                'description' => 'La página que buscas no existe.',
                'og_type' => 'website',
                'og_url' => BASE_URL . '/',
            ],
        ]);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function param(string $name, string $default = ''): string
    {
        $value = $_GET[$name] ?? $default;
        return is_scalar($value) ? trim(strip_tags((string) $value)) : '';
    }

    protected function post(string $name, string $default = ''): string
    {
        $value = $_POST[$name] ?? $default;
        return is_scalar($value) ? trim(strip_tags((string) $value)) : '';
    }

    protected function generateSlug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $text
        );
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text) ?? '';
        return trim($text, '-');
    }

    protected function ensureCsrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION[CSRF_TOKEN_NAME];
    }

    protected function csrfToken(): string
    {
        return $this->ensureCsrfToken();
    }

    protected function csrfField(): string
    {
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="'
            . htmlspecialchars($this->csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    protected function validateCsrf(): bool
    {
        $sessionToken = (string) ($_SESSION[CSRF_TOKEN_NAME] ?? '');
        $token = (string) ($_POST[CSRF_TOKEN_NAME] ?? '');
        return $sessionToken !== '' && $token !== '' && hash_equals($sessionToken, $token);
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect(BASE_URL . '/admin/login');
        }
    }
}
