<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row g-5 align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="fw-bold mb-3">Tentang <?= esc($site->appName) ?></h1>
            <p class="lead text-muted"><?= esc($site->appName) ?> adalah sistem peminjaman alat untuk Laboratorium Terpadu. Mahasiswa dapat melihat katalog alat beserta stoknya, lalu mengajukan peminjaman secara daring.</p>
            <p>Laboran memeriksa setiap pengajuan, menyetujui atau menolaknya, mencatat serah terima alat, dan menandai alat yang sudah dikembalikan. Stok alat otomatis berkurang saat pengajuan disetujui dan bertambah kembali saat alat dikembalikan.</p>
        </div>
        <div class="col-lg-6">
            <div class="row g-3">
                <?php foreach ([
                    ['icon' => 'bi-shield-check', 'title' => 'Akses Terkendali', 'text' => 'Halaman laboran dan mahasiswa dipisahkan dengan filter peran.'],
                    ['icon' => 'bi-graph-up', 'title' => 'Stok Akurat', 'text' => 'Perubahan stok dijalankan di dalam transaksi database.'],
                    ['icon' => 'bi-clock-history', 'title' => 'Riwayat Lengkap', 'text' => 'Setiap pengajuan tercatat bersama statusnya.'],
                    ['icon' => 'bi-images', 'title' => 'Foto Alat', 'text' => 'Setiap alat dilengkapi foto agar mudah dikenali.'],
                ] as $feature) : ?>
                    <div class="col-sm-6">
                        <div class="card h-100 border-0 bg-light">
                            <div class="card-body">
                                <i class="bi <?= $feature['icon'] ?> fs-3 text-primary"></i>
                                <h2 class="h6 fw-bold mt-2"><?= esc($feature['title']) ?></h2>
                                <p class="small text-muted mb-0"><?= esc($feature['text']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>

    <h2 class="h4 fw-bold mb-3">Aturan Peminjaman</h2>
    <ol class="list-group list-group-numbered mb-4">
        <li class="list-group-item">Peminjam wajib masuk menggunakan akun mahasiswa yang terdaftar.</li>
        <li class="list-group-item">Lama peminjaman maksimal <?= (int) $site->maxLoanDays ?> hari, dengan jumlah maksimal <?= (int) $site->maxLoanQuantity ?> unit per pengajuan.</li>
        <li class="list-group-item">Alat diambil di laboratorium setelah pengajuan berstatus "Disetujui".</li>
        <li class="list-group-item">Kerusakan atau kehilangan alat menjadi tanggung jawab peminjam.</li>
    </ol>
</div>
<?= $this->endSection() ?>
