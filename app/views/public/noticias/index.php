<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Actualidad</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                            <li class="active">Noticias</li>
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
            <?php if (empty($resultados['items'])): ?>
            <div class="text-center py-5">
                <p class="lead mb-0">No hay noticias publicadas en este momento.</p>
            </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($resultados['items'] as $i => $n): ?>
                <?php $topClass = $i < 3 ? '' : ' mt-5'; ?>
                <?php $targetUrl = !empty($n['link_externo']) ? $n['link_externo'] : BASE_URL . '/noticias'; ?>
                <?php $linkAttrs = !empty($n['link_externo']) ? ' target="_blank" rel="noopener nofollow"' : ''; ?>
                <div class="col-lg-4 col-md-6 grids5-info<?= $topClass ?>">
                    <a <?= $linkAttrs ?> href="<?= htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8') ?>" class="d-block ddp-card-media">
                        <img src="<?= ddpImg($n['foto'] ?? '', 'ddp-nl' . ($n['id'] ?? $i), 700, 394) ?>" alt="<?= htmlspecialchars($n['titulo']) ?>" class="img-fluid" />
                    </a>
                    <div class="blog-info">
                        <h5><?= ddpFechaLarga($n['fecha_publicacion']) ?></h5>
                        <h4><a <?= $linkAttrs ?> href="<?= htmlspecialchars($targetUrl) ?>" class="d-block"><?= htmlspecialchars($n['titulo']) ?></a></h4>
                        <a <?= $linkAttrs ?> href="<?= htmlspecialchars($targetUrl) ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php
                $baseUrl = BASE_URL . '/noticias';
                require APP_PATH . '/views/public/partials/paginador.php';
            ?>
        </div>
    </section>
</div>