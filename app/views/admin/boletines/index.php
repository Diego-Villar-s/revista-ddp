<?php
$esAdminOrEditor = \App\Core\Auth::hasRole('admin', 'editor');
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Boletines NTEP (<?= (int) $resultados['total'] ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/boletines/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nuevo boletín</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Boletín</th>
                    <th>Fecha</th>
                    <th>Portada</th>
                    <th>PDF</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados['items'])): ?>
                <tr><td colspan="5" class="text-center text-muted py-5">No hay boletines.</td></tr>
                <?php endif; ?>
                <?php foreach ($resultados['items'] as $b): ?>
                <tr>
                    <td>
                        <a href="<?= BASE_URL ?>/admin/boletines/<?= (int) $b['id'] ?>/editar" class="fw-semibold text-primary"><?= htmlspecialchars($b['numero_boletin']) ?></a>
                        <?php if (!empty($b['resumen'])): ?>
                        <div class="text-secondary small text-truncate" style="max-width:320px"><?= htmlspecialchars(mb_substr($b['resumen'], 0, 60)) ?>…</div>
                        <?php endif; ?>
                    </td>
                    <td class="text-secondary"><?= $b['fecha_publicacion'] ? date('d/m/Y', strtotime($b['fecha_publicacion'])) : '—' ?></td>
                    <td>
                        <?php if ($b['foto_portada']): ?><img src="<?= htmlspecialchars(ddpImgUrl($b['foto_portada']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($b['numero_boletin'], ENT_QUOTES, 'UTF-8') ?>" class="table-thumb"><?php else: ?><span class="text-secondary">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($b['archivo_pdf']): ?>
                        <a href="<?= htmlspecialchars(ddpImgUrl($b['archivo_pdf']), ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-danger"><i class="ti ti-file-type-pdf me-1"></i>Ver PDF</a>
                        <?php else: ?><span class="text-secondary">—</span><?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/boletines/<?= (int) $b['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <?php if ($esAdminOrEditor): ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/boletines/<?= (int) $b['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar este boletín?">
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
    $baseUrl = BASE_URL . '/admin/boletines';
    require APP_PATH . '/views/admin/partials/paginator.php';
?>