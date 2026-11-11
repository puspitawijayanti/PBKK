<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5 text-center">
    <i class="bi bi-shield-lock display-1 text-danger"></i>
    <h1 class="h3 fw-bold mt-3">403 &middot; Akses ditolak</h1>
    <p class="text-body-secondary mb-4">
        Halaman ini hanya untuk <?= esc(implode(' / ', array_map('role_label', $allowedRoles ?? []))) ?>.
        <?php if (current_user() !== null) : ?>Anda masuk sebagai <?= role_label(current_user()['role']) ?>.<?php endif ?>
    </p>
    <a class="btn btn-primary" href="<?= url_to('home') ?>">Kembali ke beranda</a>
</div>
<?= $this->endSection() ?>
