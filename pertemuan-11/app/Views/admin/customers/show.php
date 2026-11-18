<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= site_url('admin/customers') ?>">Pelanggan</a></li><li class="breadcrumb-item active"><?= esc($customer['name']) ?></li></ol></nav>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="se-card p-4 mb-3">
            <h2 class="h6 fw-bold">Informasi Akun</h2>
            <dl class="mb-0 small">
                <dt>Nama</dt><dd><?= esc($customer['name']) ?></dd>
                <dt>Email</dt><dd><?= esc($customer['email']) ?></dd>
                <dt>Telepon</dt><dd><?= esc((string) $customer['phone']) ?: '-' ?></dd>
                <dt>Status akun</dt><dd><?= $customer['is_active'] ? 'Aktif' : 'Nonaktif' ?></dd>
                <dt>Terdaftar</dt><dd class="mb-0"><?= tgl($customer['created_at']) ?></dd>
            </dl>
        </div>
        <div class="se-card p-4">
            <h2 class="h6 fw-bold">Alamat</h2>
            <?php foreach ($addresses as $a): ?>
                <div class="small mb-2"><strong><?= esc($a['recipient_name']) ?></strong> <?= $a['is_default'] ? '<span class="badge bg-primary">Utama</span>' : '' ?><br><?= esc($a['address']) ?>, <?= esc($a['city']) ?>, <?= esc($a['province']) ?> <?= esc($a['postal_code']) ?></div>
            <?php endforeach; ?>
            <?php if (! $addresses): ?><p class="small text-muted mb-0">Belum ada alamat.</p><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="se-card p-0 overflow-hidden">
            <div class="p-3 pb-2"><h2 class="h6 fw-bold mb-0">Riwayat Pesanan</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>No. Pesanan</th><th>Tanggal</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr><td><?= esc($o['order_number']) ?></td><td><?= tgl($o['order_date']) ?></td><td class="text-end"><?= rupiah($o['total']) ?></td><td><?= order_status_badge($o['order_status']) ?></td><td class="text-end"><a href="<?= site_url('admin/orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td></tr>
                    <?php endforeach; ?>
                    <?php if (! $orders): ?><tr><td colspan="5" class="text-center text-muted py-3">Belum ada pesanan.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
