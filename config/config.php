<?php
/**
 * Configuración general de DDP Noticias.
 *
 * Todos los valores sensibles y de despliegue llegan por variables de entorno.
 * Ver .env.example para el detalle. En local se puede usar un archivo .env en la
 * raíz; en Railway las variables se configuran desde el panel del servicio.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

// El .env solo rellena lo que el servidor no traiga ya definido.
ddp_env_load(dirname(__DIR__) . '/.env');

// Entorno: local, staging o production.
define('ENVIRONMENT', ddp_env('APP_ENV', 'local'));

if (ENVIRONMENT === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
    ini_set('default_charset', 'UTF-8');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('default_charset', 'UTF-8');
}

// Prefijo URL de la instalación, sin barra final. Se detecta solo salvo que se
// fuerce con APP_BASE_URL (útil si el proxy no expone public/ en SCRIPT_NAME).
define('BASE_URL', ddp_normalize_base_url((string) (ddp_env('APP_BASE_URL') ?? ddp_detect_base_url())));

// public/ y uploads/ siguen siendo árboles físicos servidos por el web server.
define('PUBLIC_URL', rtrim((string) (ddp_env('APP_PUBLIC_URL') ?? (BASE_URL . '/public')), '/'));
define('UPLOADS_URL', rtrim((string) (ddp_env('APP_UPLOADS_URL') ?? (BASE_URL . '/uploads')), '/'));

// Rutas físicas absolutas del proyecto.
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CORE_PATH', APP_PATH . '/core');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');

// MariaDB 10.4+/MySQL 8.0+. Railway puede inyectar DATABASE_URL; si no, se usan
// las variables discretas. Ver ddp_database_settings() en config/env.php.
$dppDatabase = ddp_database_settings();
define('DB_HOST', $dppDatabase['host']);
define('DB_PORT', $dppDatabase['port']);
define('DB_NAME', $dppDatabase['name']);
define('DB_USER', $dppDatabase['user']);
define('DB_PASS', $dppDatabase['pass']);
unset($dppDatabase);

// La BD solo guarda rutas; los binarios viven en uploads/.
define('MAX_FILE_SIZE', 50 * 1024 * 1024);
define('MAX_IMAGE_SIZE', 10 * 1024 * 1024);
define('MAX_AUDIO_SIZE', 50 * 1024 * 1024);
define('MAX_VIDEO_SIZE', 200 * 1024 * 1024);
define('MAX_PDF_SIZE', 50 * 1024 * 1024);
define('IMAGE_MAX_WIDTH', 1920);
define('IMAGE_THUMB_WIDTH', 400);
define('IMAGE_QUALITY', 80);

define('ITEMS_PER_PAGE', 10); // listados generales
define('REPORT_ITEMS_PER_PAGE', 12); // listado público de reportajes

define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);
define('PASSWORD_RESET_EXPIRY_HOURS', 1);

define('SITE_NAME', 'DDP Noticias');
define('SITE_NAME_FULL', 'Diálogo y Desarrollo Perú');
define('SITE_DESCRIPTION', 'Portal de noticias sociales, territoriales y de desarrollo sostenible');
