<?php /** @var array $p */ ?>
<div class="product-card d-flex flex-column">
    <a href="<?= site_url('product/' . $p['slug']) ?>">
        <img class="thumb" src="<?= product_image_url($p['image']) ?>" alt="<?= esc($p['name']) ?>" loading="lazy">
    </a>
    <div class="p-3 d-flex flex-column flex-grow-1">
        <div class="small text-muted mb-1"><?= esc($p['category_name'] ?? '') ?></div>
        <a class="name mb-1" href="<?= site_url('product/' . $p['slug']) ?>"><?= esc($p['name']) ?></a>
        <div class="price mb-2"><?= rupiah($p['price']) ?></div>
        <div class="mt-auto d-flex justify-content-between align-items-center">
            <?php if ((int) $p['stock'] > 0): ?>
                <span class="small text-muted">Stok <?= (int) $p['stock'] ?></span>
                <a href="<?= site_url('product/' . $p['slug']) ?>" class="btn btn-sm btn-outline-primary">Lihat</a>
            <?php else: ?>
                <span class="badge bg-secondary">Stok habis</span>
                <a href="<?= site_url('product/' . $p['slug']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
            <?php endif; ?>
        </div>
    </div>
</div>
