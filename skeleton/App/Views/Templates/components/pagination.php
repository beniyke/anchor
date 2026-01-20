<?php
$paginator = $pagination['paginator'];
$route = $pagination['route'] ?? null;
$query = $pagination['query'] ?? 'page';
$with = $pagination['with'] ?? [];
?>
<?php if ($paginator->hasPages()): ?>
    <nav aria-label="Page navigation for results" class="d-flex justify-content-center my-4">
        <ul class="pagination shadow-sm">

            <!-- Previous Page Link -->
            <li class="page-item <?= $paginator->hasPreviousPage() ? '' : 'disabled' ?>">
                <a class="page-link" href="<?= $paginator->hasPreviousPage() ? url(route($route), ([$query => $paginator->previousPage()] + $with)) : '#' ?>" aria-disabled="<?= $paginator->hasPreviousPage() ? 'false' : 'true' ?>" tabindex="<?= $paginator->hasPreviousPage() ? '0' : '-1' ?>">
                    &laquo; Previous
                </a>
            </li>

            <?php foreach ($paginator->getPagesInRange(2) as $page): ?>
                <?php if ($page === '...'): ?>
                    <li class="page-item disabled" aria-hidden="true">
                        <span class="page-link">...</span>
                    </li>
                <?php else: ?>
                    <li class="page-item <?= (int) $page === $paginator->currentPage ? 'active' : '' ?>">
                        <a class="page-link"
                            href="<?= url(route($route), ([$query => (int) $page] + $with)) ?>"
                            <?= (int) $page === $paginator->currentPage ? 'aria-current="page"' : '' ?>>
                            <?= $page ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Next Page Link -->
            <li class="page-item <?= $paginator->hasNextPage() ? '' : 'disabled' ?>">
                <a class="page-link"
                    href="<?= $paginator->hasNextPage() ? url(route($route), ([$query => $paginator->nextPage()] + $with)) : '#' ?>"
                    aria-disabled="<?= $paginator->hasNextPage() ? 'false' : 'true' ?>"
                    tabindex="<?= $paginator->hasNextPage() ? '0' : '-1' ?>">
                    Next &raquo;
                </a>
            </li>
        </ul>
    </nav>

    <!-- Summary Text -->
    <div class="text-center text-muted small mb-4">
        Showing <strong><?= $paginator->getFrom() ?></strong> to <strong><?= $paginator->getTo() ?></strong> of <strong><?= $paginator->total ?></strong> results
    </div>

<?php endif; ?>