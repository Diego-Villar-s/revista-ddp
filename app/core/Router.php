<?php
namespace App\Core;

/**
 * Router manual con rutas exactas y parámetros simples.
 */
final class Router
{
    /** @var array<string, array<string, array{controller: class-string, method: string}>> */
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(): void
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $url = $this->parseUrl();
        unset($_GET['url']);

        if (isset($this->routes[$method][$url])) {
            $route = $this->routes[$method][$url];
            $this->callController($route['controller'], $route['method']);
            return;
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            $parameterNames = [];
            $regexParts = [];
            $segments = preg_split('/(\{\w+\})/', $pattern, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
            foreach ($segments as $segment) {
                if (preg_match('/^\{(\w+)\}$/', $segment, $parameterMatch) === 1) {
                    $parameterNames[] = $parameterMatch[1];
                    $multiSegment = in_array($parameterMatch[1], ['path', 'file', 'archivo'], true);
                    $regexParts[] = '(' . ($multiSegment ? '.+' : '[^/]+') . ')';
                } else {
                    $regexParts[] = preg_quote($segment, '#');
                }
            }
            $regex = '#^' . implode('', $regexParts) . '$#';
            if (!preg_match($regex, $url, $matches)) {
                continue;
            }

            foreach ($parameterNames as $index => $name) {
                $value = rawurldecode((string) ($matches[$index + 1] ?? ''));
                $_GET[$name] = $value;
            }
            $this->callController($route['controller'], $route['method']);
            return;
        }

        http_response_code(404);
        $this->render404();
    }

    private function parseUrl(): string
    {
        $value = $_GET['url'] ?? '/';
        if (!is_string($value)) {
            $value = '/';
        }
        $value = rawurldecode($value);
        $value = rtrim($value, '/');
        return '/' . ltrim($value, '/');
    }

    private function render404(): void
    {
        require_once APP_PATH . '/views/public/partials/helpers.php';
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        $seo = [
            'title' => 'Página no encontrada | ' . SITE_NAME,
            'description' => 'La página que buscas no existe.',
            'og_type' => 'website',
            'og_url' => BASE_URL . '/',
        ];
        ob_start();
        require APP_PATH . '/views/public/404.php';
        $content = (string) ob_get_clean();
        require APP_PATH . '/views/public/layout.php';
    }

    private function callController(string $controllerClass, string $method): void
    {
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo 'Error: Controlador no encontrado.';
            return;
        }
        $controller = new $controllerClass();
        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo 'Error: Método no encontrado.';
            return;
        }
        $controller->{$method}();
    }
}
