<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('tools') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> Cari Alat</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <?php foreach ([
        ['label' => 'Menunggu Persetujuan', 'value' => $counts['pending'], 'icon' => 'fa-hourglass-half', 'color' => 'warning', 'status' => 'pending'],
        ['label' => 'Disetujui (siap diambil)', 'value' => $counts['approved'], 'icon' => 'fa-check', 'color' => 'info', 'status' => 'approved'],
        ['label' => 'Sedang Dipinjam', 'value' => $counts['borrowed'], 'icon' => 'fa-hand-holding', 'color' => 'primary', 'status' => 'borrowed'],
        ['label' => 'Sudah Dikembalikan', 'value' => $counts['returned'], 'icon' => 'fa-undo', 'color' => 'success', 'status' => 'returned'],
    ] as $card) : ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= site_url('borrower/loans?status=' . $card['status']) ?>" class="card border-left-<?= $card['color'] ?> shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $card['color'] ?> text-uppercase mb-1"><?= esc($card['label']) ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $card['value'] ?></div>
                        </div>
                        <div class="col-auto"><i class="fas <?= $card['icon'] ?> fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Peminjaman Berjalan</h6></div>
    <div class="card-body p-0">
        <?php if ($activeLoans === []) : ?>
            <div class="text-center text-muted py-5">
                <p>Belum ada peminjaman berjalan.</p>
                <a href="<?= site_url('tools') ?>" class="btn btn-primary btn-sm">Lihat katalog alat</a>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light"><tr><th>Alat</th><th>Periode</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($activeLoans as $loan) : ?>
                            <tr>
                                <td><?= esc($loan['tool_name']) ?> <span class="text-muted">&times;<?= (int) $loan['quantity'] ?></span></td>
                                <td class="small">
                                    <?= date_id($loan['borrow_date']) ?> &ndash; <?= date_id($loan['return_date']) ?>
                                    <?php if ($loan['status'] === 'borrowed' && $loan['return_date'] < date('Y-m-d')) : ?>
                                        <span class="badge badge-danger">Terlambat</span>
                                    <?php endif ?>
                                </td>
                                <td><?= loan_status_badge($loan['status']) ?></td>
                                <td class="text-right"><a href="<?= site_url('borrower/loans/' . $loan['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
