<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors  = session('errors') ?? [];
$value   = static fn (string $field, mixed $default = '') => old($field, $product[$field] ?? $default);
$invalid = static fn (string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$maxMb   = config(\Config\Shop::class)->maxProductSizeKb / 1024;
?>
<a class="small text-decoration-none" href="<?= url_to('admin.products') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>

<form class="card border-0 shadow-sm mt-3" method="post" enctype="multipart/form-data" action="<?= $product ? url_to('admin.product.update', $product['id']) : url_to('admin.product.create') ?>" data-once>
    <?= csrf_field() ?>
    <div class="card-body row g-3">
        <div class="col-lg-8 row g-3 m-0 p-0">
            <div class="col-md-7">
                <label class="form-label" for="name">Nama produk</label>
                <input class="form-control<?= $invalid('name') ?>" id="name" name="name" value="<?= esc($value('name'), 'attr') ?>" required maxlength="150">
                <?php if (isset($errors['name'])) : ?><div class="invalid-feedback"><?= esc($errors['name']) ?></div><?php endif ?>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="slug">Slug</label>
                <input class="form-control<?= $invalid('slug') ?>" id="slug" name="slug" value="<?= esc($value('slug'), 'attr') ?>" maxlength="170" aria-describedby="slugHelp">
                <div id="slugHelp" class="<?= isset($errors['slug']) ? 'invalid-feedback' : 'form-text' ?>"><?= esc($errors['slug'] ?? 'Kosongkan untuk dibuat otomatis.') ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="category_id">Kategori</label>
                <select class="form-select<?= $invalid('category_id') ?>" id="category_id" name="category_id" required>
                    <option value="">Pilih kategori</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['id'] ?>" <?= (int) $value('category_id') === $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?><?= $category['is_active'] ? '' : ' (nonaktif)' ?></option>
                    <?php endforeach ?>
                </select>
                <?php if (isset($errors['category_id'])) : ?><div class="invalid-feedback"><?= esc($errors['category_id']) ?></div><?php endif ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="active" <?= $value('status', 'active') === 'active' ? 'selected' : '' ?>>Aktif (tampil di katalog)</option>
                    <option value="inactive" <?= $value('status', 'active') === 'inactive' ? 'selected' : '' ?>>Nonaktif (disembunyikan)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="price">Harga (Rp)</label>
                <input class="form-control<?= $invalid('price') ?>" type="number" id="price" name="price" value="<?= esc((string) ($value('price') === '' ? '' : (float) $value('price')), 'attr') ?>" min="0" step="100" required>
                <?php if (isset($errors['price'])) : ?><div class="invalid-feedback"><?= esc($errors['price']) ?></div><?php endif ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="stock">Stok</label>
                <input class="form-control<?= $invalid('stock') ?>" type="number" id="stock" name="stock" value="<?= esc((string) $value('stock', 0), 'attr') ?>" min="0" step="1" required>
                <?php if (isset($errors['stock'])) : ?><div class="invalid-feedback"><?= esc($errors['stock']) ?></div><?php endif ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="5" maxlength="5000"><?= esc($value('description')) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="icon">Ikon cadangan</label>
                <input class="form-control<?= $invalid('icon') ?>" id="icon" name="icon" value="<?= esc($value('icon', 'bi-box'), 'attr') ?>" aria-describedby="iconHelp">
                <div id="iconHelp" class="form-text">Ditampilkan jika belum ada gambar. Contoh: <code>bi-earbuds</code>.</div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <div class="form-check form-switch mt-md-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?= $value('is_featured') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_featured">Tampilkan sebagai produk unggulan</label>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <label class="form-label" for="image">Gambar produk</label>
            <div class="mb-2 image-box">
                <?php if (! empty($product['image'])) : ?>
                    <img id="imagePreview" class="img-fluid rounded border" src="<?= base_url('uploads/products/' . $product['image']) ?>" alt="Gambar saat ini">
                <?php else : ?>
                    <img id="imagePreview" class="img-fluid rounded border d-none" alt="Pratinjau gambar">
                <?php endif ?>
            </div>
            <input class="form-control<?= $invalid('image') ?>" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="imagePreview" aria-describedby="imageHelp">
            <div id="imageHelp" class="<?= isset($errors['image']) ? 'invalid-feedback' : 'form-text' ?>"><?= esc($errors['image'] ?? 'JPG, PNG, atau WEBP, maksimal ' . $maxMb . ' MB.') ?></div>
            <?php if (! empty($product['image'])) : ?>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                    <label class="form-check-label small" for="remove_image">Hapus gambar saat ini</label>
                </div>
            <?php endif ?>
        </div>

        <div class="col-12">
            <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Simpan produk</button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
