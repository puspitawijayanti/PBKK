<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Pelanggan</h1>
<form method="get" class="mb-3" style="max-width:420px">
    <div class="input-group"><input type="search" class="form-control" name="q" value="<?= esc($q) ?>" placeholder="Cari nama / email..." aria-label="Cari"><button class="btn btn-primary" type="submit">Cari</button></div>
</form>
<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Terdaftar</th><th class="text-center">Pesanan</th><th class="text-end">Total belanja</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($customers as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($c['name']) ?> <?= $c['is_active'] ? '' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                    <td><?= esc($c['email']) ?></td>
                    <td><?= esc((string) $c['phone']) ?: '-' ?></td>
                    <td><?= tgl($c['created_at'], false) ?></td>
                    <td class="text-center"><?= (int) $c['total_orders'] ?></td>
                    <td class="text-end"><?= rupiah($c['total_spent']) ?></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/customers/' . $c['id']) ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $customers): ?><tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pelanggan.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
