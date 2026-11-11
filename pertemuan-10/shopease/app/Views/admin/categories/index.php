<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-end mb-3">
    <a class="btn btn-primary" href="<?= url_to('admin.category.new') ?>"><i class="bi bi-plus-lg me-1"></i>Tambah kategori</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Kategori</th><th scope="col">Slug</th><th scope="col" class="text-center">Produk</th><th scope="col">Status</th><th scope="col" class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) : ?>
                    <tr>
                        <td><i class="bi <?= esc($category['icon'], 'attr') ?> me-2 text-primary"></i><strong><?= esc($category['name']) ?></strong>
                            <div class="small text-body-secondary"><?= esc($category['description'] ?? '') ?></div></td>
                        <td><code><?= esc($category['slug']) ?></code></td>
                        <td class="text-center"><?= $category['product_count'] ?></td>
                        <td><?= $category['is_active'] ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>' ?></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url_to('admin.category.edit', $category['id']) ?>"><i class="bi bi-pencil"></i><span class="visually-hidden">Ubah <?= esc($category['name']) ?></span></a>
                            <form class="d-inline" action="<?= url_to('admin.category.delete', $category['id']) ?>" method="post" data-confirm="Hapus kategori <?= esc($category['name'], 'attr') ?>?">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i><span class="visually-hidden">Hapus <?= esc($category['name']) ?></span></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pagerLinks ?></div>
<?= $this->endSection() ?>
