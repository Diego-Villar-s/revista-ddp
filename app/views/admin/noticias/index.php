<?php
$esAdminOrEditor = \App\Core\Auth::hasRole('admin', 'editor');
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Noticias (<?= (int) $resultados['total'] ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/noticias/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nueva noticia</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="get" action="<?= BASE_URL ?>/admin/noticias" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label">Buscar</label>
                <input type="search" name="busqueda" class="form-control" placeholder="Título…" value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Noticia</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados['items'])): ?>
                <tr><td colspan="4" class="text-center text-muted py-5">No hay noticias.</td></tr>
                <?php endif; ?>
                <?php foreach ($resultados['items'] as $n): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($n['foto']): ?>
                            <img src="<?= htmlspecialchars(ddpImgUrl($n['foto']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($n['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="table-thumb">
                            <?php endif; ?>
                            <div>
                                <a href="<?= BASE_URL ?>/noticias" class="fw-semibold d-block text-primary"><?= htmlspecialchars($n['titulo']) ?></a>
                                <span class="text-secondary small">/noticias/<?= htmlspecialchars($n['slug']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-secondary"><?= $n['fecha_publicacion'] ? date('d/m/Y', strtotime($n['fecha_publicacion'])) : '—' ?></td>
                    <td>
                        <?php if ($n['link_externo']): ?>
                        <span class="badge bg-azure-lt" title="<?= htmlspecialchars($n['link_externo']) ?>"><i class="ti ti-external-link me-1"></i>Link externo</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-lt">Portada</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/noticias/<?= (int) $n['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <?php if ($esAdminOrEditor): ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/noticias/<?= (int) $n['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar esta noticia?">
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
    $baseUrl = BASE_URL . '/admin/noticias';
    require APP_PATH . '/views/admin/partials/paginator.php';
?>