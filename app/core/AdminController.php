<?php
namespace App\Core;

/**
 * Middleware común del panel: sesión, roles, CSRF, flashes y logs.
 *
 * La autorización se aplica explícitamente en cada acción para que cada CRUD
 * mantenga una regla visible y no dependa solamente de la ruta.
 */
class AdminController
{
    protected array $user = [];

    public function __construct()
    {
        require_once APP_PATH . '/views/public/partials/helpers.php';
        if (!Auth::check()) {
            $this->redirect(BASE_URL . '/admin/login');
        }
        $this->user = Auth::user() ?? [];
    }

    protected function requireRole(string ...$roles): void
    {
        if (!Auth::hasRole(...$roles)) {
            $this->forbidden();
        }
    }

    protected function requireAnyRole(array $roles): void
    {
        if (!in_array($this->user['rol'] ?? '', $roles, true)) {
            $this->forbidden();
        }
    }

    protected function forbidden(): void
    {
        http_response_code(403);
        echo 'Acceso denegado: no tiene permisos suficientes para esta acción.';
        exit;
    }

    protected function view(string $view, array $data = []): void
    {
        require_once APP_PATH . '/views/public/partials/helpers.php';
        $this->ensureCsrfToken();
        if (!array_key_exists('flash', $data)) {
            $data['flash'] = $_SESSION['flash'] ?? null;
            unset($_SESSION['flash']);
        }
        extract($data, EXTR_SKIP);
        $viewPath = APP_PATH . '/views/admin/' . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(500);
            echo 'Vista admin no encontrada: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }
        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();
        require APP_PATH . '/views/admin/layout.php';
    }

    protected function standalone(string $view, array $data = []): void
    {
        require_once APP_PATH . '/views/public/partials/helpers.php';
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

    protected function post(string $name, string $default = ''): string
    {
        $value = $_POST[$name] ?? $default;
        return is_scalar($value) ? trim(strip_tags((string) $value)) : '';
    }

    protected function param(string $name, string $default = ''): string
    {
        $value = $_GET[$name] ?? $default;
        return is_scalar($value) ? trim(strip_tags((string) $value)) : '';
    }

    protected function postRaw(string $name, string $default = ''): string
    {
        $value = $_POST[$name] ?? $default;
        return is_scalar($value) ? (string) $value : '';
    }

    protected function validateCsrf(): void
    {
        $sessionToken = (string) ($_SESSION[CSRF_TOKEN_NAME] ?? '');
        $token = (string) ($_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF'] ?? '');
        if ($sessionToken === '' || $token === '' || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            echo 'Token CSRF inválido o expirado. Vuelva a cargar el formulario.';
            exit;
        }
    }

    protected function csrfField(): string
    {
        $this->ensureCsrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="'
            . htmlspecialchars((string) $_SESSION[CSRF_TOKEN_NAME], ENT_QUOTES, 'UTF-8') . '">';
    }

    protected function ensureCsrfToken(): void
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function uniqueSlug(string $slug, string $table, int $excludeId = 0): string
    {
        $allowedTables = ['reportajes', 'noticias', 'podcasts', 'videos', 'paginas'];
        if (!in_array($table, $allowedTables, true)) {
            throw new \InvalidArgumentException('Tabla no permitida para generar slug.');
        }
        $slug = $this->generateSlug($slug) ?: 'item';
        $base = $slug;
        $counter = 1;
        while (Database::getInstance()->count(
            "SELECT COUNT(*) FROM {$table} WHERE slug = ? AND id != ?",
            [$slug, $excludeId]
        ) > 0) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }

    protected function generateSlug(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $slug
        );
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '';
        return trim($slug, '-');
    }

    protected function log(string $accion, string $entidad, ?int $entidadId = null, string $detalles = ''): void
    {
        Database::getInstance()->insert(
            'INSERT INTO logs_actividad (usuario_id, accion, entidad, entidad_id, detalles) VALUES (?, ?, ?, ?, ?)',
            [Auth::id(), $accion, $entidad, $entidadId, $detalles]
        );
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
