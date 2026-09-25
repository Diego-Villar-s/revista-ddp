<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible" role="alert"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" action="<?= BASE_URL ?>/admin/configuracion" class="card">
    <?= $this->csrfField() ?>
    <div class="card-header"><h3 class="card-title">Datos visibles del sitio</h3></div>
    <div class="card-body"><div class="row g-3">
        <div class="col-12"><label class="form-label">Descripción institucional</label><textarea class="form-control" name="institutional_text" rows="4"><?= htmlspecialchars($config['institutional_text'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Descripción SEO</label><textarea class="form-control" name="site_description" rows="3"><?= htmlspecialchars($config['site_description'] ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Texto del botón quiénes somos</label><input class="form-control" name="about_cta" value="<?= htmlspecialchars($config['about_cta'] ?? 'Nosotros') ?>"></div>
        <div class="col-md-6"><label class="form-label">Correo de contacto</label><input class="form-control" type="email" name="contact_email" value="<?= htmlspecialchars($config['contact_email'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Título del video institucional</label><input class="form-control" name="youtube_title" value="<?= htmlspecialchars($config['youtube_title'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">Embed de YouTube</label><input class="form-control" type="url" name="youtube_embed" value="<?= htmlspecialchars($config['youtube_embed'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Facebook</label><input class="form-control" type="url" name="social_facebook" value="<?= htmlspecialchars($config['social_facebook'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">TikTok</label><input class="form-control" type="url" name="social_tiktok" value="<?= htmlspecialchars($config['social_tiktok'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Instagram</label><input class="form-control" type="url" name="social_instagram" value="<?= htmlspecialchars($config['social_instagram'] ?? '') ?>"></div>
    </div></div>
    <div class="card-footer text-end"><button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Guardar configuración</button></div>
</form>
