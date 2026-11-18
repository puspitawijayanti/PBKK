<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $p = $product; $action = $p ? site_url('admin/products/' . $p['id']) : site_url('admin/products'); $v = fn ($f, $d = '') => old($f, $p[$f] ?? $d); ?>
<h1 class="h3 fw-bold mb-3"><?= $p ? 'Ubah Produk' : 'Tambah Produk' ?></h1>
<div class="se-card p-4" style="max-width:820px">
    <form action="<?= $action ?>" method="post" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label" for="name">Nama produk</label>
                <input class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= esc($v('name')) ?>" required>
                <?= field_error('name') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="category_id">Kategori</label>
                <select class="form-select<?= invalid_class('category_id') ?>" id="category_id" name="category_id" required>
                    <option value="">Pilih...</option>
                    <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (int) $v('category_id') === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?>
                </select>
                <?= field_error('category_id') ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea class="form-control<?= invalid_class('description') ?>" id="description" name="description" rows="4"><?= esc($v('description')) ?></textarea>
                <?= field_error('description') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="price">Harga (Rp)</label>
                <input type="number" class="form-control<?= invalid_class('price') ?>" id="price" name="price" min="0" step="any" value="<?= esc($v('price') !== '' ? (string) (float) $v('price') : '') ?>" required>
                <?= field_error('price') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="stock">Stok</label>
                <input type="number" class="form-control<?= invalid_class('stock') ?>" id="stock" name="stock" min="0" step="1" value="<?= esc((string) $v('stock', 0)) ?>" required>
                <?= field_error('stock') ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select<?= invalid_class('status') ?>" id="status" name="status">
                    <option value="active" <?= $v('status', 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= $v('status', 'active') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
                <?= field_error('status') ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="image">Foto produk <span class="text-muted small">(JPG/PNG/WEBP, maks. 2 MB)</span></label>
                <?php if ($p && $p['image']): ?>
                    <div class="mb-2"><img src="<?= product_image_url($p['image']) ?>" alt="Foto saat ini" style="height:90px;border-radius:.6rem"> <span class="small text-muted ms-2">Unggah file baru untuk mengganti.</span></div>
                <?php endif; ?>
                <input class="form-control<?= invalid_class('image') ?>" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                <?= field_error('image') ?>
            </div>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/products') ?>">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
