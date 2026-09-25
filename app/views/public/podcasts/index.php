<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Podcast</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                            <li class="active">Podcast</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <?php if (empty($podcasts)): ?>
        <div class="text-center py-5">
            <p class="lead mb-0">No hay episodios publicados aún.</p>
        </div>
        <?php else: ?>
        <div class="row justify-content-center">
            <?php foreach ($podcasts as $p): ?>
            <div class="col-lg-3 col-sm-6 mb-sm-0 mb-5">
                <div class="area-box">
                    <button type="button" class="ddp-media-trigger" data-ddp-media-trigger data-tipo="podcast" data-id="<?= (int) ($p['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Reproducir <?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (!empty($p['portada'])): ?>
                        <img src="<?= ddpImg($p['portada'], 'ddp-pl' . ($p['id'] ?? ''), 160, 160) ?>" class="w-100" alt="<?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                        <img src="<?= PUBLIC_URL ?>/assets/img/podcast.png" alt="Podcast" class="w-100">
                        <?php endif; ?>
                    </button>
                    <button type="button" class="ddp-media-trigger" data-ddp-media-trigger data-tipo="podcast" data-id="<?= (int) ($p['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="mt-3 d-block font-weight-normal"><?= htmlspecialchars($p['descripcion'] ?: $p['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                    </button>
                    <span class="text-muted small"><?= ddpFechaCorta($p['fecha_publicacion']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>