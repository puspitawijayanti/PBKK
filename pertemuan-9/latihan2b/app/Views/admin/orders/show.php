<?php
$next = \App\Models\OrderModel::FLOW[$order['status']];
$actions = [
    'processing'       => ['label' => 'Verifikasi Pembayaran', 'class' => 'btn-success', 'icon' => 'fa-check', 'confirm' => 'Pembayaran sudah sesuai? Pesanan akan diproses.'],
    'awaiting_payment' => ['label' => 'Tolak Bukti Transfer', 'class' => 'btn-outline-warning', 'icon' => 'fa-undo', 'confirm' => 'Tolak bukti transfer? Pembeli harus mengunggah ulang.'],
    'completed'        => ['label' => 'Tandai Selesai', 'class' => 'btn-success', 'icon' => 'fa-flag-checkered', 'confirm' => 'Tandai pesanan selesai?'],
    'cancelled'        => ['label' => 'Batalkan Pesanan', 'class' => 'btn-outline-danger', 'icon' => 'fa-times', 'confirm' => 'Batalkan pesanan ini? Stok produk akan dikembalikan.'],
];
?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/orders') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Daftar Pesanan</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <span>Status saat ini: <?= order_status_badge($order['status']) ?></span>
            <span class="small text-muted">Dibuat <?= date_id($order['created_at'], true) ?></span>
        </div>
        <?= view('partials/order_timeline', ['order' => $order]) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Item Pesanan (snapshot saat checkout)</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th>Produk</th><th class="text-right">Harga</th><th class="text-center">Qty</th><th class="text-right">Subtotal</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?= esc($item['product_name']) ?><?= $item['product_id'] === null ? ' <span class="badge badge-secondary">produk dihapus</span>' : '' ?></td>
                                <td class="text-right"><?= rupiah($item['price']) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-right"><?= rupiah($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-right">Subtotal</td><td class="text-right"><?= rupiah($order['subtotal']) ?></td></tr>
                        <tr><td colspan="3" class="text-right">Ongkir</td><td class="text-right"><?= (int) $order['shipping_cost'] === 0 ? 'Gratis' : rupiah($order['shipping_cost']) ?></td></tr>
                        <tr class="font-weight-bold"><td colspan="3" class="text-right">Total</td><td class="text-right"><?= rupiah($order['total']) ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Pembeli dan Pengiriman</h6></div>
            <div class="card-body small">
                <p class="mb-1"><strong>Akun:</strong> <?= esc($order['buyer_name']) ?> (<?= esc($order['buyer_email']) ?>)</p>
                <p class="mb-1"><strong>Penerima:</strong> <?= esc($order['recipient_name']) ?> &middot; <?= esc($order['phone']) ?></p>
                <p class="mb-1"><strong>Alamat:</strong> <?= esc($order['address']) ?>, <?= esc($order['city']) ?> <?= esc($order['postal_code']) ?></p>
                <?php if ($order['note']) : ?><p class="mb-1"><strong>Catatan:</strong> <?= esc($order['note']) ?></p><?php endif ?>
                <?php if ($order['tracking_number']) : ?><p class="mb-0"><strong>Pengiriman:</strong> <?= esc($order['courier']) ?> &middot; <?= esc($order['tracking_number']) ?></p><?php endif ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Tindakan</h6></div>
            <div class="card-body">
                <?php if ($next === []) : ?>
                    <p class="text-muted mb-0">Pesanan sudah <?= esc(strtolower(order_status_label($order['status']))) ?>. Tidak ada tindakan lanjutan.</p>
                <?php endif ?>
                <?php if ($order['status'] === 'awaiting_payment') : ?>
                    <p class="small text-muted">Menunggu pembeli mengunggah bukti transfer.</p>
                <?php endif ?>
                <?php if (in_array('shipped', $next, true)) : ?>
                    <form action="<?= site_url('admin/orders/' . $order['id'] . '/status') ?>" method="post" class="mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="shipped">
                        <div class="form-row">
                            <div class="form-group col-5 mb-2">
                                <label for="courier" class="small">Kurir</label>
                                <select class="custom-select custom-select-sm<?= invalid_class('courier') ?>" id="courier" name="courier">
                                    <?php foreach ($shop->couriers as $courier) : ?>
                                        <option value="<?= esc($courier, 'attr') ?>"<?= old('courier') === $courier ? ' selected' : '' ?>><?= esc($courier) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group col-7 mb-2">
                                <label for="tracking_number" class="small">Nomor Resi</label>
                                <input type="text" class="form-control form-control-sm text-uppercase<?= invalid_class('tracking_number') ?>" id="tracking_number" name="tracking_number" value="<?= old('tracking_number') ?>" required>
                            </div>
                        </div>
                        <?= field_error('courier') ?>
                        <?= field_error('tracking_number') ?>
                        <button type="submit" class="btn btn-primary btn-block btn-sm"><i class="fas fa-truck"></i> Kirim Pesanan &amp; Simpan Resi</button>
                    </form>
                <?php endif ?>
                <?php foreach ($next as $target) : ?>
                    <?php if (! isset($actions[$target]) || ($target === 'awaiting_payment' && $order['status'] !== 'awaiting_verification')) : ?>
                        <?php continue ?>
                    <?php endif ?>
                    <form action="<?= site_url('admin/orders/' . $order['id'] . '/status') ?>" method="post" class="mb-2" onsubmit="return confirm('<?= esc($actions[$target]['confirm'], 'js') ?>')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="<?= $target ?>">
                        <button type="submit" class="btn <?= $actions[$target]['class'] ?> btn-block btn-sm"><i class="fas <?= $actions[$target]['icon'] ?>"></i> <?= esc($actions[$target]['label']) ?></button>
                    </form>
                <?php endforeach ?>
                <p class="small text-muted mt-3 mb-0">Alur status dikunci oleh <code>OrderModel::FLOW</code>.</p>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bukti Transfer</h6></div>
            <div class="card-body">
                <?php if ($order['payment_proof']) : ?>
                    <a href="<?= site_url('admin/orders/' . $order['id'] . '/proof') ?>" target="_blank" rel="noopener">
                        <img src="<?= site_url('admin/orders/' . $order['id'] . '/proof') ?>" alt="Bukti transfer <?= esc($order['code'], 'attr') ?>" class="img-fluid rounded border">
                    </a>
                    <p class="small text-muted mt-2 mb-0">Diunggah <?= date_id($order['paid_at'], true) ?>. Jumlah yang harus diterima: <strong><?= rupiah($order['total']) ?></strong>.</p>
                <?php else : ?>
                    <p class="text-muted mb-0">Belum ada bukti transfer.</p>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
