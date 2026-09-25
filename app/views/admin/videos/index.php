<?php
$esAdminOrEditor = \App\Core\Auth::hasRole('admin', 'editor');
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Videos (<?= (int) $resultados['total'] ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/videos/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nuevo video</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="get" action="<?= BASE_URL ?>/admin/videos" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label">Buscar</label>
                <input type="search" name="busqueda" class="form-control" placeholder="Título…" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
            </div>
            <div class="col-sm-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="borrador" <?= (($filtros['estado'] ?? '') === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                    <option value="publicado" <?= (($filtros['estado'] ?? '') === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                </select>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Video</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados['items'])): ?>
                <tr><td colspan="5" class="text-center text-muted py-5">No hay videos.</td></tr>
                <?php endif; ?>
                <?php foreach ($resultados['items'] as $v): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($v['poster']): ?>
                            <img src="<?= htmlspecialchars(ddpImgUrl($v['poster']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($v['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="table-thumb">
                            <?php endif; ?>
                            <div>
                                <a href="<?= BASE_URL ?>/admin/videos/<?= (int) $v['id'] ?>/editar" class="fw-semibold text-primary d-block text-truncate" style="max-width:280px"><?= htmlspecialchars($v['titulo']) ?></a>
                                <span class="text-secondary small">/videos/<?= htmlspecialchars($v['slug']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($v['tipo'] === 'embed'): ?>
                        <span class="badge bg-azure-lt"><i class="ti ti-external-link me-1"></i>Embed</span>
                        <?php else: ?>
                        <span class="badge bg-purple-lt"><i class="ti ti-video me-1"></i>Archivo</span>
                        <?php endif; ?>
                        <?php if ((int) $v['duracion_segundos']): ?>
                        <div class="text-secondary small mt-1"><?= gmdate('i:s', (int) $v['duracion_segundos']) ?> min</div>
                        <?php endif; ?>
                    </td>
                    <td><?php $clase = $v['estado'] === 'publicado' ? 'success' : 'warning'; ?><span class="badge bg-<?= $clase ?>-lt text-<?= $clase ?>"><?= htmlspecialchars($v['estado']) ?></span></td>
                    <td class="text-secondary"><?= $v['fecha_publicacion'] ? date('d/m/Y', strtotime($v['fecha_publicacion'])) : '—' ?></td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/videos/<?= (int) $v['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <?php if ($esAdminOrEditor): ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/videos/<?= (int) $v['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar este video?">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">
                                <button type="submit" class="btn btn-icon btn-outline-danger btn-table-action" title="Eliminar"><i class="ti ti-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
    $baseUrl = BASE_URL . '/admin/videos';
    require APP_PATH . '/views/admin/partials/paginator.php';
?>