<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<ul class="nav nav-pills mb-3 small">
    <li class="nav-item"><a class="nav-link<?= $status === '' ? ' active' : '' ?>" href="<?= site_url('borrower/loans') ?>">Semua</a></li>
    <?php foreach (\App\Models\LoanModel::STATUSES as $item) : ?>
        <li class="nav-item"><a class="nav-link<?= $status === $item ? ' active' : '' ?>" href="<?= site_url('borrower/loans?status=' . $item) ?>"><?= esc(loan_status_label($item)) ?></a></li>
    <?php endforeach ?>
</ul>

<div class="card shadow mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>#</th><th>Alat</th><th>Periode</th><th>Diajukan</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if ($loans === []) : ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat peminjaman.</td></tr>
                    <?php endif ?>
                    <?php foreach ($loans as $loan) : ?>
                        <tr>
                            <td class="small text-muted"><?= $loan['id'] ?></td>
                            <td><?= esc($loan['tool_name']) ?> <span class="text-muted">&times;<?= (int) $loan['quantity'] ?></span></td>
                            <td class="small"><?= date_id($loan['borrow_date']) ?> &ndash; <?= date_id($loan['return_date']) ?></td>
                            <td class="small text-muted"><?= date_id($loan['created_at'], true) ?></td>
                            <td><?= loan_status_badge($loan['status']) ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('borrower/loans/' . $loan['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                <?php if ($loan['status'] === 'pending') : ?>
                                    <form action="<?= site_url('borrower/loans/' . $loan['id'] . '/cancel') ?>" method="post" class="d-inline" onsubmit="return confirm('Batalkan pengajuan ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Batalkan</button>
                                    </form>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white"><?= $pager->links('default', 'bootstrap') ?></div>
</div>
<?= $this->endSection() ?>
