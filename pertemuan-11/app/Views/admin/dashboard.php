<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Dashboard</h1>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Total Produk', $stats['products'], null],
        ['Total Customer', $stats['customers'], null],
        ['Total Pesanan', $stats['orders'], null],
        ['Total Penjualan', rupiah($stats['sales']), 'Pesanan diproses/dikirim/selesai'],
        ['Menunggu Pembayaran', $stats['pending'], null],
        ['Menunggu Verifikasi', $stats['awaitingVerify'], current_role() === 'admin' ? site_url('admin/payments') : null],
        ['Siap Dikirim', $stats['toShip'], site_url('admin/shipments')],
    ];
    ?>
    <?php foreach ($cards as [$label, $value, $hint]): ?>
        <div class="col-6 col-md-4 col-xl-3">
            <div class="stat-card">
                <div class="label"><?= esc($label) ?></div>
                <div class="value"><?= is_int($value) ? number_format($value, 0, ',', '.') : esc($value) ?></div>
                <?php if ($hint && str_starts_with((string) $hint, 'http')): ?>
                    <a class="small text-decoration-none" href="<?= $hint ?>">Lihat &rarr;</a>
                <?php elseif ($hint): ?>
                    <div class="small text-muted"><?= esc($hint) ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="se-card p-3 h-100">
            <h2 class="h6 fw-bold">Penjualan 14 Hari Terakhir</h2>
            <div style="height:280px"><canvas id="salesChart" aria-label="Grafik penjualan harian" role="img"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="se-card p-3 h-100">
            <h2 class="h6 fw-bold">Distribusi Status Pesanan</h2>
            <?php if ($chart['statusDist']): ?>
                <div style="height:280px"><canvas id="statusChart" aria-label="Distribusi status pesanan" role="img"></canvas></div>
            <?php else: ?>
                <p class="text-muted small mb-0">Belum ada pesanan.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="se-card p-0 overflow-hidden">
            <div class="p-3 pb-2 d-flex justify-content-between"><h2 class="h6 fw-bold mb-0">Pesanan Terbaru</h2><a class="small text-decoration-none" href="<?= site_url('admin/orders') ?>">Semua pesanan &rarr;</a></div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>No. Pesanan</th><th>Customer</th><th class="text-end">Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td><a class="text-decoration-none" href="<?= site_url('admin/orders/' . $o['id']) ?>"><?= esc($o['order_number']) ?></a></td>
                            <td><?= esc($o['customer_name']) ?></td>
                            <td class="text-end"><?= rupiah($o['total']) ?></td>
                            <td><?= order_status_badge($o['order_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (! $recentOrders): ?><tr><td colspan="4" class="text-center text-muted py-3">Belum ada pesanan.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="se-card p-3 mb-3">
            <h2 class="h6 fw-bold">Produk Terlaris</h2>
            <?php foreach ($topProducts as $t): ?>
                <div class="d-flex justify-content-between small py-1 border-bottom"><span><?= esc($t['product_name']) ?></span><span class="text-muted"><?= (int) $t['qty'] ?> terjual</span></div>
            <?php endforeach; ?>
            <?php if (! $topProducts): ?><p class="text-muted small mb-0">Belum ada data penjualan.</p><?php endif; ?>
        </div>
        <div class="se-card p-3">
            <h2 class="h6 fw-bold">Stok Menipis (&le; 5)</h2>
            <?php foreach ($lowStock as $p): ?>
                <div class="d-flex justify-content-between small py-1 border-bottom"><span><?= esc($p['name']) ?></span><span class="badge bg-<?= (int) $p['stock'] === 0 ? 'danger' : 'warning text-dark' ?>"><?= (int) $p['stock'] ?></span></div>
            <?php endforeach; ?>
            <?php if (! $lowStock): ?><p class="text-muted small mb-0">Semua stok aman.</p><?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/chart.umd.js') ?>"></script>
<script>
(function () {
    var data = <?= json_encode($chart, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
    var rp = function (v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); };

    new Chart(document.getElementById('salesChart'), {
        data: {
            labels: data.labels,
            datasets: [
                { type: 'bar', label: 'Penjualan (Rp)', data: data.sales, backgroundColor: 'rgba(79,70,229,.75)', borderRadius: 4, yAxisID: 'y' },
                { type: 'line', label: 'Jumlah transaksi', data: data.counts, borderColor: '#db2777', backgroundColor: '#db2777', tension: 0, pointRadius: 3, yAxisID: 'y1' }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { tooltip: { callbacks: { label: function (c) { return c.dataset.yAxisID === 'y' ? c.dataset.label + ': ' + rp(c.parsed.y) : c.dataset.label + ': ' + c.parsed.y; } } } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: function (v) { return v >= 1000000 ? (v / 1000000) + ' jt' : (v / 1000) + ' rb'; } } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } }
            }
        }
    });

    var sc = document.getElementById('statusChart');
    if (sc) {
        new Chart(sc, {
            type: 'doughnut',
            data: { labels: Object.keys(data.statusDist), datasets: [{ data: Object.values(data.statusDist), backgroundColor: ['#f59e0b', '#38bdf8', '#4f46e5', '#64748b', '#16a34a', '#dc2626'] }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
})();
</script>
<?= $this->endSection() ?>
