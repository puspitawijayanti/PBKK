<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<form class="card card-body border-0 shadow-sm mb-3" method="get" action="<?= url_to('admin.reports') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-sm-6 col-md-3">
            <label class="form-label small" for="from">Dari tanggal</label>
            <input class="form-control" type="date" id="from" name="from" value="<?= esc($from, 'attr') ?>">
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label small" for="to">Sampai tanggal</label>
            <input class="form-control" type="date" id="to" name="to" value="<?= esc($to, 'attr') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small" for="status">Status pesanan</label>
            <select class="form-select" id="status" name="status">
                <option value="">Semua status</option>
                <?php foreach (\App\Models\OrderModel::STATUSES as $value) : ?>
                    <option value="<?= $value ?>" <?= $status === $value ? 'selected' : '' ?>><?= order_status_label($value) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Tampilkan</button></div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-body-secondary">Penjualan terverifikasi</div>
            <div class="fs-5 fw-bold text-primary"><?= rupiah($revenue) ?></div>
            <div class="small text-body-secondary">status diproses, dikirim, selesai</div>
        </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-body-secondary">Jumlah pesanan</div>
            <div class="fs-5 fw-bold"><?= count($orders) ?></div>
            <div class="small text-body-secondary"><?= date('d M Y', strtotime($from)) ?> &ndash; <?= date('d M Y', strtotime($to)) ?></div>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="small text-body-secondary mb-1">Rekap per status</div>
            <div class="d-flex flex-wrap gap-2 small">
                <?php foreach ($summary as $value => $row) : ?>
                    <?php if ($row['count'] > 0) : ?>
                        <span><?= status_badge('order', $value) ?> <?= $row['count'] ?> &middot; <?= rupiah($row['total']) ?></span>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if ($orders === []) : ?><span class="text-body-secondary">Tidak ada pesanan pada periode ini.</span><?php endif ?>
            </div>
        </div></div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Tanggal</th><th scope="col">No. Pesanan</th><th scope="col">Customer</th><th scope="col">Status</th><th scope="col">Pembayaran</th><th scope="col" class="text-end">Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td class="text-nowrap"><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                        <td><a href="<?= url_to('admin.order.show', $order['id']) ?>"><?= esc($order['order_number']) ?></a></td>
                        <td><?= esc($order['customer_name']) ?></td>
                        <td><?= status_badge('order', $order['order_status']) ?></td>
                        <td><?= status_badge('payment', $order['payment_status']) ?></td>
                        <td class="text-end text-nowrap"><?= rupiah($order['total']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
