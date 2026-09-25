<?php
$especialId = (int) ($especial['id'] ?? 0);
$tituloCompleto = (string) ($especial['titulo_completo'] ?? 'Especial');
$tituloLeet = (string) ($especial['titulo_leet'] ?? '');
$palabraResaltada = (string) ($especial['palabra_resaltada'] ?? '');
?>
<div class="ddp-carousel-item">
    <article class="ddp-especial-card">
        <button type="button" class="ddp-especial-card-media ddp-media-trigger" data-ddp-media-trigger data-tipo="especial" data-id="<?= $especialId ?>" data-title="<?= htmlspecialchars($tituloCompleto, ENT_QUOTES, 'UTF-8') ?>" aria-label="Reproducir <?= htmlspecialchars($tituloCompleto, ENT_QUOTES, 'UTF-8') ?>">
            <span class="ddp-especial-card-leet"><?= ddpResaltarLeet($tituloLeet, $palabraResaltada) ?></span>
            <span class="ddp-especial-play" aria-hidden="true"><span class="fa fa-play"></span></span>
        </button>
        <p class="ddp-especial-card-title"><?= htmlspecialchars($tituloCompleto, ENT_QUOTES, 'UTF-8') ?></p>
    </article>
</div>
