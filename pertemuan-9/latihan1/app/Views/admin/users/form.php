<?php $isEdit = $user !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="<?= $isEdit ? site_url('admin/users/' . $user['id']) : site_url('admin/users') ?>" method="post" autocomplete="off">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $user['name'] ?? '') ?>" required>
                        <?= field_error('name') ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control<?= invalid_class('username') ?>" id="username" name="username" value="<?= old('username', $user['username'] ?? '') ?>" required>
                            <?= field_error('username') ?>
                            <small class="form-text text-muted">Untuk mahasiswa gunakan NRP.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="role">Peran</label>
                            <select class="custom-select<?= invalid_class('role') ?>" id="role" name="role">
                                <?php foreach (['borrower', 'admin'] as $item) : ?>
                                    <option value="<?= $item ?>"<?= old('role', $user['role'] ?? 'borrower') === $item ? ' selected' : '' ?>><?= esc(role_label($item)) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('role') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control<?= invalid_class('password') ?>" id="password" name="password" autocomplete="new-password"<?= $isEdit ? '' : ' required' ?>>
                            <?= field_error('password') ?>
                            <?php if ($isEdit) : ?>
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti password.</small>
                            <?php endif ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" class="form-control<?= invalid_class('password_confirm') ?>" id="password_confirm" name="password_confirm" autocomplete="new-password">
                            <?= field_error('password_confirm') ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
