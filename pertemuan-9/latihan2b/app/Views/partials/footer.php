<?php $shop = config('Shop') ?>
<footer class="footer-coffee text-white-50 pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h5 class="text-white"><i class="bi bi-cup-hot-fill"></i> <?= esc($shop->storeName) ?></h5>
                <p class="small mb-0"><?= esc($shop->tagline) ?>. Disangrai dalam jumlah kecil setiap minggu agar tetap segar sampai di cangkir Anda.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Belanja</h6>
                <ul class="list-unstyled small">
                    <li><a class="link-light link-underline-opacity-0" href="<?= site_url('products') ?>">Semua Produk</a></li>
                    <li><a class="link-light link-underline-opacity-0" href="<?= site_url('products?sort=price_asc') ?>">Harga Terendah</a></li>
                    <li><a class="link-light link-underline-opacity-0" href="<?= site_url('cart') ?>">Keranjang</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Hubungi Kami</h6>
                <p class="small mb-1"><i class="bi bi-geo-alt"></i> <?= esc($shop->address) ?></p>
                <p class="small mb-1"><i class="bi bi-whatsapp"></i> <?= esc($shop->phone) ?></p>
                <p class="small mb-0"><i class="bi bi-envelope"></i> <?= esc($shop->email) ?></p>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="small text-center mb-0">&copy; <?= date('Y') ?> <?= esc($shop->storeName) ?> &middot; Ongkir <?= rupiah($shop->shippingCost) ?>, gratis untuk belanja di atas <?= rupiah($shop->freeShippingThreshold) ?>.</p>
    </div>
</footer>
