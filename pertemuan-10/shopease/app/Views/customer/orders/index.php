<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="h3 fw-bold mb-3">Pesanan Saya</h1>

    <ul class="nav nav-pills small mb-3 flex-nowrap overflow-auto">
        <li class="nav-item"><a class="nav-link text-nowrap<?= $status === '' ? ' active' : '' ?>" href="<?= url_to('orders') ?>">Semua</a></li>
        <?php foreach (\App\Models\OrderModel::STATUSES as $value) : ?>
            <li class="nav-item"><a class="nav-link text-nowrap<?= $status === $value ? ' active' : '' ?>" href="<?= url_to('orders') ?>?status=<?= $value ?>"><?= order_status_label($value) ?></a></li>
        <?php endforeach ?>
    </ul>

    <?php if ($orders === []) : ?>
        <div class="text-center py-5 border rounded-4 bg-white">
            <i class="bi bi-receipt display-4 text-body-secondary"></i>
            <p class="fw-semibold mt-3 mb-1">Belum ada pesanan<?= $status !== '' ? ' dengan status ini' : '' ?></p>
            <a class="btn btn-primary mt-2" href="<?= url_to('products') ?>">Mulai belanja</a>
        </div>
    <?php else : ?>
        <div class="d-grid gap-3">
            <?php foreach ($orders as $order) : ?>
                <a class="card border-0 shadow-sm text-reset text-decoration-none order-row" href="<?= url_to('order.show', $order['order_number']) ?>">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <div class="fw-semibold"><?= esc($order['order_number']) ?></div>
                            <div class="small text-body-secondary"><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></div>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <?= status_badge('order', $order['order_status']) ?>
                            <?= status_badge('payment', $order['payment_status']) ?>
                        </div>
                        <div class="text-end">
                            <div class="small text-body-secondary">Total</div>
                            <strong><?= rupiah($order['total']) ?></strong>
                        </div>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
        <div class="mt-4"><?= $pagerLinks ?></div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
