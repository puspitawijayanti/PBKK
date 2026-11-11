<?php
/** @var array $product */
$url = url_to('product.show', $product['slug']);
?>
<article class="card product-card h-100">
    <a href="<?= $url ?>" class="position-relative d-block text-decoration-none" tabindex="-1" aria-hidden="true">
        <?= product_thumb($product) ?>
        <?php if ($product['stock'] === 0) : ?>
            <span class="badge text-bg-secondary position-absolute top-0 start-0 m-2">Stok habis</span>
        <?php elseif ($product['stock'] < 10) : ?>
            <span class="badge text-bg-warning position-absolute top-0 start-0 m-2">Sisa <?= $product['stock'] ?></span>
        <?php endif ?>
    </a>
    <div class="card-body d-flex flex-column">
        <p class="small text-body-secondary mb-1"><?= esc($product['category_name']) ?></p>
        <h3 class="h6 card-title mb-2">
            <a class="stretched-link text-reset text-decoration-none" href="<?= $url ?>"><?= esc($product['name']) ?></a>
        </h3>
        <p class="fw-bold text-primary mt-auto mb-0"><?= rupiah($product['price']) ?></p>
    </div>
</article>
