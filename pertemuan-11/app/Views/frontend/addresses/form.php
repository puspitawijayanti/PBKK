<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$a   = $address;
$val = fn (string $f) => old($f, $a[$f] ?? '');
$action = $a ? site_url('account/addresses/' . $a['id']) : site_url('account/addresses');
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="se-card p-4">
            <h1 class="h4 fw-bold mb-3"><?= $a ? 'Ubah Alamat' : 'Tambah Alamat' ?></h1>
            <form action="<?= $action ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="return" value="<?= esc($return ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="recipient_name">Nama penerima</label>
                        <input class="form-control<?= invalid_class('recipient_name') ?>" id="recipient_name" name="recipient_name" value="<?= esc($val('recipient_name')) ?>" required>
                        <?= field_error('recipient_name') ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">No. telepon</label>
                        <input class="form-control<?= invalid_class('phone') ?>" id="phone" name="phone" value="<?= esc($val('phone')) ?>" inputmode="tel" required>
                        <?= field_error('phone') ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="address">Alamat lengkap</label>
                        <textarea class="form-control<?= invalid_class('address') ?>" id="address" name="address" rows="3" required><?= esc($val('address')) ?></textarea>
                        <?= field_error('address') ?>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="city">Kota</label>
                        <input class="form-control<?= invalid_class('city') ?>" id="city" name="city" value="<?= esc($val('city')) ?>" required>
                        <?= field_error('city') ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="province">Provinsi</label>
                        <input class="form-control<?= invalid_class('province') ?>" id="province" name="province" value="<?= esc($val('province')) ?>" required>
                        <?= field_error('province') ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="postal_code">Kode pos</label>
                        <input class="form-control<?= invalid_class('postal_code') ?>" id="postal_code" name="postal_code" value="<?= esc($val('postal_code')) ?>" inputmode="numeric" maxlength="5" required>
                        <?= field_error('postal_code') ?>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" <?= old('is_default', $a['is_default'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_default">Jadikan alamat utama</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a class="btn btn-outline-secondary" href="<?= site_url(($return ?? '') === 'checkout' ? 'checkout' : 'account/addresses') ?>">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
