<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <?php foreach ([
        ['label' => 'Omzet Bulan Ini', 'value' => rupiah($revenueMonth), 'icon' => 'fa-calendar', 'color' => 'primary', 'url' => 'admin/orders'],
        ['label' => 'Total Omzet', 'value' => rupiah($revenueTotal), 'icon' => 'fa-wallet', 'color' => 'success', 'url' => 'admin/orders?status=completed'],
        ['label' => 'Perlu Verifikasi', 'value' => $counts['awaiting_verification'], 'icon' => 'fa-file-invoice-dollar', 'color' => 'warning', 'url' => 'admin/orders?status=awaiting_verification'],
        ['label' => 'Siap Dikirim', 'value' => $counts['processing'], 'icon' => 'fa-box', 'color' => 'info', 'url' => 'admin/orders?status=processing'],
    ] as $card) : ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= site_url($card['url']) ?>" class="card border-left-<?= $card['color'] ?> shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $card['color'] ?> text-uppercase mb-1"><?= esc($card['label']) ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc((string) $card['value']) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas <?= $card['icon'] ?> fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Omzet per Bulan (pesanan dibayar)</h6></div>
            <div class="card-body">
                <div class="chart-area"><canvas id="revenueChart" role="img" aria-label="Grafik omzet per bulan"></canvas></div>
                <details class="mt-3 small">
                    <summary class="text-muted">Lihat sebagai tabel</summary>
                    <table class="table table-sm mt-2 mb-0">
                        <thead><tr><th>Bulan</th><th class="text-right">Pesanan</th><th class="text-right">Omzet</th></tr></thead>
                        <tbody>
                            <?php foreach ($monthLabels as $index => $label) : ?>
                                <tr><td><?= esc($label) ?></td><td class="text-right"><?= $monthOrders[$index] ?></td><td class="text-right"><?= rupiah($monthRevenue[$index]) ?></td></tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </details>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Status Pesanan</h6></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($counts as $status => $total) : ?>
                    <a href="<?= site_url('admin/orders?status=' . $status) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <?= order_status_badge($status) ?>
                        <span class="font-weight-bold text-gray-800"><?= $total ?></span>
                    </a>
                <?php endforeach ?>
            </ul>
            <div class="card-body small text-muted">Pembeli terdaftar: <strong><?= $buyers ?></strong></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Produk Terlaris</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0 small">
                    <thead class="thead-light"><tr><th>Produk</th><th class="text-right">Terjual</th><th class="text-right">Omzet</th></tr></thead>
                    <tbody>
                        <?php foreach ($bestSellers as $item) : ?>
                            <tr><td><?= esc($item['product_name']) ?></td><td class="text-right"><?= (int) $item['sold'] ?></td><td class="text-right"><?= rupiah($item['revenue']) ?></td></tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Menunggu Verifikasi</h6></div>
            <div class="card-body">
                <?php if ($toVerify === []) : ?>
                    <p class="text-muted text-center my-3">Tidak ada pembayaran yang menunggu.</p>
                <?php endif ?>
                <?php foreach ($toVerify as $order) : ?>
                    <a href="<?= site_url('admin/orders/' . $order['id']) ?>" class="d-flex justify-content-between mb-3 text-decoration-none">
                        <span><strong class="text-gray-800"><?= esc($order['code']) ?></strong><br><span class="small text-muted"><?= esc($order['buyer_name']) ?></span></span>
                        <span class="font-weight-bold"><?= rupiah($order['total']) ?></span>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Stok Menipis</h6></div>
            <div class="card-body">
                <?php if ($lowStock === []) : ?>
                    <p class="text-muted text-center my-3">Semua stok aman.</p>
                <?php endif ?>
                <?php foreach ($lowStock as $product) : ?>
                    <div class="d-flex justify-content-between mb-2 small">
                        <a href="<?= site_url('admin/products/' . $product['id'] . '/edit') ?>"><?= esc($product['name']) ?></a>
                        <span class="badge badge-<?= (int) $product['stock'] === 0 ? 'danger' : 'warning' ?>"><?= (int) $product['stock'] ?></span>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/sb-admin-2/vendor/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dashboard-charts.js') ?>"></script>
<script>
    renderBarChart('revenueChart', <?= json_encode($monthLabels) ?>, <?= json_encode($monthRevenue) ?>, 'Omzet', false, function (value) {
        return 'Rp' + Number(value).toLocaleString('id-ID');
    });
</script>
<?= $this->endSection() ?>
