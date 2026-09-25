SET NAMES utf8mb4;

-- DDP Noticias / esquema de base de datos
-- MySQL 8.0+ / MariaDB 10.4+ / InnoDB / utf8mb4
-- La base almacena rutas y metadata; nunca binarios.

CREATE DATABASE IF NOT EXISTS revista_digital
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Garantiza el charset aunque la base ya existiera con otro default.
ALTER DATABASE revista_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE revista_digital;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombres VARCHAR(100) NOT NULL,
  ap_paterno VARCHAR(60) NOT NULL,
  ap_materno VARCHAR(60) NOT NULL DEFAULT '',
  email VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin','editor','redactor') NOT NULL DEFAULT 'redactor',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  intentos_fallidos INT UNSIGNED NOT NULL DEFAULT 0,
  bloqueado_hasta DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  token VARCHAR(64) NOT NULL,
  expira_en DATETIME NOT NULL,
  usado TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_password_resets_token (token),
  KEY idx_password_resets_usuario (usuario_id),
  CONSTRAINT fk_password_resets_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS autores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombres VARCHAR(120) NOT NULL,
  ap_paterno VARCHAR(60) NOT NULL DEFAULT '',
  ap_materno VARCHAR(60) NOT NULL DEFAULT '',
  nickname VARCHAR(100) NOT NULL DEFAULT '',
  es_nickname TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reportajes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  resumen_corto VARCHAR(500) NOT NULL DEFAULT '',
  desarrollo LONGTEXT,
  video_embed VARCHAR(500) NOT NULL DEFAULT '',
  foto_principal VARCHAR(255) NOT NULL DEFAULT '',
  alt_foto_principal VARCHAR(255) NOT NULL DEFAULT '',
  pdf_adjunto VARCHAR(255) NOT NULL DEFAULT '',
  fecha_publicacion DATE NULL,
  estado ENUM('borrador','publicado','archivado') NOT NULL DEFAULT 'borrador',
  es_destacado TINYINT(1) NOT NULL DEFAULT 0,
  meta_titulo VARCHAR(255) NOT NULL DEFAULT '',
  meta_descripcion VARCHAR(320) NOT NULL DEFAULT '',
  autor_id INT UNSIGNED NULL,
  usuario_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_reportajes_slug (slug),
  KEY idx_reportajes_fecha (fecha_publicacion),
  KEY idx_reportajes_estado (estado),
  KEY idx_reportajes_destacado (es_destacado),
  KEY idx_reportajes_autor (autor_id),
  KEY idx_reportajes_usuario (usuario_id),
  CONSTRAINT fk_reportajes_autor FOREIGN KEY (autor_id)
    REFERENCES autores(id) ON DELETE SET NULL,
  CONSTRAINT fk_reportajes_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reportajes_fotos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reportaje_id INT UNSIGNED NOT NULL,
  url_foto VARCHAR(255) NOT NULL DEFAULT '',
  url_foto_thumb VARCHAR(255) NOT NULL DEFAULT '',
  url_foto_webp VARCHAR(255) NOT NULL DEFAULT '',
  orden SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  descripcion VARCHAR(255) NOT NULL DEFAULT '',
  KEY idx_reportajes_fotos_reportaje (reportaje_id),
  CONSTRAINT fk_reportajes_fotos_reportaje FOREIGN KEY (reportaje_id)
    REFERENCES reportajes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS noticias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  foto VARCHAR(255) NOT NULL DEFAULT '',
  link_externo VARCHAR(500) NOT NULL DEFAULT '',
  fecha_publicacion DATE NULL,
  estado ENUM('borrador','publicado','archivado') NOT NULL DEFAULT 'borrador',
  usuario_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_noticias_slug (slug),
  KEY idx_noticias_fecha (fecha_publicacion),
  KEY idx_noticias_usuario (usuario_id),
  CONSTRAINT fk_noticias_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS boletines (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero_boletin VARCHAR(50) NOT NULL,
  resumen TEXT,
  temas TEXT NULL,
  foto_portada VARCHAR(255) NOT NULL DEFAULT '',
  archivo_pdf VARCHAR(255) NOT NULL DEFAULT '',
  fecha_publicacion DATE NULL,
  estado ENUM('borrador','publicado','archivado') NOT NULL DEFAULT 'borrador',
  usuario_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_boletines_numero (numero_boletin),
  KEY idx_boletines_fecha (fecha_publicacion),
  KEY idx_boletines_usuario (usuario_id),
  CONSTRAINT fk_boletines_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS podcasts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  tipo ENUM('embed','archivo') NOT NULL DEFAULT 'embed',
  url_embed VARCHAR(500) NULL,
  archivo_audio VARCHAR(255) NULL,
  portada VARCHAR(255) NOT NULL DEFAULT '',
  duracion_segundos INT UNSIGNED NULL,
  tamano_bytes BIGINT UNSIGNED NULL,
  fecha_publicacion DATE NULL,
  estado ENUM('borrador','publicado') NOT NULL DEFAULT 'borrador',
  usuario_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_podcasts_slug (slug),
  KEY idx_podcasts_estado (estado),
  KEY idx_podcasts_fecha (fecha_publicacion),
  KEY idx_podcasts_usuario (usuario_id),
  CONSTRAINT fk_podcasts_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS videos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  tipo ENUM('embed','archivo') NOT NULL DEFAULT 'embed',
  url_embed VARCHAR(500) NULL,
  archivo_video VARCHAR(255) NULL,
  poster VARCHAR(255) NULL,
  duracion_segundos INT UNSIGNED NULL,
  tamano_bytes BIGINT UNSIGNED NULL,
  fecha_publicacion DATE NULL,
  estado ENUM('borrador','publicado') NOT NULL DEFAULT 'borrador',
  usuario_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_videos_slug (slug),
  KEY idx_videos_estado (estado),
  KEY idx_videos_fecha (fecha_publicacion),
  KEY idx_videos_usuario (usuario_id),
  CONSTRAINT fk_videos_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS especiales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo_completo VARCHAR(255) NOT NULL,
  palabra_resaltada VARCHAR(255) NOT NULL DEFAULT '',
  titulo_leet VARCHAR(255) NOT NULL,
  url_video VARCHAR(500) NOT NULL,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  KEY idx_especiales_activo_fecha (activo, fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS paginas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  contenido LONGTEXT,
  imagen VARCHAR(255) NOT NULL DEFAULT '',
  meta_titulo VARCHAR(255) NOT NULL DEFAULT '',
  meta_descripcion VARCHAR(320) NOT NULL DEFAULT '',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_paginas_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS configuracion (
  clave VARCHAR(80) NOT NULL PRIMARY KEY,
  valor TEXT NOT NULL,
  tipo VARCHAR(20) NOT NULL DEFAULT 'text',
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS logs_actividad (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NULL,
  accion VARCHAR(100) NOT NULL,
  entidad VARCHAR(50) NOT NULL,
  entidad_id INT UNSIGNED NULL,
  detalles TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_logs_usuario (usuario_id),
  KEY idx_logs_entidad (entidad, entidad_id),
  KEY idx_logs_fecha (created_at),
  CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
