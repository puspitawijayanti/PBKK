<?php $isEdit = $course !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-9">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?= $isEdit ? site_url('admin/courses/' . $course['id']) : site_url('admin/courses') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="code">Kode MK</label>
                            <input type="text" class="form-control text-uppercase<?= invalid_class('code') ?>" id="code" name="code" value="<?= old('code', $course['code'] ?? '') ?>" maxlength="15" required>
                            <?= field_error('code') ?>
                        </div>
                        <div class="form-group col-md-9">
                            <label for="name">Nama Mata Kuliah</label>
                            <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $course['name'] ?? '') ?>" required>
                            <?= field_error('name') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="study_program_id">Program Studi</label>
                            <select class="custom-select<?= invalid_class('study_program_id') ?>" id="study_program_id" name="study_program_id" required>
                                <option value="">Pilih program studi</option>
                                <?php foreach ($programs as $id => $name) : ?>
                                    <option value="<?= $id ?>"<?= (string) old('study_program_id', $course['study_program_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('study_program_id') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lecturer_id">Dosen Pengampu</label>
                            <select class="custom-select<?= invalid_class('lecturer_id') ?>" id="lecturer_id" name="lecturer_id">
                                <option value="">Belum ditentukan</option>
                                <?php foreach ($lecturers as $id => $name) : ?>
                                    <option value="<?= $id ?>"<?= (string) old('lecturer_id', $course['lecturer_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('lecturer_id') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="credits">SKS</label>
                            <input type="number" class="form-control<?= invalid_class('credits') ?>" id="credits" name="credits" min="1" max="6" value="<?= old('credits', $course['credits'] ?? 3) ?>" required>
                            <?= field_error('credits') ?>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="semester">Semester</label>
                            <input type="number" class="form-control<?= invalid_class('semester') ?>" id="semester" name="semester" min="1" max="8" value="<?= old('semester', $course['semester'] ?? 1) ?>" required>
                            <?= field_error('semester') ?>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="quota">Kuota</label>
                            <input type="number" class="form-control<?= invalid_class('quota') ?>" id="quota" name="quota" min="1" max="200" value="<?= old('quota', $course['quota'] ?? $config->defaultQuota) ?>" required>
                            <?= field_error('quota') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="schedule">Jadwal</label>
                            <input type="text" class="form-control<?= invalid_class('schedule') ?>" id="schedule" name="schedule" value="<?= old('schedule', $course['schedule'] ?? '') ?>" placeholder="Senin 07.00-09.30">
                            <?= field_error('schedule') ?>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="room">Ruang</label>
                            <input type="text" class="form-control<?= invalid_class('room') ?>" id="room" name="room" value="<?= old('room', $course['room'] ?? '') ?>">
                            <?= field_error('room') ?>
                        </div>
                    </div>
                    <p class="small text-muted">Mata kuliah semester ganjil (1, 3, 5, 7) ditawarkan pada semester Ganjil; semester genap ditawarkan pada semester Genap.</p>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
