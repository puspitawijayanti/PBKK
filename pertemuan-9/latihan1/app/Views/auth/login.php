<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div class="step-icon mx-auto mb-3"><i class="bi bi-mortarboard"></i></div>
                        <h1 class="h4 fw-bold">Masuk Mahasiswa</h1>
                        <p class="small text-muted mb-0">Gunakan NRP sebagai username untuk mengajukan peminjaman alat.</p>
                    </div>
                    <form action="<?= site_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control<?= invalid_class('username') ?>" id="username" name="username" value="<?= old('username') ?>" autocomplete="username" autofocus>
                            <?= field_error('username') ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control<?= invalid_class('password') ?>" id="password" name="password" autocomplete="current-password">
                            <?= field_error('password') ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
                    </form>
                    <hr>
                    <p class="small text-center text-muted mb-0">Laboran? <a href="<?= site_url('admin/login') ?>">Masuk ke panel laboran</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
