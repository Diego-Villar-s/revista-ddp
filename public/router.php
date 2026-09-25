<?php

declare(strict_types=1);

/**
 * Router para el servidor embebido de PHP (`php -S`).
 *
 * Existe porque el servidor embebido NO reescribe URLs: a diferencia del
 * `RewriteRule (.*) public/index.php?url=$1` de Apache, no inyecta el
 * parámetro `url` que lee App\Core\Router::parseUrl(). Este archivo
 * reproduce ese comportamiento.
 *
 * Comportamiento:
 *   1. Bloquea directorios privados, archivos ocultos y uploads ejecutables.
 *   2. Sirve /uploads/... desde el directorio hermano (queda fuera del docroot).
 *   3. Sirve como archivo estatico lo que exista dentro de public/.
 *   4. Enruta todo lo demas a index.php con ?url=.
 */

$requestPath = urldecode((string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH));
if ($requestPath === '') {
    $requestPath = '/';
}

/** Normaliza a una ruta con barra inicial, sin barra final (salvo "/"). */
$normalize = static function (string $path): string {
    $path = '/' . ltrim(str_replace('\\', '/', $path), '/');
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }
    return $path === '' ? '/' : $path;
};
$requestPath = $normalize($requestPath);

/** Corta una respuesta y detiene la ejecucion. */
$abort = static function (int $status = 404): void {
    http_response_code($status);
    exit;
};

// ---------------------------------------------------------------- 1. Seguridad
// Sustituye al RewriteRule ^(?:app|config|database|storage|reference)(?:/|$) - [F,L]
// del .htaccess, que el servidor embebido no lee.
$privatePrefixes = ['/app', '/config', '/database', '/storage', '/reference', '/vendor', '/node_modules', '/tests'];
foreach ($privatePrefixes as $prefix) {
    if ($requestPath === $prefix || str_starts_with($requestPath, $prefix . '/')) {
        $abort();
    }
}

// Archivos ocultos (.env, .git, .user.ini...) y este propio router.
if (preg_match('#(^|/)\.#', $requestPath) || $requestPath === '/router.php') {
    $abort();
}

// El front controller nunca se sirve como archivo estatico.
if ($requestPath === '/index.php') {
    $abort();
}

// ------------------------------------------------------- 2. /uploads/ (fuera del docroot)
// uploads/ es hermano de public/, asi que el servidor embebido no lo alcanza.
// Se transmite con soporte de Range para que los videos conserven la barra
// de busqueda. Equivale a lo que hacia el .htaccess raiz sirviendo el arbol.
if ($requestPath === '/uploads' || str_starts_with($requestPath, '/uploads/')) {
    $uploadsRoot = realpath(__DIR__ . '/../uploads');
    $relative = ltrim(substr($requestPath, strlen('/uploads')), '/');
    $target = $uploadsRoot !== false && $relative !== '' ? realpath($uploadsRoot . '/' . $relative) : false;

    // Sin archivo, con traversal o fuera de uploads/: se corta.
    if (
        $target === false
        || !is_file($target)
        || $uploadsRoot === false
        || !str_starts_with($target, $uploadsRoot . DIRECTORY_SEPARATOR)
    ) {
        $abort();
    }

    // Nunca ejecutar codigo subido por el admin (uploads/.htaccess).
    if (preg_match('/\.(php|phtml|phar|cgi|pl|py|sh|htaccess)$/i', $target)) {
        $abort();
    }

    $size = (int) filesize($target);
    $start = 0;
    $end = $size - 1;
    $status = 200;

    $range = $_SERVER['HTTP_RANGE'] ?? '';
    if ($range !== '' && preg_match('/^bytes=(\d*)-(\d*)$/', trim($range), $matches)) {
        if ($matches[1] !== '') {
            $start = (int) $matches[1];
        }
        if ($matches[2] !== '') {
            $end = (int) $matches[2];
        }
        if ($end >= $size) {
            $end = $size - 1;
        }
        if ($start > $end || $start >= $size) {
            http_response_code(416);
            header('Content-Range: bytes */' . $size);
            exit;
        }
        $status = 206;
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
    }

    $mime = function_exists('mime_content_type') ? (mime_content_type($target) ?: null) : null;
    header('Content-Type: ' . ($mime ?? 'application/octet-stream'));
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . ($end - $start + 1));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=2592000');
    http_response_code($status);

    $handle = fopen($target, 'rb');
    if ($handle === false) {
        $abort(500);
    }
    fseek($handle, $start);
    $remaining = $end - $start + 1;
    while ($remaining > 0 && !feof($handle)) {
        $chunk = fread($handle, (int) min(8192, $remaining));
        if ($chunk === false || $chunk === '') {
            break;
        }
        echo $chunk;
        $remaining -= strlen($chunk);
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }
    fclose($handle);
    exit;
}

// ------------------------------------------- 3. Ajustes del prefijo /public
// La app construye PUBLIC_URL como BASE_URL . '/public'. Con el docroot en
// public/ esa ruta no existe, asi que se avisa a la config que aqui public/
// ES la raiz del sitio. Asi el HTML pide /assets/... en vez de
// /public/assets/... y los archivos se resuelven como estaticos.
if ($requestPath === '/public') {
    $requestPath = '/';
} elseif (str_starts_with($requestPath, '/public/')) {
    $requestPath = $normalize(substr($requestPath, strlen('/public')));
}
putenv('APP_PUBLIC_URL=/');
putenv('APP_UPLOADS_URL=/uploads');
$_ENV['APP_PUBLIC_URL'] = '/';
$_ENV['APP_UPLOADS_URL'] = '/uploads';

// --------------------------------------------- 4. Archivos estaticos de public/
// Devolver false hace que el servidor embebido sirva el archivo tal cual,
// con su Content-Type correcto (text/css, image/png...).
if ($requestPath !== '/' && !str_ends_with($requestPath, '.php')) {
    $candidate = realpath(__DIR__ . $requestPath);
    if (
        $candidate !== false
        && is_file($candidate)
        && str_starts_with($candidate, __DIR__ . DIRECTORY_SEPARATOR)
    ) {
        return false;
    }
}

// ------------------------------------------------------- 5. Front controller
// Aqui esta el equivalente del RewriteRule de Apache: el Router lee $_GET['url'].
$_GET['url'] = $requestPath;
require __DIR__ . '/index.php';
