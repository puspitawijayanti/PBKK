<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="fw-bold mb-4">Checkout</h1>
    <form action="<?= site_url('checkout') ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Alamat Pengiriman</h2>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label for="recipient_name" class="form-label">Nama Penerima</label>
                                <input type="text" class="form-control<?= invalid_class('recipient_name') ?>" id="recipient_name" name="recipient_name" value="<?= old('recipient_name', $user['name']) ?>" required>
                                <?= field_error('recipient_name') ?>
                            </div>
                            <div class="col-md-5">
                                <label for="phone" class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control<?= invalid_class('phone') ?>" id="phone" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>" required>
                                <?= field_error('phone') ?>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control<?= invalid_class('address') ?>" id="address" name="address" rows="3" required><?= old('address', $user['address'] ?? '') ?></textarea>
                                <?= field_error('address') ?>
                            </div>
                            <div class="col-md-8">
                                <label for="city" class="form-label">Kota / Kabupaten</label>
                                <input type="text" class="form-control<?= invalid_class('city') ?>" id="city" name="city" value="<?= old('city') ?>" required>
                                <?= field_error('city') ?>
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Kode Pos</label>
                                <input type="text" inputmode="numeric" class="form-control<?= invalid_class('postal_code') ?>" id="postal_code" name="postal_code" maxlength="5" value="<?= old('postal_code') ?>" required>
                                <?= field_error('postal_code') ?>
                            </div>
                            <div class="col-12">
                                <label for="note" class="form-label">Catatan (opsional)</label>
                                <input type="text" class="form-control<?= invalid_class('note') ?>" id="note" name="note" value="<?= old('note') ?>" placeholder="Contoh: digiling untuk V60">
                                <?= field_error('note') ?>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="save_address" name="save_address" checked>
                                    <label class="form-check-label small" for="save_address">Simpan nomor HP dan alamat ini ke profil saya</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Pesanan (<?= count($items) ?> produk)</h2>
                        <ul class="list-unstyled small mb-0">
                            <?php foreach ($items as $item) : ?>
                                <li class="d-flex justify-content-between mb-2">
                                    <span><?= esc($item['product']['name']) ?> &times; <?= $item['quantity'] ?><?= ! $item['enoughStock'] ? ' <span class="text-danger">(stok kurang)</span>' : '' ?></span>
                                    <span><?= rupiah($item['subtotal']) ?></span>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
                <?= view('cart/_summary', ['totals' => $totals, 'shop' => $shop]) ?>
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body small">
                        <h2 class="h6 fw-bold">Pembayaran: Transfer Bank</h2>
                        <p class="mb-0 text-muted">Setelah pesanan dibuat, transfer total tagihan ke rekening yang tertera dalam <?= $shop->paymentDeadlineHours ?> jam, lalu unggah bukti transfer.</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-coffee btn-lg w-100 mt-3">Buat Pesanan</button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
