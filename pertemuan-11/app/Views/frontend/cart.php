<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Keranjang Belanja</h1>

<?php if (! $items): ?>
    <div class="se-card p-5 text-center">
        <p class="text-muted mb-3">Keranjang Anda masih kosong.</p>
        <a href="<?= site_url('catalog') ?>" class="btn btn-primary">Jelajahi Katalog</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="se-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Produk</th><th class="text-end">Harga</th><th style="width:170px">Jumlah</th><th class="text-end">Subtotal</th><th></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img class="cart-thumb" src="<?= product_image_url($it['image']) ?>" alt="">
                                        <div>
                                            <a class="fw-semibold text-decoration-none" href="<?= site_url('product/' . $it['slug']) ?>"><?= esc($it['name']) ?></a>
                                            <?php if ($it['status'] !== 'active'): ?>
                                                <div class="small text-danger">Produk tidak tersedia</div>
                                            <?php elseif ((int) $it['quantity'] > (int) $it['stock']): ?>
                                                <div class="small text-danger">Stok tersisa <?= (int) $it['stock'] ?></div>
                                            <?php else: ?>
                                                <div class="small text-muted">Stok <?= (int) $it['stock'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end"><?= rupiah($it['price']) ?></td>
                                <td>
                                    <form action="<?= site_url('cart/update/' . $it['id']) ?>" method="post" class="d-flex gap-1">
                                        <?= csrf_field() ?>
                                        <input type="number" name="quantity" value="<?= (int) $it['quantity'] ?>" min="1" max="<?= max(1, (int) $it['stock']) ?>" class="form-control form-control-sm" aria-label="Jumlah <?= esc($it['name']) ?>">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Ubah</button>
                                    </form>
                                </td>
                                <td class="text-end fw-semibold"><?= rupiah($it['line_total']) ?></td>
                                <td class="text-end">
                                    <form action="<?= site_url('cart/remove/' . $it['id']) ?>" method="post" onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Hapus">&times;</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="se-card p-4">
                <h2 class="h6 fw-bold">Ringkasan</h2>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span><?= rupiah($subtotal) ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Ongkos kirim</span><span><?= rupiah(config('Shop')->shippingCost) ?></span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold mb-3"><span>Total</span><span><?= rupiah($subtotal + config('Shop')->shippingCost) ?></span></div>
                <a href="<?= site_url('checkout') ?>" class="btn btn-primary w-100">Lanjut ke Checkout</a>
                <a href="<?= site_url('catalog') ?>" class="btn btn-link w-100 mt-1">Lanjut belanja</a>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
