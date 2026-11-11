<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $user = current_user() ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= url_to('home') ?>">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= url_to('category', $product['category_slug']) ?>"><?= esc($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5">
        <div class="col-md-6">
            <?= product_thumb($product, 'product-thumb-lg rounded-4') ?>
        </div>
        <div class="col-md-6">
            <a class="badge text-bg-light text-decoration-none mb-2" href="<?= url_to('category', $product['category_slug']) ?>">
                <i class="bi <?= esc($product['category_icon'], 'attr') ?> me-1"></i><?= esc($product['category_name']) ?>
            </a>
            <h1 class="h2 fw-bold"><?= esc($product['name']) ?></h1>
            <p class="display-6 fw-bold text-primary mb-3"><?= rupiah($product['price']) ?></p>

            <p class="mb-3">
                <?php if ($product['stock'] > 0) : ?>
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Stok tersedia: <strong><?= $product['stock'] ?></strong></span>
                <?php else : ?>
                    <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Stok habis</span>
                <?php endif ?>
            </p>

            <h2 class="h6 fw-bold">Deskripsi</h2>
            <p class="text-body-secondary"><?= nl2br(esc($product['description'] ?? '')) ?></p>

            <div class="border rounded-3 p-3 bg-white">
                <?php if ($user !== null && $user['role'] !== 'customer') : ?>
                    <p class="small text-body-secondary mb-0"><i class="bi bi-info-circle me-1"></i>Anda masuk sebagai <?= role_label($user['role']) ?>. Pembelian hanya untuk akun customer.</p>
                <?php else : ?>
                    <form action="<?= url_to('cart.add') ?>" method="post" data-once>
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-3">
                                <label class="form-label small" for="qty">Jumlah</label>
                                <input class="form-control" type="number" id="qty" name="quantity" value="1" min="1" max="<?= max(1, $product['stock']) ?>" required <?= $product['stock'] === 0 ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-6 col-sm">
                                <button class="btn btn-outline-primary w-100" type="submit" <?= $product['stock'] === 0 ? 'disabled' : '' ?>>
                                    <i class="bi bi-cart-plus me-1"></i>Keranjang
                                </button>
                            </div>
                            <div class="col-6 col-sm">
                                <button class="btn btn-primary w-100" type="submit" name="buy_now" value="1" <?= $product['stock'] === 0 ? 'disabled' : '' ?>>
                                    Beli sekarang
                                </button>
                            </div>
                        </div>
                        <?php if ($user === null) : ?>
                            <p class="small text-body-secondary mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Anda akan diminta masuk terlebih dahulu.</p>
                        <?php endif ?>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?php if ($related !== []) : ?>
        <section class="mt-5" aria-labelledby="judulTerkait">
            <h2 id="judulTerkait" class="h5 fw-bold mb-3">Produk lain di <?= esc($product['category_name']) ?></h2>
            <div class="row row-cols-2 row-cols-md-4 g-3">
                <?php foreach ($related as $item) : ?>
                    <div class="col"><?= view('frontend/products/_card', ['product' => $item]) ?></div>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
