<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 fw-bold mb-0">Kategori Produk</h1>
    <a href="<?= site_url('admin/categories/create') ?>" class="btn btn-primary">+ Tambah Kategori</a>
</div>
<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>No</th><th>Nama</th><th>Slug</th><th class="text-center">Produk</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php $no = ($pager->getCurrentPage() - 1) * config('Shop')->adminPerPage; ?>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td class="fw-semibold"><?= esc($c['name']) ?><?php if ($c['description']): ?><div class="small text-muted fw-normal"><?= esc($c['description']) ?></div><?php endif; ?></td>
                    <td class="text-muted"><?= esc($c['slug']) ?></td>
                    <td class="text-center"><?= (int) $c['product_count'] ?></td>
                    <td><?= $c['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                    <td class="text-end">
                        <a href="<?= site_url('admin/categories/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form action="<?= site_url('admin/categories/' . $c['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori &quot;<?= esc($c['name'], 'js') ?>&quot;?')">
                            <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $categories): ?><tr><td colspan="6" class="text-center text-muted py-4">Belum ada kategori.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
