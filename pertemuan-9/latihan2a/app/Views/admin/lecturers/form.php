<?php $isEdit = $lecturer !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/lecturers') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?= $isEdit ? site_url('admin/lecturers/' . $lecturer['id']) : site_url('admin/lecturers') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="lecturer_number">NIDN</label>
                            <input type="text" inputmode="numeric" class="form-control<?= invalid_class('lecturer_number') ?>" id="lecturer_number" name="lecturer_number" value="<?= old('lecturer_number', $lecturer['lecturer_number'] ?? '') ?>" required>
                            <?= field_error('lecturer_number') ?>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="name">Nama Lengkap dan Gelar</label>
                            <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $lecturer['name'] ?? '') ?>" required>
                            <?= field_error('name') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="study_program_id">Program Studi (homebase)</label>
                        <select class="custom-select<?= invalid_class('study_program_id') ?>" id="study_program_id" name="study_program_id" required>
                            <option value="">Pilih program studi</option>
                            <?php foreach ($programs as $id => $name) : ?>
                                <option value="<?= $id ?>"<?= (string) old('study_program_id', $lecturer['study_program_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                            <?php endforeach ?>
                        </select>
                        <?= field_error('study_program_id') ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label for="email">Email</label>
                            <input type="email" class="form-control<?= invalid_class('email') ?>" id="email" name="email" value="<?= old('email', $lecturer['email'] ?? '') ?>">
                            <?= field_error('email') ?>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="phone">Telepon</label>
                            <input type="text" class="form-control<?= invalid_class('phone') ?>" id="phone" name="phone" value="<?= old('phone', $lecturer['phone'] ?? '') ?>">
                            <?= field_error('phone') ?>
                        </div>
                    </div>
                    <?php if (! $isEdit) : ?>
                        <p class="small text-muted">Akun login dosen dibuat otomatis dengan username dan password awal = NIDN.</p>
                    <?php endif ?>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
