<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$status        = $order['order_status'];
$payStatus     = (string) ($order['payment_status'] ?? 'unpaid');
$canUpload     = $status === 'pending_payment' && in_array($payStatus, ['unpaid', 'rejected'], true);
?>
<nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= site_url('orders') ?>">Pesanan Saya</a></li>
    <li class="breadcrumb-item active"><?= esc($order['order_number']) ?></li>
</ol></nav>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Pesanan <?= esc($order['order_number']) ?></h1>
        <div class="text-muted small">Dibuat <?= tgl($order['order_date']) ?></div>
    </div>
    <div class="text-end">
        <div><?= order_status_badge($status) ?></div>
        <div class="mt-1 small">Pembayaran: <?= payment_status_badge($payStatus) ?></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="se-card p-0 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Produk</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img class="cart-thumb" src="<?= product_image_url($it['image'] ?? null) ?>" alt="">
                                    <?= esc($it['product_name']) ?>
                                </div>
                            </td>
                            <td class="text-end"><?= rupiah($it['price']) ?></td>
                            <td class="text-center"><?= (int) $it['quantity'] ?></td>
                            <td class="text-end"><?= rupiah($it['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end text-muted">Subtotal</td><td class="text-end"><?= rupiah($order['subtotal']) ?></td></tr>
                        <tr><td colspan="3" class="text-end text-muted">Ongkos kirim</td><td class="text-end"><?= rupiah($order['shipping_cost']) ?></td></tr>
                        <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold price"><?= rupiah($order['total']) ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if ($status === 'pending_payment'): ?>
        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Pembayaran</h2>
            <p class="mb-1">Transfer sebesar <strong class="price"><?= rupiah($order['total']) ?></strong> ke:</p>
            <ul class="list-unstyled mb-3">
                <li><strong><?= esc($bank['bank']) ?></strong></li>
                <li>No. rekening: <strong><?= esc($bank['number']) ?></strong></li>
                <li>Atas nama: <?= esc($bank['holder']) ?></li>
            </ul>

            <?php if ($payStatus === 'rejected'): ?>
                <div class="alert alert-danger">Bukti pembayaran sebelumnya <strong>ditolak</strong><?= ! empty($order['payment_note']) ? ': ' . esc($order['payment_note']) : '' ?>. Silakan unggah ulang bukti yang benar.</div>
            <?php endif; ?>

            <?php if ($canUpload): ?>
            <form action="<?= site_url('orders/' . $order['id'] . '/payment') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <label for="proof" class="form-label">Bukti pembayaran (JPG/PNG/WEBP, maks. 2 MB)</label>
                <div class="input-group">
                    <input type="file" class="form-control" id="proof" name="proof" accept="image/jpeg,image/png,image/webp" required>
                    <button class="btn btn-primary" type="submit">Unggah Bukti</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
        <?php elseif (! empty($order['proof_file'])): ?>
        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Bukti Pembayaran</h2>
            <a href="<?= site_url('payment/proof/' . $order['id']) ?>" target="_blank" rel="noopener">
                <img src="<?= site_url('payment/proof/' . $order['id']) ?>" alt="Bukti pembayaran" class="img-thumbnail" style="max-height:220px">
            </a>
            <?php if ($payStatus === 'verified'): ?>
                <div class="small text-success mt-2">Terverifikasi pada <?= tgl($order['verified_at']) ?></div>
            <?php elseif ($payStatus === 'submitted'): ?>
                <div class="small text-muted mt-2">Menunggu pemeriksaan admin.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Alamat Pengiriman</h2>
            <p class="mb-0 small"><?= esc((string) $order['shipping_address']) ?></p>
            <?php if (! empty($order['notes'])): ?><p class="small text-muted mt-2 mb-0">Catatan: <?= esc($order['notes']) ?></p><?php endif; ?>
        </div>

        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Pengiriman</h2>
            <?php if (! empty($order['courier'])): ?>
                <div class="small">Kurir: <strong><?= esc($order['courier']) ?></strong></div>
                <div class="small">No. resi: <strong><?= esc($order['tracking_number']) ?></strong></div>
                <div class="small">Status: <?= esc(shipment_status_label($order['shipment_status'])) ?></div>
                <?php if (! empty($order['shipped_at'])): ?><div class="small text-muted">Dikirim <?= tgl($order['shipped_at']) ?></div><?php endif; ?>
            <?php else: ?>
                <div class="small text-muted">Belum ada informasi pengiriman.</div>
            <?php endif; ?>
        </div>

        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Status Pesanan</h2>
            <ul class="timeline small">
                <?php foreach ($logs as $log): ?>
                    <li>
                        <div class="fw-semibold"><?= esc(order_status_label($log['new_status'])) ?></div>
                        <div class="text-muted"><?= tgl($log['created_at']) ?></div>
                        <?php if (! empty($log['note'])): ?><div><?= esc($log['note']) ?></div><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($status === 'pending_payment'): ?>
            <form action="<?= site_url('orders/' . $order['id'] . '/cancel') ?>" method="post" onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger w-100" type="submit">Batalkan Pesanan</button>
            </form>
        <?php elseif ($status === 'shipped'): ?>
            <form action="<?= site_url('orders/' . $order['id'] . '/received') ?>" method="post" onsubmit="return confirm('Konfirmasi bahwa pesanan sudah diterima?')">
                <?= csrf_field() ?>
                <button class="btn btn-success w-100" type="submit">Pesanan Sudah Diterima</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
