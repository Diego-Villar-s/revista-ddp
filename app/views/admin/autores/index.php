<div class="card">
    <div class="card-header">
        <h3 class="card-title">Autores (<?= count($autores) ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/autores/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nuevo autor</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Nickname</th>
                    <th>Reportajes publicados</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($autores)): ?>
                <tr><td colspan="4" class="text-center text-muted py-5">No hay autores.</td></tr>
                <?php endif; ?>
                <?php foreach ($autores as $a): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars(trim($a['nombres'] . ' ' . $a['ap_paterno'] . ' ' . $a['ap_materno'])) ?></td>
                    <td>
                        <?php if ($a['es_nickname'] && $a['nickname']): ?>
                        <span class="badge bg-secondary-lt"><?= htmlspecialchars($a['nickname']) ?></span>
                        <?php else: ?><span class="text-secondary">—</span><?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-primary-lt"><?= (int) $a['total_reportajes'] ?></span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/autores/<?= (int) $a['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <form method="post" action="<?= BASE_URL ?>/admin/autores/<?= (int) $a['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar este autor? Los reportajes asociados quedarán sin autor (Redacción).">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">
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