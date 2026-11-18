<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Verifikasi Pembayaran</h1>
<ul class="nav nav-pills mb-3">
    <?php foreach (['submitted' => 'Menunggu verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'unpaid' => 'Belum dibayar', 'all' => 'Semua'] as $k => $label): ?>
        <li class="nav-item"><a class="nav-link <?= $status === $k ? 'active' : '' ?>" href="<?= site_url('admin/payments?status=' . $k) ?>"><?= $label ?></a></li>
    <?php endforeach; ?>
</ul>
<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>No. Pesanan</th><th>Customer</th><th class="text-end">Jumlah</th><th>Status pembayaran</th><th>Diverifikasi oleh</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($p['order_number']) ?><div class="small text-muted fw-normal"><?= tgl($p['order_date']) ?></div></td>
                    <td><?= esc($p['customer_name']) ?></td>
                    <td class="text-end"><?= rupiah($p['amount']) ?></td>
                    <td><?= payment_status_badge($p['payment_status']) ?><?php if ($p['note']): ?><div class="small text-danger"><?= esc($p['note']) ?></div><?php endif; ?></td>
                    <td class="small"><?= $p['verifier_name'] ? esc($p['verifier_name']) . '<br><span class="text-muted">' . tgl($p['verified_at']) . '</span>' : '-' ?></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= site_url('admin/orders/' . $p['order_id']) ?>" class="btn btn-sm btn-outline-primary"><?= $p['payment_status'] === 'submitted' ? 'Periksa' : 'Detail' ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $payments): ?><tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data pembayaran pada filter ini.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
