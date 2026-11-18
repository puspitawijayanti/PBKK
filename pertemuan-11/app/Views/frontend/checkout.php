<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Checkout</h1>

<?php if ($problems): ?>
    <div class="alert alert-warning">
        <strong>Perbaiki keranjang sebelum melanjutkan:</strong>
        <ul class="mb-2"><?php foreach ($problems as $p): ?><li><?= esc($p) ?></li><?php endforeach; ?></ul>
        <a href="<?= site_url('cart') ?>" class="btn btn-sm btn-warning">Ke keranjang</a>
    </div>
<?php endif; ?>

<form action="<?= site_url('checkout') ?>" method="post" id="checkoutForm">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="se-card p-4 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold mb-0">1. Alamat Pengiriman</h2>
                    <a href="<?= site_url('account/addresses/create?return=checkout') ?>" class="btn btn-sm btn-outline-primary">+ Alamat baru</a>
                </div>
                <?php if (! $addresses): ?>
                    <div class="alert alert-info mb-0">Anda belum memiliki alamat. Tambahkan alamat pengiriman terlebih dahulu.</div>
                <?php else: ?>
                    <?php
                    $selected = (int) old('address_id');
                    if (! $selected) {
                        $selected = (int) $addresses[0]['id'];
                        foreach ($addresses as $a) {
                            if ($a['is_default']) { $selected = (int) $a['id']; break; }
                        }
                    }
                    ?>
                    <?php foreach ($addresses as $a): ?>
                        <div class="form-check border rounded-3 p-3 ps-5 mb-2">
                            <input class="form-check-input" type="radio" name="address_id" id="addr<?= $a['id'] ?>" value="<?= $a['id'] ?>" <?= $selected === (int) $a['id'] ? 'checked' : '' ?> required>
                            <label class="form-check-label w-100" for="addr<?= $a['id'] ?>">
                                <strong><?= esc($a['recipient_name']) ?></strong> <span class="text-muted">&middot; <?= esc($a['phone']) ?></span>
                                <?php if ($a['is_default']): ?><span class="badge bg-primary ms-1">Utama</span><?php endif; ?>
                                <div class="small text-muted"><?= esc($a['address']) ?>, <?= esc($a['city']) ?>, <?= esc($a['province']) ?> <?= esc($a['postal_code']) ?></div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <?= field_error('address_id') ?>
                <?php endif; ?>
            </div>

            <div class="se-card p-4">
                <h2 class="h6 fw-bold">2. Catatan <span class="text-muted fw-normal small">(opsional)</span></h2>
                <textarea name="notes" class="form-control<?= invalid_class('notes') ?>" rows="2" maxlength="500" placeholder="Contoh: kirim sore hari"><?= esc(old('notes')) ?></textarea>
                <?= field_error('notes') ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="se-card p-4">
                <h2 class="h6 fw-bold">Ringkasan Pesanan</h2>
                <?php foreach ($items as $it): ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= esc($it['name']) ?> &times; <?= (int) $it['quantity'] ?></span>
                        <span><?= rupiah($it['line_total']) ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Subtotal</span><span><?= rupiah($subtotal) ?></span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Ongkos kirim</span><span><?= rupiah($shipping) ?></span></div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span class="price"><?= rupiah($total) ?></span></div>
                <p class="small text-muted mt-2">Total final dihitung ulang oleh sistem berdasarkan harga dan stok terbaru. Pembayaran dilakukan via transfer setelah pesanan dibuat.</p>
                <button type="submit" class="btn btn-primary w-100" <?= ($problems || ! $addresses) ? 'disabled' : '' ?>>Buat Pesanan</button>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// cegah submit ganda
document.getElementById('checkoutForm').addEventListener('submit', function () {
    var b = this.querySelector('button[type=submit]');
    b.disabled = true; b.textContent = 'Memproses...';
});
</script>
<?= $this->endSection() ?>
