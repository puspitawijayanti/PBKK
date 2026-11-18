<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Pesanan</h1>
<form method="get" class="se-card p-3 mb-3">
    <div class="row g-2">
        <div class="col-md-5"><input type="search" name="q" class="form-control" placeholder="Cari no. pesanan / nama customer..." value="<?= esc($filters['q']) ?>" aria-label="Cari"></div>
        <div class="col-md-4">
            <select name="status" class="form-select" aria-label="Status">
                <option value="">Semua status</option>
                <?php foreach (\App\Models\OrderModel::STATUSES as $s): ?><option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= esc(order_status_label($s)) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= site_url('admin/orders') ?>">&times;</a></div>
    </div>
</form>
<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>No. Pesanan</th><th>Tanggal</th><th>Customer</th><th class="text-end">Total</th><th>Pembayaran</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($o['order_number']) ?></td>
                    <td><?= tgl($o['order_date']) ?></td>
                    <td><?= esc($o['customer_name']) ?></td>
                    <td class="text-end"><?= rupiah($o['total']) ?></td>
                    <td><?= payment_status_badge((string) ($o['payment_status'] ?? 'unpaid')) ?></td>
                    <td><?= order_status_badge($o['order_status']) ?></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/orders/' . $o['id']) ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $orders): ?><tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
