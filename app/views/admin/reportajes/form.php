<?php
/**
 * Admin: Formulario de reportaje (crear/editar)
 * Es = create o edit
 */
use App\Core\Auth;

$esRedactor = $esRedactor ?? false;
$esAdminOrEditor = Auth::hasRole('admin', 'editor');
$reportajeId = (int) ($reportaje['id'] ?? 0);
$autoresProps = $autores ?? [];
?><!-- Flash -->
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- ===================== Columna principal ===================== -->
    <div class="col-lg-8">
        <form method="post" id="formReportaje"
              action="<?= $modo === 'create' ? BASE_URL . '/admin/reportajes/nuevo' : BASE_URL . '/admin/reportajes/' . $reportajeId . '/editar' ?>"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contenido del reportaje</h3>
                </div>
                <div class="card-body">
                    <!-- Título -->
                    <div class="mb-3">
                        <label class="form-label required">Título</label>
                        <input type="text" id="f_titulo" name="titulo" class="form-control" data-contador="70" maxlength="255"
                               value="<?= htmlspecialchars($reportaje['titulo']) ?>" required placeholder="Recomendado: hasta 70 caracteres">
                        <div class="form-hint">Aviso editorial: se recomienda un título de máximo 70 caracteres para SEO y redes.</div>
                    </div>

                    <!-- Slug -->
                    <div class="mb-3">
                        <label class="form-label required">Slug (URL)</label>
                        <input type="text" id="f_slug" name="slug" class="form-control font-monospace"
                               value="<?= htmlspecialchars($reportaje['slug']) ?>" required placeholder="se-genera-automaticamente-desde-el-titulo">
                        <div class="form-hint">Se genera automáticamente desde el título. Puede editarlo; debe ser único y sin espacios.</div>
                    </div>

                    <!-- Resumen corto -->
                    <div class="mb-3">
                        <label class="form-label">Resumen corto</label>
                        <textarea id="f_resumen" name="resumen_corto" class="form-control" rows="3" data-contador="160" maxlength="500" placeholder="Resumen breve (recomendado: hasta 160 caracteres)"><?= htmlspecialchars($reportaje['resumen_corto']) ?></textarea>
                        <div class="form-hint">Aparece en las tarjetas del sitio y como extracto. Se recomienda no exceder 160 caracteres.</div>
                    </div>

                    <!-- Desarrollo (TinyMCE) -->
                    <div class="mb-0">
                        <label class="form-label">Desarrollo</label>
                        <textarea id="f_desarrollo" name="desarrollo" class="form-control" rows="16"><?= htmlspecialchars($reportaje['desarrollo']) ?></textarea>
                        <div class="form-hint">Editor enriquecido: use el botón de imagen para insertar fotos de la galería o URLs.</div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Video embed (opcional)</label>
                    <input type="url" name="video_embed" class="form-control" value="<?= htmlspecialchars($reportaje['video_embed'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://www.youtube.com/embed/…">
                    <div class="form-hint">Si se completa, el destacado de la portada muestra un popup de video.</div>
                </div>
            </div>

            <!-- Imagen principal -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Imagen principal</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Foto principal</label>
                            <input type="file" name="foto_principal" id="f_foto_principal" class="form-control" accept="image/jpeg,image/png,image/webp" data-preview="prevFotoPrincipal">
                            <div class="form-hint">JPG/PNG/WebP · máx <?= (int) (MAX_IMAGE_SIZE / 1024 / 1024) ?> MB · se optimiza a <?= IMAGE_MAX_WIDTH ?>px y se genera WebP.</div>
                            <div id="prevFotoPrincipal" class="upload-preview">
                                <?php if (!empty($reportaje['foto_principal'])): ?>
                                <img src="<?= htmlspecialchars(ddpImgUrl($reportaje['foto_principal']), ENT_QUOTES, 'UTF-8') ?>" alt="Foto principal actual">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="foto_principal_actual" value="<?= htmlspecialchars($reportaje['foto_principal']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Texto alternativo (Alt)</label>
                            <input type="text" name="alt_foto_principal" id="f_alt" class="form-control" data-contador="120"
                                   value="<?= htmlspecialchars($reportaje['alt_foto_principal']) ?>" required placeholder="Descripción breve y precisa de la imagen">
                            <div class="form-hint">Obligatorio para accesibilidad y SEO.</div>

                            <label class="form-label mt-3">PDF adjunto (opcional)</label>
                            <input type="file" name="pdf_adjunto" class="form-control" accept="application/pdf">
                            <div class="form-hint">
                                <?php if (!empty($reportaje['pdf_adjunto'])): ?>
                                Actual: <a href="<?= htmlspecialchars(ddpImgUrl($reportaje['pdf_adjunto']), ENT_QUOTES, 'UTF-8') ?>" target="_blank">abrir PDF</a> — deje vacío para conservarlo.
                                <?php else: ?>Solo PDF · máx <?= (int) (MAX_IMAGE_SIZE / 1024 / 1024) ?> MB.<?php endif; ?>
                            </div>
                            <input type="hidden" name="pdf_adjunto_actual" value="<?= htmlspecialchars($reportaje['pdf_adjunto']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publicación -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Publicación</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Fecha de publicación</label>
                            <input type="date" id="f_fecha" name="fecha_publicacion" class="form-control" value="<?= htmlspecialchars($reportaje['fecha_publicacion'] ?: date('Y-m-d')) ?>" required>
                            <div class="form-hint">Si es futura, el reportaje no aparecerá en el sitio público hasta esa fecha.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Autor</label>
                            <select id="f_autor_id" name="autor_id" class="form-select">
                                <option value="">— Asignar automáticamente "Redacción" —</option>
                                <?php foreach ($autoresProps as $aId => $aNombre): ?>
                                <option value="<?= (int) $aId ?>" <?= ((int) ($reportaje['autor_id'] ?? 0) === (int) $aId) ? 'selected' : '' ?>><?= htmlspecialchars($aNombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Si no se selecciona, se asume "Redacción".</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select id="f_estado" name="estado" class="form-select" <?= $esRedactor ? 'disabled' : '' ?>>
                                <option value="borrador" <?= ($reportaje['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                                <option value="publicado" <?= ($reportaje['estado'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                                <option value="archivado" <?= ($reportaje['estado'] === 'archivado') ? 'selected' : '' ?>>Archivado</option>
                            </select>
                            <?php if ($esRedactor): ?>
                            <input type="hidden" name="estado" value="borrador">
                            <div class="form-hint text-danger">Como redactor, el reportaje se guarda siempre en borrador. Solo editores/admin pueden publicar.</div>
                            <?php else: ?>
                            <div class="form-hint">"Publicado" con fecha futura no es visible en el sitio hasta el día indicado.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($esAdminOrEditor): ?>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="es_destacado" id="f_destacado" value="1" <?= $reportaje['es_destacado'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="f_destacado">Marcar como <strong>reportaje destacado</strong> en portada</label>
                        <div class="form-hint">Se usa como imagen principal de la portada. La sección "Especiales" muestra videos publicados. Muy pocos destacados es mejor.</div>
                    </div>
                    <?php endif; ?>

                    <!-- Checklist y vista previa -->
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" id="btnPreview" class="btn btn-outline-primary" data-url="<?= BASE_URL ?>/admin/reportajes/<?= $reportajeId ?: 0 ?>/preview">
                            <i class="ti ti-eye me-1"></i>Vista previa
                        </button>
                        <?php if ($esAdminOrEditor): ?>
                        <button type="button" id="btnRevisarChecklist" class="btn btn-outline-warning"><i class="ti ti-checklist me-1"></i>Revisar checklist</button>
                        <?php endif; ?>
                        <div id="checklistResultado" class="w-100"></div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">SEO (optimización para buscadores)</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta título</label>
                        <input type="text" id="f_meta_titulo" name="meta_titulo" class="form-control" data-contador="70" maxlength="255"
                               value="<?= htmlspecialchars($reportaje['meta_titulo']) ?>" placeholder="Recomendado: máximo 70 caracteres">
                        <div class="form-hint">Si se deja vacío, se usa el título del reportaje.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Meta descripción</label>
                        <textarea id="f_meta_descripcion" name="meta_descripcion" class="form-control" rows="2" maxlength="320" data-meta-desc placeholder="Recomendado: entre 120 y 160 caracteres"><?= htmlspecialchars($reportaje['meta_descripcion']) ?></textarea>
                        <div class="form-hint">Se muestra en Google y redes sociales. Rango recomendado: 120–160 caracteres.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="<?= BASE_URL ?>/admin/reportajes" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar reportaje</button>
            </div>
        </form>
    </div>

    <!-- ===================== Columna lateral ===================== -->
    <div class="col-lg-4">
        <!-- Galería de fotos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Galería del reportaje</h3>
            </div>
            <div class="card-body">
                <?php if ($reportajeId): ?>
                <form method="post" enctype="multipart/form-data" class="mb-3" id="formGaleria">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">
                    <input type="hidden" name="reportaje_id" value="<?= $reportajeId ?>">
                    <label class="form-label">Subir fotos a la galería</label>
                    <input type="file" name="foto_galeria" class="form-control mb-2" accept="image/*">
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" name="descripcion" class="form-control" placeholder="Descripción (opcional)">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-upload me-1"></i>Subir</button>
                        </div>
                    </div>
                </form>
                    <div id="galeriaProgreso" class="progress progress-sm mt-3" hidden aria-label="Progreso de subida">
                        <div id="galeriaBar" class="progress-bar" style="width:0%"></div>
                    </div>
                <div class="gallery-thumbs">
                    <?php if (empty($fotos)): ?>
                    <p class="text-muted small">Sin fotos. Guarde primero el reportaje y luego suba imágenes.</p>
                    <?php endif; ?>
                    <?php foreach ($fotos as $f): ?>
                    <div class="gallery-item-admin" data-foto-id="<?= (int) $f['id'] ?>">
                        <img src="<?= htmlspecialchars(ddpImgUrl($f['url_foto_thumb'] ?: $f['url_foto']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($f['descripcion']) ?>">
                        <button type="button" class="btn btn-icon btn-sm btn-danger g-del" data-eliminar-foto="<?= (int) $f['id'] ?>" title="Eliminar"><i class="ti ti-x"></i></button>
                        <div class="g-item-body">
                            <input type="text" class="form-control" name="desc_<?= (int) $f['id'] ?>" value="<?= htmlspecialchars($f['descripcion']) ?>" placeholder="Descripción">
                            <input type="number" class="form-control mt-1" name="orden_<?= (int) $f['id'] ?>" value="<?= (int) $f['orden'] ?>" placeholder="Orden">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted small mb-0">Guarde el reportaje primero para poder gestionar su galería de imágenes.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historial de cambios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de cambios</h3>
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($historial)): ?>
                <div class="list-group-item text-muted small">Sin cambios registrados aún.</div>
                <?php endif; ?>
                <?php foreach ($historial as $h): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-primary-lt"><?= htmlspecialchars($h['accion']) ?></span>
                        <small class="text-secondary"><?= date('d/m H:i', strtotime($h['created_at'])) ?></small>
                    </div>
                    <div class="small text-muted mt-1 text-truncate" title="<?= htmlspecialchars($h['detalles'] ?? '') ?>">
                        <?= htmlspecialchars($h['detalles'] ?? '') ?>
                    </div>
                    <small class="text-secondary">Por <?= htmlspecialchars(trim(($h['nombres'] ?? '') . ' ' . ($h['ap_paterno'] ?? ''))) ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de vista previa -->
<div class="modal modal-blur fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista previa del reportaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body preview-html"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// El upload con progreso se inicializa en public/assets/js/admin.js.
document.addEventListener('DOMContentLoaded', function () {
    // Botones eliminar foto de galería
    document.querySelectorAll('[data-eliminar-foto]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.dataset.eliminarFoto;
            if (!window.confirm('¿Eliminar esta foto de la galería?')) return;
            const f = document.createElement('form');
            f.method = 'post';
            f.action = '<?= BASE_URL ?>/admin/reportajes/fotos/' + id + '/eliminar';
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = window.DDP_CSRF_NAME;
            hidden.value = csrfToken();
            f.appendChild(hidden);
            document.body.appendChild(f);
            f.submit();
        });
    });
});
</script>