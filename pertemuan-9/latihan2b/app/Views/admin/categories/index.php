<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/categories/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kategori</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light"><tr><th>Nama</th><th>Slug</th><th>Deskripsi</th><th class="text-center">Produk</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td class="font-weight-bold text-gray-800"><?= esc($category['name']) ?></td>
                            <td><code><?= esc($category['slug']) ?></code></td>
                            <td class="small"><?= esc($category['description'] ?? '') ?></td>
                            <td class="text-center"><a href="<?= site_url('admin/products?category=' . $category['id']) ?>"><?= (int) $category['product_count'] ?></a></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-warning" aria-label="Ubah"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/categories/' . $category['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori <?= esc($category['name'], 'js') ?>?')">
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
    <div class="card-footer bg-white"><?= $pager->links('default', 'bootstrap') ?></div>
</div>
<?= $this->endSection() ?>
