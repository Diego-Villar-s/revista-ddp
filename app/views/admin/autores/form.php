<?php
$id = (int) ($autor['id'] ?? 0);
?>
<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>

<form method="post"
      action="<?= $id ? BASE_URL . '/admin/autores/' . $id . '/editar' : BASE_URL . '/admin/autores/nuevo' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION[CSRF_TOKEN_NAME] ?? '') ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Autor</h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Nombres</label>
                            <input type="text" name="nombres" class="form-control" maxlength="120"
                                   value="<?= htmlspecialchars($autor['nombres']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ap. paterno</label>
                            <input type="text" name="ap_paterno" class="form-control" maxlength="60"
                                   value="<?= htmlspecialchars($autor['ap_paterno']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ap. materno</label>
                            <input type="text" name="ap_materno" class="form-control" maxlength="60"
                                   value="<?= htmlspecialchars($autor['ap_materno']) ?>">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="es_nickname" id="f_es_nickname" value="1" <?= $autor['es_nickname'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="f_es_nickname">Usar nickname como nombre visible</label>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Nickname</label>
                        <input type="text" name="nickname" class="form-control" maxlength="100"
                               value="<?= htmlspecialchars($autor['nickname']) ?>" placeholder="Ej: Redacción DDP">
                        <div class="form-hint">Si está activado el switch, el nombre mostrado será el nickname.</div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/admin/autores" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Guardar autor</button>
                </div>
            </div>
        </div>
    </div>
</form>