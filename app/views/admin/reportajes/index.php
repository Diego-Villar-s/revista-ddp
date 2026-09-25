<!-- =============================================================
     Admin: Listado de reportajes con filtros
     ============================================================= -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reportajes (<?= (int) $resultados['total'] ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/reportajes/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nuevo reportaje</a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-body border-bottom py-3">
        <form method="get" action="<?= BASE_URL ?>/admin/reportajes" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label">Buscar</label>
                <input type="search" name="busqueda" class="form-control" placeholder="Título o slug…" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
            </div>
            <div class="col-sm-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="borrador" <?= (($filtros['estado'] ?? '') === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                    <option value="publicado" <?= (($filtros['estado'] ?? '') === 'publicado') ? 'selected' : '' ?>>Publicado</option>
                    <option value="archivado" <?= (($filtros['estado'] ?? '') === 'archivado') ? 'selected' : '' ?>>Archivado</option>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label">Autor</label>
                <select name="autor_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($autores as $aId => $aNombre): ?>
                    <option value="<?= (int) $aId ?>" <?= ((int) ($filtros['autor_id'] ?? 0) === (int) $aId) ? 'selected' : '' ?>><?= htmlspecialchars($aNombre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label">Fecha desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($filtros['desde'] ?? '') ?>">
            </div>
            <div class="col-sm-1">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($filtros['hasta'] ?? '') ?>">
            </div>
            <div class="col-sm-2">
                <label class="form-label">Destacado</label>
                <select name="destacado" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" <?= (($filtros['destacado'] ?? '') === '1') ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= (($filtros['destacado'] ?? '') === '0') ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Reportaje</th>
                    <th>Autor</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Destacado</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados['items'])): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">No se encontraron reportajes con los filtros indicados.</td></tr>
                <?php endif; ?>
                <?php foreach ($resultados['items'] as $r): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($r['foto_principal']): ?>
                            <img src="<?= htmlspecialchars(ddpImgUrl($r['foto_principal']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($r['alt_foto_principal'] ?: $r['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="table-thumb">
                            <?php endif; ?>
                            <div>
                                <a href="<?= BASE_URL ?>/reportajes/<?= htmlspecialchars($r['slug']) ?>" target="_blank" class="fw-semibold text-primary d-block text-truncate" style="max-width:280px"><?= htmlspecialchars($r['titulo']) ?></a>
                                <span class="text-secondary small">#<?= (int) $r['id'] ?> · /reportajes/<?= htmlspecialchars($r['slug']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars(trim(($r['autor_nombres'] ?? '') . ' ' . ($r['autor_ap'] ?? ''))) ?: '<em class="text-secondary">Sin autor</em>' ?></div>
                        <div class="text-secondary small">por <?= htmlspecialchars($r['usr_nombres'] ?? 'redacción') ?></div>
                    </td>
                    <td>
                        <?php $clase = $r['estado'] === 'publicado' ? 'success' : ($r['estado'] === 'borrador' ? 'warning' : 'secondary'); ?>
                        <span class="badge bg-<?= $clase ?>-lt text-<?= $clase ?>"><?= htmlspecialchars($r['estado']) ?></span>
                    </td>
                    <td class="text-secondary"><?= $r['fecha_publicacion'] ? date('d/m/Y', strtotime($r['fecha_publicacion'])) : '—' ?></td>
                    <td>
                        <span data-estado-destacado class="badge <?= $r['es_destacado'] ? 'bg-success' : 'bg-secondary' ?>"><?= $r['es_destacado'] ? 'Sí' : 'No' ?></span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/reportajes/<?= (int) $r['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <?php if (\App\Core\Auth::hasRole('admin', 'editor')): ?>
                            <button type="button" class="btn btn-icon btn-outline-warning btn-table-action" data-toggle-destacado="<?= (int) $r['id'] ?>" title="Toggle destacado"><i class="ti ti-star"></i></button>
                            <form method="post" action="<?= BASE_URL ?>/admin/reportajes/<?= (int) $r['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar este reportaje y sus fotos? Esta acción no se puede deshacer.">
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
    $baseUrl = BASE_URL . '/admin/reportajes';
    require APP_PATH . '/views/admin/partials/paginator.php';
?>