<?php
use App\Core\Auth;
$esAdminOrEditor = Auth::hasRole('admin', 'editor');
$id = (int) ($noticia['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      action="<?= $id ? BASE_URL . '/admin/noticias/' . $id . '/editar' : BASE_URL . '/admin/noticias/nuevo' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

    <div class="card">
        <div class="card-header"><h3 class="card-title">Datos de la noticia</h3></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label required">Título</label>
                <input type="text" name="titulo" id="f_titulo" class="form-control" data-contador="70" maxlength="255"
                       value="<?= htmlspecialchars($noticia['titulo']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label required">Slug</label>
                <input type="text" name="slug" id="f_slug" class="form-control font-monospace"
                       value="<?= htmlspecialchars($noticia['slug']) ?>" required>
                <div class="form-hint">Se genera automáticamente desde el título.</div>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Enlace externo (opcional)</label>
                    <input type="url" name="link_externo" class="form-control" value="<?= htmlspecialchars($noticia['link_externo']) ?>" placeholder="https://…">
                    <div class="form-hint">Si se llena, la noticia llevará a ese enlace externo.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha de publicación</label>
                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= htmlspecialchars($noticia['fecha_publicacion'] ?: date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" <?= \App\Core\Auth::hasRole('redactor') ? 'disabled' : '' ?>>
                        <option value="borrador" <?= (($noticia['estado'] ?? 'borrador') === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                        <option value="publicado" <?= (($noticia['estado'] ?? '') === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                        <option value="archivado" <?= (($noticia['estado'] ?? '') === 'archivado') ? 'selected' : '' ?>>Archivado</option>
                    </select>
                    <?php if (\App\Core\Auth::hasRole('redactor')): ?><input type="hidden" name="estado" value="borrador"><?php endif; ?>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Foto (opcional)</label>
                <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp" data-preview="prevFoto">
                <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($noticia['foto'] ?? '') ?>">
                <div class="form-hint">JPG/PNG/WebP · máx <?= (int) (MAX_IMAGE_SIZE / 1024 / 1024) ?> MB.</div>
                <div id="prevFoto" class="upload-preview">
                    <?php if (!empty($noticia['foto'])): ?>
                    <img src="<?= htmlspecialchars(ddpImgUrl($noticia['foto']), ENT_QUOTES, 'UTF-8') ?>" alt="Foto actual">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/admin/noticias" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar noticia</button>
        </div>
    </div>
</form>