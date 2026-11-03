<?php $isEdit = $student !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/students') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?= $isEdit ? site_url('admin/students/' . $student['id']) : site_url('admin/students') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="student_number">NIM</label>
                            <input type="text" inputmode="numeric" class="form-control<?= invalid_class('student_number') ?>" id="student_number" name="student_number" value="<?= old('student_number', $student['student_number'] ?? '') ?>" required>
                            <?= field_error('student_number') ?>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $student['name'] ?? '') ?>" required>
                            <?= field_error('name') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="study_program_id">Program Studi</label>
                            <select class="custom-select<?= invalid_class('study_program_id') ?>" id="study_program_id" name="study_program_id" required>
                                <option value="">Pilih program studi</option>
                                <?php foreach ($programs as $id => $name) : ?>
                                    <option value="<?= $id ?>"<?= (string) old('study_program_id', $student['study_program_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('study_program_id') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="entry_year">Angkatan</label>
                            <input type="number" class="form-control<?= invalid_class('entry_year') ?>" id="entry_year" name="entry_year" min="2000" max="<?= date('Y') + 1 ?>" value="<?= old('entry_year', $student['entry_year'] ?? date('Y')) ?>" required>
                            <?= field_error('entry_year') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="advisor_id">Dosen Wali</label>
                        <select class="custom-select<?= invalid_class('advisor_id') ?>" id="advisor_id" name="advisor_id">
                            <option value="">Belum ditentukan</option>
                            <?php foreach ($lecturers as $id => $name) : ?>
                                <option value="<?= $id ?>"<?= (string) old('advisor_id', $student['advisor_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                            <?php endforeach ?>
                        </select>
                        <?= field_error('advisor_id') ?>
                    </div>
                    <?php if (! $isEdit) : ?>
                        <div class="alert alert-info small">Akun login mahasiswa dibuat otomatis dalam satu transaksi: <strong>username = NIM</strong> dan <strong>password awal = NIM</strong>.</div>
                    <?php endif ?>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <?php if ($isEdit) : ?>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Ringkasan Akademik</h6></div>
                <div class="card-body">
                    <p class="mb-1">IPK: <strong><?= format_gpa($summary['gpa']) ?></strong></p>
                    <p class="mb-1">SKS lulus dinilai: <strong><?= $summary['gradedCredits'] ?></strong></p>
                    <p class="mb-3">Total SKS diambil: <strong><?= $summary['totalCredits'] ?></strong></p>
                    <a href="<?= site_url('admin/users?q=' . $student['student_number']) ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-key"></i> Reset password</a>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
