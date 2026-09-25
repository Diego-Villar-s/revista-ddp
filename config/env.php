<?php

declare(strict_types=1);

/**
 * Cargador de variables de entorno sin dependencias externas.
 *
 * El proyecto no usa Composer, así que no hay phpdotenv disponible. Este
 * archivo reproduce el comportamiento mínimo necesario:
 *
 *   1. Lee el archivo .env de la raíz del proyecto (solo en local).
 *   2. NO sobrescribe variables ya definidas por el sistema: en Railway las
 *      variables llegan por el panel y tienen prioridad sobre el archivo.
 *   3. Expone ddp_env() para leer valores con valor por defecto.
 */

/**
 * Normaliza el valor crudo de una línea .env.
 * Admite comillas simples/dobles y comentarios al final de la línea.
 */
function ddp_env_parse_value(string $raw): string
{
    $value = trim($raw);

    // Comentario de línea completa o al final (respeta las comillas).
    if ($value !== '' && $value[0] === '#') {
        return '';
    }
    if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'")) {
        $quote = $value[0];
        $end = strrpos($value, $quote);
        if ($end !== false && $end > 0) {
            $inner = substr($value, 1, $end - 1);
            return $quote === '"'
                ? str_replace(['\\n', '\\t', '\\"', '\\\\'], ["\n", "\t", '"', '\\'], $inner)
                : $inner;
        }
    }

    // Valor sin comillas: elimina el comentario final separado por espacio.
    $pos = strpos($value, ' #');
    if ($pos !== false) {
        $value = substr($value, 0, $pos);
    }

    return trim($value);
}

/**
 * Carga un archivo .env en el entorno del proceso.
 * Las variables ya presentes (inyectadas por el servidor) nunca se pisan.
 */
function ddp_env_load(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }

        $separator = strpos($line, '=');
        if ($separator === false) {
            continue;
        }

        $name = trim(substr($line, 0, $separator));
        if ($name === '' || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) !== 1) {
            continue;
        }

        // Prioridad del servidor: si ya existe, no se sobrescribe.
        if (getenv($name) !== false || isset($_ENV[$name]) || isset($_SERVER[$name])) {
            continue;
        }

        $value = ddp_env_parse_value(substr($line, $separator + 1));
        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

/**
 * Lee una variable de entorno devolviendo $default si no existe o está vacía.
 */
function ddp_env(string $name, ?string $default = null): ?string
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? null;
    }
    if ($value === null) {
        return $default;
    }
    // Recorta el espacio exterior: al copiar valores desde el panel de
    // Railway o desde un .env se cuela un espacio con facilidad. Con
    // DB_USER valiendo " root" en vez de "root", MySQL responde
    // "Access denied for user ' root'" con la contrasena correcta.
    $value = trim((string) $value);
    return $value === '' ? $default : $value;
}

/**
 * Detecta el prefijo URL donde vive la aplicación a partir de SCRIPT_NAME.
 *
 * Funciona en los dos despliegues previstos sin tocar el código:
 *   - XAMPP en subcarpeta: SCRIPT_NAME=/revista-ddp/public/index.php -> /revista-ddp
 *   - Railway en la raiz:   SCRIPT_NAME=/public/index.php             -> (vacio)
 *
 * Devuelve '' cuando la app vive en la raíz del dominio.
 */
function ddp_detect_base_url(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $position = strpos($script, '/public/');
    if ($position === false) {
        return '';
    }
    return rtrim(substr($script, 0, $position), '/');
}

/**
 * Normaliza un prefijo URL: sin barra final y con barra inicial si no es vacío.
 */
function ddp_normalize_base_url(string $url): string
{
    $url = trim(str_replace('\\', '/', $url));
    if ($url === '' || $url === '/') {
        return '';
    }
    return '/' . trim($url, '/');
}

/**
 * Resuelve la configuración de base de datos.
 *
 * Prioridad:
 *   1. DATABASE_URL  (formato mysql://usuario:clave@host:puerto/basedatos)
 *   2. DB_HOST / DB_PORT / DB_NAME / DB_USER / DB_PASS
 */
function ddp_database_settings(): array
{
    $settings = [
        'host' => ddp_env('DB_HOST', 'localhost'),
        'port' => ddp_env('DB_PORT', '3306'),
        'name' => ddp_env('DB_NAME', 'revista_digital'),
        'user' => ddp_env('DB_USER', 'root'),
        'pass' => ddp_env('DB_PASS', ''),
    ];

    $url = ddp_env('DATABASE_URL');
    if ($url === null || $url === '') {
        return $settings;
    }

    $parts = parse_url($url);
    if ($parts === false || !isset($parts['host'])) {
        return $settings;
    }

    $settings['host'] = $parts['host'];
    $settings['port'] = (string) ($parts['port'] ?? 3306);
    if (isset($parts['path'])) {
        $database = ltrim($parts['path'], '/');
        if ($database !== '') {
            $settings['name'] = $database;
        }
    }
    if (isset($parts['user'])) {
        $settings['user'] = rawurldecode($parts['user']);
    }
    if (isset($parts['pass'])) {
        $settings['pass'] = rawurldecode($parts['pass']);
    }

    return $settings;
}
