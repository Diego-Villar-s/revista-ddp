<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Videos</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                            <li class="active">Videos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <?php if (empty($videos)): ?>
            <div class="text-center py-5">
                <p class="lead mb-0">No hay videos publicados aún.</p>
            </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($videos as $i => $v): ?>
                <?php $topClass = $i < 3 ? '' : ' mt-5'; ?>
                <div class="col-lg-4 col-md-6 grids5-info<?= $topClass ?>">
                    <button type="button" class="d-block ddp-media-trigger" data-ddp-media-trigger data-tipo="video" data-id="<?= (int) ($v['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Reproducir <?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (!empty($v['poster'])): ?>
                        <img src="<?= ddpImg($v['poster'], 'ddp-vl' . ($v['id'] ?? $i), 700, 400) ?>" alt="<?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid" />
                        <?php else: ?>
                        <span class="video-gd-right d-flex align-items-center justify-content-center" style="height:220px; background: var(--bg-grey);">
                            <span class="video-play-icon"><span class="fa fa-play"></span></span>
                        </span>
                        <?php endif; ?>
                    </button>
                    <div class="blog-info">
                        <h5><?= ddpFechaCorta($v['fecha_publicacion']) ?>
                            <?php if ($v['duracion_segundos']): ?>· <?= ddpFormatoDuracion($v['duracion_segundos']) ?><?php endif; ?>
                        </h5>
                        <h4><button type="button" class="d-block ddp-media-trigger" data-ddp-media-trigger data-tipo="video" data-id="<?= (int) ($v['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?></button></h4>
                        <button type="button" class="btn mt-4 p-0 ddp-media-trigger" data-ddp-media-trigger data-tipo="video" data-id="<?= (int) ($v['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?>">Ver <span class="fa fa-arrow-right"></span></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>