<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/products/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/products') ?>" method="get" class="form-inline">
            <input type="search" name="q" class="form-control form-control-sm mr-2 mb-2 mb-sm-0" value="<?= esc($keyword, 'attr') ?>" placeholder="Nama atau asal" aria-label="Cari produk">
            <select name="category" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $id => $name) : ?>
                    <option value="<?= $id ?>"<?= $categoryId === (int) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                <?php endforeach ?>
            </select>
            <select name="sort" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Urutkan">
                <?php foreach (\App\Models\ProductModel::SORTS as $value => $label) : ?>
                    <option value="<?= $value ?>"<?= $sort === $value ? ' selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="thead-light"><tr><th>Foto</th><th>Produk</th><th>Kategori</th><th class="text-right">Harga</th><th class="text-center">Stok</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    <?php if ($products === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Produk tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td><img src="<?= product_photo_url($product['photo']) ?>" alt="" class="table-thumb rounded"></td>
                            <td>
                                <div class="font-weight-bold text-gray-800"><?= esc($product['name']) ?></div>
                                <div class="small text-muted"><?= esc($product['origin']) ?> &middot; <?= (int) $product['weight_gram'] ?> g &middot; <?= esc(roast_label($product['roast_level'])) ?></div>
                            </td>
                            <td class="small"><?= esc($product['category_name']) ?></td>
                            <td class="text-right"><?= rupiah($product['price']) ?></td>
                            <td class="text-center"><span class="badge badge-<?= (int) $product['stock'] > 5 ? 'success' : ((int) $product['stock'] > 0 ? 'warning' : 'danger') ?>"><?= (int) $product['stock'] ?></span></td>
                            <td><?= (int) $product['is_active'] === 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-sm btn-warning" aria-label="Ubah"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/products/' . $product['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus produk <?= esc($product['name'], 'js') ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger" aria-label="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
        <span class="small text-muted">Total <?= $pager->getTotal() ?> produk</span>
        <?= $pager->links('default', 'bootstrap') ?>
    </div>
</div>
<?= $this->endSection() ?>
