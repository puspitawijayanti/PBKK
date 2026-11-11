<footer class="site-footer mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="d-flex align-items-center gap-2 fw-bold fs-5 text-white text-decoration-none mb-2" href="<?= url_to('home') ?>">
                    <span class="brand-mark"><i class="bi bi-bag-heart-fill"></i></span><?= esc($shop->name) ?>
                </a>
                <p class="mb-0"><?= esc($shop->tagline) ?> Katalog terpusat, keranjang belanja, pembayaran manual, dan pelacakan pesanan dalam satu aplikasi.</p>
            </div>
            <div class="col-6 col-lg-2">
                <h2 class="h6 text-white">Belanja</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= url_to('products') ?>">Semua produk</a></li>
                    <li><a href="<?= url_to('products') ?>?sort=newest">Produk terbaru</a></li>
                    <li><a href="<?= url_to('products') ?>?sort=price_asc">Harga terendah</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h2 class="h6 text-white">Akun</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?= url_to('login') ?>">Masuk</a></li>
                    <li><a href="<?= url_to('register') ?>">Daftar</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h2 class="h6 text-white">Hubungi kami</h2>
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-geo-alt me-2"></i><?= esc($shop->address) ?></li>
                    <li><i class="bi bi-envelope me-2"></i><?= esc($shop->email) ?></li>
                    <li><i class="bi bi-telephone me-2"></i><?= esc($shop->phone) ?></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="small mb-0">&copy; <?= date('Y') ?> <?= esc($shop->name) ?>. Dibuat dengan CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?> untuk mata kuliah PBKK.</p>
    </div>
</footer>
