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
//
// El resolvedor busca los directorios sin distinguir mayusculas. El
// namespace es App\Controllers\Admin pero la carpeta es
// app/controllers/admin, y en Linux esa diferencia rompe la carga: el
// panel entero devolvia 500 "Controlador no encontrado" solo en
// produccion, porque en Windows el sistema de archivos no distingue.
$ddpResolve = static function (string $baseDir, string $relative): ?string {
    $segments = explode('\\', $relative);
    $fileName = array_pop($segments) . '.php';

    $directory = rtrim(str_replace('\\', '/', $baseDir), '/');
    foreach ($segments as $segment) {
        $candidate = $directory . '/' . $segment;
        if (is_dir($candidate)) {
            $directory = $candidate;
            continue;
        }
        $matched = null;
        $entries = @scandir($directory);
        if ($entries !== false) {
            foreach ($entries as $entry) {
                if ($entry !== '.' && $entry !== '..' && strcasecmp($entry, $segment) === 0) {
                    $matched = $directory . '/' . $entry;
                    break;
                }
            }
        }
        if ($matched === null) {
            return null;
        }
        $directory = $matched;
    }

    $file = $directory . '/' . $fileName;
    return is_file($file) ? $file : null;
};

$ddpAutoload = static function (string $prefix, string $baseDir) use ($ddpResolve) {
    return static function (string $class) use ($prefix, $baseDir, $ddpResolve): void {
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }
        $file = $ddpResolve($baseDir, substr($class, strlen($prefix)));
        if ($file !== null) {
            require_once $file;
        }
    };
};

spl_autoload_register($ddpAutoload('App\\Core\\', CORE_PATH));
spl_autoload_register($ddpAutoload('App\\Models\\', APP_PATH . '/models'));
spl_autoload_register($ddpAutoload('App\\Controllers\\', APP_PATH . '/controllers'));

$router = new App\Core\Router();
require APP_PATH . '/routes.php';
$router->dispatch();
