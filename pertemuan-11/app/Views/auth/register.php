<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="se-card p-4">
            <h1 class="h4 fw-bold mb-1">Buat Akun Customer</h1>
            <p class="text-muted">Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk</a>.</p>

            <form action="<?= site_url('register') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama lengkap</label>
                    <input type="text" id="name" name="name" class="form-control<?= invalid_class('name') ?>" value="<?= esc(old('name')) ?>" required>
                    <?= field_error('name') ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control<?= invalid_class('email') ?>" value="<?= esc(old('email')) ?>" required autocomplete="username">
                    <?= field_error('email') ?>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">No. telepon <span class="text-muted small">(opsional)</span></label>
                    <input type="text" id="phone" name="phone" class="form-control<?= invalid_class('phone') ?>" value="<?= esc(old('phone')) ?>" inputmode="tel">
                    <?= field_error('phone') ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control<?= invalid_class('password') ?>" required autocomplete="new-password">
                    <div class="form-text">Minimal 8 karakter, mengandung huruf dan angka.</div>
                    <?= field_error('password') ?>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Konfirmasi password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control<?= invalid_class('password_confirm') ?>" required autocomplete="new-password">
                    <?= field_error('password_confirm') ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
