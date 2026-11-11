<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Banner utama -->
<section class="hero py-5">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge rounded-pill text-bg-light text-primary mb-3"><i class="bi bi-stars me-1"></i>Toko online satu pintu</span>
                <h1 class="display-5 fw-bold mb-3">Temukan kebutuhan harianmu di <?= esc($shop->name) ?></h1>
                <p class="lead mb-4"><?= esc($shop->tagline) ?> Pilih produk, masukkan ke keranjang, bayar dengan transfer, lalu pantau pesananmu sampai tiba di rumah.</p>

                <form class="hero-search mb-4" role="search" action="<?= url_to('products') ?>" method="get">
                    <label class="visually-hidden" for="heroSearch">Cari produk</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                        <input class="form-control border-0" type="search" id="heroSearch" name="q" placeholder="Mau belanja apa hari ini?">
                        <button class="btn btn-warning fw-semibold" type="submit">Cari</button>
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-light btn-lg" href="<?= url_to('products') ?>">Lihat katalog <i class="bi bi-arrow-right"></i></a>
                    <a class="btn btn-outline-light btn-lg" href="<?= url_to('register') ?>">Buat akun gratis</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-grid">
                    <?php foreach (array_slice($featured, 0, 4) as $item) : ?>
                        <a class="hero-tile cat-<?= $item['category_id'] ?>" href="<?= url_to('product.show', $item['slug']) ?>">
                            <i class="bi <?= esc($item['icon'], 'attr') ?>"></i>
                            <span class="hero-tile-label">
                                <span class="d-block small"><?= esc($item['name']) ?></span>
                                <strong><?= rupiah($item['price']) ?></strong>
                            </span>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Keunggulan layanan -->
<section class="py-4 border-bottom bg-white">
    <div class="container">
        <div class="row g-3 text-center text-md-start">
            <?php foreach ([
                ['bi-grid-3x3-gap', 'Katalog terpusat', 'Harga dan stok selalu sesuai data toko.'],
                ['bi-cart-check', 'Checkout mudah', 'Keranjang dan alamat pengiriman dalam satu alur.'],
                ['bi-shield-check', 'Pembayaran terverifikasi', 'Bukti transfer diperiksa langsung oleh admin.'],
                ['bi-truck', 'Pesanan terlacak', 'Status dan nomor resi bisa dipantau kapan saja.'],
            ] as [$icon, $heading, $text]) : ?>
                <div class="col-6 col-md-3">
                    <div class="d-md-flex gap-3 align-items-start">
                        <span class="feature-icon mb-2 mb-md-0"><i class="bi <?= $icon ?>"></i></span>
                        <div>
                            <h2 class="h6 mb-1"><?= $heading ?></h2>
                            <p class="small text-body-secondary mb-0"><?= $text ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- Kategori -->
<section class="py-5" aria-labelledby="judulKategori">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 id="judulKategori" class="h3 fw-bold mb-1">Belanja per kategori</h2>
                <p class="text-body-secondary mb-0">Pilih kategori untuk langsung menyaring katalog.</p>
            </div>
            <a class="btn btn-link text-decoration-none d-none d-sm-inline" href="<?= url_to('products') ?>">Semua produk <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
            <?php foreach ($categories as $category) : ?>
                <div class="col">
                    <a class="category-card cat-<?= $category['id'] ?>" href="<?= url_to('category', $category['slug']) ?>">
                        <i class="bi <?= esc($category['icon'], 'attr') ?>"></i>
                        <span class="fw-semibold"><?= esc($category['name']) ?></span>
                        <span class="small opacity-75"><?= $category['product_count'] ?> produk</span>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- Produk unggulan -->
<section class="py-5 bg-body-tertiary" aria-labelledby="judulUnggulan">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 id="judulUnggulan" class="h3 fw-bold mb-1">Produk unggulan</h2>
                <p class="text-body-secondary mb-0">Pilihan favorit pelanggan minggu ini.</p>
            </div>
            <a class="btn btn-link text-decoration-none d-none d-sm-inline" href="<?= url_to('products') ?>">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
            <?php foreach ($featured as $product) : ?>
                <div class="col"><?= view('frontend/products/_card', ['product' => $product]) ?></div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- Cara belanja (PRD 5.1 Alur Customer) -->
<section class="py-5" aria-labelledby="judulCara">
    <div class="container">
        <h2 id="judulCara" class="h3 fw-bold text-center mb-1">Cara belanja di <?= esc($shop->name) ?></h2>
        <p class="text-body-secondary text-center mb-5">Empat langkah dari memilih produk sampai pesanan diterima.</p>
        <ol class="row g-4 list-unstyled steps mb-0">
            <?php foreach ([
                ['bi-search-heart', 'Pilih produk', 'Cari berdasarkan nama atau kategori, lalu cek harga dan stok di halaman detail.'],
                ['bi-cart-plus', 'Masukkan keranjang', 'Atur jumlah barang, lalu masuk atau daftar akun untuk checkout.'],
                ['bi-receipt', 'Checkout & bayar', 'Pilih alamat, konfirmasi pesanan, dan unggah bukti transfer.'],
                ['bi-box2-heart', 'Pantau pesanan', 'Ikuti status verifikasi, pemrosesan, hingga nomor resi pengiriman.'],
            ] as $i => [$icon, $heading, $text]) : ?>
                <li class="col-sm-6 col-lg-3">
                    <div class="step h-100">
                        <span class="step-number"><?= $i + 1 ?></span>
                        <i class="bi <?= $icon ?> fs-2 text-primary"></i>
                        <h3 class="h6 mt-3"><?= $heading ?></h3>
                        <p class="small text-body-secondary mb-0"><?= $text ?></p>
                    </div>
                </li>
            <?php endforeach ?>
        </ol>
    </div>
</section>

<!-- Produk terbaru -->
<section class="py-5 bg-body-tertiary" aria-labelledby="judulTerbaru">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 id="judulTerbaru" class="h3 fw-bold mb-1">Baru masuk</h2>
                <p class="text-body-secondary mb-0">Produk terbaru yang ditambahkan ke katalog.</p>
            </div>
            <a class="btn btn-link text-decoration-none d-none d-sm-inline" href="<?= url_to('products') ?>?sort=newest">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row row-cols-2 row-cols-lg-4 g-3 g-md-4">
            <?php foreach ($newest as $product) : ?>
                <div class="col"><?= view('frontend/products/_card', ['product' => $product]) ?></div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- Ajakan daftar -->
<section class="pt-5">
    <div class="container">
        <div class="cta rounded-4 p-4 p-md-5 d-md-flex align-items-center justify-content-between gap-4">
            <div class="mb-3 mb-md-0">
                <h2 class="h3 fw-bold mb-1">Siap mulai belanja?</h2>
                <p class="mb-0">Daftar dengan nama, email, dan password. Setelah itu kamu bisa checkout dan melacak semua pesananmu.</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <a class="btn btn-warning btn-lg fw-semibold" href="<?= url_to('register') ?>">Daftar sekarang</a>
                <a class="btn btn-outline-light btn-lg" href="<?= url_to('login') ?>">Masuk</a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
