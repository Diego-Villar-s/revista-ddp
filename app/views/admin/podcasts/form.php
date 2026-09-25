<?php
$id = (int) ($podcast['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      action="<?= $id ? BASE_URL . '/admin/podcasts/' . $id . '/editar' : BASE_URL . '/admin/podcasts/nuevo' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

    <div class="card">
        <div class="card-header"><h3 class="card-title">Episodio de podcast</h3></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label required">Título del episodio</label>
                <input type="text" name="titulo" id="f_titulo" class="form-control" maxlength="255"
                       value="<?= htmlspecialchars($podcast['titulo']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción corta</label>
                <textarea name="descripcion" class="form-control" rows="3" data-contador="240"><?= htmlspecialchars($podcast['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label required">Slug</label>
                <input type="text" name="slug" id="f_slug" class="form-control font-monospace"
                       value="<?= htmlspecialchars($podcast['slug']) ?>" required>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Medio del audio</label>
                    <select name="tipo" id="tipoMedio" class="form-select" data-tipomedio>
                        <option value="embed" <?= ($podcast['tipo'] === 'embed') ? 'selected' : '' ?>>Link de embed (Spotify/YouTube)</option>
                        <option value="archivo" <?= ($podcast['tipo'] === 'archivo') ? 'selected' : '' ?>>Archivo de audio local</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha</label>
                    <input type="date" name="fecha_publicacion" class="form-control" value="<?= htmlspecialchars($podcast['fecha_publicacion'] ?: date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" <?= \App\Core\Auth::hasRole('redactor') ? 'disabled' : '' ?>>
                        <option value="borrador" <?= ($podcast['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                        <option value="publicado" <?= ($podcast['estado'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                    </select>
                    <?php if (\App\Core\Auth::hasRole('redactor')): ?>
                        <input type="hidden" name="estado" value="borrador">
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3" id="embedField">
                <label class="form-label">Link de embed (Spotify/YouTube)</label>
                <input type="url" name="url_embed" class="form-control" value="<?= htmlspecialchars($podcast['url_embed']) ?>" placeholder="https://open.spotify.com/embed/…">
            </div>
            <div class="mt-3" id="archivoField">
                <label class="form-label">Archivo de audio</label>
                <input type="file" name="archivo_audio" class="form-control" accept="audio/mpeg,audio/ogg,audio/wav">
                <input type="hidden" name="archivo_audio_actual" value="<?= htmlspecialchars($podcast['archivo_audio'] ?? '') ?>">
                <?php if (!empty($podcast['archivo_audio'])): ?>
                <div class="form-hint">Actual: <a href="<?= htmlspecialchars(ddpImgUrl($podcast['archivo_audio']), ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars(basename($podcast['archivo_audio'])) ?></a> — deje vacío para conservarlo.</div>
                <?php else: ?>
                <div class="form-hint">MP3/OGG/WAV · máx <?= (int) (MAX_AUDIO_SIZE / 1024 / 1024) ?> MB. Sin ffmpeg se guarda el archivo original.</div>
                <?php endif; ?>
            </div>

            <div class="mt-3">
                <label class="form-label">Portada del episodio</label>
                <input type="file" name="portada" class="form-control" accept="image/jpeg,image/png,image/webp" data-preview="prevPortada">
                <input type="hidden" name="portada_actual" value="<?= htmlspecialchars($podcast['portada'] ?? '') ?>">
                <div class="form-hint">JPG/PNG/WebP · se optimiza y convierte a WebP.</div>
                <div id="prevPortada" class="upload-preview">
                    <?php if (!empty($podcast['portada'])): ?>
                    <img src="<?= htmlspecialchars(ddpImgUrl($podcast['portada']), ENT_QUOTES, 'UTF-8') ?>" alt="Portada actual">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>/admin/podcasts" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar podcast</button>
        </div>
    </div>
</form>