<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h2 class="h6 fw-bold">Ringkasan Belanja</h2>
        <dl class="row small mb-0">
            <dt class="col-7 fw-normal">Subtotal</dt><dd class="col-5 text-end"><?= rupiah($totals['subtotal']) ?></dd>
            <dt class="col-7 fw-normal">Ongkos kirim</dt>
            <dd class="col-5 text-end"><?= $totals['shipping'] === 0 ? '<span class="text-success fw-semibold">Gratis</span>' : rupiah($totals['shipping']) ?></dd>
            <dt class="col-7 fs-6">Total</dt><dd class="col-5 text-end fs-6 fw-bold text-coffee"><?= rupiah($totals['total']) ?></dd>
        </dl>
        <?php if (! $totals['freeShipping']) : ?>
            <div class="alert alert-warning small mt-3 mb-0">Belanja <?= rupiah($totals['missing']) ?> lagi untuk gratis ongkir (berlaku untuk subtotal di atas <?= rupiah($shop->freeShippingThreshold) ?>).</div>
        <?php else : ?>
            <div class="alert alert-success small mt-3 mb-0">Selamat, pesanan ini gratis ongkir!</div>
        <?php endif ?>
    </div>
</div>
