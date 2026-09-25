<?php
$id = (int) ($especial['id'] ?? 0);
$accion = $id ? BASE_URL . '/admin/especiales/' . $id . '/editar' : BASE_URL . '/admin/especiales/nuevo';
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post" action="<?= $accion ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $id ? 'Editar especial' : 'Nuevo especial' ?></h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label required" for="f_titulo_completo">Título completo</label>
                <input type="text" class="form-control" id="f_titulo_completo" name="titulo_completo" maxlength="255" required
                       value="<?= htmlspecialchars($especial['titulo_completo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-hint">Se muestra debajo de la tarjeta negra, en texto normal.</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="f_palabra_resaltada">Palabra a resaltar</label>
                <input type="text" class="form-control" id="f_palabra_resaltada" name="palabra_resaltada" maxlength="255"
                       value="<?= htmlspecialchars($especial['palabra_resaltada'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-hint">Escribe la palabra o frase exacta. También se acepta la forma normal si el título leet usa 4, 3, 1 o 0.</div>
            </div>

            <div class="mb-3">
                <label class="form-label required" for="f_titulo_leet">Título estilizado (leet)</label>
                <textarea class="form-control font-monospace" id="f_titulo_leet" name="titulo_leet" rows="4" maxlength="255" required
                          data-contador="255"><?= htmlspecialchars($especial['titulo_leet'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="form-hint">Se escribe manualmente, por ejemplo: <code>un negocio r3nt4bl3 para bandas cr1m1n4l3s</code>.</div>
            </div>

            <div class="mb-3">
                <label class="form-label required" for="f_url_video">URL del video</label>
                <input type="url" class="form-control" id="f_url_video" name="url_video" maxlength="500" required
                       placeholder="https://www.youtube.com/watch?v=… o https://vimeo.com/…"
                       value="<?= htmlspecialchars($especial['url_video'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-hint">Se aceptan enlaces normales de YouTube, YouTube Shorts y Vimeo; el sistema genera el embed.</div>
            </div>

            <div class="mb-3">
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" <?= (int) ($especial['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
                    <span class="form-check-label">Activo</span>
                </label>
                <div class="form-hint">Solo los especiales activos aparecen en la portada.</div>
            </div>

            <?php if ($id && !empty($especial['fecha_creacion'])): ?>
            <div class="text-secondary small">Fecha de creación: <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $especial['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/admin/especiales" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar especial</button>
        </div>
    </div>
</form>
