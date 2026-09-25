# AGENTS.md

## Runtime y flujo HTTP

- Es una aplicación MVC manual en PHP 8.2, sin Composer, npm, framework ni generación de autoload. `public/index.php` es el único front controller y registra exclusivamente `App\Core`, `App\Models` y `App\Controllers`.
- Las rutas viven en `app/routes.php`. El `.htaccess` raíz envía las rutas limpias a `public/index.php`, deja `public/` y `uploads/` como árboles físicos y bloquea `app/`, `config/`, `database/`, `storage/` y `reference/`.
- `Router` coloca los parámetros de ruta en `$_GET`; los handlers POST existentes leen IDs desde `$_GET`, no desde `$_POST`. Los placeholders `path`, `file` y `archivo` son los únicos que aceptan `/`.
- `BASE_URL` es solo el prefijo de URL (`/revista-ddp`); `PUBLIC_URL` apunta a `/revista-ddp/public`. Cambiar la subcarpeta de despliegue obliga a revisar `config/config.php` y los `RewriteBase`.
- `sitemap.xml` y `robots.txt` se generan en `SitemapController`; actualiza su lista al agregar páginas públicas.

## Base de datos y contenido

- `database/schema.sql` y `database/seed.sql` son la fuente de instalación; no hay migraciones incrementales. Para sembrar de nuevo hay que recrear `revista_digital` porque el seed usa `INSERT` y no limpia tablas.
- La capa de datos es UTF-8 de extremo a extremo: el DSN de PDO declara `charset=utf8mb4`, ejecuta `SET NAMES utf8mb4`, y ambos SQL comienzan con esa sentencia; las tablas y columnas de texto usan `utf8mb4_unicode_ci`.
- La BD solo guarda rutas y metadata. `MediaProcessor` devuelve rutas relativas a `uploads/`; `ddpImgUrl()` convierte `assets/...` a `/public/assets` y las demás rutas a `/uploads/...`.
- Reportajes, podcasts y videos son públicos solo con `estado='publicado'` y fecha no futura; noticias y boletines también exigen `estado='publicado'`. Las páginas y la configuración institucional se administran mediante `paginas` y `configuracion`.
- La tabla `especiales` usa `titulo_completo`, `palabra_resaltada`, `titulo_leet`, `url_video`, `fecha_creacion` y `activo`; el texto leet se escribe manualmente en el admin. `EspecialAdminController` y `/admin/especiales` son la única fuente editorial de la sección.
- La portada consume solo `especiales` activos en un carrusel de cuatro items por página, con caption, flechas y puntos; no tiene CTA. `ddpResaltarLeet()` resalta el texto sin permitir HTML y `ddpVideoEmbedUrl()` convierte YouTube/Vimeo a embed.

## Verificación

- No hay PHPUnit, framework de tests, task runner, CI, lint o typecheck configurado. El chequeo disponible es `C:\xampp\php\php.exe -l <archivo.php>`; para todo el árbol: `Get-ChildItem -Recurse -Filter *.php | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }`.
- Las pruebas de integración se hacen con Apache/PHP y MariaDB reales. En este entorno el PDF de la BD se cargó con `C:\xampp\mysql\bin\mysql.exe`; no hay `php` en el `PATH` global.
- `public/healthcheck.php`, `server.php` y las configuraciones Apache de prueba son temporales: no deben quedar en una entrega.

## Seguridad y administración

- `public/index.php` inicia sesión antes de emitir salida. `AdminController` solo garantiza autenticación; cada acción debe añadir `requireRole`/`requireAnyRole` y `validateCsrf()` para mutaciones. Las llamadas AJAX envían `X-CSRF`.
- El rich text de TinyMCE usa `Validator::sanitizeHtml()`; no se debe escapar con `post()` como si fuera texto plano. Las acciones admin suelen registrar `AdminController::log()` y mostrar `flash`.
- El redactor queda limitado a sus propios contenidos y nunca debe poder publicar o borrar contenido ajeno, aunque manipule el POST. Los usuarios solo se administran como admin.
- En `ENVIRONMENT=local`, recuperación de contraseña muestra el enlace en pantalla y contacto solo escribe `storage/logs/contactos.txt`; no hay SMTP.

## Media

- `uploads/` debe quedar fuera de `public/` y ser escribible por Apache. No volver a una ruta PHP de streaming: el `.htaccess` raíz sirve `/uploads/...` directamente para Range Requests.
- `MediaProcessor` valida MIME/tamaño; con GD crea original/thumb/WebP, y sin GD conserva el original. `ffmpeg` es opcional: si existe transcodea audio/video y extrae poster; si no, conserva el archivo original.
- El endpoint público `/reproductor` solo devuelve filas publicadas y renderiza embed o archivo según la BD; el JS del modal debe pausar y eliminar `audio`/`video`/`iframe` al cerrar.
- Los triggers multimedia del sitio público son botones con `data-ddp-media-trigger`; el JS usa delegación en `document` y `preventDefault()` para evitar saltos de navegación. `ddp-overrides.css` y `ddp-media.js` se cargan con versión por `filemtime`.
- El overlay de reportajes usa `public/assets/img/brand-swoosh.svg`, con doble capa blanca/roja y el rojo de marca `#E0020D` extraído de `logo.png`; se aplica también a la imagen de detalle y galería.
- `.ddp-card-media` es el marco de imagen de las tarjetas: fija `aspect-ratio` (16:9 por defecto) con `overflow: hidden` y obliga a `width/height: 100%` + `object-fit: cover`. `MediaProcessor::resizeToWidth()` conserva la proporción original, así que el recorte depende del CSS; no reintroducir `height: auto` ni `max-height` en esas imágenes ni poner texto dentro del marco (el `overflow: hidden` lo recortaría).
- `reference/` solo puede contener capturas visuales solicitadas explícitamente por el usuario; no se copia al document root ni se sirve. Los assets públicos necesarios están en `public/assets/`.
