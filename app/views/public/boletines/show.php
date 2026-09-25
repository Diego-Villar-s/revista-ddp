<section class="breadcrumb-area py-sm-5 py-4"><div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
    <h2 class="title-big">Boletín NTEP</h2>
    <div class="breadcrumb"><ul><li><a href="<?= BASE_URL ?>/">Inicio</a></li><li><a href="<?= BASE_URL ?>/boletines">Boletines</a></li><li class="active"><?= htmlspecialchars($boletin['numero_boletin'], ENT_QUOTES, 'UTF-8') ?></li></ul></div>
</div></div></div></div></section>
<section class="w3l-homeblock5 py-0"><div class="container py-lg-5 py-4"><div class="row">
    <div class="col-lg-8 align-self">
        <h3 class="title-big mb-4">Boletín <?= htmlspecialchars($boletin['numero_boletin'], ENT_QUOTES, 'UTF-8') ?></h3>
        <?php if (!empty($boletin['resumen'])): ?><p><?= htmlspecialchars($boletin['resumen'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php foreach (preg_split('/\R/u', (string) ($boletin['temas'] ?? '')) ?: [] as $tema): ?>
            <?php if (trim($tema) !== ''): ?><p>- <?= htmlspecialchars(trim($tema), ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php endforeach; ?>
        <div class="row mt-sm-4 mt-2 px-3">
            <div class="col-6 p-0"><span>Nº <?= htmlspecialchars($boletin['numero_boletin'], ENT_QUOTES, 'UTF-8') ?></span><h4><?= ddpFechaDia($boletin['fecha_publicacion']) ?></h4></div>
            <?php if (!empty($boletin['archivo_pdf'])): ?><div class="col-6 p-0"><a target="_blank" rel="noopener" href="<?= htmlspecialchars(ddpImgUrl($boletin['archivo_pdf']), ENT_QUOTES, 'UTF-8') ?>" class="facebook"><span class="fa fa-download"></span></a><h4>Descargar PDF</h4></div><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4 mt-lg-0 mt-4"><img src="<?= ddpImg($boletin['foto_portada'], 'ddp-bs' . ($boletin['id'] ?? ''), 700, 900) ?>" class="img-fluid radius-image" alt="Portada del boletín <?= htmlspecialchars($boletin['numero_boletin'], ENT_QUOTES, 'UTF-8') ?>"></div>
</div></div></section>
