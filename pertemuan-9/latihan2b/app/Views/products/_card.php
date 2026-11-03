<div class="card product-card h-100 border-0 shadow-sm">
    <a href="<?= site_url('products/' . $product['slug']) ?>" class="ratio ratio-4x3 product-thumb">
        <img src="<?= product_photo_url($product['photo']) ?>" alt="Foto <?= esc($product['name'], 'attr') ?>" loading="lazy" class="object-fit-cover rounded-top">
    </a>
    <div class="card-body d-flex flex-column">
        <div class="small text-muted mb-1"><?= esc($product['category_name']) ?> &middot; <?= esc($product['origin']) ?></div>
        <h3 class="h6 fw-bold mb-2"><a href="<?= site_url('products/' . $product['slug']) ?>" class="text-reset text-decoration-none"><?= esc($product['name']) ?></a></h3>
        <div class="small mb-3"><span class="badge text-bg-light"><?= esc(roast_label($product['roast_level'])) ?></span> <span class="badge text-bg-light"><?= (int) $product['weight_gram'] ?> g</span></div>
        <div class="mt-auto d-flex justify-content-between align-items-center">
            <span class="fw-bold text-coffee"><?= rupiah($product['price']) ?></span>
            <?php if ((int) $product['stock'] > 0) : ?>
                <form action="<?= site_url('cart') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-sm btn-coffee" aria-label="Tambah <?= esc($product['name'], 'attr') ?> ke keranjang"><i class="bi bi-bag-plus"></i></button>
                </form>
            <?php else : ?>
                <span class="badge text-bg-secondary">Habis</span>
            <?php endif ?>
        </div>
    </div>
</div>
