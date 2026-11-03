<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<ul class="nav nav-pills mb-3 small">
    <li class="nav-item">
        <a class="nav-link<?= $status === '' ? ' active' : '' ?>" href="<?= site_url('admin/loans') ?>">Semua <span class="badge badge-light"><?= array_sum($counts) ?></span></a>
    </li>
    <?php foreach (\App\Models\LoanModel::STATUSES as $item) : ?>
        <li class="nav-item">
            <a class="nav-link<?= $status === $item ? ' active' : '' ?>" href="<?= site_url('admin/loans?status=' . $item) ?>"><?= esc(loan_status_label($item)) ?> <span class="badge badge-light"><?= $counts[$item] ?></span></a>
        </li>
    <?php endforeach ?>
</ul>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/loans') ?>" method="get" class="form-inline">
            <input type="hidden" name="status" value="<?= esc($status, 'attr') ?>">
            <input type="search" name="q" class="form-control form-control-sm mr-2 mb-2 mb-sm-0" value="<?= esc($keyword, 'attr') ?>" placeholder="Nama peminjam, NRP, atau alat" aria-label="Cari peminjaman">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Periode</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($loans === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>
                    <?php endif ?>
                    <?php foreach ($loans as $loan) : ?>
                        <tr>
                            <td class="small text-muted"><?= $loan['id'] ?></td>
                            <td><?= esc($loan['user_name']) ?><div class="small text-muted"><?= esc($loan['username']) ?></div></td>
                            <td>
                                <?= esc($loan['tool_name']) ?> <span class="text-muted">&times;<?= (int) $loan['quantity'] ?></span>
                                <div class="small text-muted">Stok saat ini: <?= (int) $loan['tool_stock'] ?></div>
                            </td>
                            <td class="small">
                                <?= date_id($loan['borrow_date']) ?> &ndash; <?= date_id($loan['return_date']) ?>
                                <?php if ($loan['status'] === 'borrowed' && $loan['return_date'] < date('Y-m-d')) : ?>
                                    <div><span class="badge badge-danger">Terlambat</span></div>
                                <?php endif ?>
                                <?php if ($loan['returned_at'] !== null) : ?>
                                    <div class="text-muted">Kembali: <?= date_id($loan['returned_at'], true) ?></div>
                                <?php endif ?>
                            </td>
                            <td class="small"><?= esc(character_limiter((string) $loan['note'], 60, '…')) ?></td>
                            <td><?= loan_status_badge($loan['status']) ?></td>
                            <td class="text-right text-nowrap"><?= view('admin/loans/_actions', ['loan' => $loan]) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white"><?= $pager->links('default', 'bootstrap') ?></div>
</div>
<?= $this->endSection() ?>
