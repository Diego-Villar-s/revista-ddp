<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Reportajes</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                            <li class="active"><a href="<?= BASE_URL ?>/reportajes">Reportajes</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-blog mt-lg-5">
    <div class="text-element-9 py-5 mt-lg-5">
        <div class="container py-lg-3">
            <div class="row grid-text-9">
                <div class="col-lg-8">
                    <div class="blog-single-post">
                        <div class="post-content">
                            <h2 class="title-single mb-3"><?= htmlspecialchars($reportaje['titulo']) ?></h2>
                        </div>
                        <div class="blo-singl mb-4">
                            <ul class="blog-single-author-date d-flex align-items-center">
                                <li class="circle"><?= htmlspecialchars(ddpAutor($reportaje)) ?></li>
                                <li><?= ddpFechaLarga($reportaje['fecha_publicacion']) ?></li>
                                <?php if ($reportaje['total_fotos'] > 0): ?>
                                <li><?= $reportaje['total_fotos'] ?> fotos</li>
                                <?php endif; ?>
                            </ul>
                            <ul class="share-post">
                                <li class="facebook">
                                    <a target="_blank" rel="noopener nofollow" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '/reportajes/' . $reportaje['slug']) ?>" title="Facebook">
                                        <span class="fa fa-facebook" aria-hidden="true"></span>
                                    </a>
                                </li>
                                <li class="twitter">
                                    <a target="_blank" rel="noopener nofollow" href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL . '/reportajes/' . $reportaje['slug']) ?>" title="Twitter">
                                        <span class="fa fa-twitter" aria-hidden="true"></span>
                                    </a>
                                </li>
                                <li class="google">
                                    <a target="_blank" rel="noopener nofollow" href="https://wa.me/?text=<?= urlencode($reportaje['titulo'] . ' ' . BASE_URL . '/reportajes/' . $reportaje['slug']) ?>" title="WhatsApp">
                                        <span class="fa fa-whatsapp" aria-hidden="true"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="single-post-image mb-4 text-center">
                            <?php if (!empty($reportaje['pdf_adjunto'])): ?>
                                <a target="_blank" rel="noopener nofollow" class="ddp-card-media d-block" href="<?= htmlspecialchars(ddpImgUrl($reportaje['pdf_adjunto']), ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="<?= ddpImg($reportaje['foto_principal'], 'ddp-rv' . $reportaje['id'], 1200, 675) ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($reportaje['alt_foto_principal'] ?: $reportaje['titulo']) ?>" />
                                </a>
                                <span class="d-block mt-3 text-muted">Clic en la imagen para ver la infografía completa</span>
                            <?php else: ?>
                                <div class="ddp-card-media">
                                    <img src="<?= ddpImg($reportaje['foto_principal'], 'ddp-rv' . $reportaje['id'], 1200, 675) ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($reportaje['alt_foto_principal'] ?: $reportaje['titulo']) ?>" />
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="single-post-content">
                            <?php if (!empty($reportaje['resumen_corto'])): ?>
                            <blockquote class="blockquote my-5">
                                <q class="mb-3 d-block"><?= htmlspecialchars($reportaje['resumen_corto']) ?></q>
                            </blockquote>
                            <?php endif; ?>
                            <?= \App\Core\Validator::sanitizeHtml((string) $reportaje['desarrollo']) ?>
                        </div>

                        <!-- Galería intercalada -->
                        <?php if (!empty($fotos)): ?>
                        <div class="single-post-content mt-4">
                            <h6 class="text-left-inner-9 mb-3">Galería de imágenes</h6>
                            <div class="row">
                                <?php foreach ($fotos as $f): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="ddp-card-media ddp-gallery-media">
                                        <img src="<?= ddpImg($f['url_foto'], 'ddp-g' . ($f['id'] ?? rand(1, 99)), 800, 450) ?>" alt="<?= htmlspecialchars($f['descripcion'] ?: $reportaje['titulo']) ?>" class="img-fluid" loading="lazy">
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <nav class="post-navigation row mb-5 py-4">
                            <div class="post-prev col-md-6 pr-sm-5">
                                <span class="nav-title"><span class="fa fa-arrow-left mr-2"></span> <a href="<?= BASE_URL ?>/reportajes">Reportajes</a></span>
                            </div>
                        </nav>
                    </div>
                </div>

                <?php require APP_PATH . '/views/public/partials/sidebar.php'; ?>
            </div>
        </div>
    </div>
</section>