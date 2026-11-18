<?php
/** @var string $message */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman tidak ditemukan | ShopEase</title>
    <style>
        body { font-family: system-ui, -apple-system, "Segoe UI", sans-serif; background: #f6f7fb; color: #1f2340; margin: 0; display: grid; place-items: center; min-height: 100vh; text-align: center; }
        h1 { font-size: 5rem; margin: 0; color: #4f46e5; }
        p { color: #6b7088; }
        a { display: inline-block; margin-top: 1rem; padding: .6rem 1.2rem; background: #4f46e5; color: #fff; border-radius: .6rem; text-decoration: none; }
    </style>
</head>
<body>
    <div>
        <h1>404</h1>
        <h2>Halaman tidak ditemukan</h2>
        <p><?= ENVIRONMENT !== 'production' ? nl2br(esc($message)) : 'Halaman yang Anda cari tidak tersedia.' ?></p>
        <a href="/">Kembali ke Beranda</a>
    </div>
</body>
</html>
