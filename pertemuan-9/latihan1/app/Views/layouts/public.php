<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Beranda') ?> | LabLend</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap5/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    <?= $this->include('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="container pt-4">
            <?= view('partials/flash', ['theme' => 'bootstrap5']) ?>
        </div>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <script src="<?= base_url('assets/bootstrap5/js/bootstrap.bundle.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
