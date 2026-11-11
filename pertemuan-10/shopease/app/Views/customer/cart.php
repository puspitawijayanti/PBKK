<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="h3 fw-bold mb-4">Keranjang Belanja</h1>

    <?php if ($items === []) : ?>
        <div class="text-center py-5 border rounded-4 bg-white">
            <i class="bi bi-cart-x display-4 text-body-secondary"></i>
            <p class="fw-semibold mt-3 mb-1">Keranjang Anda masih kosong</p>
            <p class="text-body-secondary small">Yuk, cari produk yang Anda butuhkan.</p>
            <a class="btn btn-primary" href="<?= url_to('products') ?>">Mulai belanja</a>
        </div>
    <?php else : ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($items as $item) : ?>
                            <li class="list-group-item p-3">
                                <div class="d-flex gap-3">
                                    <a class="flex-shrink-0 cart-thumb" href="<?= url_to('product.show', $item['slug']) ?>"><?= product_thumb($item, 'rounded-3') ?></a>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between gap-2">
                                            <div>
                                                <a class="fw-semibold text-reset text-decoration-none" href="<?= url_to('product.show', $item['slug']) ?>"><?= esc($item['name']) ?></a>
                                                <div class="small text-body-secondary"><?= rupiah($item['price']) ?> / barang &middot; stok <?= $item['stock'] ?></div>
                                            </div>
                                            <strong class="text-nowrap"><?= rupiah($item['subtotal']) ?></strong>
                                        </div>

                                        <?php if (! $item['available']) : ?>
                                            <div class="alert alert-warning py-1 px-2 small mt-2 mb-0">Produk ini sudah tidak dijual. Hapus dari keranjang untuk melanjutkan.</div>
                                        <?php elseif (! $item['enoughStock']) : ?>
                                            <div class="alert alert-warning py-1 px-2 small mt-2 mb-0">Stok tersisa <?= $item['stock'] ?>. Kurangi jumlah untuk melanjutkan.</div>
                                        <?php endif ?>

                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                            <form class="d-flex gap-2" action="<?= url_to('cart.update', $item['id']) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <label class="visually-hidden" for="qty<?= $item['id'] ?>">Jumlah <?= esc($item['name']) ?></label>
                                                <input class="form-control form-control-sm qty-input" type="number" id="qty<?= $item['id'] ?>" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= max(1, $item['stock']) ?>" required>
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">Ubah</button>
                                            </form>
                                            <form action="<?= url_to('cart.remove', $item['id']) ?>" method="post" data-confirm="Hapus <?= esc($item['name'], 'attr') ?> dari keranjang?">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-sm btn-link text-danger text-decoration-none" type="submit"><i class="bi bi-trash me-1"></i>Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
                <a class="btn btn-link text-decoration-none mt-2 px-0" href="<?= url_to('products') ?>"><i class="bi bi-arrow-left me-1"></i>Lanjut belanja</a>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-lg-top summary-card">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Ringkasan belanja</h2>
                        <dl class="row small mb-0">
                            <dt class="col-7 fw-normal">Subtotal</dt><dd class="col-5 text-end"><?= rupiah($totals['subtotal']) ?></dd>
                            <dt class="col-7 fw-normal">Ongkos kirim (tetap)</dt><dd class="col-5 text-end"><?= rupiah($totals['shipping']) ?></dd>
                            <dt class="col-7 fs-6">Total</dt><dd class="col-5 text-end fs-6 fw-bold text-primary"><?= rupiah($totals['total']) ?></dd>
                        </dl>
                        <?php if ($totals['valid']) : ?>
                            <a class="btn btn-primary w-100 mt-3" href="<?= url_to('checkout') ?>">Lanjut ke checkout</a>
                        <?php else : ?>
                            <button class="btn btn-primary w-100 mt-3" type="button" disabled>Lanjut ke checkout</button>
                            <p class="small text-danger mt-2 mb-0">Perbaiki item yang ditandai terlebih dahulu.</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
