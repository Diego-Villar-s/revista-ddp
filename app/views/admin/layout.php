<?php
/**
 * Layout base del panel admin con Tabler (Bootstrap 5 vía CDN)
 * Estructura oficial de preview.tabler.io:
 *   - body.layout-fluid
 *   - div.page: envuelve TODO (aside + page-wrapper)
 *   - aside.navbar.navbar-vertical.navbar-expand-lg: PRIMERO y FUERA del page-wrapper
 *   - .page-wrapper: topbar + page-header + page-body (cada sección con .container-xl)
 */
use App\Core\Auth;

$adminUser = Auth::user();
$adminRole = $adminUser['rol'] ?? '';
$adminNombre = trim(($adminUser['nombres'] ?? '') . ' ' . ($adminUser['ap_paterno'] ?? ''));
$adminIniciales = strtoupper(mb_substr($adminUser['nombres'] ?? '', 0, 1)) . strtoupper((mb_substr($adminUser['ap_paterno'] ?? '', 0, 1)));

// Ruta activa para resaltar el menú
$adminPath = '/' . strtok($_SERVER['REQUEST_URI'] ?? '/admin', '?');
$adminPath = preg_replace('#^' . preg_quote(BASE_URL, '#') . '#', '', $adminPath);

function adminActive($prefix, $path): string {
    return str_starts_with($path, $prefix) ? 'active' : '';
}
$contenidoActivo = $adminPath !== '/admin' && (adminActive('/admin/reportajes', $adminPath) || adminActive('/admin/noticias', $adminPath) || adminActive('/admin/boletines', $adminPath) || adminActive('/admin/podcasts', $adminPath) || adminActive('/admin/videos', $adminPath) || adminActive('/admin/especiales', $adminPath) || adminActive('/admin/autores', $adminPath) || adminActive('/admin/paginas', $adminPath) || adminActive('/admin/configuracion', $adminPath));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titlePage ?? 'Panel') ?> — DDP</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin.css">

    <script>
        window.DDP_BASE_URL = '<?= BASE_URL ?>';
        window.DDP_CSRF_NAME = '<?= CSRF_TOKEN_NAME ?>';
    </script>
</head>
<body class="layout-fluid">
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler-theme.min.js"></script>
    <div class="page">

        <!-- ===================== SIDEBAR ===================== -->
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Menu principal">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="<?= BASE_URL ?>/admin" class="d-flex align-items-center text-decoration-none">
                        <img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="DDP" width="36" height="36" class="rounded-circle me-2" style="object-fit:cover;">
                        <span class="text-white fw-bold">DDP Panel</span>
                    </a>
                </h1>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link <?= adminActive('/admin', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin">
                                <span class="nav-link-icon"><i class="ti ti-dashboard"></i></span>
                                <span class="nav-link-title">Panel de Control</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= $contenidoActivo ? 'active' : '' ?>" href="#navbar-contenido" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= $contenidoActivo ? 'true' : 'false' ?>">
                                <span class="nav-link-icon"><i class="ti ti-file-text"></i></span>
                                <span class="nav-link-title">Contenido</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item <?= adminActive('/admin/reportajes', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reportajes">Reportajes</a>
                                <a class="dropdown-item <?= adminActive('/admin/noticias', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/noticias">Noticias</a>
                                <a class="dropdown-item <?= adminActive('/admin/boletines', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/boletines">Boletines</a>
                                <a class="dropdown-item <?= adminActive('/admin/podcasts', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/podcasts">Podcasts</a>
                                <a class="dropdown-item <?= adminActive('/admin/videos', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/videos">Videos</a>
                                 <a class="dropdown-item <?= adminActive('/admin/especiales', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/especiales">Especiales</a>
                                <a class="dropdown-item <?= adminActive('/admin/autores', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/autores">Autores</a>
                                 <a class="dropdown-item <?= adminActive('/admin/paginas', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/paginas">Páginas</a>
                                 <a class="dropdown-item <?= adminActive('/admin/configuracion', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/configuracion">Configuración</a>
                            </div>
                        </li>
                        <?php if ($adminRole === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= adminActive('/admin/usuarios', $adminPath) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/usuarios">
                                <span class="nav-link-icon"><i class="ti ti-users"></i></span>
                                <span class="nav-link-title">Usuarios</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/" target="_blank">
                                <span class="nav-link-icon"><i class="ti ti-world"></i></span>
                                <span class="nav-link-title">Ver sitio</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="<?= BASE_URL ?>/admin/logout">
                                <span class="nav-link-icon"><i class="ti ti-logout"></i></span>
                                <span class="nav-link-title">Salir</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- ===================== PAGE WRAPPER ===================== -->
        <div class="page-wrapper">

            <!-- TOPBAR -->
            <header class="navbar navbar-expand-md d-print-none">
                <div class="container-xl">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Menu superior">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-nav flex-row order-md-last">
                        <div class="nav-item d-none d-md-flex me-3">
                            <a href="#" class="nav-link px-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notificaciones"><i class="ti ti-bell"></i></a>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Perfil de usuario">
                                <span class="avatar avatar-sm bg-blue-lt"><?= htmlspecialchars($adminIniciales) ?></span>
                                <div class="d-none d-xl-block ps-2">
                                    <div><?= htmlspecialchars($adminNombre) ?></div>
                                    <div class="mt-1 small text-muted"><?= htmlspecialchars($adminRole) ?></div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header">
                                    <span class="text-truncate"><?= htmlspecialchars($adminUser['email'] ?? '') ?></span>
                                </div>
                                <a href="<?= BASE_URL ?>/" target="_blank" class="dropdown-item"><i class="ti ti-world me-2"></i>Ver sitio público</a>
                                <a href="<?= BASE_URL ?>/admin/logout" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i>Cerrar sesión</a>
                            </div>
                        </div>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar-menu">
                        <div>
                            <form action="<?= BASE_URL ?>/admin/reportajes" method="get" autocomplete="off">
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                    <input type="text" name="busqueda" class="form-control" placeholder="Buscar reportajes..." aria-label="Buscar reportajes">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- PAGE HEADER -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title"><?= htmlspecialchars($titlePage ?? 'Panel') ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAGE BODY -->
            <div class="page-body">
                <div class="container-xl">
                    <?= $content ?>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="<?= BASE_URL ?>/" class="link-secondary">Sitio público</a></li>
                                <li class="list-inline-item"><a href="<?= BASE_URL ?>/sitemap.xml" class="link-secondary">Sitemap</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">&copy; <?= date('Y') ?> <?= SITE_NAME_FULL ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/admin.js"></script>
</body>
</html>