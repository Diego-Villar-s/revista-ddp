<?php
/**
 * Página de Login - Estilo Tabler (page-center)
 * Fidelidad al demo: componente auth de Tabler
 */
if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
// Mensajes transversales
$_errorMensaje = $error ?? '';
$_errorTipo = 'danger';
if (isset($_GET['reset'])) {
    $_errorMensaje = 'Contraseña restablecida correctamente. Ya puede iniciar sesión.';
    $_errorTipo = 'success';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | <?= SITE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.6.0/dist/tabler-icons.min.css" rel="stylesheet">
</head>
<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <!-- Encabezado -->
            <div class="text-center mb-4">
                <a href="<?= BASE_URL ?>/" class="d-inline-flex align-items-center gap-2 navbar-brand">
                    <img src="<?= BASE_URL ?>/public/assets/img/logo.png" height="40" width="40" alt="DDP">
                    <span class="fs-3 fw-bold text-primary"><?= SITE_NAME ?></span>
                </a>
                <p class="text-muted mt-2">Acceso al panel editorial</p>
            </div>

            <?php if ($_errorMensaje): ?>
            <div class="alert alert-<?= $_errorTipo ?>" role="alert">
                <?= htmlspecialchars($_errorMensaje) ?>
            </div>
            <?php endif; ?>

            <!-- Card de login -->
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 card-title mb-3 text-center">Iniciar sesión</h2>
                    <form action="<?= BASE_URL ?>/admin/login" method="post" autocomplete="off" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME]) ?>">
                        <div class="mb-3">
                            <label class="form-label required">Correo electrónico</label>
                            <div class="input-icon mb-2">
                                <span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="correo@dialogoydesarrollo.com.pe" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label required">Contraseña</label>
                            <div class="input-icon mb-2">
                                <span class="input-icon-addon"><i class="ti ti-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-muted mt-3">
                ¿Olvidaste tu contraseña? <a href="<?= BASE_URL ?>/admin/recuperar" tabindex="-1">Recupérala aquí</a>
            </div>
            <div class="text-center text-muted mt-2">
                <a href="<?= BASE_URL ?>/" tabindex="-1"><i class="ti ti-arrow-left me-1"></i>Volver al sitio</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>