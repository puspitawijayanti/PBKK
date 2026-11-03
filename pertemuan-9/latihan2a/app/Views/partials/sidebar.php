<?php
$role = session('user_role');
$menus = [
    'admin' => [
        ['heading' => null, 'items' => [
            ['url' => 'admin', 'match' => 'admin', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
        ]],
        ['heading' => 'Master Data', 'items' => [
            ['url' => 'admin/study-programs', 'match' => 'admin/study-programs*', 'icon' => 'fa-university', 'label' => 'Program Studi'],
            ['url' => 'admin/lecturers', 'match' => 'admin/lecturers*', 'icon' => 'fa-chalkboard-teacher', 'label' => 'Dosen'],
            ['url' => 'admin/students', 'match' => 'admin/students*', 'icon' => 'fa-user-graduate', 'label' => 'Mahasiswa'],
            ['url' => 'admin/courses', 'match' => 'admin/courses*', 'icon' => 'fa-book', 'label' => 'Mata Kuliah'],
        ]],
        ['heading' => 'Akademik', 'items' => [
            ['url' => 'admin/advisors', 'match' => 'admin/advisors*', 'icon' => 'fa-user-friends', 'label' => 'Dosen Wali'],
            ['url' => 'admin/users', 'match' => 'admin/users*', 'icon' => 'fa-key', 'label' => 'Akun & Password'],
        ]],
    ],
    'lecturer' => [
        ['heading' => null, 'items' => [
            ['url' => 'lecturer', 'match' => 'lecturer', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
        ]],
        ['heading' => 'Pengajaran', 'items' => [
            ['url' => 'lecturer/classes', 'match' => 'lecturer/classes*', 'icon' => 'fa-chalkboard', 'label' => 'Kelas & Nilai'],
            ['url' => 'lecturer/advisees', 'match' => 'lecturer/advisees*', 'icon' => 'fa-user-friends', 'label' => 'Mahasiswa Perwalian'],
        ]],
    ],
    'student' => [
        ['heading' => null, 'items' => [
            ['url' => 'student', 'match' => 'student', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
        ]],
        ['heading' => 'Perkuliahan', 'items' => [
            ['url' => 'student/enrollments', 'match' => 'student/enrollments*', 'icon' => 'fa-clipboard-list', 'label' => 'KRS'],
            ['url' => 'student/results', 'match' => 'student/results*', 'icon' => 'fa-poll', 'label' => 'KHS & IPK'],
        ]],
    ],
][$role] ?? [];
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion d-print-none" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url($role ?? 'login') ?>">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-graduation-cap"></i></div>
        <div class="sidebar-brand-text mx-3">SIAKAD</div>
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

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" type="button" aria-label="Ciutkan menu"></button>
    </div>
</ul>
