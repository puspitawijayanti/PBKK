<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 fw-bold text-center mb-4">Daftar Akun Pembeli</h1>
                    <form action="<?= site_url('register') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name') ?>" autocomplete="name">
                            <?= field_error('name') ?>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control<?= invalid_class('email') ?>" id="email" name="email" value="<?= old('email') ?>" autocomplete="email">
                                <?= field_error('email') ?>
                            </div>
                            <div class="col-md-5">
                                <label for="phone" class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control<?= invalid_class('phone') ?>" id="phone" name="phone" value="<?= old('phone') ?>" placeholder="08xxxxxxxxxx" autocomplete="tel">
                                <?= field_error('phone') ?>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control<?= invalid_class('password') ?>" id="password" name="password" autocomplete="new-password">
                                <?= field_error('password') ?>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label">Ulangi Password</label>
                                <input type="password" class="form-control<?= invalid_class('password_confirm') ?>" id="password_confirm" name="password_confirm" autocomplete="new-password">
                                <?= field_error('password_confirm') ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-coffee w-100">Buat Akun</button>
                    </form>
                    <p class="small text-center text-muted mt-4 mb-0">Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
