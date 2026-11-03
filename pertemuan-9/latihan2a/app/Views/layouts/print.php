<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | SIAKAD</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap5/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/print.css') ?>">
</head>
<body>
    <div class="print-toolbar">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <a href="<?= esc($backUrl ?? site_url('student'), 'attr') ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
            <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
    </div>
    <main class="sheet">
        <header class="sheet-header">
            <div class="sheet-logo">UNR</div>
            <div>
                <div class="sheet-institution"><?= esc(config('Academic')->institutionName) ?></div>
                <div class="small">Sistem Informasi Akademik</div>
            </div>
        </header>
        <?= $this->renderSection('content') ?>
        <footer class="sheet-footer small">Dicetak dari SIAKAD pada <?= date_id(date('Y-m-d H:i:s'), true) ?> WIB</footer>
    </main>
</body>
</html>
