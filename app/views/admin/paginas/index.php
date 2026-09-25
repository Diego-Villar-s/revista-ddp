<?php
$id = (int) ($pagina['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Páginas institucionales</h3>
        <a href="<?= BASE_URL ?>/admin/paginas/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nueva página</a>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Título</th><th>Slug</th><th>Estado</th><th class="w-1"></th></tr></thead>
            <tbody>
            <?php foreach ($paginas as $pagina): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($pagina['titulo']) ?></strong></td>
                    <td><code><?= htmlspecialchars($pagina['slug']) ?></code></td>
                    <td><span class="badge bg-<?= $pagina['activo'] ? 'green' : 'secondary' ?>"><?= $pagina['activo'] ? 'Activa' : 'Inactiva' ?></span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/admin/paginas/<?= (int) $pagina['id'] ?>/editar"><i class="ti ti-edit"></i></a>
                        <form method="post" class="d-inline" action="<?= BASE_URL ?>/admin/paginas/<?= (int) $pagina['id'] ?>/eliminar" data-confirm="¿Eliminar esta página?">
                            <?= $this->csrfField() ?>
                            <button class="btn btn-sm btn-outline-danger" type="submit"><i class="ti ti-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$paginas): ?><tr><td colspan="4" class="text-center text-secondary">No hay páginas registradas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
