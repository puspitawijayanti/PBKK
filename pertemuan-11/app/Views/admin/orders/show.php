<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$isAdmin   = current_role() === 'admin';
$status    = $order['order_status'];
$payStatus = (string) ($order['payment_status'] ?? 'unpaid');
?>
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/orders') ?>">Pesanan</a></li><li class="breadcrumb-item active"><?= esc($order['order_number']) ?></li></ol></nav>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Pesanan <?= esc($order['order_number']) ?></h1>
        <div class="text-muted small">Dibuat <?= tgl($order['order_date']) ?> &middot; <?= esc($order['customer_name']) ?> (<?= esc($order['customer_email']) ?>)</div>
    </div>
    <div class="text-end"><div><?= order_status_badge($status) ?></div><div class="small mt-1">Pembayaran: <?= payment_status_badge($payStatus) ?></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="se-card p-0 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Produk (snapshot)</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr><td><?= esc($it['product_name']) ?></td><td class="text-end"><?= rupiah($it['price']) ?></td><td class="text-center"><?= (int) $it['quantity'] ?></td><td class="text-end"><?= rupiah($it['subtotal']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end text-muted">Subtotal</td><td class="text-end"><?= rupiah($order['subtotal']) ?></td></tr>
                        <tr><td colspan="3" class="text-end text-muted">Ongkos kirim</td><td class="text-end"><?= rupiah($order['shipping_cost']) ?></td></tr>
                        <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold"><?= rupiah($order['total']) ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Pembayaran</h2>
            <?php if (! empty($order['proof_file'])): ?>
                <?php if ($isAdmin): ?>
                    <a href="<?= site_url('payment/proof/' . $order['id']) ?>" target="_blank" rel="noopener"><img src="<?= site_url('payment/proof/' . $order['id']) ?>" alt="Bukti pembayaran" class="img-thumbnail mb-2" style="max-height:240px"></a>
                <?php else: ?>
                    <p class="small text-muted">Bukti pembayaran hanya dapat dilihat oleh Administrator.</p>
                <?php endif; ?>
                <?php if (! empty($order['payment_note'])): ?><div class="small text-danger">Catatan penolakan: <?= esc($order['payment_note']) ?></div><?php endif; ?>
                <?php if ($payStatus === 'verified'): ?><div class="small text-success">Diverifikasi <?= tgl($order['verified_at']) ?></div><?php endif; ?>
            <?php else: ?>
                <p class="small text-muted mb-0">Belum ada bukti pembayaran.</p>
            <?php endif; ?>

            <?php if ($isAdmin && $payStatus === 'submitted' && $status === 'waiting_verification'): ?>
                <hr>
                <div class="d-flex flex-wrap gap-2 align-items-start">
                    <form action="<?= site_url('admin/payments/' . $order['id'] . '/verify') ?>" method="post" onsubmit="return confirm('Setujui pembayaran ini?')">
                        <?= csrf_field() ?><button class="btn btn-success" type="submit">Setujui Pembayaran</button>
                    </form>
                    <form action="<?= site_url('admin/payments/' . $order['id'] . '/reject') ?>" method="post" class="d-flex gap-2 flex-grow-1">
                        <?= csrf_field() ?>
                        <input type="text" name="reason" class="form-control" placeholder="Alasan penolakan" minlength="5" maxlength="200" required aria-label="Alasan penolakan">
                        <button class="btn btn-outline-danger text-nowrap" type="submit">Tolak</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php if (in_array($status, ['processing', 'shipped'], true)): ?>
        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold"><?= $status === 'processing' ? 'Kirim Pesanan' : 'Perbarui Data Pengiriman' ?></h2>
            <form action="<?= site_url('admin/orders/' . $order['id'] . '/ship') ?>" method="post" class="row g-2">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <label class="form-label small" for="courier">Kurir</label>
                    <select class="form-select" id="courier" name="courier" required>
                        <option value="">Pilih kurir...</option>
                        <?php foreach ($couriers as $c): ?><option <?= old('courier', $order['courier'] ?? '') === $c ? 'selected' : '' ?>><?= esc($c) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small" for="tracking_number">Nomor resi</label>
                    <input class="form-control" id="tracking_number" name="tracking_number" value="<?= esc(old('tracking_number', $order['tracking_number'] ?? '')) ?>" required minlength="5" maxlength="100">
                </div>
                <div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit"><?= $status === 'processing' ? 'Tandai Dikirim' : 'Simpan' ?></button></div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Alamat Pengiriman</h2>
            <p class="small mb-0"><?= esc((string) $order['shipping_address']) ?></p>
            <?php if (! empty($order['notes'])): ?><p class="small text-muted mt-2 mb-0">Catatan: <?= esc($order['notes']) ?></p><?php endif; ?>
        </div>

        <?php if (! empty($order['courier'])): ?>
        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Pengiriman</h2>
            <div class="small">Kurir: <strong><?= esc($order['courier']) ?></strong></div>
            <div class="small">No. resi: <strong><?= esc($order['tracking_number']) ?></strong></div>
            <div class="small">Status: <?= esc(shipment_status_label($order['shipment_status'])) ?></div>
            <div class="small text-muted">Dikirim: <?= tgl($order['shipped_at']) ?> &middot; Diterima: <?= tgl($order['delivered_at']) ?></div>
        </div>
        <?php endif; ?>

        <div class="se-card p-4 mb-4">
            <h2 class="h6 fw-bold">Riwayat Status</h2>
            <ul class="timeline small">
                <?php foreach ($logs as $log): ?>
                    <li>
                        <div class="fw-semibold"><?= esc(order_status_label($log['new_status'])) ?></div>
                        <div class="text-muted"><?= tgl($log['created_at']) ?> &middot; <?= esc($log['actor_name'] ?? 'Sistem') ?></div>
                        <?php if (! empty($log['note'])): ?><div><?= esc($log['note']) ?></div><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($status === 'shipped'): ?>
            <form action="<?= site_url('admin/orders/' . $order['id'] . '/complete') ?>" method="post" class="mb-3" onsubmit="return confirm('Tandai pesanan selesai?')">
                <?= csrf_field() ?><button class="btn btn-success w-100" type="submit">Tandai Selesai</button>
            </form>
        <?php endif; ?>

        <?php if ($isAdmin && in_array($status, ['pending_payment', 'waiting_verification', 'processing'], true)): ?>
            <form action="<?= site_url('admin/orders/' . $order['id'] . '/cancel') ?>" method="post" onsubmit="return confirm('Batalkan pesanan? Stok akan dikembalikan.')">
                <?= csrf_field() ?>
                <input type="text" name="reason" class="form-control mb-2" placeholder="Alasan pembatalan (opsional)" maxlength="200" aria-label="Alasan pembatalan">
                <button class="btn btn-outline-danger w-100" type="submit">Batalkan Pesanan</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
