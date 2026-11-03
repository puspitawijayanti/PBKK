<?php $isEdit = $category !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/categories') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?= $isEdit ? site_url('admin/categories/' . $category['id']) : site_url('admin/categories') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="name">Nama Kategori</label>
                        <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $category['name'] ?? '') ?>" maxlength="80" required autofocus>
                        <?= field_error('name') ?>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
