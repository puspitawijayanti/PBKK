<?php
$menus = [
    ['heading' => null, 'items' => [
        ['url' => 'admin', 'match' => 'admin', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
    ]],
    ['heading' => 'Katalog', 'items' => [
        ['url' => 'admin/products', 'match' => 'admin/products*', 'icon' => 'fa-mug-hot', 'label' => 'Produk'],
        ['url' => 'admin/categories', 'match' => 'admin/categories*', 'icon' => 'fa-tags', 'label' => 'Kategori'],
    ]],
    ['heading' => 'Penjualan', 'items' => [
        ['url' => 'admin/orders', 'match' => 'admin/orders*', 'icon' => 'fa-receipt', 'label' => 'Pesanan'],
    ]],
];
?>
<ul class="navbar-nav bg-gradient-coffee sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('admin') ?>">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-coffee"></i></div>
        <div class="sidebar-brand-text mx-3">Kopi Nusantara</div>
    </a>

    <?php foreach ($menus as $group) : ?>
        <hr class="sidebar-divider<?= $group['heading'] === null ? ' my-0' : '' ?>">
        <?php if ($group['heading'] !== null) : ?>
            <div class="sidebar-heading"><?= esc($group['heading']) ?></div>
        <?php endif ?>
        <?php foreach ($group['items'] as $item) : ?>
            <li class="nav-item<?= url_is($item['match']) ? ' active' : '' ?>">
                <a class="nav-link" href="<?= site_url($item['url']) ?>">
                    <i class="fas fa-fw <?= $item['icon'] ?>"></i>
                    <span><?= esc($item['label']) ?></span>
                </a>
            </li>
        <?php endforeach ?>
    <?php endforeach ?>

    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('/') ?>"><i class="fas fa-fw fa-store"></i><span>Lihat Toko</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" type="button" aria-label="Ciutkan menu"></button>
    </div>
</ul>
