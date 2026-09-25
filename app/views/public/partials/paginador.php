<?php
/** Paginador visual de la referencia: Ant, números y Sig. */
$page = max(1, (int) ($resultados['page'] ?? 1));
$totalPages = max(0, (int) ($resultados['totalPages'] ?? 0));
$baseUrl = $baseUrl ?? (BASE_URL . '/reportajes');
$periodo = (string) ($_GET['archivo'] ?? '');
$urlFor = static function (int $number) use ($baseUrl, $periodo): string {
    if ($periodo !== '') {
        return $baseUrl . '?archivo=' . rawurlencode($periodo) . '&pagina=' . $number;
    }
    if (str_ends_with($baseUrl, '/reportajes') && $number > 1) {
        return $baseUrl . '/pagina/' . $number;
    }
    return $number > 1 ? $baseUrl . '?pagina=' . $number : $baseUrl;
};
if ($totalPages > 0):
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
?>
<nav class="pagination ddp-pagination" aria-label="Paginación">
    <ul>
        <?php if ($page > 1): ?>
            <li class="prev"><a href="<?= htmlspecialchars($urlFor($page - 1), ENT_QUOTES, 'UTF-8') ?>" rel="prev">Ant</a></li>
        <?php else: ?>
            <li class="prev disabled"><span>Ant</span></li>
        <?php endif; ?>
        <?php if ($start > 1): ?>
            <li><a href="<?= htmlspecialchars($urlFor(1), ENT_QUOTES, 'UTF-8') ?>">1</a></li>
            <?php if ($start > 2): ?><li class="disabled"><span>…</span></li><?php endif; ?>
        <?php endif; ?>
        <?php for ($number = $start; $number <= $end; $number++): ?>
            <li class="<?= $number === $page ? 'active' : '' ?>">
                <a href="<?= htmlspecialchars($urlFor($number), ENT_QUOTES, 'UTF-8') ?>" <?= $number === $page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
            </li>
        <?php endfor; ?>
        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><li class="disabled"><span>…</span></li><?php endif; ?>
            <li><a href="<?= htmlspecialchars($urlFor($totalPages), ENT_QUOTES, 'UTF-8') ?>"><?= $totalPages ?></a></li>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
            <li class="next"><a href="<?= htmlspecialchars($urlFor($page + 1), ENT_QUOTES, 'UTF-8') ?>" rel="next">Sig</a></li>
        <?php else: ?>
            <li class="next disabled"><span>Sig</span></li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
