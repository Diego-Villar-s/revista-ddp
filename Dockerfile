# ============================================================
# DDP Noticias - imagen de produccion para Railway
# ============================================================
# Se usa Dockerfile en lugar de la autodeteccion de Railpack/Nixpacks
# porque esa autodeteccion clasifica el proyecto como "Staticfile" (por
# el directorio public/) y arranca Caddy en lugar de PHP: el Procfile
# se ignora y / responde 404, lo que hace fallar el healthcheck.
#
# Con este Dockerfile Railway construye la imagen exactamente como
# se indica aqui, sin adivinar el lenguaje.
#
# El comando de arranque es el mismo del Procfile.
# ============================================================

FROM php:8.2-cli

# pdo_mysql es imprescindible; mbstring, json y fileinfo ya vienen
# compilados en las imagenes oficiales de PHP. gd es opcional: sin el,
# MediaProcessor conserva el archivo original sin fallar (ver AGENTS.md).
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libfreetype6-dev \
        libzip-dev; \
    docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip; \
    rm -rf /var/lib/apt/lists/*

# Con `php -S` no se lee .user.ini (solo lo leen CGI/FastCGI), asi que
# los limites de subida van en un php.ini del sistema. Son los mismos
# valores que public/.user.ini, y cubren MAX_VIDEO_SIZE de 200 MB.
RUN printf '%s\n' \
    'upload_max_filesize = 210M' \
    'post_max_size = 220M' \
    'memory_limit = 256M' \
    'max_execution_time = 300' \
    'max_input_time = 300' \
    'max_input_vars = 5000' \
    'session.gc_maxlifetime = 86400' \
    'session.cookie_httponly = 1' \
    'session.cookie_samesite = Lax' \
    'display_errors = Off' \
    'log_errors = On' \
    > /usr/local/etc/php/conf.d/ddp.ini

WORKDIR /app
COPY . /app

# El servidor embebido es monohilo; con varios workers se atienden
# en paralelo las peticiones de assets y las de PHP.
ENV PHP_CLI_SERVER_WORKERS=4

EXPOSE 8080

# Equivalente exacto del Procfile. El document root es public/ y el
# router reproduce el .htaccess raiz (ver public/router.php).
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-8080} -t public public/router.php"]
