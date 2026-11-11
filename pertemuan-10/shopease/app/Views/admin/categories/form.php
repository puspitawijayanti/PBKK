<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$value  = static fn (string $field, mixed $default = '') => old($field, $category[$field] ?? $default);
?>
<a class="small text-decoration-none" href="<?= url_to('admin.categories') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>

<form class="card border-0 shadow-sm mt-3 form-narrow" method="post" action="<?= $category ? url_to('admin.category.update', $category['id']) : url_to('admin.category.create') ?>" data-once>
    <?= csrf_field() ?>
    <div class="card-body row g-3">
        <div class="col-md-7">
            <label class="form-label" for="name">Nama kategori</label>
            <input class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc($value('name'), 'attr') ?>" required maxlength="100">
            <?php if (isset($errors['name'])) : ?><div class="invalid-feedback"><?= esc($errors['name']) ?></div><?php endif ?>
        </div>
        <div class="col-md-5">
            <label class="form-label" for="slug">Slug</label>
            <input class="form-control<?= isset($errors['slug']) ? ' is-invalid' : '' ?>" id="slug" name="slug" value="<?= esc($value('slug'), 'attr') ?>" maxlength="120" aria-describedby="slugHelp">
            <div id="slugHelp" class="<?= isset($errors['slug']) ? 'invalid-feedback' : 'form-text' ?>"><?= esc($errors['slug'] ?? 'Kosongkan untuk dibuat otomatis dari nama.') ?></div>
        </div>
        <div class="col-md-7">
            <label class="form-label" for="icon">Ikon (Bootstrap Icons)</label>
            <input class="form-control<?= isset($errors['icon']) ? ' is-invalid' : '' ?>" id="icon" name="icon" value="<?= esc($value('icon', 'bi-tag'), 'attr') ?>" placeholder="bi-tag" aria-describedby="iconHelp">
            <div id="iconHelp" class="form-text">Contoh: <code>bi-headphones</code>, <code>bi-handbag</code>.</div>
        </div>
        <div class="col-md-5">
            <label class="form-label" for="is_active">Status</label>
            <select class="form-select" id="is_active" name="is_active">
                <option value="1" <?= (string) (int) $value('is_active', 1) === '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= (string) (int) $value('is_active', 1) === '0' ? 'selected' : '' ?>>Nonaktif (produk di dalamnya disembunyikan)</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"><?= esc($value('description')) ?></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Simpan</button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
