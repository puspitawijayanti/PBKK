<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $status = $order['order_status'] ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= url_to('orders') ?>">Pesanan Saya</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($order['order_number']) ?></li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Pesanan <?= esc($order['order_number']) ?></h1>
            <p class="small text-body-secondary mb-0">Dibuat <?= date('d M Y, H:i', strtotime($order['order_date'])) ?></p>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <?= status_badge('order', $status) ?>
            <?= status_badge('payment', $payment['payment_status'] ?? null) ?>
            <?php if ($shipment) : ?><?= status_badge('shipment', $shipment['shipment_status']) ?><?php endif ?>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <?php if ($status === 'pending_payment') : ?>
                <section class="card border-0 shadow-sm mb-4 border-start border-4 border-warning" aria-labelledby="judulBayar">
                    <div class="card-body">
                        <h2 id="judulBayar" class="h6 fw-bold"><i class="bi bi-bank me-1"></i>Instruksi pembayaran</h2>
                        <?php if (($payment['payment_status'] ?? '') === 'rejected') : ?>
                            <div class="alert alert-danger small">
                                <strong>Bukti pembayaran sebelumnya ditolak.</strong> Alasan: <?= esc($payment['notes']) ?>. Silakan unggah ulang bukti yang benar.
                            </div>
                        <?php endif ?>
                        <p class="small mb-2">Transfer tepat sebesar <strong class="text-primary fs-6"><?= rupiah($order['total']) ?></strong> ke salah satu rekening berikut:</p>
                        <ul class="small mb-3">
                            <?php foreach ($shop->bankAccounts as $account) : ?>
                                <li><strong><?= esc($account['bank']) ?></strong> <?= esc($account['number']) ?> a.n. <?= esc($account['holder']) ?></li>
                            <?php endforeach ?>
                        </ul>
                        <form action="<?= url_to('order.payment', $order['order_number']) ?>" method="post" enctype="multipart/form-data" data-once>
                            <?= csrf_field() ?>
                            <label class="form-label small fw-semibold" for="proof">Unggah bukti transfer</label>
                            <input class="form-control mb-1" type="file" id="proof" name="proof" accept="image/jpeg,image/png,image/webp" data-preview="proofPreview" required aria-describedby="proofHelp">
                            <div id="proofHelp" class="form-text mb-2">Format JPG, PNG, atau WEBP. Maksimal <?= $shop->maxProofSizeKb / 1024 ?> MB.</div>
                            <img id="proofPreview" class="img-thumbnail d-none mb-2 proof-preview" alt="Pratinjau bukti transfer">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-upload me-1"></i>Kirim bukti pembayaran</button>
                        </form>
                    </div>
                </section>
            <?php elseif ($status === 'waiting_verification') : ?>
                <div class="alert alert-info"><i class="bi bi-hourglass-split me-1"></i>Bukti pembayaran sudah dikirim dan sedang diperiksa admin.</div>
            <?php elseif ($status === 'shipped') : ?>
                <div class="alert alert-primary d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span><i class="bi bi-truck me-1"></i>Pesanan dikirim via <strong><?= esc($shipment['courier']) ?></strong>, resi <strong><?= esc($shipment['tracking_number']) ?></strong>.</span>
                    <form action="<?= url_to('order.complete', $order['order_number']) ?>" method="post" data-confirm="Konfirmasi bahwa pesanan sudah Anda terima?" data-once>
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-success" type="submit"><i class="bi bi-check2-circle me-1"></i>Pesanan diterima</button>
                    </form>
                </div>
            <?php endif ?>

            <section class="card border-0 shadow-sm mb-4" aria-labelledby="judulProduk">
                <div class="card-body">
                    <h2 id="judulProduk" class="h6 fw-bold mb-3">Rincian produk</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th scope="col">Produk</th><th scope="col" class="text-end">Harga</th><th scope="col" class="text-center">Jml</th><th scope="col" class="text-end">Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach ($items as $item) : ?>
                                    <tr>
                                        <td><?= $item['slug'] ? '<a href="' . url_to('product.show', $item['slug']) . '">' . esc($item['product_name']) . '</a>' : esc($item['product_name']) ?></td>
                                        <td class="text-end text-nowrap"><?= rupiah($item['price']) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end text-nowrap"><?= rupiah($item['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot class="small">
                                <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end"><?= rupiah($order['subtotal']) ?></td></tr>
                                <tr><td colspan="3" class="text-end">Ongkos kirim</td><td class="text-end"><?= rupiah($order['shipping_cost']) ?></td></tr>
                                <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end text-primary"><?= rupiah($order['total']) ?></td></tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="small text-body-secondary mt-2 mb-0">Harga tercatat saat pesanan dibuat dan tidak berubah walaupun harga produk diperbarui.</p>
                </div>
            </section>

            <?php if ($status === 'pending_payment') : ?>
                <form action="<?= url_to('order.cancel', $order['order_number']) ?>" method="post" data-confirm="Batalkan pesanan ini?">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-x-circle me-1"></i>Batalkan pesanan</button>
                </form>
            <?php endif ?>
        </div>

        <div class="col-lg-5">
            <section class="card border-0 shadow-sm mb-4">
                <div class="card-body small">
                    <h2 class="h6 fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i>Dikirim ke</h2>
                    <p class="mb-0"><strong><?= esc($order['recipient_name']) ?></strong> &middot; <?= esc($order['phone']) ?><br><?= esc($order['shipping_address']) ?></p>
                    <?php if ($order['notes']) : ?><p class="mt-2 mb-0 text-body-secondary">Catatan: <?= esc($order['notes']) ?></p><?php endif ?>
                </div>
            </section>

            <section class="card border-0 shadow-sm mb-4">
                <div class="card-body small">
                    <h2 class="h6 fw-bold mb-2"><i class="bi bi-credit-card me-1"></i>Pembayaran</h2>
                    <p class="mb-1">Status: <?= status_badge('payment', $payment['payment_status'] ?? null) ?></p>
                    <?php if (! empty($payment['proof_file'])) : ?>
                        <a href="<?= url_to('order.proof', $order['order_number']) ?>" target="_blank" rel="noopener">Lihat bukti transfer</a>
                    <?php endif ?>
                    <?php if (! empty($payment['verified_at']) && $payment['payment_status'] === 'verified') : ?>
                        <p class="mb-0 mt-1 text-body-secondary">Diverifikasi <?= date('d M Y, H:i', strtotime($payment['verified_at'])) ?></p>
                    <?php endif ?>
                </div>
            </section>

            <section class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Riwayat status</h2>
                    <?= view('partials/order_timeline', ['logs' => $logs]) ?>
                </div>
            </section>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
