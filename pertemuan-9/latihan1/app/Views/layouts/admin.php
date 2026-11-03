<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($title ?? 'Panel') ?> | Panel LabLend</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sb-admin-2/css/sb-admin-2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body id="page-top">
    <div id="wrapper">
        <?= $this->include('partials/sidebar') ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?= $this->include('partials/admin_navbar') ?>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-2 mb-sm-0 text-gray-800"><?= esc($title ?? 'Panel') ?></h1>
                        <div><?= $this->renderSection('actions') ?></div>
                    </div>

                    <?= view('partials/flash', ['theme' => 'bootstrap4']) ?>
                    <?= $this->renderSection('content') ?>
                </div>
            </div>

            <?= $this->include('partials/admin_footer') ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top" aria-label="Kembali ke atas"><i class="fas fa-angle-up"></i></a>

    <script src="<?= base_url('assets/sb-admin-2/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    <script src="<?= base_url('assets/sb-admin-2/js/sb-admin-2.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
