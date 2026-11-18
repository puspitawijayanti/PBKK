<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="se-card p-4">
            <h1 class="h4 fw-bold mb-1">Masuk ke ShopEase</h1>
            <p class="text-muted">Belum punya akun? <a href="<?= site_url('register') ?>">Daftar di sini</a>.</p>

            <form action="<?= site_url('login') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control<?= invalid_class('email') ?>" value="<?= esc(old('email')) ?>" required autofocus autocomplete="username">
                    <?= field_error('email') ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control<?= invalid_class('password') ?>" required autocomplete="current-password">
                    <?= field_error('password') ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
