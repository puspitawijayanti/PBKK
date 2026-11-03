<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="fw-bold mb-4">Keranjang Belanja</h1>

    <?php if ($items === []) : ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-bag display-4"></i>
            <p class="mt-3">Keranjang Anda masih kosong.</p>
            <a href="<?= site_url('products') ?>" class="btn btn-coffee">Mulai belanja</a>
        </div>
    <?php else : ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <form action="<?= site_url('cart/update') ?>" method="post" id="cartForm">
                    <?= csrf_field() ?>
                </form>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light"><tr><th>Produk</th><th>Harga</th><th style="width: 7rem">Jumlah</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($items as $item) : ?>
                                    <?php $product = $item['product'] ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= product_photo_url($product['photo']) ?>" alt="" class="cart-thumb rounded">
                                                <div>
                                                    <a href="<?= site_url('products/' . $product['slug']) ?>" class="fw-semibold text-reset text-decoration-none"><?= esc($product['name']) ?></a>
                                                    <div class="small text-muted"><?= (int) $product['weight_gram'] ?> g &middot; stok <?= (int) $product['stock'] ?></div>
                                                    <?php if (! $item['enoughStock']) : ?>
                                                        <div class="small text-danger">Jumlah melebihi stok tersedia.</div>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small"><?= rupiah($item['price']) ?></td>
                                        <td>
                                            <label for="qty<?= $product['id'] ?>" class="visually-hidden">Jumlah <?= esc($product['name']) ?></label>
                                            <input type="number" form="cartForm" class="form-control form-control-sm" id="qty<?= $product['id'] ?>" name="quantities[<?= $product['id'] ?>]" min="0" max="<?= min((int) $product['stock'], $shop->maxQuantityPerItem) ?>" value="<?= $item['quantity'] ?>">
                                        </td>
                                        <td class="text-end fw-semibold"><?= rupiah($item['subtotal']) ?></td>
                                        <td class="text-end">
                                            <form action="<?= site_url('cart/' . $product['id'] . '/remove') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-link text-danger" aria-label="Hapus <?= esc($product['name'], 'attr') ?>"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body d-flex justify-content-between">
                        <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary btn-sm">&larr; Lanjut belanja</a>
                        <button type="submit" form="cartForm" class="btn btn-outline-coffee btn-sm"><i class="bi bi-arrow-repeat"></i> Perbarui jumlah</button>
                    </div>
                </div>
                <p class="small text-muted mt-2">Harga dan stok selalu dibaca ulang dari database setiap kali keranjang dibuka.</p>
            </div>
            <div class="col-lg-4">
                <?= view('cart/_summary', ['totals' => $totals, 'shop' => $shop]) ?>
                <a href="<?= site_url('checkout') ?>" class="btn btn-coffee btn-lg w-100 mt-3">Checkout</a>
                <?php if (session('user_role') === null) : ?>
                    <p class="small text-muted text-center mt-2">Anda akan diminta masuk atau mendaftar, lalu kembali ke halaman checkout.</p>
                <?php endif ?>
            </div>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
