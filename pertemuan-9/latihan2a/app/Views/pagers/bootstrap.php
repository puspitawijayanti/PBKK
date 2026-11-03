<?php $pager->setSurroundCount(2) ?>
<nav aria-label="Navigasi halaman">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <?php if ($pager->hasPreviousPage()) : ?>
            <li class="page-item"><a class="page-link" href="<?= esc($pager->getFirst(), 'attr') ?>" aria-label="Halaman pertama">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="<?= esc($pager->getPreviousPage(), 'attr') ?>" aria-label="Halaman sebelumnya">&lsaquo;</a></li>
        <?php endif ?>
        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
                <a class="page-link" href="<?= esc($link['uri'], 'attr') ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>
        <?php if ($pager->hasNextPage()) : ?>
            <li class="page-item"><a class="page-link" href="<?= esc($pager->getNextPage(), 'attr') ?>" aria-label="Halaman berikutnya">&rsaquo;</a></li>
            <li class="page-item"><a class="page-link" href="<?= esc($pager->getLast(), 'attr') ?>" aria-label="Halaman terakhir">&raquo;</a></li>
        <?php endif ?>
    </ul>
</nav>
