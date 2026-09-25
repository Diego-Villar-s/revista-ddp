<?php
/**
 * Front Controller: único punto de entrada HTTP.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

// La sesión se inicia una sola vez y antes de emitir cualquier salida.
require_once APP_PATH . '/core/Session.php';
App\Core\Session::start(BASE_URL . '/');

// Autoload manual: no existe Composer en este proyecto.
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\Core\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = CORE_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\Models\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/models/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\Controllers\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/controllers/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$router = new App\Core\Router();
require APP_PATH . '/routes.php';
$router->dispatch();
