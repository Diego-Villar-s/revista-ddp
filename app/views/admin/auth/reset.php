<?php
/**
 * Página de restablecimiento de contraseña (token válido)
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
    <title>Restablecer contraseña | <?= SITE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.6.0/dist/tabler-icons.min.css" rel="stylesheet">
</head>
<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/public/assets/img/logo.png" height="44" width="44" alt="DDP">
                <p class="text-muted mt-2">Nueva contraseña</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title mb-3">Define tu nueva contraseña</h2>
                    <form action="<?= BASE_URL ?>/admin/reset/<?= htmlspecialchars($token) ?>" method="post" autocomplete="off" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">
                        <div class="mb-3">
                            <label class="form-label required">Nueva contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
                            <small class="form-hint">Usa al menos 8 caracteres.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Repetir contraseña</label>
                            <input type="password" name="password2" class="form-control" placeholder="Repite la contraseña" required minlength="8">
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-lock me-1"></i>Restablecer contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
</body>
</html>