<?php
$id = (int) ($pagina['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>
<form method="post" action="<?= $id ? BASE_URL . '/admin/paginas/' . $id . '/editar' : BASE_URL . '/admin/paginas/nuevo' ?>">
    <?= $this->csrfField() ?>
    <div class="card">
        <div class="card-header"><h3 class="card-title"><?= $id ? 'Editar página' : 'Nueva página' ?></h3></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Título</label>
                    <input class="form-control" name="titulo" value="<?= htmlspecialchars($pagina['titulo'] ?? '') ?>" required maxlength="180">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Slug</label>
                    <input class="form-control" name="slug" value="<?= htmlspecialchars($pagina['slug'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Contenido HTML</label>
                    <textarea class="form-control" name="contenido" rows="12"><?= htmlspecialchars($pagina['contenido'] ?? '') ?></textarea>
                    <div class="form-hint">Se permiten únicamente etiquetas editoriales básicas.</div>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Ruta de imagen</label>
                    <input class="form-control" name="imagen_actual" value="<?= htmlspecialchars($pagina['imagen'] ?? '') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="activo" value="1" <?= !empty($pagina['activo']) ? 'checked' : '' ?>><label class="form-check-label ms-2">Publicar página</label></div>
                </div>
                <div class="col-md-6"><label class="form-label">Meta título</label><input class="form-control" name="meta_titulo" value="<?= htmlspecialchars($pagina['meta_titulo'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Meta descripción</label><textarea class="form-control" name="meta_descripcion" rows="3"><?= htmlspecialchars($pagina['meta_descripcion'] ?? '') ?></textarea></div>
            </div>
        </div>
        <div class="card-footer text-end"><button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Guardar</button></div>
    </div>
</form>
