<!-- Partial: Sidebar estilo original w3l-blog (left-text-9) -->
<?php
    if (!isset($ultimos)) {
        $m = new App\Models\Reportaje();
        $ultimos = $m->getUltimos(3);
    }
    if (!isset($archivos)) {
        $m = new App\Models\Reportaje();
        $archivos = $m->getArchivos();
    }
?>
<div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
    <div class="left-top-9 mt-5 pt-sm-3">
        <h6 class="heading-small-text-9 mb-3">Últimas noticias</h6>
        <?php foreach ($ultimos as $u): ?>
        <?php $uUrl = BASE_URL . '/reportajes/' . $u['slug']; ?>
        <a href="<?= $uUrl ?>" class="p-post d-block py-2">
            <h6 class="text-left-inner-9"><?= htmlspecialchars($u['titulo']) ?></h6>
            <span class="sub-inner-text-9"><?= ddpFechaCorta($u['fecha_publicacion']) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="categories mt-5 pt-sm-3">
        <h6 class="heading-small-text-9">Archivos</h6>
        <ul>
            <?php foreach ($archivos as $a): ?>
            <li><a href="<?= BASE_URL ?>/reportajes<?= $a['periodo'] ? '?archivo=' . $a['periodo'] : '' ?>"> <?= htmlspecialchars($a['etiqueta']) ?></a></li>
            <?php endforeach; ?>
            <?php if (empty($archivos)): ?>
            <li><span class="text-muted">Sin archivos disponibles.</span></li>
            <?php endif; ?>
        </ul>
    </div>
</div>