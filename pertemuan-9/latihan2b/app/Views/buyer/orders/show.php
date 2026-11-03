<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <a href="<?= site_url('orders') ?>" class="small text-decoration-none">&larr; Pesanan Saya</a>
            <h1 class="h3 fw-bold mb-0"><?= esc($order['code']) ?></h1>
        </div>
        <?= order_status_badge($order['status']) ?>
    </div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-body"><?= view('partials/order_timeline', ['order' => $order]) ?></div></div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold">Rincian Produk</h2>
                    <table class="table align-middle small mb-0">
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td><img src="<?= product_photo_url($item['product_photo']) ?>" alt="" class="cart-thumb rounded"></td>
                                    <td>
                                        <?php if ($item['product_slug'] !== null) : ?>
                                            <a href="<?= site_url('products/' . $item['product_slug']) ?>" class="text-reset"><?= esc($item['product_name']) ?></a>
                                        <?php else : ?>
                                            <?= esc($item['product_name']) ?>
                                        <?php endif ?>
                                        <div class="text-muted"><?= $item['quantity'] ?> &times; <?= rupiah($item['price']) ?></div>
                                    </td>
                                    <td class="text-end fw-semibold"><?= rupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="2" class="text-end">Subtotal</td><td class="text-end"><?= rupiah($order['subtotal']) ?></td></tr>
                            <tr><td colspan="2" class="text-end">Ongkos kirim</td><td class="text-end"><?= (int) $order['shipping_cost'] === 0 ? 'Gratis' : rupiah($order['shipping_cost']) ?></td></tr>
                            <tr class="fw-bold"><td colspan="2" class="text-end">Total</td><td class="text-end text-coffee"><?= rupiah($order['total']) ?></td></tr>
                        </tfoot>
                    </table>
                    <p class="small text-muted mt-2 mb-0">Nama dan harga produk disimpan saat checkout, sehingga tidak berubah walaupun harga di katalog berubah.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body small">
                    <h2 class="h6 fw-bold">Dikirim ke</h2>
                    <p class="mb-1 fw-semibold"><?= esc($order['recipient_name']) ?> &middot; <?= esc($order['phone']) ?></p>
                    <p class="mb-1"><?= nl2br(esc($order['address'])) ?>, <?= esc($order['city']) ?> <?= esc($order['postal_code']) ?></p>
                    <?php if ($order['note']) : ?><p class="mb-1 text-muted">Catatan: <?= esc($order['note']) ?></p><?php endif ?>
                    <?php if ($order['tracking_number']) : ?>
                        <p class="mb-0 mt-2"><i class="bi bi-truck"></i> <?= esc($order['courier']) ?> &middot; Resi <strong><?= esc($order['tracking_number']) ?></strong></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <?php if ($order['status'] === 'awaiting_payment') : ?>
                <div class="card border-warning shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h6 fw-bold">Selesaikan Pembayaran</h2>
                        <p class="small">Transfer tepat <strong class="text-coffee fs-5"><?= rupiah($order['total']) ?></strong> ke salah satu rekening berikut:</p>
                        <ul class="list-unstyled small">
                            <?php foreach ($shop->bankAccounts as $account) : ?>
                                <li class="mb-2"><strong><?= esc($account['bank']) ?></strong> <?= esc($account['number']) ?><br><span class="text-muted">a.n. <?= esc($account['holder']) ?></span></li>
                            <?php endforeach ?>
                        </ul>
                        <p class="small text-muted">Batas pembayaran: <?= date_id(date('Y-m-d H:i:s', strtotime($order['created_at']) + $shop->paymentDeadlineHours * 3600), true) ?></p>
                        <form action="<?= site_url('orders/' . $order['code'] . '/payment') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <label for="payment_proof" class="form-label small fw-semibold">Bukti transfer (JPG/PNG/WEBP, maks 2 MB)</label>
                            <input type="file" class="form-control form-control-sm<?= invalid_class('payment_proof') ?>" id="payment_proof" name="payment_proof" accept="image/jpeg,image/png,image/webp" required>
                            <?= field_error('payment_proof') ?>
                            <button type="submit" class="btn btn-coffee w-100 mt-3"><i class="bi bi-upload"></i> Unggah Bukti Transfer</button>
                        </form>
                    </div>
                </div>
                <form action="<?= site_url('orders/' . $order['code'] . '/cancel') ?>" method="post" onsubmit="return confirm('Batalkan pesanan ini? Stok produk akan dikembalikan.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Batalkan Pesanan</button>
                </form>
            <?php elseif ($order['status'] === 'awaiting_verification') : ?>
                <div class="alert alert-info">Bukti transfer sudah diterima pada <?= date_id($order['paid_at'], true) ?> dan sedang diverifikasi admin.</div>
            <?php elseif ($order['status'] === 'shipped') : ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="small">Pesanan sedang dalam perjalanan. Konfirmasi setelah paket diterima.</p>
                        <form action="<?= site_url('orders/' . $order['code'] . '/complete') ?>" method="post" onsubmit="return confirm('Konfirmasi paket sudah diterima?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-check2-circle"></i> Pesanan Diterima</button>
                        </form>
                    </div>
                </div>
            <?php endif ?>

            <?php if ($order['payment_proof']) : ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h2 class="h6 fw-bold">Bukti Transfer</h2>
                        <img src="<?= site_url('orders/' . $order['code'] . '/proof') ?>" alt="Bukti transfer pesanan <?= esc($order['code'], 'attr') ?>" class="img-fluid rounded border">
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
