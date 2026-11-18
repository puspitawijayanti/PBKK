<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="se-hero p-4 p-md-5 mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <span class="badge bg-light text-dark mb-2">Promo Pembukaan</span>
            <h1 class="display-6 fw-bold">Belanja online mudah dan aman di ShopEase</h1>
            <p class="lead mb-4">Temukan elektronik, fashion, aksesoris, dan banyak lagi. Pesan sekarang, bayar dengan transfer, pantau pesanan Anda hingga sampai.</p>
            <a href="<?= site_url('catalog') ?>" class="btn btn-light btn-lg">Mulai Belanja</a>
        </div>
    </div>
</section>

<?php if ($categories): ?>
<section class="mb-4">
    <h2 class="h5 se-section-title mb-3">Kategori</h2>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($categories as $c): ?>
            <a class="chip" href="<?= site_url('catalog?category=' . urlencode($c['slug'])) ?>"><?= esc($c['name']) ?> <span class="text-muted">(<?= (int) $c['product_count'] ?>)</span></a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h5 se-section-title mb-0">Produk Terbaru</h2>
        <a href="<?= site_url('catalog') ?>" class="text-decoration-none">Lihat semua &rarr;</a>
    </div>
    <?php if ($latest): ?>
        <div class="row g-3">
            <?php foreach ($latest as $p): ?>
                <div class="col-6 col-md-4 col-lg-3"><?= view('partials/product_card', ['p' => $p]) ?></div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Belum ada produk.</p>
    <?php endif; ?>
</section>

<?php if ($bestSeller): ?>
<section class="mb-4">
    <h2 class="h5 se-section-title mb-3">Produk Terlaris</h2>
    <div class="row g-3">
        <?php foreach ($bestSeller as $p): ?>
            <div class="col-6 col-md-4 col-lg-3"><?= view('partials/product_card', ['p' => $p]) ?></div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
