<?php
$isEdit = $tool !== null;
$action = $isEdit ? site_url('admin/tools/' . $tool['id']) : site_url('admin/tools');
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/tools') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= $action ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Informasi Alat</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Alat</label>
                        <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $tool['name'] ?? '') ?>" maxlength="120" required>
                        <?= field_error('name') ?>
                        <?php if ($isEdit) : ?>
                            <small class="form-text text-muted">Slug saat ini: <code><?= esc($tool['slug']) ?></code>. Slug dibuat ulang jika nama diubah.</small>
                        <?php endif ?>
                        <?= field_error('slug') ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="category_id">Kategori</label>
                            <select class="custom-select<?= invalid_class('category_id') ?>" id="category_id" name="category_id" required>
                                <option value="">Pilih kategori</option>
                                <?php foreach ($categories as $id => $name) : ?>
                                    <option value="<?= $id ?>"<?= (string) old('category_id', $tool['category_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('category_id') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="stock">Stok</label>
                            <input type="number" class="form-control<?= invalid_class('stock') ?>" id="stock" name="stock" min="0" max="1000" value="<?= old('stock', $tool['stock'] ?? 1) ?>" required>
                            <?= field_error('stock') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="condition">Kondisi</label>
                            <select class="custom-select<?= invalid_class('condition') ?>" id="condition" name="condition">
                                <?php foreach (\App\Models\ToolModel::CONDITIONS as $condition) : ?>
                                    <option value="<?= $condition ?>"<?= old('condition', $tool['condition'] ?? 'good') === $condition ? ' selected' : '' ?>><?= esc(condition_label($condition)) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('condition') ?>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control<?= invalid_class('description') ?>" id="description" name="description" rows="6" maxlength="2000"><?= old('description', $tool['description'] ?? '') ?></textarea>
                        <?= field_error('description') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Foto Alat</h6></div>
                <div class="card-body text-center">
                    <img src="<?= tool_photo_url($tool['photo'] ?? null) ?>" id="photoPreview" data-placeholder="<?= base_url('assets/img/no-photo.svg') ?>" class="img-fluid rounded border mb-3 photo-preview" alt="Pratinjau foto">
                    <div class="custom-file text-left">
                        <input type="file" class="custom-file-input<?= invalid_class('photo') ?>" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" data-preview="photoPreview">
                        <label class="custom-file-label" for="photo" data-browse="Pilih">Pilih foto...</label>
                    </div>
                    <?= field_error('photo') ?>
                    <small class="form-text text-muted text-left">Format JPG, PNG, atau WEBP. Maksimal 2 MB. Nama file diacak otomatis.</small>
                    <?php if ($isEdit && ! empty($tool['photo'])) : ?>
                        <div class="custom-control custom-checkbox text-left mt-2">
                            <input type="checkbox" class="custom-control-input" id="remove_photo" name="remove_photo" value="1">
                            <label class="custom-control-label small" for="remove_photo">Hapus foto saat ini</label>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Alat' ?></button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/photo-preview.js') ?>"></script>
<?= $this->endSection() ?>
