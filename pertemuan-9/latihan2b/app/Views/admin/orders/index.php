<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<ul class="nav nav-pills mb-3 small">
    <li class="nav-item"><a class="nav-link<?= $status === '' ? ' active' : '' ?>" href="<?= site_url('admin/orders') ?>">Semua <span class="badge badge-light"><?= array_sum($counts) ?></span></a></li>
    <?php foreach (\App\Models\OrderModel::STATUSES as $item) : ?>
        <li class="nav-item"><a class="nav-link<?= $status === $item ? ' active' : '' ?>" href="<?= site_url('admin/orders?status=' . $item) ?>"><?= esc(order_status_label($item)) ?> <span class="badge badge-light"><?= $counts[$item] ?></span></a></li>
    <?php endforeach ?>
</ul>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/orders') ?>" method="get" class="form-inline">
            <input type="hidden" name="status" value="<?= esc($status, 'attr') ?>">
            <input type="search" name="q" class="form-control form-control-sm mr-2" value="<?= esc($keyword, 'attr') ?>" placeholder="Kode, pembeli, atau penerima" aria-label="Cari pesanan">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light"><tr><th>Kode</th><th>Pembeli</th><th>Tanggal</th><th class="text-right">Total</th><th>Status</th><th>Resi</th><th></th></tr></thead>
                <tbody>
                    <?php if ($orders === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><code><?= esc($order['code']) ?></code></td>
                            <td><?= esc($order['buyer_name']) ?><div class="small text-muted"><?= esc($order['city']) ?></div></td>
                            <td class="small"><?= date_id($order['created_at'], true) ?></td>
                            <td class="text-right font-weight-bold"><?= rupiah($order['total']) ?></td>
                            <td><?= order_status_badge($order['status']) ?></td>
                            <td class="small"><?= esc($order['tracking_number'] ?? '-') ?></td>
                            <td class="text-right"><a href="<?= site_url('admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">Kelola</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white"><?= $pager->links('default', 'bootstrap') ?></div>
</div>
<?= $this->endSection() ?>
