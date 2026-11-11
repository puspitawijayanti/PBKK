<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$selected = (int) (session()->getFlashdata('selected_address') ?? old('address_id') ?? ($addresses[0]['id'] ?? 0));
$formOpen = $addresses === [] || session()->getFlashdata('address_form_open');
?>
<div class="container py-4">
    <h1 class="h3 fw-bold mb-4">Checkout</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm mb-4" aria-labelledby="judulAlamat">
                <div class="card-body">
                    <h2 id="judulAlamat" class="h6 fw-bold mb-3"><i class="bi bi-geo-alt me-1"></i>Alamat pengiriman</h2>

                    <?php if ($addresses === []) : ?>
                        <p class="small text-body-secondary">Anda belum memiliki alamat. Tambahkan alamat pengiriman terlebih dahulu.</p>
                    <?php endif ?>

                    <?php foreach ($addresses as $address) : ?>
                        <div class="address-option d-flex gap-3 align-items-start border rounded-3 p-3 mb-2">
                            <input class="form-check-input mt-1" type="radio" name="address_id" form="checkoutForm" id="addr<?= $address['id'] ?>" value="<?= $address['id'] ?>" <?= $selected === $address['id'] ? 'checked' : '' ?> required>
                            <label class="flex-grow-1" for="addr<?= $address['id'] ?>">
                                <strong><?= esc($address['recipient_name']) ?></strong> &middot; <?= esc($address['phone']) ?>
                                <?php if ($address['is_default']) : ?><span class="badge text-bg-light ms-1">Utama</span><?php endif ?>
                                <span class="d-block small text-body-secondary"><?= esc(\App\Models\AddressModel::format($address)) ?></span>
                            </label>
                            <form action="<?= url_to('address.delete', $address['id']) ?>" method="post" data-confirm="Hapus alamat ini?">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-link text-danger p-0" type="submit" aria-label="Hapus alamat <?= esc($address['recipient_name'], 'attr') ?>"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    <?php endforeach ?>

                    <details class="mt-3" <?= $formOpen ? 'open' : '' ?>>
                        <summary class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Tambah alamat baru</summary>
                        <form class="row g-2 mt-2" action="<?= url_to('address.create') ?>" method="post" data-once>
                            <?= csrf_field() ?>
                            <div class="col-sm-6">
                                <label class="form-label small" for="recipient_name">Nama penerima</label>
                                <input class="form-control" id="recipient_name" name="recipient_name" value="<?= esc(old('recipient_name', current_user()['name']), 'attr') ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small" for="phone">Nomor HP</label>
                                <input class="form-control" type="tel" id="phone" name="phone" value="<?= esc(old('phone', ''), 'attr') ?>" placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small" for="address">Alamat lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan" required><?= esc(old('address', '')) ?></textarea>
                            </div>
                            <div class="col-sm-5">
                                <label class="form-label small" for="city">Kota/Kabupaten</label>
                                <input class="form-control" id="city" name="city" value="<?= esc(old('city', ''), 'attr') ?>" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label small" for="province">Provinsi</label>
                                <input class="form-control" id="province" name="province" value="<?= esc(old('province', ''), 'attr') ?>" required>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label small" for="postal_code">Kode pos</label>
                                <input class="form-control" id="postal_code" name="postal_code" value="<?= esc(old('postal_code', ''), 'attr') ?>" inputmode="numeric" maxlength="5" required>
                            </div>
                            <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Simpan alamat</button></div>
                        </form>
                    </details>
                </div>
            </section>

            <section class="card border-0 shadow-sm" aria-labelledby="judulItem">
                <div class="card-body">
                    <h2 id="judulItem" class="h6 fw-bold mb-3"><i class="bi bi-bag me-1"></i>Produk dipesan</h2>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($items as $item) : ?>
                            <li class="d-flex justify-content-between gap-2 py-2 border-bottom">
                                <span><?= esc($item['name']) ?> <span class="text-body-secondary">&times; <?= $item['quantity'] ?></span></span>
                                <span class="text-nowrap"><?= rupiah($item['subtotal']) ?></span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <form id="checkoutForm" class="card border-0 shadow-sm sticky-lg-top summary-card" action="<?= url_to('checkout.store') ?>" method="post" data-once>
                <?= csrf_field() ?>
                <input type="hidden" name="checkout_token" value="<?= esc($token, 'attr') ?>">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">Ringkasan pembayaran</h2>
                    <dl class="row small mb-3">
                        <dt class="col-7 fw-normal">Subtotal (<?= array_sum(array_column($items, 'quantity')) ?> barang)</dt><dd class="col-5 text-end"><?= rupiah($totals['subtotal']) ?></dd>
                        <dt class="col-7 fw-normal">Ongkos kirim</dt><dd class="col-5 text-end"><?= rupiah($totals['shipping']) ?></dd>
                        <dt class="col-7 fs-6">Total bayar</dt><dd class="col-5 text-end fs-6 fw-bold text-primary"><?= rupiah($totals['total']) ?></dd>
                    </dl>

                    <label class="form-label small" for="notes">Catatan untuk penjual (opsional)</label>
                    <textarea class="form-control mb-3" id="notes" name="notes" rows="2" maxlength="500"><?= esc(old('notes', '')) ?></textarea>

                    <p class="small text-body-secondary"><i class="bi bi-bank me-1"></i>Pembayaran melalui transfer bank. Instruksi dan rekening tujuan muncul setelah pesanan dibuat.</p>

                    <button class="btn btn-primary w-100" type="submit" <?= $addresses === [] || ! $totals['valid'] ? 'disabled' : '' ?>>Buat pesanan</button>
                    <?php if (! $totals['valid']) : ?>
                        <p class="small text-danger mt-2 mb-0">Ada item yang stoknya tidak mencukupi. <a href="<?= url_to('cart') ?>">Perbaiki di keranjang</a>.</p>
                    <?php endif ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
