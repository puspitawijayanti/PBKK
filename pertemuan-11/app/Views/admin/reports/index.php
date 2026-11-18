<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 fw-bold mb-0">Laporan Penjualan</h1>
    <a class="btn btn-outline-primary" href="<?= site_url('admin/reports?' . http_build_query(['from' => $from, 'to' => $to, 'status' => $status, 'export' => 'csv'])) ?>">Unduh CSV</a>
</div>

<form method="get" class="se-card p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label small mb-1" for="from">Dari tanggal</label><input type="date" class="form-control" id="from" name="from" value="<?= esc($from) ?>"></div>
        <div class="col-md-3"><label class="form-label small mb-1" for="to">Sampai tanggal</label><input type="date" class="form-control" id="to" name="to" value="<?= esc($to) ?>"></div>
        <div class="col-md-4">
            <label class="form-label small mb-1" for="status">Status pesanan</label>
            <select class="form-select" id="status" name="status">
                <option value="">Semua status</option>
                <?php foreach (\App\Models\OrderModel::STATUSES as $s): ?><option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= esc(order_status_label($s)) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Tampilkan</button></div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="stat-card"><div class="label">Jumlah pesanan (periode)</div><div class="value"><?= (int) $summary['orders'] ?></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="label">Total penjualan</div><div class="value"><?= rupiah($summary['sales']) ?></div><div class="small text-muted">Status diproses, dikirim, dan selesai</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="label">Per status</div>
        <?php foreach ($summary['byStatus'] as $s => $n): ?><div class="small d-flex justify-content-between"><span><?= esc(order_status_label($s)) ?></span><strong><?= (int) $n ?></strong></div><?php endforeach; ?>
        <?php if (! $summary['byStatus']): ?><div class="small text-muted">-</div><?php endif; ?>
    </div></div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="se-card p-0 overflow-hidden">
            <div class="p-3 pb-2"><h2 class="h6 fw-bold mb-0">Rekap Harian</h2></div>
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th class="text-center">Transaksi</th><th class="text-end">Penjualan</th></tr></thead>
                <tbody>
                <?php foreach ($daily as $d): ?><tr><td><?= tgl($d['tanggal'], false) ?></td><td class="text-center"><?= (int) $d['jumlah'] ?></td><td class="text-end"><?= rupiah($d['penjualan']) ?></td></tr><?php endforeach; ?>
                <?php if (! $daily): ?><tr><td colspan="3" class="text-center text-muted py-3">Tidak ada penjualan.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="se-card p-0 overflow-hidden">
            <div class="p-3 pb-2"><h2 class="h6 fw-bold mb-0">Daftar Transaksi</h2></div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>No. Pesanan</th><th>Tanggal</th><th>Customer</th><th class="text-end">Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr><td><a class="text-decoration-none" href="<?= site_url('admin/orders/' . $r['id']) ?>"><?= esc($r['order_number']) ?></a></td><td><?= tgl($r['order_date']) ?></td><td><?= esc($r['customer_name']) ?></td><td class="text-end"><?= rupiah($r['total']) ?></td><td><?= order_status_badge($r['order_status']) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (! $rows): ?><tr><td colspan="5" class="text-center text-muted py-3">Tidak ada transaksi pada periode ini.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
