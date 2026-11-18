<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 fw-bold mb-0">Produk</h1>
    <a href="<?= site_url('admin/products/create') ?>" class="btn btn-primary">+ Tambah Produk</a>
</div>

<form method="get" class="se-card p-3 mb-3">
    <div class="row g-2">
        <div class="col-md-4"><input type="search" name="q" class="form-control" placeholder="Cari nama produk..." value="<?= esc($filters['q']) ?>" aria-label="Cari"></div>
        <div class="col-md-3">
            <select name="category_id" class="form-select" aria-label="Kategori">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (int) $filters['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select" aria-label="Status">
                <option value="">Semua status</option>
                <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?= site_url('admin/products') ?>">&times;</a></div>
    </div>
</form>

<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th></th><th>Produk</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img class="table-thumb" src="<?= product_image_url($p['image']) ?>" alt=""></td>
                    <td class="fw-semibold"><?= esc($p['name']) ?></td>
                    <td><?= esc($p['category_name']) ?></td>
                    <td class="text-end"><?= rupiah($p['price']) ?></td>
                    <td class="text-center"><?= (int) $p['stock'] ?></td>
                    <td><?= $p['status'] === 'active' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= site_url('admin/products/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form action="<?= site_url('admin/products/' . $p['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus produk ini? Jika sudah pernah dipesan, produk hanya dinonaktifkan.')">
                            <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $products): ?><tr><td colspan="7" class="text-center text-muted py-4">Produk tidak ditemukan.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
