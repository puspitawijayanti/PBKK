<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$status  = $order['order_status'];
$isAdmin = current_user()['role'] === 'admin';
?>
<a class="small text-decoration-none" href="<?= url_to('admin.orders') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke daftar pesanan</a>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 my-3">
    <div>
        <h2 class="h4 fw-bold mb-1"><?= esc($order['order_number']) ?></h2>
        <p class="small text-body-secondary mb-0"><?= date('d M Y, H:i', strtotime($order['order_date'])) ?> &middot; <?= esc($order['customer_name']) ?> (<?= esc($order['customer_email']) ?>)</p>
    </div>
    <div class="d-flex flex-wrap gap-1">
        <?= status_badge('order', $status) ?>
        <?= status_badge('payment', $payment['payment_status'] ?? null) ?>
        <?php if ($shipment) : ?><?= status_badge('shipment', $shipment['shipment_status']) ?><?php endif ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Rincian produk (snapshot harga)</h3>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th scope="col">Produk</th><th scope="col" class="text-end">Harga</th><th scope="col" class="text-center">Jml</th><th scope="col" class="text-end">Subtotal</th></tr></thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td><?= esc($item['product_name']) ?></td>
                                    <td class="text-end text-nowrap"><?= rupiah($item['price']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end text-nowrap"><?= rupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                        <tfoot class="small">
                            <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end"><?= rupiah($order['subtotal']) ?></td></tr>
                            <tr><td colspan="3" class="text-end">Ongkos kirim</td><td class="text-end"><?= rupiah($order['shipping_cost']) ?></td></tr>
                            <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end"><?= rupiah($order['total']) ?></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body small">
                <h3 class="h6 fw-bold mb-2">Pengiriman</h3>
                <p class="mb-2"><strong><?= esc($order['recipient_name']) ?></strong> &middot; <?= esc($order['phone']) ?><br><?= esc($order['shipping_address']) ?></p>
                <?php if ($order['notes']) : ?><p class="mb-2 text-body-secondary">Catatan customer: <?= esc($order['notes']) ?></p><?php endif ?>
                <?php if ($shipment && $shipment['courier']) : ?>
                    <p class="mb-0">Kurir <strong><?= esc($shipment['courier']) ?></strong>, resi <strong><?= esc($shipment['tracking_number']) ?></strong>
                        <?php if ($shipment['shipped_at']) : ?>&middot; dikirim <?= date('d M Y H:i', strtotime($shipment['shipped_at'])) ?><?php endif ?>
                        <?php if ($shipment['delivered_at']) : ?>&middot; diterima <?= date('d M Y H:i', strtotime($shipment['delivered_at'])) ?><?php endif ?>
                    </p>
                <?php endif ?>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Riwayat status</h3>
                <?= view('partials/order_timeline', ['logs' => $logs]) ?>
            </div>
        </section>
    </div>

    <div class="col-xl-5">
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-2">Pembayaran</h3>
                <p class="small mb-2">
                    Transfer bank &middot; <?= rupiah($payment['amount'] ?? 0) ?> &middot; <?= status_badge('payment', $payment['payment_status'] ?? null) ?>
                    <?php if (! empty($payment['verifier_name'])) : ?>
                        <br><span class="text-body-secondary">Diperiksa <?= esc($payment['verifier_name']) ?>, <?= date('d M Y H:i', strtotime($payment['verified_at'])) ?></span>
                    <?php endif ?>
                    <?php if (! empty($payment['notes'])) : ?><br><span class="text-danger">Catatan: <?= esc($payment['notes']) ?></span><?php endif ?>
                </p>

                <?php if (! empty($payment['proof_file'])) : ?>
                    <a href="<?= url_to('admin.order.proof', $order['id']) ?>" target="_blank" rel="noopener">
                        <img class="img-fluid rounded border proof-image" src="<?= url_to('admin.order.proof', $order['id']) ?>" alt="Bukti transfer <?= esc($order['order_number'], 'attr') ?>">
                    </a>
                <?php else : ?>
                    <p class="small text-body-secondary mb-0">Customer belum mengunggah bukti pembayaran.</p>
                <?php endif ?>

                <?php if ($isAdmin && $status === 'waiting_verification') : ?>
                    <hr>
                    <form action="<?= url_to('admin.order.verify', $order['id']) ?>" method="post" data-once>
                        <?= csrf_field() ?>
                        <p class="small mb-2">Pastikan nominal <strong><?= rupiah($order['total']) ?></strong> sudah masuk ke rekening toko.</p>
                        <label class="form-label small" for="note">Alasan penolakan (wajib jika ditolak)</label>
                        <input class="form-control form-control-sm mb-2" id="note" name="note" maxlength="200" placeholder="Contoh: nominal tidak sesuai">
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" type="submit" name="decision" value="approve"><i class="bi bi-check2 me-1"></i>Terima pembayaran</button>
                            <button class="btn btn-outline-danger btn-sm" type="submit" name="decision" value="reject"><i class="bi bi-x me-1"></i>Tolak</button>
                        </div>
                    </form>
                <?php elseif (! $isAdmin && $status === 'waiting_verification') : ?>
                    <p class="small text-body-secondary mt-2 mb-0">Verifikasi pembayaran dilakukan oleh admin.</p>
                <?php endif ?>
            </div>
        </section>

        <?php if ($status === 'processing') : ?>
            <section class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-truck me-1"></i>Kirim pesanan</h3>
                    <form action="<?= url_to('admin.order.ship', $order['id']) ?>" method="post" data-once>
                        <?= csrf_field() ?>
                        <label class="form-label small" for="courier">Kurir</label>
                        <select class="form-select mb-2" id="courier" name="courier" required>
                            <?php foreach ($couriers as $courier) : ?>
                                <option value="<?= esc($courier, 'attr') ?>" <?= old('courier') === $courier ? 'selected' : '' ?>><?= esc($courier) ?></option>
                            <?php endforeach ?>
                        </select>
                        <label class="form-label small" for="tracking_number">Nomor resi</label>
                        <input class="form-control mb-3" id="tracking_number" name="tracking_number" value="<?= esc(old('tracking_number', ''), 'attr') ?>" required minlength="6" maxlength="60" pattern="[A-Za-z0-9]+" title="Huruf dan angka saja">
                        <button class="btn btn-primary" type="submit">Simpan &amp; tandai dikirim</button>
                    </form>
                </div>
            </section>
        <?php elseif ($status === 'shipped') : ?>
            <section class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-2">Selesaikan pesanan</h3>
                    <p class="small text-body-secondary">Gunakan jika customer sudah menerima barang tetapi belum menekan tombol "Pesanan diterima".</p>
                    <form action="<?= url_to('admin.order.complete', $order['id']) ?>" method="post" data-confirm="Tandai pesanan ini selesai?" data-once>
                        <?= csrf_field() ?>
                        <button class="btn btn-success btn-sm" type="submit"><i class="bi bi-check2-all me-1"></i>Tandai selesai</button>
                    </form>
                </div>
            </section>
        <?php endif ?>

        <?php if ($isAdmin && in_array('cancelled', \App\Models\OrderModel::FLOW[$status], true)) : ?>
            <section class="card border-0 shadow-sm border-start border-4 border-danger">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-2">Batalkan pesanan</h3>
                    <p class="small text-body-secondary">Stok produk akan dikembalikan. Pesanan tidak dihapus, hanya berstatus dibatalkan.</p>
                    <form action="<?= url_to('admin.order.cancel', $order['id']) ?>" method="post" data-confirm="Yakin membatalkan pesanan ini?">
                        <?= csrf_field() ?>
                        <input class="form-control form-control-sm mb-2" name="note" maxlength="200" placeholder="Alasan pembatalan (opsional)" aria-label="Alasan pembatalan">
                        <button class="btn btn-outline-danger btn-sm" type="submit">Batalkan pesanan</button>
                    </form>
                </div>
            </section>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
