#!/bin/bash
set -e

# 1. Actualizar sistema e instalar Git y Docker (Compatible con Ubuntu y Amazon Linux)
if command -v apt-get &> /dev/null; then
    apt-get update -y
    apt-get install -y git docker.io
elif command -v dnf &> /dev/null; then
    dnf update -y
    dnf install -y git docker
elif command -v yum &> /dev/null; then
    yum update -y
    yum install -y git docker
fi

# Iniciar y habilitar Docker
systemctl start docker
systemctl enable docker

# 2. Clonar tu repositorio
cd /opt
git clone https://github.com
cd revista-ddp

# Asegurar carpetas y permisos de escritura para subidas y logs
mkdir -p uploads storage/logs
chmod -R 777 uploads storage/logs

# 3. Crear una red interna de Docker para conectar la web con la base de datos
docker network create ddp-net

# 4. Levantar contenedor de MySQL 8.0 con tu esquema y datos iniciales
docker run -d \
  --name ddp-mysql \
  --network ddp-net \
  --restart unless-stopped \
  -e MYSQL_ROOT_PASSWORD=rootpass \
  -e MYSQL_DATABASE=revista_digital \
  -v /opt/revista-ddp/database/schema.sql:/docker-entrypoint-initdb.d/1-schema.sql \
  -v /opt/revista-ddp/database/seed.sql:/docker-entrypoint-initdb.d/2-seed.sql \
  mysql:8.0 \
  --character-set-server=utf8mb4 \
  --collation-server=utf8mb4_unicode_ci

# Esperar 20 segundos a que MySQL termine de crear las tablas
sleep 20

# 5. Construir la imagen de tu proyecto usando tu Dockerfile
docker build -t revista-ddp-app .

# 6. Arrancar la aplicación en el puerto 80 conectada a MySQL
docker run -d \
  --name ddp-web \
  --network ddp-net \
  --restart unless-stopped \
  -p 80:80 \
  -e PORT=80 \
  -e APP_ENV=production \
  -e APP_BASE_URL="" \
  -e DB_HOST=ddp-mysql \
  -e DB_PORT=3306 \
  -e DB_NAME=revista_digital \
  -e DB_USER=root \
  -e DB_PASS=rootpass \
  -v /opt/revista-ddp/uploads:/app/uploads \
  -v /opt/revista-ddp/storage/logs:/app/storage/logs \
  revista-ddp-app
