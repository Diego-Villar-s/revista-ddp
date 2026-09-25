<?php
/**
 * Página de recuperación de contraseña
 * En modo local (XAMPP sin SMTP) el link se muestra en pantalla.
 */
if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | <?= SITE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.6.0/dist/tabler-icons.min.css" rel="stylesheet">
</head>
<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/public/assets/img/logo.png" height="44" width="44" alt="DDP">
                <p class="text-muted mt-2">Recuperación de contraseña</p>
            </div>

            <?php if ($linkReset): ?>
            <?php if (str_starts_with($linkReset, 'http')): ?>
            <div class="alert alert-success" role="alert">
                <h3 class="h4 mb-2">Link de restablecimiento generado</h3>
                <p class="mb-2">
                    En <strong>modo desarrollo</strong> (XAMPP local sin SMTP) el link se muestra en pantalla.
                    En producción se enviará por correo (PHPMailer).
                </p>
                <p class="mb-0">
                    <a href="<?= htmlspecialchars($linkReset) ?>" class="btn btn-primary">
                        <i class="ti ti-link me-1"></i> Mostrar/resetear contraseña
                    </a>
                </p>
            </div>
            <?php else: ?>
            <div class="alert alert-warning" role="alert"><?= htmlspecialchars($linkReset) ?></div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title mb-3">¿Olvidaste tu contraseña?</h2>
                    <p class="text-muted">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>
                    <form action="<?= BASE_URL ?>/admin/recuperar" method="post" autocomplete="off" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME]) ?>">
                        <div class="mb-3">
                            <label class="form-label required">Correo electrónico</label>
                            <div class="input-icon mb-2">
                                <span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="correo@dialogoydesarrollo.com.pe" required autofocus>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Solicitar enlace</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-muted mt-3">
                <a href="<?= BASE_URL ?>/admin/login" tabindex="-1"><i class="ti ti-arrow-left me-1"></i>Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>