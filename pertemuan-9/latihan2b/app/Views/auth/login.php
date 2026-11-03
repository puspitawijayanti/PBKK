<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 fw-bold text-center mb-1">Masuk</h1>
                    <p class="small text-muted text-center mb-4"><?= $toCheckout ? 'Masuk untuk melanjutkan checkout. Isi keranjang Anda tetap tersimpan.' : 'Masuk untuk melihat pesanan Anda.' ?></p>
                    <form action="<?= site_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control<?= invalid_class('email') ?>" id="email" name="email" value="<?= old('email') ?>" autocomplete="email" autofocus>
                            <?= field_error('email') ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control<?= invalid_class('password') ?>" id="password" name="password" autocomplete="current-password">
                            <?= field_error('password') ?>
                        </div>
                        <button type="submit" class="btn btn-coffee w-100">Masuk</button>
                    </form>
                    <p class="small text-center text-muted mt-4 mb-0">Belum punya akun? <a href="<?= site_url('register') ?>">Daftar sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
