<?php
$pageContent = \App\Core\Validator::sanitizeHtml((string) ($pagina['contenido'] ?? ''));
$error = $_GET['error'] ?? '';
$success = isset($_GET['exito']);
?>
<section class="breadcrumb-area py-sm-5 py-4"><div class="container"><div class="row"><div class="col-md-12"><div class="breadcrumb-contents">
    <h2 class="title-big"><?= htmlspecialchars($pagina['titulo'] ?? 'Contacto', ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><ul><li><a href="<?= BASE_URL ?>/">Inicio</a></li><li class="active">Contacto</li></ul></div>
</div></div></div></div></section>
<section class="w3l-blog mt-lg-5"><div class="container py-lg-5"><div class="row g-5">
    <div class="col-lg-5"><?= $pageContent ?></div>
    <div class="col-lg-7">
        <?php if ($success): ?><div class="alert alert-success">Gracias por escribirnos. Recibimos tu mensaje.</div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger">Revisa los campos del formulario e inténtalo nuevamente.</div><?php endif; ?>
        <form id="form-contacto" method="post" action="<?= BASE_URL ?>/contacto" class="card card-body border-0 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div class="mb-3"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" required maxlength="100"></div>
            <div class="mb-3"><label class="form-label" for="email">Correo</label><input class="form-control" id="email" name="email" type="email" required maxlength="150"></div>
            <div class="mb-3"><label class="form-label" for="asunto">Asunto</label><input class="form-control" id="asunto" name="asunto" required maxlength="180"></div>
            <div class="mb-3"><label class="form-label" for="mensaje">Mensaje</label><textarea class="form-control" id="mensaje" name="mensaje" rows="6" required maxlength="2000"></textarea></div>
            <button class="btn btn-style btn-primary" type="submit">Enviar mensaje</button>
        </form>
    </div>
</div></div></section>
