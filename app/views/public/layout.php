<?php
/** Layout público basado en la captura local de /reference. */
$siteConfig = $siteConfig ?? [];
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$scheme = $https ? 'https' : 'http';
$host = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
$siteOrigin = $scheme . '://' . $host;
$siteName = $siteConfig['site_name'] ?? SITE_NAME;
$siteNameFull = $siteConfig['site_name_full'] ?? SITE_NAME_FULL;
$siteDescription = $siteConfig['site_description'] ?? SITE_DESCRIPTION;
$seoTitle = $seo['title'] ?? ($siteName . ' | ' . $siteNameFull);
$seoDescription = $seo['description'] ?? $siteDescription;
$ogType = $seo['og_type'] ?? 'website';
$ogUrl = $seo['og_url'] ?? BASE_URL . '/';
$ogImage = $seo['og_image'] ?? '';
$assetVersion = '1';
$assetVersionCandidates = [
    ROOT_PATH . '/public/assets/css/ddp-overrides.css',
    ROOT_PATH . '/public/assets/js/ddp-media.js',
];
foreach ($assetVersionCandidates as $assetPath) {
    if (is_file($assetPath)) {
        $assetVersion = max($assetVersion, (string) filemtime($assetPath));
    }
}
if ($ogImage !== '' && !str_starts_with($ogImage, 'http') && !str_starts_with($ogImage, 'assets/') && !str_starts_with($ogImage, 'uploads/') && !str_starts_with($ogImage, '/')) {
    $ogImage = BASE_URL . '/uploads/' . $ogImage;
}
if ($ogImage !== '' && str_starts_with($ogImage, 'assets/')) {
    $ogImage = PUBLIC_URL . '/' . $ogImage;
}
if ($ogImage !== '' && str_starts_with($ogImage, 'uploads/')) {
    $ogImage = BASE_URL . '/' . $ogImage;
}
if (!preg_match('#^https?://#i', $ogUrl)) {
    $ogUrl = $siteOrigin . '/' . ltrim($ogUrl, '/');
}
if ($ogImage !== '' && !preg_match('#^https?://#i', $ogImage)) {
    $ogImage = $siteOrigin . '/' . ltrim($ogImage, '/');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8') ?>">

    <meta property="og:type" content="<?= htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($ogImage !== ''): ?>
        <meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="es_PE">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Orden de CSS taken de la referencia local. -->
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/reference-fonts.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/style-starter.css">
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/ddp-overrides.css?v=<?= rawurlencode($assetVersion) ?>">
    <link rel="icon" type="image/png" href="<?= PUBLIC_URL ?>/assets/img/logo.png">
</head>
<body>
<div class="theme-switch visually-hidden" aria-hidden="true"><input type="checkbox" aria-label="Tema"></div>
<?php require APP_PATH . '/views/public/partials/header.php'; ?>
<?= $content ?>
<?php require APP_PATH . '/views/public/partials/footer.php'; ?>
<?php require APP_PATH . '/views/public/partials/reproductor-modal.php'; ?>

<!-- Orden de JS tomado de la referencia; el switch de tema estaba comentado. -->
<script src="<?= PUBLIC_URL ?>/assets/js/jquery-3.3.1.min.js"></script>
<script src="<?= PUBLIC_URL ?>/assets/js/theme-change.js"></script>
<script src="<?= PUBLIC_URL ?>/assets/js/easyResponsiveTabs.js"></script>
<script src="<?= PUBLIC_URL ?>/assets/js/owl.carousel.js"></script>
<script src="<?= PUBLIC_URL ?>/assets/js/jquery.magnific-popup.min.js"></script>
<script src="<?= PUBLIC_URL ?>/assets/js/bootstrap.min.js"></script>
<script src="<?= PUBLIC_URL ?>/assets/js/ddp-media.js?v=<?= rawurlencode($assetVersion) ?>"></script>
<script>
$(function () {
    if ($('#parentHorizontalTab').length) {
        $('#parentHorizontalTab').easyResponsiveTabs({type: 'default', width: 'auto', fit: true, tabidentify: 'hor_1'});
    }
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        responsiveClass: true,
        autoplay: false,
        responsive: {
            0: {items: 1, nav: true},
            480: {items: 2, nav: true},
            768: {items: 3, nav: true},
            1000: {items: 4, nav: true}
        }
    });
    $('.popup-with-zoom-anim').magnificPopup({type: 'inline', fixedContentPos: false, fixedBgPos: true, overflowY: 'auto', closeBtnInside: true, removalDelay: 300, mainClass: 'my-mfp-zoom-in'});
    $('.popup-with-move-anim').magnificPopup({type: 'inline', fixedContentPos: false, fixedBgPos: true, overflowY: 'auto', closeBtnInside: true, removalDelay: 300, mainClass: 'my-mfp-move-in'});

    $(window).on('scroll', function () {
        if ($(window).scrollTop() >= 80) $('#site-header').addClass('nav-fixed');
        else $('#site-header').removeClass('nav-fixed');
    });
    $('.navbar-toggler').on('click', function () { $('body').toggleClass('noscroll'); });
    $(window).on('resize', function () {
        if ($(window).width() > 991) $('header').removeClass('active');
    });
});
</script>
</body>
</html>
