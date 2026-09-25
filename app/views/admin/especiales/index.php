<div class="card">
    <div class="card-header">
        <h3 class="card-title">Especiales (<?= (int) $resultados['total'] ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/especiales/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nuevo especial</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="get" action="<?= BASE_URL ?>/admin/especiales" class="row g-2 align-items-end">
            <div class="col-sm-5">
                <label class="form-label">Buscar</label>
                <input type="search" name="busqueda" class="form-control" placeholder="Título, leet o palabra resaltada…" value="<?= htmlspecialchars($filtros['busqueda'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Estado</label>
                <select name="activo" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" <?= (string) ($filtros['activo'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                    <option value="0" <?= (string) ($filtros['activo'] ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
                </select>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Especial</th>
                    <th>Palabra resaltada</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados['items'])): ?>
                <tr><td colspan="5" class="text-center text-muted py-5">No hay especiales.</td></tr>
                <?php endif; ?>
                <?php foreach ($resultados['items'] as $especial): ?>
                <tr>
                    <td>
                        <a href="<?= BASE_URL ?>/admin/especiales/<?= (int) $especial['id'] ?>/editar" class="fw-semibold text-primary d-block text-truncate" style="max-width:320px"><?= htmlspecialchars($especial['titulo_completo'], ENT_QUOTES, 'UTF-8') ?></a>
                        <span class="text-secondary small font-monospace text-truncate d-block" style="max-width:320px"><?= htmlspecialchars($especial['titulo_leet'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td><span class="badge bg-pink-lt text-dark"><?= htmlspecialchars($especial['palabra_resaltada'] ?: '—', ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?php $activo = (int) $especial['activo'] === 1; ?><span class="badge bg-<?= $activo ? 'green' : 'secondary' ?>-lt text-<?= $activo ? 'green' : 'secondary' ?>"><?= $activo ? 'Activo' : 'Inactivo' ?></span></td>
                    <td class="text-secondary"><?= htmlspecialchars(date('d/m/Y', strtotime((string) $especial['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/especiales/<?= (int) $especial['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <form method="post" action="<?= BASE_URL ?>/admin/especiales/<?= (int) $especial['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar este especial?">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-icon btn-outline-danger btn-table-action" title="Eliminar"><i class="ti ti-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
    $baseUrl = BASE_URL . '/admin/especiales';
    require APP_PATH . '/views/admin/partials/paginator.php';
?>
