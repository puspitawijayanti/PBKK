<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('tools/' . $tool['slug']) ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali ke detail alat</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <img src="<?= tool_photo_url($tool['photo']) ?>" class="card-img-top" alt="Foto <?= esc($tool['name'], 'attr') ?>">
            <div class="card-body">
                <div class="small text-muted"><?= esc($tool['category_name']) ?></div>
                <h5 class="font-weight-bold text-gray-800"><?= esc($tool['name']) ?></h5>
                <p class="mb-1">Stok tersedia: <strong><?= (int) $tool['stock'] ?></strong> unit</p>
                <p class="mb-0">Kondisi: <?= esc(condition_label($tool['condition'])) ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan</h6></div>
            <div class="card-body">
                <form action="<?= site_url('borrower/loans') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tool_id" value="<?= $tool['id'] ?>">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="quantity">Jumlah</label>
                            <input type="number" class="form-control<?= invalid_class('quantity') ?>" id="quantity" name="quantity" min="1" max="<?= min((int) $tool['stock'], $site->maxLoanQuantity) ?>" value="<?= old('quantity', 1) ?>" required>
                            <?= field_error('quantity') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="borrow_date">Tanggal Pinjam</label>
                            <input type="date" class="form-control<?= invalid_class('borrow_date') ?>" id="borrow_date" name="borrow_date" min="<?= date('Y-m-d') ?>" value="<?= old('borrow_date', date('Y-m-d')) ?>" required>
                            <?= field_error('borrow_date') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="return_date">Tanggal Kembali</label>
                            <input type="date" class="form-control<?= invalid_class('return_date') ?>" id="return_date" name="return_date" min="<?= date('Y-m-d') ?>" value="<?= old('return_date', date('Y-m-d', strtotime('+3 days'))) ?>" required>
                            <?= field_error('return_date') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="note">Keperluan</label>
                        <textarea class="form-control<?= invalid_class('note') ?>" id="note" name="note" rows="4" maxlength="500" placeholder="Contoh: Praktikum Rangkaian Listrik modul 3" required><?= old('note') ?></textarea>
                        <?= field_error('note') ?>
                    </div>
                    <p class="small text-muted">Lama peminjaman maksimal <?= (int) $site->maxLoanDays ?> hari dan maksimal <?= (int) $site->maxLoanQuantity ?> unit per pengajuan. Stok baru dikurangi setelah laboran menyetujui pengajuan.</p>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
