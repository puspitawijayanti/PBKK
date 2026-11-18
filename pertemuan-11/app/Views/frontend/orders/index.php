<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Riwayat Pesanan</h1>

<?php if (! $orders): ?>
    <div class="se-card p-5 text-center">
        <p class="text-muted mb-3">Anda belum memiliki pesanan.</p>
        <a href="<?= site_url('catalog') ?>" class="btn btn-primary">Mulai Belanja</a>
    </div>
<?php else: ?>
    <div class="se-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>No. Pesanan</th><th>Tanggal</th><th class="text-end">Total</th><th>Pembayaran</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($o['order_number']) ?></td>
                        <td><?= tgl($o['order_date']) ?></td>
                        <td class="text-end"><?= rupiah($o['total']) ?></td>
                        <td><?= payment_status_badge((string) ($o['payment_status'] ?? 'unpaid')) ?></td>
                        <td><?= order_status_badge($o['order_status']) ?></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('orders/' . $o['id']) ?>">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
