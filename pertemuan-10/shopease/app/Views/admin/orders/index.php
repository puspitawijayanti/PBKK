<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<ul class="nav nav-pills small mb-3 flex-nowrap overflow-auto">
    <li class="nav-item"><a class="nav-link text-nowrap<?= $status === '' ? ' active' : '' ?>" href="<?= url_to('admin.orders') ?>">Semua <span class="badge text-bg-light"><?= array_sum($statusCounts) ?></span></a></li>
    <?php foreach ($statusCounts as $value => $count) : ?>
        <li class="nav-item"><a class="nav-link text-nowrap<?= $status === $value ? ' active' : '' ?>" href="<?= url_to('admin.orders') ?>?status=<?= $value ?>"><?= order_status_label($value) ?> <span class="badge text-bg-light"><?= $count ?></span></a></li>
    <?php endforeach ?>
</ul>

<form class="card card-body border-0 shadow-sm mb-3" method="get" action="<?= url_to('admin.orders') ?>">
    <input type="hidden" name="status" value="<?= esc($status, 'attr') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small" for="q">Cari</label>
            <input class="form-control" type="search" id="q" name="q" value="<?= esc($keyword, 'attr') ?>" placeholder="No. pesanan, nama customer, atau penerima">
        </div>
        <div class="col-md-4">
            <label class="form-label small" for="payment">Status pembayaran</label>
            <select class="form-select" id="payment" name="payment">
                <option value="">Semua</option>
                <?php foreach (\App\Models\PaymentModel::STATUSES as $value) : ?>
                    <option value="<?= $value ?>" <?= $payment === $value ? 'selected' : '' ?>><?= payment_status_label($value) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-1"></i>Cari</button></div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">No. Pesanan</th><th scope="col">Tanggal</th><th scope="col">Customer</th><th scope="col" class="text-end">Total</th><th scope="col">Pesanan</th><th scope="col">Pembayaran</th><th scope="col"><span class="visually-hidden">Aksi</span></th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($order['order_number']) ?></td>
                        <td class="small text-nowrap"><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                        <td><?= esc($order['customer_name']) ?><div class="small text-body-secondary"><?= esc($order['customer_email']) ?></div></td>
                        <td class="text-end text-nowrap"><?= rupiah($order['total']) ?></td>
                        <td><?= status_badge('order', $order['order_status']) ?></td>
                        <td><?= status_badge('payment', $order['payment_status']) ?></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url_to('admin.order.show', $order['id']) ?>">Detail</a></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($orders === []) : ?>
                    <tr><td colspan="7" class="text-center text-body-secondary py-4">Tidak ada pesanan yang cocok.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pagerLinks ?></div>
<?= $this->endSection() ?>
