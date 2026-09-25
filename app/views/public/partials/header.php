<?php
$curPath = '/' . strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?');
$curPath = preg_replace('#^' . preg_quote(BASE_URL, '#') . '#', '', $curPath) ?: '/';
$curPath = $curPath === '/' ? '/' : rtrim($curPath, '/');

if (!function_exists('publicActive')) {
    function publicActive(string $path, string $current): string
    {
        if ($current === $path) {
            return ' active';
        }
        if (in_array($path, ['/reportajes', '/podcast', '/boletines', '/videos'], true)
            && str_starts_with($current, $path . '/')) {
            return ' active';
        }
        return '';
    }
}
$logo = PUBLIC_URL . '/assets/img/logo.png';
?>
<header id="site-header" class="fixed-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg stroke">
            <a class="navbar-brand" href="<?= BASE_URL ?>/">
                <img src="<?= $logo ?>" alt="<?= htmlspecialchars(SITE_NAME_FULL, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars(SITE_NAME_FULL, ENT_QUOTES, 'UTF-8') ?>" style="height:75px;">
            </a>
            <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
                <span class="navbar-toggler-icon fa icon-close fa-times"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item<?= publicActive('/', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/">Inicio<?= $curPath === '/' ? ' <span class="sr-only">(current)</span>' : '' ?></a></li>
                    <li class="nav-item<?= publicActive('/noticias', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/#actualidad">Actualidad</a></li>
                    <li class="nav-item<?= publicActive('/reportajes', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/reportajes">Reportajes</a></li>
                    <li class="nav-item<?= publicActive('/podcast', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/podcast">Podcast</a></li>
                    <li class="nav-item<?= publicActive('/boletines', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/boletines">Boletín NTEP</a></li>
                    <li class="nav-item<?= publicActive('/alianzas', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/alianzas">Alianzas</a></li>
                    <li class="nav-item<?= publicActive('/sobre-nosotros', $curPath) ?>"><a class="nav-link" href="<?= BASE_URL ?>/sobre-nosotros">Sobre D&amp;D</a></li>
                    <li class="ml-2"><a href="<?= BASE_URL ?>/contacto#form-contacto" class="btn btn-style btn-outline-secondary">Contacto</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>
