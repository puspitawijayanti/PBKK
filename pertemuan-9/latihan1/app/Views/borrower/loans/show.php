<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('borrower/loans') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Riwayat</a>
<?= $this->endSection() ?>

<?php
$steps = ['pending', 'approved', 'borrowed', 'returned'];
$currentStep = array_search($loan['status'], $steps, true);
?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><?= esc($loan['tool_name']) ?></h6>
                <?= loan_status_badge($loan['status']) ?>
            </div>
            <div class="card-body">
                <?php if ($loan['status'] === 'rejected') : ?>
                    <div class="alert alert-danger">Pengajuan ini ditolak oleh laboran. Silakan hubungi laboratorium untuk informasi lebih lanjut.</div>
                <?php else : ?>
                    <ol class="loan-steps list-unstyled d-flex justify-content-between mb-4">
                        <?php foreach ($steps as $index => $step) : ?>
                            <li class="text-center flex-fill<?= $currentStep !== false && $index <= $currentStep ? ' done' : '' ?>">
                                <span class="loan-step-dot"><?= $index + 1 ?></span>
                                <div class="small mt-1"><?= esc(loan_status_label($step)) ?></div>
                            </li>
                        <?php endforeach ?>
                    </ol>
                <?php endif ?>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Jumlah</dt><dd class="col-sm-8"><?= (int) $loan['quantity'] ?> unit</dd>
                    <dt class="col-sm-4">Tanggal pinjam</dt><dd class="col-sm-8"><?= date_id($loan['borrow_date']) ?></dd>
                    <dt class="col-sm-4">Rencana kembali</dt><dd class="col-sm-8"><?= date_id($loan['return_date']) ?></dd>
                    <dt class="col-sm-4">Dikembalikan</dt><dd class="col-sm-8"><?= date_id($loan['returned_at'], true) ?></dd>
                    <dt class="col-sm-4">Diajukan</dt><dd class="col-sm-8"><?= date_id($loan['created_at'], true) ?></dd>
                    <dt class="col-sm-4">Keperluan</dt><dd class="col-sm-8"><?= nl2br(esc((string) $loan['note'])) ?></dd>
                </dl>
            </div>
            <?php if ($loan['status'] === 'pending') : ?>
                <div class="card-footer bg-white text-right">
                    <form action="<?= site_url('borrower/loans/' . $loan['id'] . '/cancel') ?>" method="post" onsubmit="return confirm('Batalkan pengajuan ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-times"></i> Batalkan Pengajuan</button>
                    </form>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <img src="<?= tool_photo_url($loan['tool_photo']) ?>" class="card-img-top" alt="Foto <?= esc($loan['tool_name'], 'attr') ?>">
            <div class="card-body">
                <a href="<?= site_url('tools/' . $loan['tool_slug']) ?>">Lihat halaman alat &rarr;</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
