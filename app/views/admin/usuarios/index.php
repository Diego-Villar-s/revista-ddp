<?php
use App\Core\Auth;
$miId = Auth::id();
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Usuarios (<?= count($usuarios) ?>)</h3>
        <div class="ms-auto">
            <a href="<?= BASE_URL ?>/admin/usuarios/nuevo" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nuevo usuario</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones (log)</th>
                    <th>Registro</th>
                    <th class="w-1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">No hay usuarios.</td></tr>
                <?php endif; ?>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar me-2 <?= $u['activo'] ? 'bg-primary-lt' : 'bg-secondary-lt' ?>">
                                <?= strtoupper(mb_substr($u['nombres'], 0, 1)) ?>
                            </span>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars(trim($u['nombres'] . ' ' . $u['ap_paterno'] . ' ' . $u['ap_materno'])) ?></div>
                                <div class="text-secondary small"><?= htmlspecialchars($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php $rolClase = $u['rol'] === 'admin' ? 'danger' : ($u['rol'] === 'editor' ? 'warning' : 'secondary'); ?>
                        <span class="badge bg-<?= $rolClase ?>-lt text-<?= $rolClase ?>"><?= htmlspecialchars($u['rol']) ?></span>
                    </td>
                    <td>
                        <?php if ((int) $u['id'] === 1): ?>
                        <span class="badge bg-success-lt text-success"><i class="ti ti-shield-check me-1"></i>Protegido</span>
                        <?php elseif ($u['activo']): ?>
                        <span class="badge bg-success-lt text-success">Activo</span>
                        <?php else: ?>
                        <span class="badge bg-danger-lt text-danger">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-secondary"><?= (int) $u['total_acciones'] ?> acciones</td>
                    <td class="text-secondary"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="<?= BASE_URL ?>/admin/usuarios/<?= (int) $u['id'] ?>/editar" class="btn btn-icon btn-outline-primary btn-table-action" title="Editar"><i class="ti ti-edit"></i></a>
                            <?php if ((int) $u['id'] !== 1 && (int) $u['id'] !== $miId): ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/usuarios/<?= (int) $u['id'] ?>/eliminar" class="d-inline" data-confirm="¿Eliminar este usuario?">
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