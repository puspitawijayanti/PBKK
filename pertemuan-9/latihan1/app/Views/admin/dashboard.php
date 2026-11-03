<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/tools/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Alat</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <?php foreach ([
        ['label' => 'Jenis Alat', 'value' => $stats['tools'], 'icon' => 'fa-toolbox', 'color' => 'primary', 'url' => 'admin/tools'],
        ['label' => 'Total Unit Tersedia', 'value' => $stats['units'], 'icon' => 'fa-boxes', 'color' => 'success', 'url' => 'admin/tools'],
        ['label' => 'Menunggu Persetujuan', 'value' => $stats['pending'], 'icon' => 'fa-hourglass-half', 'color' => 'warning', 'url' => 'admin/loans?status=pending'],
        ['label' => 'Peminjaman Aktif', 'value' => $stats['active'], 'icon' => 'fa-hand-holding', 'color' => 'info', 'url' => 'admin/loans?status=borrowed'],
    ] as $card) : ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= site_url($card['url']) ?>" class="card border-left-<?= $card['color'] ?> shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $card['color'] ?> text-uppercase mb-1"><?= esc($card['label']) ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($card['value'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="fas <?= $card['icon'] ?> fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="row">
    <div class="col-xl-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pengajuan Peminjaman per Bulan</h6>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="monthlyLoansChart" aria-label="Grafik jumlah pengajuan per bulan" role="img"></canvas></div>
                <details class="mt-3 small">
                    <summary class="text-muted">Lihat sebagai tabel</summary>
                    <table class="table table-sm mt-2 mb-0">
                        <thead><tr><th>Bulan</th><th class="text-right">Pengajuan</th></tr></thead>
                        <tbody>
                            <?php foreach ($monthlyLabels as $index => $label) : ?>
                                <tr><td><?= esc($label) ?></td><td class="text-right"><?= $monthlyTotals[$index] ?></td></tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </details>
            </div>
        </div>
    </div>
    <div class="col-xl-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Jumlah Jenis Alat per Kategori</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar"><canvas id="categoryToolsChart" aria-label="Grafik jumlah alat per kategori" role="img"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Pengajuan Terlama yang Belum Diproses</h6>
                <a href="<?= site_url('admin/loans?status=pending') ?>" class="small">Lihat semua</a>
            </div>
            <div class="card-body p-0">
                <?php if ($pendingLoans === []) : ?>
                    <p class="text-muted text-center my-4">Tidak ada pengajuan yang menunggu.</p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light"><tr><th>Peminjam</th><th>Alat</th><th>Tanggal</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($pendingLoans as $loan) : ?>
                                    <tr>
                                        <td><?= esc($loan['user_name']) ?><div class="small text-muted"><?= esc($loan['username']) ?></div></td>
                                        <td><?= esc($loan['tool_name']) ?> <span class="text-muted">&times;<?= (int) $loan['quantity'] ?></span></td>
                                        <td class="small"><?= date_id($loan['borrow_date']) ?></td>
                                        <td class="text-right"><?= view('admin/loans/_actions', ['loan' => $loan]) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Stok Menipis (&le; 2 unit)</h6>
            </div>
            <div class="card-body">
                <?php if ($lowStockTools === []) : ?>
                    <p class="text-muted text-center my-4">Semua stok aman.</p>
                <?php else : ?>
                    <?php foreach ($lowStockTools as $tool) : ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <a href="<?= site_url('admin/tools/' . $tool['id'] . '/edit') ?>" class="font-weight-bold"><?= esc($tool['name']) ?></a>
                                <div class="small text-muted"><?= esc($tool['category_name']) ?></div>
                            </div>
                            <span class="badge badge-<?= (int) $tool['stock'] === 0 ? 'danger' : 'warning' ?> badge-pill"><?= (int) $tool['stock'] ?> unit</span>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>
                <hr>
                <div class="small text-muted">Peminjam terdaftar: <strong><?= $stats['borrowers'] ?></strong> &middot; Pesan masuk: <strong><?= $stats['messages'] ?></strong></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/sb-admin-2/vendor/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dashboard-charts.js') ?>"></script>
<script>
    renderBarChart('monthlyLoansChart', <?= json_encode($monthlyLabels) ?>, <?= json_encode($monthlyTotals) ?>, 'Pengajuan', false);
    renderBarChart('categoryToolsChart', <?= json_encode($categoryNames) ?>, <?= json_encode($categoryTools) ?>, 'Jenis alat', true);
</script>
<?= $this->endSection() ?>
