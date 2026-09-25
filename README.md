# DDP Noticias — Portal y panel administrativo

Portal de noticias de **Diálogo y Desarrollo Perú** implementado en PHP puro con MVC manual, Apache y MySQL/MariaDB. El sitio público consume la base de datos y el panel editorial permite administrar reportajes, noticias, boletines, podcasts, videos, autores, páginas, configuración y usuarios.

## 1. Requisitos

- Windows con **XAMPP**.
- Apache 2.4 con `mod_rewrite` habilitado.
- PHP 8.2 con `pdo_mysql`, `fileinfo`, `mbstring` y `session`.
- `gd` es recomendable para optimizes imágenes; si no está disponible, el procesador conserva el original para no perder la subida.
- MySQL 8.0+ o MariaDB 10.4+.
- `ffmpeg` es opcional. Si está en el `PATH`, se convierten audio/video; si no, se conserva el archivo original.

El proyecto no usa Composer, npm, Symfony, Laravel ni un paso de compilación de assets. El panel carga Tabler y TinyMCE desde CDN; los assets públicos de la referencia están guardados localmente.

## 2. Instalación en XAMPP

1. Copie la carpeta autónoma `revista-ddp/` a `C:\xampp\htdocs\revista-ddp`.
2. Verifique en `C:\xampp\apache\conf\httpd.conf` que exista `LoadModule rewrite_module modules/mod_rewrite.so` y que el directorio tenga `AllowOverride All`.
3. Inicie Apache y MySQL desde el panel de XAMPP.
4. Abra phpMyAdmin, cree/seleccione la base `revista_digital` e importe en este orden:
   - `database/schema.sql`
   - `database/seed.sql`
   Ambos archivos están guardados en UTF-8 sin BOM y empiezan con `SET NAMES utf8mb4;` para que las tildes y ñ no se conviertan al importar. Si usa la consola de MySQL, añada `--default-character-set=utf8mb4`.
5. Para volver a sembrar desde cero, elimine primero la base `revista_digital`. Los scripts no son migraciones incrementales ni limpian datos existentes.
6. Copie `.env.example` a `.env` y ajústelo si hace falta. La instalación local usa `APP_BASE_URL=/revista-ddp`, `DB_HOST=localhost`, `DB_USER=root` y `DB_PASS` vacía. Ajuste `APP_BASE_URL` si la carpeta tiene otro nombre.
7. Garantice permisos de escritura para el usuario de Apache en `uploads/` y `storage/logs/`.
8. Para archivos grandes, ajuste en `php.ini` `upload_max_filesize=200M`, `post_max_size=220M` y `max_file_uploads=20`, y reinicie Apache.

En XAMPP se deja el DocumentRoot en `C:\\xampp\\htdocs`: el `.htaccess` de la carpeta autónoma envía las rutas a `public/index.php` y deja `/uploads/` como directorio físico. No se copia `uploads/` dentro de `public/`.

URLs:

- Público: `http://localhost/revista-ddp/`
- Reportajes: `http://localhost/revista-ddp/reportajes`
- Página 2 (con 13 o más reportajes publicados): `http://localhost/revista-ddp/reportajes/pagina/2`
- Panel: `http://localhost/revista-ddp/admin/login`

## 3. Variables de entorno

La configuración **no** está hardcodeada en `config/config.php`. Todo valor de
conexión o despliegue se lee de una variable de entorno. Copie `.env.example` a
`.env` en la raíz para desarrollo local:

```bash
copy .env.example .env      # Windows
cp .env.example .env        # Linux/macOS
```

| Variable | Para qué sirve | Valor local típico |
|---|---|---|
| `APP_ENV` | `local` muestra errores; `production` los oculta | `local` |
| `APP_BASE_URL` | Prefijo URL. **Vacío si la app está en la raíz** | `/revista-ddp` |
| `DATABASE_URL` | Conexión completa; tiene prioridad sobre las demás | *(vacío)* |
| `DB_HOST` | Host de MySQL/MariaDB | `localhost` |
| `DB_PORT` | Puerto de MySQL | `3306` |
| `DB_NAME` | Nombre de la base | `revista_digital` |
| `DB_USER` | Usuario de la base | `root` |
| `DB_PASS` | Contraseña de la base | *(vacío)* |

`APP_BASE_URL` se detecta solo a partir de `SCRIPT_NAME`; solo es forzarlo si el
proxy no expone `public/` en esa variable. `.env` está en `.gitignore`: **nunca se
sube al repositorio**.

### Usuarios iniciales

`database/seed.sql` crea tres usuarios de demostración. Las contraseñas ya no
están en claro en este README, pero los hashes bcrypt del seed corresponden a
contraseñas conocidas: **cámbialas antes de publicar el sitio** desde el panel
(`Usuarios` → editar → contraseña). Para producción se recomienda no sembrar
usuarios y crearlos a mano.

## 4. Estructura

```text
revista-ddp/
├── app/
│   ├── controllers/          controladores públicos y admin
│   ├── core/                 Router, Database, Auth, Session, Validator, MediaProcessor, MediaPlayer
│   ├── models/               modelos PDO de las entidades (incluye Especial)
│   └── views/
│       ├── public/           portada, listados, detalle y partials
│       └── admin/            Tabler, formularios y paginador
├── config/config.php         configuración local
├── database/
│   ├── schema.sql            tablas, FK e índices
│   └── seed.sql              datos de demostración
├── public/
│   ├── assets/               CSS, JS, imágenes y fuentes
│   ├── .htaccess             front controller de public/
│   └── index.php             único punto de entrada PHP
├── uploads/                  archivos binarios fuera de public/
├── storage/logs/            BITÁCORA de contacto y errores
├── .htaccess                 reglas del document root del proyecto
└── README.md
```

La base de datos solo guarda rutas y metadata. `MediaProcessor` crea los archivos dentro de `uploads/`; las vistas convierten esas rutas en `/uploads/...` o `/public/assets/...`.

## 5. Referencia visual local

Se usaron los HTML y assets de `reference/` antes de eliminar esa carpeta del proyecto. Se copiaron sin cambios los recursos disponibles de `index_files/` a:

- `public/assets/css/reference-fonts.css`
- `public/assets/css/style-starter.css`
- `public/assets/js/jquery-3.3.1.min.js`
- `public/assets/js/theme-change.js`
- `public/assets/js/easyResponsiveTabs.js`
- `public/assets/js/owl.carousel.js`
- `public/assets/js/jquery.magnific-popup.min.js`
- `public/assets/js/bootstrap.min.js`
- `public/assets/js/ddp-media.js` (modal único de reproducción y carrusel de Especiales)
- `public/assets/img/brand-swoosh.svg` (símbolo de marca reutilizado en reportajes; rojo extraído del logo: `#E0020D`)
- Imágenes de logo, banner, reportajes, noticias, boletines, podcasts y especiales.

Los archivos de la captura tienen el mismo hash que sus copias en `public/assets`. La referencia menciona `lightbox-plus-jquery.min.js.descarga`, pero ese archivo no estaba guardado en ninguna carpeta local; no se descargó nada. El sitio usa el lightbox `jquery.magnific-popup.min.js` que sí está disponible y mantiene el comportamiento visual de zoom.

Las páginas dinámicas conservan las clases y el orden de secciones de la referencia: header fijo, reportajes destacado + grid, noticias, Boletín NTEP, podcast, especiales, quiénes somos, redes y footer. No se usan páginas HTML estáticas de la carpeta de referencia.

`reference/` queda únicamente para capturas visuales de comparación solicitadas por el usuario; nunca se copia al document root ni se sirve públicamente.

## 6. Rutas y paginación

- `/`
- `/reportajes`
- `/reportajes/pagina/{n}`
- `/reportajes/{slug}`
- `/noticias`
- `/boletines`
- `/boletines/{numero}`
- `/podcast`
- `/podcast/{slug}`
- `/videos`
- `/videos/{slug}`
- `/reproductor?tipo=podcast|video|especial&id={id}` (endpoint JSON público del modal)
- `/sobre-nosotros`
- `/alianzas`
- `/contacto`
- `/sitemap.xml`
- `/robots.txt`
- `/admin/*`

El listado público de reportajes calcula `CEIL(total_publicados / 12)`, usa `LIMIT/OFFSET` y genera enlaces a `/reportajes/pagina/{n}`. También acepta `?pagina=2`. El panel de reportajes usa el mismo tamaño de página. El paginador muestra `Ant`, números cercanos y `Sig`, con el número actual en rojo sólido. Con 12 reportajes publicados o menos solo se genera la página 1: el número 2 no aparece y `Sig` queda deshabilitado; una página 2 solo se crea al llegar a 13 reportajes publicados.

## 7. Base de datos y contenido dinámico

`schema.sql` incluye `usuarios`, `password_resets`, `autores`, `reportajes`, `reportajes_fotos`, `noticias`, `boletines`, `podcasts`, `videos`, `especiales`, `paginas`, `configuracion` y `logs_actividad`.

- Reportajes, podcasts y videos solo son públicos cuando `estado='publicado'` y `fecha_publicacion <= CURDATE()`.
- Noticias y boletines son públicos cuando `estado='publicado'` y su fecha no es futura.
- La sección **Especiales** de la portada consume `especiales` activos: cada tarjeta negra muestra `titulo_leet` en mayúsculas, resalta `palabra_resaltada` en magenta, muestra el play superpuesto y coloca `titulo_completo` debajo. El carrusel usa cuatro items por página en escritorio, flechas `‹`/`›` y puntos por página; no tiene CTA.
- Los thumbnails de Podcast, Videos y Especiales son botones con `data-tipo` y `data-id`, no enlaces vacíos: el JS hace `preventDefault()` antes de abrir el modal, por lo que no hay salto al footer ni navegación accidental. El modal único del layout consulta `/reproductor`, recibe el HTML según `embed`/`archivo` y lo elimina al cerrar para detener la reproducción.
- El admin de Especiales vive en `/admin/especiales`; permite crear, editar, activar/desactivar y eliminar registros. Las URLs de YouTube/Vimeo se validan y convierten a embed automáticamente.
- Las páginas **Sobre D&D**, **Alianzas** y **Contacto** salen de `paginas`.
- Los textos institucionales, redes, correo y video de quiénes somos salen de `configuracion` y se editan en `/admin/configuracion`.
- Las tarjetas públicas de reportajes y noticias reutilizan la clase `.ddp-card-media`, que aplica `brand-swoosh.svg` (capa blanca + capa roja `#E0020D`) en la esquina superior derecha. También se aplica a la imagen principal y galería de la vista de detalle.
- `.ddp-card-media` es además el **marco de proporción fija** de todas las tarjetas: declara `aspect-ratio: var(--ddp-card-ratio, 16 / 9)` y `overflow: hidden`, y su `<img>` usa `width: 100%; height: 100%; object-fit: cover; object-position: center`. Así todas las tarjetas quedan alineadas en altura aunque el admin suba imágenes de cualquier proporción. No se debe usar `height: auto` ni `max-height` en esas imágenes: `MediaProcessor::resizeToWidth()` conserva la proporción original, por eso el recorte lo resuelve el CSS y no el generador de miniaturas.
- La conexión PDO declara `charset=utf8mb4` en el DSN y ejecuta `SET NAMES utf8mb4;`; el esquema fija `utf8mb4_unicode_ci` en la base, tablas y columnas de texto. `config/config.php` fija `default_charset` y los `.htaccess` añaden `AddDefaultCharset UTF-8`.

## 8. Panel editorial y seguridad

- `public/index.php` inicia la sesión antes de producir salida.
- Los controladores admin heredan de `AdminController`, que exige autenticación.
- Cada acción sensible declara sus roles; el redactor queda limitado a sus propios contenidos y nunca puede publicar reportajes mediante un POST manipulado.
- Todos los formularios y acciones AJAX validan CSRF. Las peticiones AJAX envían `X-CSRF`.
- Las contraseñas usan `password_hash()` y `password_verify()`; se bloquea una cuenta después de 5 intentos durante 15 minutos.
- Los textos enriquecidos pasan por `Validator::sanitizeHtml()`; las entradas ordinarias se sanean y se escapan al imprimir.
- Las acciones del panel se registran en `logs_actividad`.
- El enlace de recuperación se muestra en pantalla en `ENVIRONMENT=local`. En producción se debe conectar PHPMailer en `AuthController::procesarRecuperar()`.
- El formulario de contacto no envía correo: deja el mensaje en `storage/logs/contactos.txt`.

## 9. Subidas y reproducción

`MediaProcessor`:

- Valida MIME real y tamaño de imágenes, audio, video y PDF.
- Para imágenes con GD genera original máximo de 1920 px, thumb de 400 px y WebP con fallback.
- Sin GD conserva el original y registra una ruta relativa válida.
- Audio/video se procesan con `ffmpeg` cuando está disponible; sin él se guarda el original, la duración queda pendiente y el panel muestra un aviso no bloqueante.
- Los archivos se sirven como estáticos mediante la excepción `uploads/` del `.htaccess` raíz; no se usa un script PHP para Range Requests. La prueba `Range: bytes=0-3` sobre un PDF devolvió HTTP 206 y `Content-Range: bytes 0-3/747`.
- `MediaPlayer` y `ReproductorController` generan el reproductor real para el modal: `iframe` responsivo para embeds y `audio`/`video` con `autoplay` para archivos. El overlay, el botón `×` y la tecla Escape eliminan el nodo del reproductor y pausan cualquier medio antes de cerrar.

En la máquina de verificación PHP 8.2.12 no tenía GD ni `ffmpeg` en el `PATH` (`php -m` no mostró `gd` y `Get-Command ffmpeg` no encontró el ejecutable). La prueba real de imagen y PDF confirmó que las subidas se escribieron en disco y respondieron por HTTP.

## 10. Verificación backend realizada

La verificación se hizo con Apache 2.4.58, PHP 8.2.12 y MariaDB 10.4.32 el 25/09/2026. El proyecto se probó bajo `/revista-ddp/` con un Apache de prueba cuyo DocumentRoot era la carpeta padre, equivalente a la carpeta autónoma dentro de `htdocs`.

| Punto | Resultado |
|---|---|
| 1. Conexión PDO | La prueba con el DSN `charset=utf8mb4` y `SET NAMES utf8mb4` devolvió `SELECT 1` correctamente. |
| 2. Apache/.htaccess | `httpd -t` devolvió `Syntax OK`; `mod_rewrite` está cargado. `public/.htaccess` fija `RewriteBase /revista-ddp/public/`. |
| 3. Rutas limpias | `/`, `/reportajes`, `/reportajes/pagina/2`, `/noticias`, `/boletines`, `/podcast`, `/videos`, `/sobre-nosotros`, `/alianzas`, `/contacto`, `/sitemap.xml`, `/robots.txt` y `/admin/login` respondieron HTTP 200. |
| 4. Sesión/protección | Login real devolvió 302 a `/admin`; `/admin` sin sesión devolvió 302 a login. |
| 5. CSRF | Token sin campo o incorrecto devolvió 403 y no creó filas; formulario de contacto con token válido devolvió 302. |
| 6. CRUD real | Se probó crear, editar y eliminar por HTTP y se comprobó la persistencia en MySQL para noticia, boletín, podcast, video, especial, autor, página y usuario; todos los flujos devolvieron 302 y la fila de prueba quedó eliminada. |
| 7. Upload | Se subió una imagen PNG y un PDF desde el panel; ambos aparecieron en disco y `/uploads/...` respondió HTTP 200. |
| 8. Roles | Redactor no pudo acceder a usuarios ni editar un reportaje ajeno; su publicación manipulada quedó en `borrador`. Editor recibió 403 en usuarios y admin recibió 200. |
| 9. Codificación UTF-8 | Se recreó `revista_digital` importando `schema.sql` y `seed.sql`; `HEX()` confirmó bytes UTF-8, las tablas y columnas de texto quedaron en `utf8mb4_unicode_ci`, y las respuestas HTML fueron `text/html; charset=UTF-8` sin secuencias mojibake. |
| 10. Especiales | La portada devuelve los registros activos de `especiales`, con `titulo_leet`, resaltado magenta, caption, play superpuesto y carrusel de cuatro items por página; incluye flechas `‹`/`›` y puntos por página, sin CTA. |
| 11. Paginación | Con 11 reportajes publicados, `/reportajes` y `/reportajes/pagina/2` muestran solo la página 1, sin número 2 y con `Sig` deshabilitado. El límite público es 12; al probar 13 reportajes, la página 2 apareció con un solo reportaje. |
| 12. Distintivo rojo | Las tarjetas de portada y listados de reportajes/noticias incluyen `.ddp-card-media`; el pseudo-elemento CSS pinta la cinta roja superior derecha automáticamente en contenido existente y nuevo. |
| 13. Modal de reproducción | `/reproductor?tipo=podcast&id=1`, `/reproductor?tipo=video&id=1` y `/reproductor?tipo=especial&id=1` devolvieron JSON 200 con `iframe`; las ramas `archivo` devolvieron `audio`/`video` con `autoplay`. El layout contiene un solo modal y los triggers aparecen en index, listados y Especiales. |
| 14. Cierre del modal | El navegador real abrió el modal desde la portada, insertó el `iframe`, lo cerró con `×` y verificó que el nodo del reproductor quedaba eliminado; overlay y Escape usan la misma lógica. |
| 15. Responsive del modal | La captura en viewport móvil mostró el reproductor dentro del ancho disponible, sin desborde horizontal; el carrusel ajusta dots/items según el ancho. |
| 16. Anti-salto | Los triggers son `<button type="button">`, el CDN local de CSS/JS lleva versión por `filemtime` y el navegador real confirmó `url` y `scrollY` sin cambios al abrir el modal. |
| 17. Especiales admin | `/admin/especiales` respondió 200; crear, editar y eliminar un especial real devolvió 302, persistió en MySQL y la fila de prueba fue eliminada. |
| 18. Símbolo de marca | Se verificó visualmente el swoosh de doble capa en portada, listados y detalle de reportajes; el SVG usa el rojo `#E0020D` extraído del `logo.png`. |
| 19. Proporción de tarjetas | Midiendo el DOM en navegador real: las 11 tarjetas de `/reportajes` miden todas `369.98 × 208.11 px` (ratio 1.7778), las 6 de la portada y las 5 de `/noticias` miden `208.1 px`, y la galería de 3 fotos `133.1 px`. En móvil (390 px) las 11 tarjetas quedan en `202.5 px` sin scroll horizontal. Las imágenes naturales son 1120×1000 (1.12), 720×500 (1.44) y 1280×720 (1.78), y ninguna se deforma: `object-fit: cover` recorta. |

**Estado de la corrección: confirmado.** La base recreada, las respuestas del sitio, el panel, la portada, el paginador, el carrusel, el endpoint de reproducción, el admin de Especiales, el símbolo de marca y una noticia creada/eliminada desde el panel se verificaron sin caracteres corruptos; **Especiales** muestra las tarjetas negras con título leet resaltado, el modal reproduce y detiene el medio al cerrar, no aparece la página 2 con 11 reportajes, todas las imágenes de reportajes llevan el swoosh de doble capa y todas las tarjetas comparten un marco 16:9 con recorte `cover`.

Comprobación adicional de sintaxis ejecutada desde la raíz:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
```

Resultado: todos los archivos PHP pasaron `php -l`.

## 11. Checklist de los cinco criterios

| Criterio | Estado |
|---|---|
| 1. Seguridad y roles | **Cumplido**: password hashes, bloqueo, CSRF, saneo, middleware y autorización por acción. |
| 2. UX/UI periodista | **Cumplido**: dashboard, logs, filtros, TinyMCE, vista previa, contadores, confirmaciones y sidebar responsivo. |
| 3. CRUD y estados | **Cumplido**: reportajes con galería, noticias, boletines/PDF, multimedia embed/archivo, autores, páginas, configuración y usuarios. |
| 4. Sitio público/SEO | **Cumplido**: contenido desde BD, fecha/estado, SEO por página, sitemap y robots dinámicos, alt texts y reproducciones. |
| 5. Reglas editoriales | **Cumplido**: límites recomendados, alt obligatorio, autoría Redacción, checklist, slugs únicos e historial. |

## 12. Despliegue en Railway

### 12.1 Por qué hace falta `nginx.template.conf`

Railway sirve PHP con **Nginx + PHP-FPM**. **No lee `.htaccess`**, así que los
`mod_rewrite` del proyecto se ignoran por completo. `nginx.template.conf` (en la
raíz) reproduce esas reglas:

| Necesidad | Cómo lo resuelve |
|---|---|
| Rutas limpias | `try_files $uri $uri/ /public/index.php?url=$uri&$args` |
| Contrato con `Router` | Pasa `?url=` igual que el `RewriteRule` de Apache, sin tocar PHP |
| `uploads/` y `public/` | `root` en la raíz del proyecto, no en `public/` |
| Bloquear `app/`, `config/`, `database/`, `storage/`, `reference/` | `location` con `deny all` para cada carpeta |
| No ejecutar PHP en `uploads/` | `location /uploads` bloquea `php`, `phtml`, `phar`, `cgi` |
| Vídeos de 200 MB | `client_max_body_size 210M` + `.user.ini` con `upload_max_filesize` |

En Railway la app queda **en la raíz del dominio**, así que `APP_BASE_URL` debe
quedar **vacía**. `config/env.php` la detecta sola vía `SCRIPT_NAME`.

### 12.1.bis Cómo arranca el sitio

**En Railway manda el `Dockerfile`.** El servicio corre:

```
php -S 0.0.0.0:$PORT -t public public/router.php
```

con `PHP_CLI_SERVER_WORKERS=4` y PHP 8.2 + `pdo_mysql`, `gd` y `zip`.

#### Por qué un Dockerfile y no la autodetección

Railpack 0.40 clasifica este repositorio como **Staticfile** (por el directorio
`public/`) y arranca **Caddy** en lugar de PHP. Medido en el build log:

```
↳ Detected Staticfile
↳ Using staticfile root dir: public
Packages
caddy │ 2.11.4
Deploy
$ caddy run --config Caddyfile --adapter caddyfile
```

Con Caddy el `Procfile` se ignora por completo, `/` responde 404 y el
healthcheck mata el despliegue:

```
Starting Healthcheck
Path: /
Attempt #1 failed with HTTP 404
1/1 replicas never became healthy!
```

Añadir `composer.json` **no** sirvió: la detección de Staticfile tiene prioridad.
El `Dockerfile` elimina la ambigüedad porque Railway construye la imagen tal
cual, sin adivinar el lenguaje. El `Procfile` se conserva en el repositorio
como documentación del comando de arranque y para otros hostings.

#### Por qué el script de router es obligatorio

El servidor embebido de PHP no reescribe URLs. Sin un router,
`Router::parseUrl()` recibiría siempre `/` y **todas las páginas devolverían la
portada con HTTP 200** (medido: 27 778 bytes idénticos en `/`, `/reportajes` y
`/admin/login`, y el panel sin campo `password`). El fallo sería invisible.

`public/router.php` reproduce lo que hacía Apache:

| Necesidad | Qué hace el router |
|---|---|
| Rutas limpias | Asigna `$_GET['url']` y carga `index.php`, igual que el `RewriteRule (.*) public/index.php?url=$1` |
| Bloquear directorios privados | `app`, `config`, `database`, `storage`, `reference`, `vendor`, `tests` → 404 |
| Bloquear ocultos | `.env`, `.git`, `.user.ini` y el propio `router.php` → 404 |
| Assets | Detecta que el document root **es** `public/` y fija `APP_PUBLIC_URL=/`, de modo que el HTML pide `/assets/...` y se sirven como estáticos |
| `uploads/` | Vive fuera del document root, así que se transmite desde el directorio hermano **con soporte de `Range`** para no perder la barra de búsqueda de los vídeos |
| No ejecutar código subido | Bloquea `php`, `phtml`, `phar`, `cgi`, `pl`, `py`, `sh` dentro de `uploads/` |

`nginx.template.conf` se conserva para despliegues que usen Nginx (Apache, otros
hostings). No interviene en Railway porque el `Dockerfile` tiene prioridad.

#### Límites de subida

`php -S` **no lee `.user.ini`** (solo lo leen CGI/FastCGI), así que los límites
van en `/usr/local/etc/php/conf.d/ddp.ini` dentro de la imagen: 210 MB de
subida, 220 MB de `post_max_size` y 256 MB de memoria, para cubrir el vídeo de
200 MB que admite el panel.

### 12.2 Pasos en el panel de Railway

1. **New Project → Deploy from GitHub repo** y elige el repositorio.
2. **Add New → Database → MySQL**. Railway genera `DATABASE_URL` automáticamente.
3. Si Railway no la genera en tu servicio, crea la variable a mano con el formato
   `mysql://USUARIO:CLAVE@HOST:PUERTO/revista_digital`, donde `HOST` suele ser
   `mysql.railway.internal`.
4. En el servicio PHP, ve a **Variables** y añade:

   | Variable | Valor |
   |---|---|
   | `APP_ENV` | `production` |
   | `APP_BASE_URL` | *(vacío)* |
   | `DATABASE_URL` | *(la que generó MySQL, o la que pegaste en el paso 3)* |
   | `DB_HOST` | `mysql.railway.internal` *(si no usas `DATABASE_URL`)* |
   | `DB_NAME` | `revista_digital` |
   | `DB_USER` | *(el del plugin)* |
   | `DB_PASS` | *(la del plugin)* |

5. **Settings → Root Directory**: déjalo como la raíz del repo.
6. En **Settings → Healthcheck Path** usa `/` (o `/reportajes`).
7. Despliega. En la primera ejecución, importa el esquema: conecta el MySQL de
   Railway y ejecuta `database/schema.sql` y `database/seed.sql`
   (`mysql -h HOST -u USER -p -e "source database/schema.sql" revista_digital`).
   Railway **no** ejecuta SQL por ti en el build.

### 12.3 Variables de entorno obligatorias

- `APP_ENV=production` — sin esto se muestran errores de PHP al usuario final.
- `DATABASE_URL` **o** el conjunto `DB_HOST`/`DB_PORT`/`DB_NAME`/`DB_USER`/`DB_PASS`.
- `APP_BASE_URL` vacío si la app vive en la raíz (lo normal en Railway).

Sin `APP_ENV=production` el sitio muestra los errores de PHP en pantalla y la
recuperación de contraseña imprime el enlace de reinicio en la página.

### 12.4 Limitaciones conocidas del entorno actual

- **No hay Composer** (`composer.json` no existe), así que Railway usa PHP 8.2 por defecto y no hay `vendor/`. El proyecto no necesita librerías: el autoload es manual en `public/index.php`.
- **El filesystem de Railway es efímero**: los archivos que suba el admin en `uploads/` se pierden al redesplegar. Monta un **volume** en `/app/uploads` y en `/app/storage/logs` si vas a publicar de verdad.
- **El seed crea usuarios con contraseñas conocidas**: cámbialas por el panel antes de abrir el sitio al público.
- **GD puede no estar instalado**: el procesador de imágenes lo detecta y, si falta, conserva el archivo original sin fallar. Si quieres miniaturas, instala GD en la imagen.
- **`ffmpeg` no está**: los vídeos/audio se guardan sin transcodificar. Es opcional.
