<?php $shop = config(\Config\Shop::class) ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= esc($shop->name . ' - ' . $shop->tagline, 'attr') ?>">
    <title><?= esc($title ?? 'Beranda') ?> | <?= esc($shop->name) ?></title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap5/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shopease.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    <a class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-light" href="#konten">Lewati ke konten</a>

    <?= view('partials/navbar', ['shop' => $shop]) ?>

    <main id="konten" class="flex-grow-1">
        <?php if (session()->getFlashdata() !== [] || session('errors')) : ?>
            <div class="container pt-3"><?= view('partials/flash') ?></div>
        <?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>

    <?= view('partials/footer', ['shop' => $shop]) ?>

    <script src="<?= base_url('assets/bootstrap5/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/shopease.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
