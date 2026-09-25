<?php
/**
 * Componente único de reproducción para podcasts y videos del sitio público.
 * El reproductor se inyecta desde /reproductor y se elimina al cerrar.
 */
?>
<div class="ddp-repro-modal" id="ddp-repro-modal" data-ddp-repro-modal data-endpoint="<?= BASE_URL ?>/reproductor" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ddp-repro-title">
    <div class="ddp-repro-overlay" data-ddp-repro-close></div>
    <div class="ddp-repro-window" role="document">
        <button type="button" class="ddp-repro-close" data-ddp-repro-close aria-label="Cerrar reproductor">&times;</button>
        <h2 class="ddp-repro-title" id="ddp-repro-title">Reproduciendo</h2>
        <div class="ddp-repro-body" data-ddp-repro-body aria-live="polite"></div>
    </div>
</div>
