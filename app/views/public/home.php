<?php
$siteConfig = $siteConfig ?? [];
$institutional = $siteConfig['institutional_text'] ?? SITE_DESCRIPTION;
$aboutCta = $siteConfig['about_cta'] ?? 'Nosotros';
$youtubeEmbed = $siteConfig['youtube_embed'] ?? '';
$youtubeTitle = $siteConfig['youtube_title'] ?? 'Video institucional';
$facebook = $siteConfig['social_facebook'] ?? '#';
$tiktok = $siteConfig['social_tiktok'] ?? '#';
$instagram = $siteConfig['social_instagram'] ?? '#';
$newsUrl = static function (array $news): string {
    return !empty($news['link_externo']) ? $news['link_externo'] : BASE_URL . '/noticias';
};
?>
<section class="breadcrumb-area py-sm-5 py-4">
    <div class="container"><div class="row"><div class="col-md-12">
        <div class="breadcrumb-contents"><h2 class="title-big">Reportajes</h2></div>
    </div></div></div>
</section>

<?php if ($reportajePrincipal): ?>
<section class="w3l-video w3l-homeblock3" id="video">
    <div class="container-fluid">
        <div class="video-grids-info row">
            <div class="video-gd-right col-lg-6 p-0">
                <div class="position-relative">
                    <a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $reportajePrincipal['slug']) ?>" class="ddp-card-media">
                        <img src="<?= ddpImg($reportajePrincipal['foto_principal'], 'ddp-featured', 960, 540) ?>" alt="<?= htmlspecialchars($reportajePrincipal['alt_foto_principal'] ?: $reportajePrincipal['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid">
                    </a>
                    <?php if (!empty($reportajePrincipal['video_embed'] ?? '')): ?>
                        <a href="#video-ddp" class="popup-with-zoom-anim play-view text-center position-absolute" aria-label="Ver video"></a>
                        <div id="video-ddp" class="zoom-anim-dialog mfp-hide">
                            <iframe src="<?= htmlspecialchars($reportajePrincipal['video_embed'], ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($reportajePrincipal['titulo'], ENT_QUOTES, 'UTF-8') ?>" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="video-gd-left col-lg-6 p-lg-5 p-4 align-self">
                <div class="p-xl-4 p-0 video-wrap">
                    <h5><?= ddpFechaCorta($reportajePrincipal['fecha_publicacion']) ?></h5>
                    <h3 class="title-big text-left mb-4"><a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $reportajePrincipal['slug']) ?>"><?= htmlspecialchars($reportajePrincipal['titulo'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                    <p><?= htmlspecialchars(mb_substr((string) $reportajePrincipal['resumen_corto'], 0, 240), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen((string) $reportajePrincipal['resumen_corto']) > 240 ? '…' : '' ?></p>
                    <a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $reportajePrincipal['slug']) ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="grids-block-5 py-1">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach ($ultimosReportajes as $r): ?>
                    <div class="col-lg-4 col-md-6 grids5-info mt-5">
                        <a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $r['slug']) ?>" class="d-block ddp-card-media">
                            <img src="<?= ddpImg($r['foto_principal'], 'ddp-r' . ($r['id'] ?? ''), 800, 450) ?>" alt="<?= htmlspecialchars($r['alt_foto_principal'] ?: $r['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid">
                        </a>
                        <div class="blog-info">
                            <h5><?= ddpFechaCorta($r['fecha_publicacion']) ?></h5>
                            <h4><a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $r['slug']) ?>" class="d-block"><?= htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8') ?></a></h4>
                            <a href="<?= BASE_URL ?>/reportajes/<?= rawurlencode((string) $r['slug']) ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination"><ul><li><a href="<?= BASE_URL ?>/reportajes">Ver todos</a></li></ul></div>
        </div>
    </section>
</div>

<section class="breadcrumb-area py-sm-5 py-1" id="actualidad">
    <div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents"><h2 class="title-big">Noticias Recientes</h2></div></div></div></div>
</section>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach ($noticiasRecientes as $i => $n): ?>
                    <?php $href = $newsUrl($n); ?>
                    <div class="col-lg-4 col-md-6 grids5-info <?= $i > 0 ? 'mt-md-0 mt-5' : '' ?>">
                        <a <?= !empty($n['link_externo']) ? 'target="_blank" rel="noopener nofollow"' : '' ?> href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="d-block ddp-card-media">
                            <img src="<?= ddpImg($n['foto'] ?? '', 'ddp-n' . ($n['id'] ?? $i), 700, 394) ?>" alt="<?= htmlspecialchars($n['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid">
                        </a>
                        <div class="blog-info">
                            <h5><?= ddpFechaLarga($n['fecha_publicacion']) ?></h5>
                            <h4><a <?= !empty($n['link_externo']) ? 'target="_blank" rel="noopener nofollow"' : '' ?> href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="d-block"><?= htmlspecialchars($n['titulo'], ENT_QUOTES, 'UTF-8') ?></a></h4>
                            <a <?= !empty($n['link_externo']) ? 'target="_blank" rel="noopener nofollow"' : '' ?> href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination"><ul><li><a href="<?= BASE_URL ?>/noticias">Ver todos</a></li></ul></div>
        </div>
    </section>
</div>

<?php if ($ultimoBoletin): ?>
<section class="w3l-homeblock5 py-0">
    <div class="container py-lg-5 py-4"><div class="row">
        <div class="col-lg-8 align-self">
            <h3 class="title-big mb-4">Boletín NTEP Año <?= date('Y', strtotime((string) $ultimoBoletin['fecha_publicacion'])) ?></h3>
            <?php foreach (preg_split('/\R/u', (string) ($ultimoBoletin['temas'] ?? '')) ?: [] as $tema): ?>
                <?php if (trim($tema) !== ''): ?><p class="">- <?= htmlspecialchars(trim($tema), ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php endforeach; ?>
            <div class="row mt-sm-4 mt-2 px-3">
                <div class="col-6 p-0"><span>Nº <?= htmlspecialchars($ultimoBoletin['numero_boletin'], ENT_QUOTES, 'UTF-8') ?></span><h4><?= ddpFechaDia($ultimoBoletin['fecha_publicacion']) ?></h4></div>
                <div class="col-6 p-0">
                    <?php if (!empty($ultimoBoletin['archivo_pdf'])): ?><a target="_blank" rel="noopener" href="<?= htmlspecialchars(ddpImgUrl($ultimoBoletin['archivo_pdf']), ENT_QUOTES, 'UTF-8') ?>" class="facebook"><span class="fa fa-download"></span></a><h4>Ver Boletín</h4><?php endif; ?>
                </div>
                <div class="col-12 text-center mt-4"><a href="<?= BASE_URL ?>/boletines" class="btn btn-style btn-primary">Ver todos</a></div>
            </div>
        </div>
        <div class="col-lg-4 mt-lg-0 mt-4"><a href="<?= BASE_URL ?>/boletines/<?= rawurlencode((string) $ultimoBoletin['numero_boletin']) ?>"><img src="<?= ddpImg($ultimoBoletin['foto_portada'], 'ddp-boletin', 700, 900) ?>" class="img-fluid radius-image" alt="Portada del boletín <?= htmlspecialchars($ultimoBoletin['numero_boletin'], ENT_QUOTES, 'UTF-8') ?>"></a></div>
    </div></div>
</section>
<?php endif; ?>

<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <h3 class="title-big mb-5 text-center">Podcast</h3>
        <div class="row">
            <?php foreach ($ultimosPodcasts as $i => $p): ?>
                <div class="col-lg-3 col-sm-6 <?= $i > 0 ? 'mt-sm-0 mt-5' : '' ?>">
                    <div class="area-box">
                        <button type="button" class="ddp-media-trigger" data-ddp-media-trigger data-tipo="podcast" data-id="<?= (int) ($p['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Reproducir <?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?= ddpImg($p['portada'] ?? '', 'ddp-p' . ($p['id'] ?? $i), 160, 160) ?>" alt="<?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        </button>
                        <button type="button" class="ddp-media-trigger" data-ddp-media-trigger data-tipo="podcast" data-id="<?= (int) ($p['id'] ?? 0) ?>" data-title="<?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                            <span class="mt-3 d-block"><?= htmlspecialchars($p['descripcion'] ?: $p['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center"><a href="<?= BASE_URL ?>/podcast" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a></div>
    </div>
</section>

<?php if ($especiales): ?>
<section class="w3l-team" id="team">
    <div class="teams1 py-5 mb-3"><div class="container py-lg-3 pb-lg-5 pb-4"><div class="teams1-content">
        <h3 class="title-big text-center mb-5">Especiales</h3>
        <div class="ddp-special-carousel" data-ddp-special-carousel aria-label="Especiales">
            <div class="ddp-carousel-viewport">
                <div class="ddp-carousel-track" data-ddp-carousel-track>
                    <?php foreach ($especiales as $especial): ?>
                        <?php require APP_PATH . '/views/public/partials/especial-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button" class="ddp-carousel-prev" data-ddp-carousel-prev aria-label="Anterior">‹</button>
            <button type="button" class="ddp-carousel-next" data-ddp-carousel-next aria-label="Siguiente">›</button>
            <div class="ddp-carousel-dots" data-ddp-carousel-dots role="tablist" aria-label="Páginas del carrusel"></div>
        </div>
    </div></div></div>
</section>
<?php endif; ?>

<section class="w3l-banner py-0" id="work">
    <div class="midd-w3 py-lg-4 py-md-3"><div class="container"><div class="row">
        <div class="col-lg-6 mt-lg-0 mt-lg-5 about-right-faq align-self">
            <h5 class="title-small mb-2"><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></h5>
            <h3 class="title-banner"><?= htmlspecialchars(SITE_NAME_FULL, ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="mt-4"><?= htmlspecialchars($institutional, ENT_QUOTES, 'UTF-8') ?></p>
            <a href="<?= BASE_URL ?>/sobre-nosotros" class="btn btn-style btn-primary mt-md-5 mt-4"><?= htmlspecialchars($aboutCta, ENT_QUOTES, 'UTF-8') ?></a>
        </div>
        <div class="col-md-6 left-wthree-img mt-lg-0 mt-4"><div class="position-relative">
            <img src="<?= PUBLIC_URL ?>/assets/img/bannerimg.jpg" alt="<?= htmlspecialchars(SITE_NAME_FULL, ENT_QUOTES, 'UTF-8') ?>" class="img-fluid">
            <?php if ($youtubeEmbed !== ''): ?>
                <a href="#video-institucional" class="popup-with-zoom-anim play-view text-center position-absolute" aria-label="<?= htmlspecialchars($youtubeTitle, ENT_QUOTES, 'UTF-8') ?>"></a>
                <div id="video-institucional" class="zoom-anim-dialog mfp-hide"><iframe src="<?= htmlspecialchars($youtubeEmbed, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($youtubeTitle, ENT_QUOTES, 'UTF-8') ?>" allow="autoplay; fullscreen" allowfullscreen></iframe></div>
            <?php endif; ?>
        </div></div>
    </div></div></div>
</section>

<div class="middle py-5" id="alianzas">
    <div class="container py-xl-5 py-lg-3"><div class="welcome-left text-center py-md-5 py-3">
        <h3 class="title-big">Síguenos en nuestras Redes Sociales</h3>
        <div class="main-social-footer-29">
            <a target="_blank" rel="noopener" href="<?= htmlspecialchars($facebook, ENT_QUOTES, 'UTF-8') ?>" class="facebook" aria-label="Facebook"><span class="fa fa-facebook-square fa-2x"></span></a>
            <a target="_blank" rel="noopener" href="<?= htmlspecialchars($tiktok, ENT_QUOTES, 'UTF-8') ?>" class="twitter" aria-label="TikTok"><img src="<?= PUBLIC_URL ?>/assets/img/tiktokg.png" alt="TikTok"></a>
            <a target="_blank" rel="noopener" href="<?= htmlspecialchars($instagram, ENT_QUOTES, 'UTF-8') ?>" class="instagram" aria-label="Instagram"><span class="fa fa-instagram fa-2x"></span></a>
        </div>
    </div></div>
</div>
