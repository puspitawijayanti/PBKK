<?php
$role = session('user_role');
$menus = $role === 'admin'
    ? [
        ['heading' => null, 'items' => [
            ['url' => 'admin', 'match' => 'admin', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
        ]],
        ['heading' => 'Inventaris', 'items' => [
            ['url' => 'admin/tools', 'match' => 'admin/tools*', 'icon' => 'fa-toolbox', 'label' => 'Data Alat'],
            ['url' => 'admin/categories', 'match' => 'admin/categories*', 'icon' => 'fa-tags', 'label' => 'Kategori'],
        ]],
        ['heading' => 'Layanan', 'items' => [
            ['url' => 'admin/loans', 'match' => 'admin/loans*', 'icon' => 'fa-clipboard-list', 'label' => 'Peminjaman'],
            ['url' => 'admin/users', 'match' => 'admin/users*', 'icon' => 'fa-users', 'label' => 'Pengguna'],
            ['url' => 'admin/messages', 'match' => 'admin/messages*', 'icon' => 'fa-envelope', 'label' => 'Pesan Masuk'],
        ]],
    ]
    : [
        ['heading' => null, 'items' => [
            ['url' => 'borrower', 'match' => 'borrower', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
        ]],
        ['heading' => 'Peminjaman', 'items' => [
            ['url' => 'tools', 'match' => 'tools*', 'icon' => 'fa-search', 'label' => 'Katalog Alat'],
            ['url' => 'borrower/loans', 'match' => 'borrower/loans*', 'icon' => 'fa-history', 'label' => 'Riwayat Saya'],
        ]],
    ];
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url($role ?? '/') ?>">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-flask"></i></div>
        <div class="sidebar-brand-text mx-3">LabLend</div>
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
        <a class="nav-link" href="<?= site_url('/') ?>">
            <i class="fas fa-fw fa-globe"></i>
            <span>Lihat Situs Publik</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" type="button" aria-label="Ciutkan menu"></button>
    </div>
</ul>
