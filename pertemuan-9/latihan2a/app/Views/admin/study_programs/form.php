<?php $isEdit = $program !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/study-programs') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?= $isEdit ? site_url('admin/study-programs/' . $program['id']) : site_url('admin/study-programs') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="code">Kode</label>
                            <input type="text" class="form-control text-uppercase<?= invalid_class('code') ?>" id="code" name="code" value="<?= old('code', $program['code'] ?? '') ?>" maxlength="10" required>
                            <?= field_error('code') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="degree">Jenjang</label>
                            <select class="custom-select<?= invalid_class('degree') ?>" id="degree" name="degree">
                                <?php foreach (\App\Models\StudyProgramModel::DEGREES as $degree) : ?>
                                    <option value="<?= $degree ?>"<?= old('degree', $program['degree'] ?? 'S1') === $degree ? ' selected' : '' ?>><?= $degree ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('degree') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama Program Studi</label>
                        <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $program['name'] ?? '') ?>" maxlength="100" required>
                        <?= field_error('name') ?>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
