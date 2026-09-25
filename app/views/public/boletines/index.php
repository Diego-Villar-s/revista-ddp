<?php
$items = $resultados['items'] ?? [];
?>
<section class="breadcrumb-area py-sm-5 py-4"><div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
    <h2 class="title-big">Boletines NTEP</h2>
    <div class="breadcrumb"><ul><li><a href="<?= BASE_URL ?>/">Inicio</a></li><li class="active">Boletines</li></ul></div>
</div></div></div></div></section>
<div class="grids-block-5 py-5"><section class="py-lg-4 py-md-3"><div class="container">
    <?php if (!$items): ?><div class="alert alert-info">No hay boletines publicados en este momento.</div><?php endif; ?>
    <div class="row">
        <?php foreach ($items as $i => $b): ?>
            <div class="col-lg-4 col-md-6 grids5-info <?= $i % 3 === 0 ? '' : 'mt-md-0 mt-5' ?>">
                <a href="<?= htmlspecialchars(ddpImgUrl($b['archivo_pdf']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="d-block">
                    <img src="<?= ddpImg($b['foto_portada'], 'ddp-b' . ($b['id'] ?? $i), 700, 900) ?>" alt="Portada del boletín <?= htmlspecialchars($b['numero_boletin'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid">
                </a>
                <div class="blog-info">
                    <h5><?= ddpFechaCorta($b['fecha_publicacion']) ?></h5>
                    <a href="<?= htmlspecialchars(ddpImgUrl($b['archivo_pdf']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn mt-4 p-0"><?= $i === 0 ? 'Ver boletín' : 'Leer' ?> <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php $baseUrl = BASE_URL . '/boletines'; require APP_PATH . '/views/public/partials/paginador.php'; ?>
</div></section></div>
