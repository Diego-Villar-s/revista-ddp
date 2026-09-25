<?php
$id = (int) ($video['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      action="<?= $id ? BASE_URL . '/admin/videos/' . $id . '/editar' : BASE_URL . '/admin/videos/nuevo' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

    <div class="card">
        <div class="card-header"><h3 class="card-title">Video</h3></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label required">Título del video</label>
                <input type="text" name="titulo" id="f_titulo" class="form-control" maxlength="255"
                       value="<?= htmlspecialchars($video['titulo']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" data-contador="240"><?= htmlspecialchars($video['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label required">Slug</label>
                <input type="text" name="slug" id="f_slug" class="form-control font-monospace"
                       value="<?= htmlspecialchars($video['slug']) ?>" required>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Medio del video</label>
                    <select name="tipo" class="form-select" data-tipomedio>
                        <option value="embed" <?= ($video['tipo'] === 'embed') ? 'selected' : '' ?>>Link de embed (YouTube/Vimeo)</option>
                        <option value="archivo" <?= ($video['tipo'] === 'archivo') ? 'selected' : '' ?>>Archivo de video local</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha</label>
                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= htmlspecialchars($video['fecha_publicacion'] ?: date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" <?= \App\Core\Auth::hasRole('redactor') ? 'disabled' : '' ?>>
                        <option value="borrador" <?= ($video['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                        <option value="publicado" <?= ($video['estado'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                    </select>
                    <?php if (\App\Core\Auth::hasRole('redactor')): ?>
                        <input type="hidden" name="estado" value="borrador">
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3" id="embedField">
                <label class="form-label">Link de embed (YouTube/Vimeo)</label>
                <input type="url" name="url_embed" class="form-control" value="<?= htmlspecialchars($video['url_embed']) ?>" placeholder="https://www.youtube.com/embed/…">
            </div>
            <div class="mt-3" id="archivoField">
                <label class="form-label">Archivo de video</label>
                <input type="file" name="archivo_video" class="form-control" accept="video/mp4,video/webm,video/ogg">
                <input type="hidden" name="archivo_video_actual" value="<?= htmlspecialchars($video['archivo_video'] ?? '') ?>">
                <?php if (!empty($video['archivo_video'])): ?>
                <div class="form-hint">Actual: <a href="<?= htmlspecialchars(ddpImgUrl($video['archivo_video']), ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars(basename($video['archivo_video'])) ?></a> — deje vacío para conservarlo.</div>
                <?php else: ?>
                <div class="form-hint">MP4/WebM/OGG · máx <?= (int) (MAX_VIDEO_SIZE / 1024 / 1024) ?> MB. Sin ffmpeg se guarda el archivo original.</div>
                <?php endif; ?>
            </div>

            <div class="mt-3">
                <label class="form-label">Poster / miniatura</label>
                <input type="file" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp" data-preview="prevPoster">
                <input type="hidden" name="poster_actual" value="<?= htmlspecialchars($video['poster'] ?? '') ?>">
                <div class="form-hint">JPG/PNG/WebP · se optimiza y convierte a WebP.</div>
                <div id="prevPoster" class="upload-preview">
                    <?php if (!empty($video['poster'])): ?>
                    <img src="<?= htmlspecialchars(ddpImgUrl($video['poster']), ENT_QUOTES, 'UTF-8') ?>" alt="Poster actual">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/admin/videos" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar video</button>
        </div>
    </div>
</form>