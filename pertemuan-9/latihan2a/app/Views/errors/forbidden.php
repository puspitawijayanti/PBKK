<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Akses Ditolak | SIAKAD</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap5/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <i class="bi bi-shield-lock display-1 text-danger"></i>
        <h1 class="display-4 fw-bold mt-3">403</h1>
        <?php if (isset($message)) : ?>
            <p class="lead"><?= esc($message) ?></p>
        <?php else : ?>
            <p class="lead">Akses ditolak. Halaman ini hanya untuk peran: <strong><?= esc(implode(', ', array_map('role_label', $allowedRoles ?? []))) ?></strong>.</p>
        <?php endif ?>
        <p class="text-muted">Anda masuk sebagai <strong><?= esc(session('user_name') ?? 'tamu') ?></strong><?= session('user_role') ? ' (' . esc(role_label(session('user_role'))) . ')' : '' ?>.</p>
        <a href="<?= site_url(session('user_role') ?? 'login') ?>" class="btn btn-primary">Kembali ke halaman saya</a>
    </div>
</body>
</html>
