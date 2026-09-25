<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Videos</h2>
                    <div class="breadcrumb">
                        <ul>
                            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                            <li class="active"><a href="<?= BASE_URL ?>/videos">Videos</a></li>
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
                            <h2 class="title-single mb-3"><?= htmlspecialchars($video['titulo']) ?></h2>
                            <p class="text-muted"><?= ddpFechaLarga($video['fecha_publicacion']) ?></p>
                        </div>

                        <!-- Reproductor -->
                        <div class="single-post-content">
                            <div class="embed-responsive embed-responsive-16by9 my-4">
                                <?php if ($video['tipo'] === 'embed' && !empty($video['url_embed'])): ?>
                                <iframe class="embed-responsive-item" src="<?= htmlspecialchars($video['url_embed']) ?>" title="<?= htmlspecialchars($video['titulo']) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" loading="lazy"></iframe>
                                <?php else: ?>
                                <video controls playsinline controlsList="nodownload" poster="<?= htmlspecialchars(ddpImgUrl($video['poster']), ENT_QUOTES, 'UTF-8') ?>" preload="metadata" style="width:100%;height:100%;background:#000">
                                    <source src="<?= htmlspecialchars(ddpImgUrl($video['archivo_video']), ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                                    Tu navegador no soporta video HTML5.
                                </video>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($video['descripcion'])): ?>
                                <p><?= htmlspecialchars($video['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                            <?php if ($video['tipo'] === 'archivo' && $video['archivo_video']): ?>
                            <p class="text-muted small">
                                <a target="_blank" rel="noopener nofollow" href="<?= htmlspecialchars(ddpImgUrl($video['archivo_video']), ENT_QUOTES, 'UTF-8') ?>"><span class="fa fa-download mr-1"></span>Descargar video</a>
                            </p>
                            <?php endif; ?>
                        </div>

                        <nav class="post-navigation row mb-5 py-4">
                            <div class="post-prev col-md-6 pr-sm-5">
                                <span class="nav-title"><span class="fa fa-arrow-left mr-2"></span> <a href="<?= BASE_URL ?>/videos">Videos</a></span>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">
                    <div class="left-top-9 mt-5 pt-sm-3">
                        <h6 class="heading-small-text-9 mb-3">Ver más videos</h6>
                        <?php foreach ($videos as $o): ?>
                        <?php if ($o['id'] === $video['id']) continue; ?>
                        <a href="<?= BASE_URL ?>/videos/<?= $o['slug'] ?>" class="p-post d-block py-2">
                            <h6 class="text-left-inner-9"><?= htmlspecialchars($o['titulo']) ?></h6>
                            <span class="sub-inner-text-9"><?= ddpFechaCorta($o['fecha_publicacion']) ?></span>
                        </a>
                        <?php endforeach; ?>
                        <?php if (count($videos) <= 1): ?>
                        <p class="text-muted small">Sin más videos.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>