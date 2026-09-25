<?php
/**
 * Token de restablecimiento inválido o expirado
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace no válido | <?= SITE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
</head>
<body class="border-top-wide border-danger d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4 text-center">
            <div class="empty">
                <div class="empty-icon text-danger"><i class="ti ti-alert-triangle" style="font-size:3rem"></i></div>
                <p class="empty-title">Enlace inválido o expirado</p>
                <p class="empty-subtitle text-muted">
                    El enlace de recuperación no es válido o ya caducó (vigencia de 1 hora).
                    Solicita uno nuevo.
                </p>
                <div class="empty-action">
                    <a href="<?= BASE_URL ?>/admin/recuperar" class="btn btn-primary">Solicitar nuevo enlace</a>
                    <a href="<?= BASE_URL ?>/admin/login" class="btn btn-ghost">Ir al login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>