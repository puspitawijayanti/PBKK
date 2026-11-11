<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<form class="card card-body border-0 shadow-sm mb-3" method="get" action="<?= url_to('admin.products') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small" for="q">Cari nama produk</label>
            <input class="form-control" type="search" id="q" name="q" value="<?= esc($keyword, 'attr') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small" for="category">Kategori</label>
            <select class="form-select" id="category" name="category">
                <option value="">Semua</option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['id'] ?>" <?= $categoryId === $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">Semua</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
        <div class="col-6 col-md-1"><button class="btn btn-outline-primary w-100" type="submit" aria-label="Filter"><i class="bi bi-funnel"></i></button></div>
        <div class="col-6 col-md-2"><a class="btn btn-primary w-100" href="<?= url_to('admin.product.new') ?>"><i class="bi bi-plus-lg me-1"></i>Tambah</a></div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Produk</th><th scope="col">Kategori</th><th scope="col" class="text-end">Harga</th><th scope="col" class="text-center">Stok</th><th scope="col">Status</th><th scope="col" class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="table-thumb"><?= product_thumb($product, 'rounded-2') ?></span>
                                <div>
                                    <strong><?= esc($product['name']) ?></strong>
                                    <?php if ($product['is_featured']) : ?><span class="badge text-bg-warning ms-1">Unggulan</span><?php endif ?>
                                    <div class="small text-body-secondary"><code><?= esc($product['slug']) ?></code></div>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($product['category_name']) ?></td>
                        <td class="text-end text-nowrap"><?= rupiah($product['price']) ?></td>
                        <td class="text-center<?= $product['stock'] < 10 ? ' text-danger fw-bold' : '' ?>"><?= $product['stock'] ?></td>
                        <td><?= $product['status'] === 'active' ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>' ?></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url_to('admin.product.edit', $product['id']) ?>"><i class="bi bi-pencil"></i><span class="visually-hidden">Ubah <?= esc($product['name']) ?></span></a>
                            <form class="d-inline" action="<?= url_to('admin.product.delete', $product['id']) ?>" method="post" data-confirm="Hapus <?= esc($product['name'], 'attr') ?>? Produk yang sudah pernah dipesan hanya akan dinonaktifkan.">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i><span class="visually-hidden">Hapus <?= esc($product['name']) ?></span></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($products === []) : ?>
                    <tr><td colspan="6" class="text-center text-body-secondary py-4">Tidak ada produk yang cocok.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pagerLinks ?></div>
<?= $this->endSection() ?>
