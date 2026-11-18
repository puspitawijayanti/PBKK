<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="text-center py-5">
    <h1 class="display-4 fw-bold text-primary">403</h1>
    <p class="lead">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <a href="<?= site_url('/') ?>" class="btn btn-primary">Kembali ke Beranda</a>
</div>
<?= $this->endSection() ?>
