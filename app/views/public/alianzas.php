<?php $pageContent = \App\Core\Validator::sanitizeHtml((string) ($pagina['contenido'] ?? '')); ?>
<section class="breadcrumb-area py-sm-5 py-4"><div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
    <h2 class="title-big"><?= htmlspecialchars($pagina['titulo'] ?? 'Alianzas', ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><ul><li><a href="<?= BASE_URL ?>/">Inicio</a></li><li class="active">Alianzas</li></ul></div>
</div></div></div></div></section>
<section class="w3l-blog mt-lg-5"><div class="container py-lg-5"><div class="row justify-content-center"><div class="col-lg-8"><div class="single-post-content"><h2 class="title-single"><?= htmlspecialchars($pagina['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2><?= $pageContent ?><a href="<?= BASE_URL ?>/contacto" class="btn btn-style btn-primary mt-4">Conversemos</a></div></div></div></div></section>
