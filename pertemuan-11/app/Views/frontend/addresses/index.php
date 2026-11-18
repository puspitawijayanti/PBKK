<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 fw-bold mb-0">Alamat Pengiriman</h1>
    <a href="<?= site_url('account/addresses/create') ?>" class="btn btn-primary">+ Tambah Alamat</a>
</div>

<?php if (! $addresses): ?>
    <div class="se-card p-5 text-center text-muted">Belum ada alamat tersimpan.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($addresses as $a): ?>
            <div class="col-md-6">
                <div class="se-card p-3 h-100 d-flex flex-column">
                    <div class="mb-2">
                        <strong><?= esc($a['recipient_name']) ?></strong>
                        <?php if ($a['is_default']): ?><span class="badge bg-primary ms-1">Utama</span><?php endif; ?>
                        <div class="text-muted small"><?= esc($a['phone']) ?></div>
                    </div>
                    <div class="small mb-3"><?= esc($a['address']) ?>, <?= esc($a['city']) ?>, <?= esc($a['province']) ?> <?= esc($a['postal_code']) ?></div>
                    <div class="mt-auto d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('account/addresses/' . $a['id'] . '/edit') ?>">Ubah</a>
                        <?php if (! $a['is_default']): ?>
                            <form action="<?= site_url('account/addresses/' . $a['id'] . '/default') ?>" method="post"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary" type="submit">Jadikan utama</button></form>
                        <?php endif; ?>
                        <form action="<?= site_url('account/addresses/' . $a['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus alamat ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button></form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
