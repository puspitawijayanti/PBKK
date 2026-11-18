<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('catalog') ?>">Katalog</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('catalog?category=' . urlencode($product['category_slug'])) ?>"><?= esc($product['category_name']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
    </ol>
</nav>

<div class="se-card p-3 p-md-4 mb-5">
    <div class="row g-4">
        <div class="col-md-5">
            <img src="<?= product_image_url($product['image']) ?>" alt="<?= esc($product['name']) ?>" class="img-fluid rounded-3 w-100" style="aspect-ratio:4/3;object-fit:cover">
        </div>
        <div class="col-md-7">
            <div class="text-muted small"><?= esc($product['category_name']) ?></div>
            <h1 class="h3 fw-bold"><?= esc($product['name']) ?></h1>
            <div class="h3 price mb-3"><?= rupiah($product['price']) ?></div>
            <p style="white-space:pre-line"><?= esc((string) $product['description']) ?></p>

            <p class="mb-3">
                <?php if ((int) $product['stock'] > 0): ?>
                    <span class="badge bg-success">Tersedia</span> <span class="text-muted">Stok: <?= (int) $product['stock'] ?></span>
                <?php else: ?>
                    <span class="badge bg-secondary">Stok habis</span>
                <?php endif; ?>
            </p>

            <?php if ((int) $product['stock'] > 0): ?>
                <?php if (! is_logged_in()): ?>
                    <a href="<?= site_url('login') ?>" class="btn btn-primary">Login untuk menambah ke keranjang</a>
                <?php elseif (current_role() !== 'customer'): ?>
                    <div class="alert alert-info mb-0">Akun <?= esc(role_label((string) current_role())) ?> tidak dapat berbelanja.</div>
                <?php else: ?>
                    <form action="<?= site_url('cart/add') ?>" method="post" class="d-flex gap-2 align-items-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <label for="qty" class="visually-hidden">Jumlah</label>
                        <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= (int) $product['stock'] ?>" class="form-control" style="width:100px" required>
                        <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($related): ?>
<section>
    <h2 class="h5 se-section-title mb-3">Produk Terkait</h2>
    <div class="row g-3">
        <?php foreach ($related as $p): ?>
            <div class="col-6 col-md-3"><?= view('partials/product_card', ['p' => $p]) ?></div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?= $this->endSection() ?>
