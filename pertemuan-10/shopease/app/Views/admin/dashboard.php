<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $maxRevenue = max(1, max(array_column($monthly, 'revenue'))) ?>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['Produk aktif', number_format($stats['products']), 'bi-box-seam', 'primary', url_to('admin.products')],
        ['Pelanggan', number_format($stats['customers']), 'bi-people', 'success', url_to('admin.users') . '?role=customer'],
        ['Total pesanan', number_format($stats['orders']), 'bi-receipt', 'info', url_to('admin.orders')],
        ['Penjualan', rupiah($stats['revenue']), 'bi-cash-stack', 'warning', url_to('admin.reports')],
    ] as [$label, $value, $icon, $color, $url]) : ?>
        <div class="col-6 col-xl-3">
            <a class="card stat-card border-0 shadow-sm h-100 text-reset text-decoration-none" href="<?= $url ?>">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon text-bg-<?= $color ?>"><i class="bi <?= $icon ?>"></i></span>
                    <div>
                        <div class="small text-body-secondary"><?= $label ?></div>
                        <div class="fs-5 fw-bold"><?= $value ?></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<?php if ($statusCounts['waiting_verification'] > 0 || $statusCounts['processing'] > 0 || $lowStock > 0) : ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php if ($statusCounts['waiting_verification'] > 0) : ?>
            <a class="btn btn-sm btn-info" href="<?= url_to('admin.orders') ?>?status=waiting_verification"><i class="bi bi-hourglass-split me-1"></i><?= $statusCounts['waiting_verification'] ?> pembayaran menunggu verifikasi</a>
        <?php endif ?>
        <?php if ($statusCounts['processing'] > 0) : ?>
            <a class="btn btn-sm btn-primary" href="<?= url_to('admin.orders') ?>?status=processing"><i class="bi bi-box me-1"></i><?= $statusCounts['processing'] ?> pesanan perlu dikirim</a>
        <?php endif ?>
        <?php if ($lowStock > 0) : ?>
            <a class="btn btn-sm btn-warning" href="<?= url_to('admin.products') ?>?status=active"><i class="bi bi-exclamation-triangle me-1"></i><?= $lowStock ?> produk stok menipis (&lt; 10)</a>
        <?php endif ?>
    </div>
<?php endif ?>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <section class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">Penjualan 6 bulan terakhir</h2>
                <div class="bar-chart" role="img" aria-label="Grafik penjualan bulanan">
                    <?php foreach ($monthly as $period => $row) : ?>
                        <div class="bar-col">
                            <span class="bar-value"><?= $row['revenue'] > 0 ? number_format($row['revenue'] / 1000, 0, ',', '.') . 'rb' : '' ?></span>
                            <div class="bar" style="height: <?= round($row['revenue'] / $maxRevenue * 100) ?>%"></div>
                            <span class="bar-label"><?= date('M y', strtotime($period . '-01')) ?></span>
                        </div>
                    <?php endforeach ?>
                </div>
                <table class="visually-hidden">
                    <caption>Penjualan per bulan</caption>
                    <tr><th scope="col">Bulan</th><th scope="col">Pesanan</th><th scope="col">Penjualan</th></tr>
                    <?php foreach ($monthly as $period => $row) : ?>
                        <tr><td><?= $period ?></td><td><?= $row['orders'] ?></td><td><?= rupiah($row['revenue']) ?></td></tr>
                    <?php endforeach ?>
                </table>
            </div>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">Status pesanan</h2>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($statusCounts as $status => $count) : ?>
                        <li class="d-flex justify-content-between align-items-center py-1">
                            <a class="text-decoration-none" href="<?= url_to('admin.orders') ?>?status=<?= $status ?>"><?= status_badge('order', $status) ?></a>
                            <strong><?= $count ?></strong>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </section>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <section class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-bold mb-0">Pesanan terbaru</h2>
                    <a class="small" href="<?= url_to('admin.orders') ?>">Lihat semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead><tr><th scope="col">No. Pesanan</th><th scope="col">Customer</th><th scope="col">Total</th><th scope="col">Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order) : ?>
                                <tr>
                                    <td><a href="<?= url_to('admin.order.show', $order['id']) ?>"><?= esc($order['order_number']) ?></a></td>
                                    <td><?= esc($order['customer_name']) ?></td>
                                    <td class="text-nowrap"><?= rupiah($order['total']) ?></td>
                                    <td><?= status_badge('order', $order['order_status']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">Produk terlaris</h2>
                <ol class="mb-0 ps-3 small">
                    <?php foreach ($bestSellers as $row) : ?>
                        <li class="mb-1"><?= esc($row['product_name']) ?> <span class="text-body-secondary">&middot; <?= (int) $row['sold'] ?> terjual</span></li>
                    <?php endforeach ?>
                    <?php if ($bestSellers === []) : ?><li class="text-body-secondary">Belum ada penjualan.</li><?php endif ?>
                </ol>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
