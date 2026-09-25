<?php
$id = (int) ($boletin['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      action="<?= $id ? BASE_URL . '/admin/boletines/' . $id . '/editar' : BASE_URL . '/admin/boletines/nuevo' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

    <div class="card">
        <div class="card-header"><h3 class="card-title">Boletín NTEP</h3></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Número de boletín</label>
                    <input type="text" name="numero_boletin" class="form-control" maxlength="50"
                           value="<?= htmlspecialchars($boletin['numero_boletin']) ?>" required placeholder="NTEP N° 12">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha de publicación</label>
                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= htmlspecialchars($boletin['fecha_publicacion'] ?: date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" <?= \App\Core\Auth::hasRole('redactor') ? 'disabled' : '' ?>>
                        <option value="borrador" <?= (($boletin['estado'] ?? 'borrador') === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                        <option value="publicado" <?= (($boletin['estado'] ?? '') === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                        <option value="archivado" <?= (($boletin['estado'] ?? '') === 'archivado') ? 'selected' : '' ?>>Archivado</option>
                    </select>
                    <?php if (\App\Core\Auth::hasRole('redactor')): ?>
                        <input type="hidden" name="estado" value="borrador">
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Resumen</label>
                <textarea name="resumen" class="form-control" rows="4"><?= htmlspecialchars($boletin['resumen'] ?? '') ?></textarea>
            </div>
            <div class="mt-3">
                <label class="form-label">Titulares destacados (uno por línea)</label>
                <textarea name="temas" class="form-control" rows="4"><?= htmlspecialchars($boletin['temas'] ?? '') ?></textarea>
            </div>
            <div class="row g-3 mt-0">
                <div class="col-md-6">
                    <label class="form-label">Foto portada</label>
                    <input type="file" name="foto_portada" class="form-control" accept="image/jpeg,image/png,image/webp" data-preview="prevPortada">
                    <input type="hidden" name="foto_portada_actual" value="<?= htmlspecialchars($boletin['foto_portada'] ?? '') ?>">
                    <div class="form-hint">JPG/PNG/WebP · máx <?= (int) (MAX_IMAGE_SIZE / 1024 / 1024) ?> MB.</div>
                    <div id="prevPortada" class="upload-preview">
                        <?php if (!empty($boletin['foto_portada'])): ?>
                        <img src="<?= htmlspecialchars(ddpImgUrl($boletin['foto_portada']), ENT_QUOTES, 'UTF-8') ?>" alt="Portada actual">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Archivo PDF</label>
                    <input type="file" name="archivo_pdf" class="form-control" accept="application/pdf">
                    <input type="hidden" name="archivo_pdf_actual" value="<?= htmlspecialchars($boletin['archivo_pdf'] ?? '') ?>">
                    <div class="form-hint">
                        <?php if (!empty($boletin['archivo_pdf'])): ?>
                        Actual: <a href="<?= htmlspecialchars(ddpImgUrl($boletin['archivo_pdf']), ENT_QUOTES, 'UTF-8') ?>" target="_blank">abrir PDF</a> — deje vacío para conservarlo.
                        <?php else: ?>Solo PDF · máx <?= (int) (MAX_PDF_SIZE / 1024 / 1024) ?> MB.<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/admin/boletines" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar boletín</button>
        </div>
    </div>
</form>