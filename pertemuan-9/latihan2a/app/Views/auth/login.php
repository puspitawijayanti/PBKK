<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($title) ?> | SIAKAD</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sb-admin-2/css/sb-admin-2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-flex bg-login-panel align-items-center text-white p-5">
                                <div>
                                    <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                    <h2 class="h4 font-weight-bold">Sistem Informasi Akademik</h2>
                                    <p><?= esc($config->institutionName) ?></p>
                                    <p class="small mb-0">Periode aktif: <strong><?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?></strong></p>
                                    <p class="small mb-0">Pengisian KRS: <strong><?= $config->enrollmentOpen ? 'dibuka' : 'ditutup' ?></strong></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-1">Masuk SIAKAD</h1>
                                        <p class="small text-muted mb-4">Mahasiswa memakai NIM, dosen memakai NIDN.</p>
                                    </div>
                                    <?= view('partials/flash', ['theme' => 'bootstrap4']) ?>
                                    <form class="user" action="<?= site_url('login') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="form-group">
                                            <label for="username" class="sr-only">NIM / NIDN / Username</label>
                                            <input type="text" class="form-control form-control-user<?= invalid_class('username') ?>" id="username" name="username" value="<?= old('username') ?>" placeholder="NIM / NIDN / Username" autocomplete="username" autofocus>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="sr-only">Password</label>
                                            <input type="password" class="form-control form-control-user<?= invalid_class('password') ?>" id="password" name="password" placeholder="Password" autocomplete="current-password">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">Masuk</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
