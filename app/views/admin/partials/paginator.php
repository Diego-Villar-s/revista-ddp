<?php
/**
 * Partial: Paginador del panel admin
 * Recibe: $resultados y $baseUrl (ruta actual)
 */
?>
<?php if (($resultados['totalPages'] ?? 0) > 1): ?>
<nav class="mt-3">
    <ul class="pagination">
        <?php
            // Conservar los filtros actuales al paginar
            $filtrosActuales = $_GET;
            unset($filtrosActuales['page']);
            $filtrosQuery = http_build_query($filtrosActuales);
            $qs = $filtrosQuery !== '' ? '&' . $filtrosQuery : '';
        ?>
        <li class="page-item <?= $resultados['page'] <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $resultados['page'] - 1 ?><?= $qs ?>" tabindex="-1" aria-disabled="true"><i class="ti ti-chevron-left"></i></a>
        </li>
        <?php for ($i = 1; $i <= $resultados['totalPages']; $i++): ?>
        <li class="page-item <?= $i === $resultados['page'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $i ?><?= $qs ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= $resultados['page'] >= $resultados['totalPages'] ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $resultados['page'] + 1 ?><?= $qs ?>"><i class="ti ti-chevron-right"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>