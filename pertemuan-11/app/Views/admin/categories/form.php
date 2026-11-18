<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $c = $category; $action = $c ? site_url('admin/categories/' . $c['id']) : site_url('admin/categories'); ?>
<h1 class="h3 fw-bold mb-3"><?= $c ? 'Ubah Kategori' : 'Tambah Kategori' ?></h1>
<div class="se-card p-4" style="max-width:640px">
    <form action="<?= $action ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="name">Nama kategori</label>
            <input class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= esc(old('name', $c['name'] ?? '')) ?>" required>
            <?= field_error('name') ?>
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Deskripsi</label>
            <textarea class="form-control<?= invalid_class('description') ?>" id="description" name="description" rows="3"><?= esc(old('description', $c['description'] ?? '')) ?></textarea>
            <?= field_error('description') ?>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= old('is_active', $c ? $c['is_active'] : 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Aktif (tampil di katalog)</label>
        </div>
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/categories') ?>">Batal</a>
    </form>
</div>
<?= $this->endSection() ?>
