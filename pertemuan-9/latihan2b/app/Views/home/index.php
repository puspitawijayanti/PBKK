<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="hero-coffee py-5 mb-5">
    <div class="container py-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge text-bg-warning mb-3">Sangrai segar setiap minggu</span>
                <h1 class="display-5 fw-bold text-white">Kopi terbaik Nusantara, dari kebun langsung ke cangkir Anda.</h1>
                <p class="lead text-white-50 my-4">Gayo, Kintamani, Toraja, sampai Flores Bajawa. Pilih biji utuh atau bubuk, kami sangrai sesuai pesanan.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= site_url('products') ?>" class="btn btn-warning btn-lg px-4">Belanja Sekarang</a>
                    <a href="<?= site_url('products?sort=price_asc') ?>" class="btn btn-outline-light btn-lg">Mulai dari yang hemat</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 bg-white bg-opacity-10 text-white">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold"><i class="bi bi-truck"></i> Ongkir <?= rupiah($shop->shippingCost) ?> flat</h2>
                        <p class="mb-3 text-white-50">Gratis ongkir untuk belanja di atas <?= rupiah($shop->freeShippingThreshold) ?>.</p>
                        <h2 class="h5 fw-bold"><i class="bi bi-bank"></i> Transfer bank</h2>
                        <p class="mb-0 text-white-50">Unggah bukti transfer, pesanan diproses setelah verifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mb-5">
    <h2 class="h4 fw-bold mb-3">Kategori</h2>
    <div class="row g-3">
        <?php foreach ($categories as $category) : ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= site_url('products?category=' . $category['id']) ?>" class="card category-tile h-100 text-decoration-none text-center">
                    <div class="card-body">
                        <i class="bi bi-cup-straw fs-3 text-coffee"></i>
                        <div class="fw-semibold text-dark mt-2"><?= esc($category['name']) ?></div>
                        <div class="small text-muted"><?= (int) $category['product_count'] ?> produk</div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</section>

<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h4 fw-bold mb-0">Baru Disangrai</h2>
        <a href="<?= site_url('products') ?>" class="btn btn-sm btn-outline-coffee">Lihat semua</a>
    </div>
    <div class="row g-4">
        <?php foreach ($newProducts as $product) : ?>
            <div class="col-6 col-md-4 col-lg-3"><?= view('products/_card', ['product' => $product]) ?></div>
        <?php endforeach ?>
    </div>
</section>

<?php if ($bestSellers !== []) : ?>
    <section class="container mb-5">
        <h2 class="h4 fw-bold mb-3">Paling Laris</h2>
        <div class="row g-3">
            <?php foreach ($bestSellers as $index => $item) : ?>
                <div class="col-md-4">
                    <div class="card border-0 bg-cream h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <span class="rank-badge"><?= $index + 1 ?></span>
                            <div>
                                <div class="fw-semibold"><?= esc($item['product_name']) ?></div>
                                <div class="small text-muted"><?= (int) $item['sold'] ?> bungkus terjual</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </section>
<?php endif ?>
<?= $this->endSection() ?>
