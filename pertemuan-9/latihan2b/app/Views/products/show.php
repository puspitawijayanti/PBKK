<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('products?category=' . $product['category_id']) ?>"><?= esc($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-6">
            <div class="ratio ratio-4x3">
                <img src="<?= product_photo_url($product['photo']) ?>" alt="Foto <?= esc($product['name'], 'attr') ?>" class="object-fit-cover rounded-4 shadow-sm">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="small text-muted"><?= esc($product['category_name']) ?></div>
            <h1 class="fw-bold"><?= esc($product['name']) ?></h1>
            <div class="fs-3 fw-bold text-coffee mb-3"><?= rupiah($product['price']) ?> <span class="fs-6 text-muted fw-normal">/ <?= (int) $product['weight_gram'] ?> g</span></div>

            <table class="table table-sm small w-auto">
                <tbody>
                    <tr><th class="pe-4 fw-normal text-muted">Asal</th><td><?= esc($product['origin']) ?></td></tr>
                    <tr><th class="pe-4 fw-normal text-muted">Proses</th><td><?= esc(process_label($product['process'])) ?></td></tr>
                    <tr><th class="pe-4 fw-normal text-muted">Sangrai</th><td><?= esc(roast_label($product['roast_level'])) ?></td></tr>
                    <tr><th class="pe-4 fw-normal text-muted">Stok</th><td><?= (int) $product['stock'] > 0 ? (int) $product['stock'] . ' bungkus' : '<span class="text-danger">Habis</span>' ?></td></tr>
                </tbody>
            </table>

            <p class="text-muted"><?= nl2br(esc((string) $product['description'])) ?></p>

            <?php if ((int) $product['stock'] > 0) : ?>
                <form action="<?= site_url('cart') ?>" method="post" class="d-flex gap-2 align-items-end">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div>
                        <label for="quantity" class="form-label small text-muted">Jumlah</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="1" max="<?= min((int) $product['stock'], $shop->maxQuantityPerItem) ?>" value="1" style="width: 6rem">
                    </div>
                    <button type="submit" class="btn btn-coffee btn-lg"><i class="bi bi-bag-plus"></i> Tambah ke Keranjang</button>
                </form>
                <?php if ($inCart > 0) : ?>
                    <p class="small text-muted mt-2"><?= $inCart ?> bungkus sudah ada di <a href="<?= site_url('cart') ?>">keranjang</a>.</p>
                <?php endif ?>
            <?php else : ?>
                <button type="button" class="btn btn-secondary btn-lg" disabled>Stok habis</button>
            <?php endif ?>
        </div>
    </div>

    <?php if ($related !== []) : ?>
        <h2 class="h5 fw-bold mt-5 mb-3">Kopi lain di kategori ini</h2>
        <div class="row g-4">
            <?php foreach ($related as $item) : ?>
                <div class="col-6 col-md-3"><?= view('products/_card', ['product' => $item]) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
