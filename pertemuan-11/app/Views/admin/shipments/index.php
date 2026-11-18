<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-3">Pengiriman</h1>
<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link <?= $tab === 'processing' ? 'active' : '' ?>" href="<?= site_url('admin/shipments?tab=processing') ?>">Siap dikirim</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'shipped' ? 'active' : '' ?>" href="<?= site_url('admin/shipments?tab=shipped') ?>">Dalam pengiriman</a></li>
</ul>
<div class="se-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>No. Pesanan</th><th>Customer</th><th style="min-width:380px"><?= $tab === 'processing' ? 'Isi data pengiriman' : 'Kurir / No. resi' ?></th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td class="fw-semibold"><a class="text-decoration-none" href="<?= site_url('admin/orders/' . $o['id']) ?>"><?= esc($o['order_number']) ?></a><div class="small text-muted fw-normal"><?= tgl($o['order_date']) ?></div></td>
                    <td><?= esc($o['customer_name']) ?><div class="small text-muted"><?= esc(mb_strimwidth((string) $o['shipping_address'], 0, 60, '...')) ?></div></td>
                    <td>
                        <form action="<?= site_url('admin/orders/' . $o['id'] . '/ship') ?>" method="post" class="d-flex gap-2">
                            <?= csrf_field() ?>
                            <select name="courier" class="form-select form-select-sm" style="max-width:150px" aria-label="Kurir" required>
                                <option value="">Kurir...</option>
                                <?php foreach ($couriers as $c): ?><option <?= ($o['courier'] ?? '') === $c ? 'selected' : '' ?>><?= esc($c) ?></option><?php endforeach; ?>
                            </select>
                            <input name="tracking_number" class="form-control form-control-sm" placeholder="No. resi" value="<?= esc((string) ($o['tracking_number'] ?? '')) ?>" minlength="5" maxlength="100" required aria-label="Nomor resi">
                            <button class="btn btn-sm btn-primary text-nowrap" type="submit"><?= $tab === 'processing' ? 'Kirim' : 'Simpan' ?></button>
                        </form>
                    </td>
                    <td class="text-end">
                        <?php if ($tab === 'shipped'): ?>
                            <form action="<?= site_url('admin/orders/' . $o['id'] . '/complete') ?>" method="post" class="d-inline" onsubmit="return confirm('Tandai pesanan selesai?')"><?= csrf_field() ?><button class="btn btn-sm btn-success" type="submit">Selesai</button></form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $orders): ?><tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pesanan pada antrean ini.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pager->links('default', 'bootstrap_full') ?></div>
<?= $this->endSection() ?>
