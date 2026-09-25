<?php $pageContent = \App\Core\Validator::sanitizeHtml((string) ($pagina['contenido'] ?? '')); ?>
<section class="breadcrumb-area py-sm-5 py-4"><div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
    <h2 class="title-big"><?= htmlspecialchars($pagina['titulo'] ?? 'Sobre D&D', ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><ul><li><a href="<?= BASE_URL ?>/">Inicio</a></li><li class="active">Sobre D&amp;D</li></ul></div>
</div></div></div></div></section>
<section class="w3l-blog mt-lg-5"><div class="container py-lg-5"><div class="row">
    <div class="col-lg-7"><div class="single-post-content"><h2 class="title-single"><?= htmlspecialchars($pagina['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2><?= $pageContent ?></div></div>
    <div class="col-lg-5 mt-lg-0 mt-5"><img src="<?= ddpImg($pagina['imagen'] ?? '', 'ddp-about', 800, 600) ?>" alt="<?= htmlspecialchars($pagina['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="img-fluid radius-image"></div>
</div></div></section>
