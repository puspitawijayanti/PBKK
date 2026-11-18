<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$sorts = ['newest' => 'Terbaru', 'price_asc' => 'Harga terendah', 'price_desc' => 'Harga tertinggi', 'name' => 'Nama A-Z'];
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <h1 class="h3 fw-bold mb-0">Katalog Produk</h1>
        <div class="text-muted small"><?= (int) $total ?> produk ditemukan<?= $filters['q'] !== '' ? ' untuk "' . esc($filters['q']) . '"' : '' ?></div>
    </div>
</div>

<form method="get" action="<?= site_url('catalog') ?>" class="se-card p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label small mb-1">Cari</label>
            <input type="search" id="q" name="q" class="form-control" value="<?= esc($filters['q']) ?>" placeholder="Nama produk...">
        </div>
        <div class="col-md-3">
            <label for="category" class="form-label small mb-1">Kategori</label>
            <select id="category" name="category" class="form-select">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= esc($c['slug']) ?>" <?= $filters['category'] === $c['slug'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="sort" class="form-label small mb-1">Urutkan</label>
            <select id="sort" name="sort" class="form-select">
                <?php foreach ($sorts as $k => $label): ?>
                    <option value="<?= $k ?>" <?= ($filters['sort'] ?: 'newest') === $k ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('catalog') ?>" title="Reset">&times;</a>
        </div>
    </div>
</form>

<?php if ($products): ?>
    <div class="row g-3">
        <?php foreach ($products as $p): ?>
            <div class="col-6 col-md-4 col-lg-3"><?= view('partials/product_card', ['p' => $p]) ?></div>
        <?php endforeach; ?>
    </div>
    <div class="d-flex justify-content-center mt-4"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?php else: ?>
    <div class="se-card p-5 text-center text-muted">Produk tidak ditemukan. Coba kata kunci atau kategori lain.</div>
<?php endif; ?>
<?= $this->endSection() ?>
