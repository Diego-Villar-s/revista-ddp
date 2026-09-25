<?php
$id = (int) ($usuario['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post"
      action="<?= $id ? BASE_URL . '/admin/usuarios/' . $id . '/editar' : BASE_URL . '/admin/usuarios/nuevo' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><?= $id ? 'Usuario del panel' : 'Nuevo usuario del panel' ?></h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Nombres</label>
                            <input type="text" name="nombres" class="form-control" maxlength="100"
                                   value="<?= htmlspecialchars($usuario['nombres']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">Ap. paterno</label>
                            <input type="text" name="ap_paterno" class="form-control" maxlength="60"
                                   value="<?= htmlspecialchars($usuario['ap_paterno']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ap. materno</label>
                            <input type="text" name="ap_materno" class="form-control" maxlength="60"
                                   value="<?= htmlspecialchars($usuario['ap_materno']) ?>">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label required">Correo electrónico</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="ti ti-mail"></i></span>
                            <input type="email" name="email" class="form-control" maxlength="150"
                                   value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Rol</label>
                            <select name="rol" class="form-select">
                                <option value="admin" <?= ($usuario['rol'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                                <option value="editor" <?= ($usuario['rol'] === 'editor') ? 'selected' : '' ?>>Editor</option>
                                <option value="redactor" <?= ($usuario['rol'] === 'redactor') ? 'selected' : '' ?>>Redactor</option>
                            </select>
                            <div class="form-hint">Admin: todo · Editor: publica contenido · Redactor: solo sus borradores.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="activo" class="form-select">
                                <option value="1" <?= $usuario['activo'] ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= !$usuario['activo'] ? 'selected' : '' ?>>Inactivo (bloqueado)</option>
                            </select>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php if (!$id): ?>
                    <div class="mb-3">
                        <label class="form-label required">Contraseña</label>
                        <input type="password" name="password" class="form-control" minlength="8" required placeholder="Mínimo 8 caracteres">
                        <div class="form-hint">Se guardará hasheada con <code>password_hash()</code>.</div>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña (opcional)</label>
                        <input type="password" name="password" class="form-control" minlength="8" placeholder="Dejar vacío para no cambiarla">
                        <div class="form-hint">Si se proporciona, se guardará hasheada con <code>password_hash()</code>.</div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar usuario</button>
                </div>
            </div>
        </div>
    </div>
</form>