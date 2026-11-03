<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="fw-bold mb-3">Pesanan Saya</h1>
    <ul class="nav nav-pills small mb-3 flex-nowrap overflow-auto">
        <li class="nav-item"><a class="nav-link<?= $status === '' ? ' active' : '' ?>" href="<?= site_url('orders') ?>">Semua</a></li>
        <?php foreach (\App\Models\OrderModel::STATUSES as $item) : ?>
            <li class="nav-item"><a class="nav-link text-nowrap<?= $status === $item ? ' active' : '' ?>" href="<?= site_url('orders?status=' . $item) ?>"><?= esc(order_status_label($item)) ?></a></li>
        <?php endforeach ?>
    </ul>

    <?php if ($orders === []) : ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt display-4"></i>
            <p class="mt-3">Belum ada pesanan.</p>
            <a href="<?= site_url('products') ?>" class="btn btn-coffee">Belanja sekarang</a>
        </div>
    <?php else : ?>
        <div class="list-group shadow-sm mb-4">
            <?php foreach ($orders as $order) : ?>
                <a href="<?= site_url('orders/' . $order['code']) ?>" class="list-group-item list-group-item-action py-3">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold"><?= esc($order['code']) ?></div>
                            <div class="small text-muted"><?= date_id($order['created_at'], true) ?></div>
                        </div>
                        <div class="text-end">
                            <?= order_status_badge($order['status']) ?>
                            <div class="fw-bold mt-1"><?= rupiah($order['total']) ?></div>
                        </div>
                    </div>
                    <?php if ($order['status'] === 'awaiting_payment') : ?>
                        <div class="small text-warning-emphasis mt-2"><i class="bi bi-exclamation-circle"></i> Segera transfer dan unggah bukti pembayaran.</div>
                    <?php elseif ($order['status'] === 'shipped') : ?>
                        <div class="small text-muted mt-2"><i class="bi bi-truck"></i> <?= esc($order['courier']) ?> &middot; Resi <?= esc($order['tracking_number']) ?></div>
                    <?php endif ?>
                </a>
            <?php endforeach ?>
        </div>
        <?= $pager->links('default', 'bootstrap') ?>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
