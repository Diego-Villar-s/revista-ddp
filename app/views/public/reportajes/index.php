<?php
$items = $resultados['items'] ?? [];
?>
<section class="breadcrumb-area py-sm-5 py-4"><div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
    <h2 class="title-big">Reportajes</h2>
    <div class="breadcrumb"><ul><li><a href="<?= BASE_URL ?>/">Inicio</a></li><li class="active">Reportajes</li></ul></div>
</div></div></div></div></section>
<div class="grids-block-5 py-5"><section class="py-lg-4 py-md-3"><div class="container">
    <?php if (!$items): ?><div class="alert alert-info">No hay reportajes publicados en este momento.</div><?php endif; ?>
    <div class="row">
        <?php foreach ($items as $i => $r): ?>
            <div class="col-lg-4 col-md-6 grids5-info<?= $i < 3 ? '' : ' mt-5' ?>">
                <a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $r['slug']) ?>" class="d-block ddp-card-media">
                    <img src="<?= ddpImg($r['foto_principal'], 'ddp-rl' . ($r['id'] ?? $i), 800, 450) ?>" alt="<?= htmlspecialchars($r['alt_foto_principal'] ?: $r['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid">
                </a>
                <div class="blog-info">
                    <h5><?= ddpFechaCorta($r['fecha_publicacion']) ?></h5>
                    <h4><a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $r['slug']) ?>" class="d-block"><?= htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8') ?></a></h4>
                    <a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $r['slug']) ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php $baseUrl = BASE_URL . '/reportajes'; require APP_PATH . '/views/public/partials/paginador.php'; ?>
</div></section></div>
