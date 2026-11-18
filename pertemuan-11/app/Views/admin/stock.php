<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Manajemen Stok</h1>
<form method="get" class="mb-3" style="max-width:420px">
    <div class="input-group"><input type="search" class="form-control" name="q" value="<?= esc($q) ?>" placeholder="Cari produk..." aria-label="Cari"><button class="btn btn-primary" type="submit">Cari</button></div>
</form>
<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Produk</th><th>Kategori</th><th>Status</th><th class="text-center">Stok saat ini</th><th style="width:240px">Atur stok</th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($p['name']) ?></td>
                    <td><?= esc($p['category_name']) ?></td>
                    <td><?= $p['status'] === 'active' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                    <td class="text-center"><span class="badge bg-<?= (int) $p['stock'] <= 5 ? ((int) $p['stock'] === 0 ? 'danger' : 'warning text-dark') : 'light text-dark border' ?>"><?= (int) $p['stock'] ?></span></td>
                    <td>
                        <form action="<?= site_url('admin/stock/' . $p['id']) ?>" method="post" class="d-flex gap-2">
                            <?= csrf_field() ?>
                            <input type="number" name="stock" min="0" step="1" value="<?= (int) $p['stock'] ?>" class="form-control form-control-sm" aria-label="Stok <?= esc($p['name']) ?>" required>
                            <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
