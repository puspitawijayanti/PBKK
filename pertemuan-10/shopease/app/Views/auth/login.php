<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-1">Masuk ke akun</h1>
                    <p class="text-body-secondary small mb-4">Masuk untuk mengelola keranjang, checkout, dan melihat pesanan.</p>

                    <form method="post" action="<?= url_to('login') ?>" data-once>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" type="email" id="email" name="email" value="<?= esc(old('email', ''), 'attr') ?>" autocomplete="email" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password">Password</label>
                            <input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Masuk</button>
                    </form>

                    <p class="small text-center mt-4 mb-0">Belum punya akun? <a href="<?= url_to('register') ?>">Daftar sekarang</a></p>
                </div>
            </div>

            <?php if (ENVIRONMENT !== 'production') : ?>
                <details class="small text-body-secondary mt-3">
                    <summary>Akun demo</summary>
                    <ul class="mb-0 mt-2">
                        <li>Customer: <code>budi@example.test</code> / <code>customer123</code></li>
                        <li>Admin: <code>admin@shopease.test</code> / <code>admin123</code></li>
                        <li>Operator: <code>operator@shopease.test</code> / <code>operator123</code></li>
                    </ul>
                </details>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
