<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Podcast</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                            <li class="active"><a href="<?= BASE_URL ?>/podcast">Podcast</a></li>
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
                            <h2 class="title-single mb-3"><?= htmlspecialchars($podcast['titulo']) ?></h2>
                            <p class="text-muted"><?= ddpFechaLarga($podcast['fecha_publicacion']) ?></p>
                        </div>

                        <!-- Reproductor -->
                        <div class="single-post-content">
                            <div class="embed-responsive embed-responsive-16by9 my-4">
                                <?php if ($podcast['tipo'] === 'embed' && !empty($podcast['url_embed'])): ?>
                                <iframe class="embed-responsive-item" src="<?= htmlspecialchars($podcast['url_embed']) ?>" allow="autoplay; clipboard-write; encrypted-media; fullscreen" loading="lazy"></iframe>
                                <?php else: ?>
                                <audio controls playsinline preload="metadata" style="width:100%">
                                    <source src="<?= htmlspecialchars(ddpImgUrl($podcast['archivo_audio']), ENT_QUOTES, 'UTF-8') ?>" type="audio/mpeg">
                                    Tu navegador no soporta audio HTML5.
                                </audio>
                                <?php endif; ?>
                            </div>
                            <?php if ($podcast['tipo'] === 'archivo' && $podcast['archivo_audio']): ?>
                            <p class="text-muted small">
                                <a target="_blank" rel="noopener nofollow" href="<?= htmlspecialchars(ddpImgUrl($podcast['archivo_audio']), ENT_QUOTES, 'UTF-8') ?>"><span class="fa fa-download mr-1"></span>Descargar audio</a>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($podcast['descripcion'])): ?>
                            <p align="justify" class="mb-4"><?= htmlspecialchars($podcast['descripcion']) ?></p>
                            <?php endif; ?>
                        </div>

                        <nav class="post-navigation row mb-5 py-4">
                            <div class="post-prev col-md-6 pr-sm-5">
                                <span class="nav-title"><span class="fa fa-arrow-left mr-2"></span> <a href="<?= BASE_URL ?>/podcast">Podcast</a></span>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
                    <div class="left-top-9 mt-5 pt-sm-3">
                        <h6 class="heading-small-text-9 mb-3">Más episodios</h6>
                        <?php foreach ($podcasts as $o): ?>
                        <?php if ($o['id'] === $podcast['id']) continue; ?>
                        <a href="<?= BASE_URL ?>/podcast/<?= $o['slug'] ?>" class="p-post d-block py-2">
                            <h6 class="text-left-inner-9"><?= htmlspecialchars($o['titulo']) ?></h6>
                            <span class="sub-inner-text-9"><?= ddpFechaCorta($o['fecha_publicacion']) ?></span>
                        </a>
                        <?php endforeach; ?>
                        <?php if (count($podcasts) <= 1): ?>
                        <p class="text-muted small">Sin más episodios.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>