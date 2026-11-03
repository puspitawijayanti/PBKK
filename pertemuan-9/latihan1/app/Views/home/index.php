<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section class="hero py-5 mb-5">
    <div class="container py-lg-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge text-bg-light text-uppercase mb-3">Laboratorium Terpadu</span>
                <h1 class="display-5 fw-bold text-white">Pinjam alat laboratorium tanpa antre formulir kertas.</h1>
                <p class="lead text-white-50 my-4">Cek ketersediaan osiloskop, mikrokontroler, sampai alat ukur secara langsung. Ajukan peminjaman, tunggu persetujuan laboran, lalu ambil alatnya di lab.</p>
                <form action="<?= site_url('tools') ?>" method="get" class="d-flex gap-2 search-hero">
                    <input type="search" name="q" class="form-control form-control-lg" placeholder="Cari alat, misalnya &quot;multimeter&quot;" aria-label="Cari alat">
                    <button type="submit" class="btn btn-warning btn-lg px-4"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <?php foreach ([
                        ['value' => $stats['tools'], 'label' => 'Jenis alat', 'icon' => 'bi-tools'],
                        ['value' => $stats['units'], 'label' => 'Unit tersedia', 'icon' => 'bi-box-seam'],
                        ['value' => $stats['categories'], 'label' => 'Kategori', 'icon' => 'bi-tags'],
                        ['value' => $stats['loans'], 'label' => 'Peminjaman tercatat', 'icon' => 'bi-clipboard-check'],
                    ] as $stat) : ?>
                        <div class="col-6">
                            <div class="stat-card p-3 rounded-3 h-100">
                                <i class="bi <?= $stat['icon'] ?> fs-4 text-warning"></i>
                                <div class="fs-3 fw-bold text-white"><?= number_format($stat['value'], 0, ',', '.') ?></div>
                                <div class="small text-white-50"><?= esc($stat['label']) ?></div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="h4 fw-bold mb-1">Kategori Alat</h2>
            <p class="text-muted mb-0">Pilih kategori untuk mempersempit pencarian.</p>
        </div>
    </div>
    <div class="row g-3">
        <?php foreach ($categories as $category) : ?>
            <div class="col-6 col-md-3">
                <a href="<?= site_url('tools?category=' . $category['id']) ?>" class="card category-card h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="fw-semibold text-dark"><?= esc($category['name']) ?></div>
                        <div class="small text-muted"><?= (int) $category['tool_count'] ?> jenis alat</div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</section>

<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="h4 fw-bold mb-1">Alat Terbaru</h2>
            <p class="text-muted mb-0">Alat yang baru masuk ke inventaris laboratorium.</p>
        </div>
        <a href="<?= site_url('tools') ?>" class="btn btn-outline-primary btn-sm">Lihat semua</a>
    </div>
    <div class="row g-4">
        <?php foreach ($latestTools as $tool) : ?>
            <div class="col-sm-6 col-lg-4">
                <?= view('tools/_card', ['tool' => $tool]) ?>
            </div>
        <?php endforeach ?>
    </div>
</section>

<section class="container mb-5">
    <div class="row g-4 text-center">
        <?php foreach ([
            ['icon' => 'bi-search', 'title' => '1. Cari Alat', 'text' => 'Telusuri katalog dan cek stok yang tersedia.'],
            ['icon' => 'bi-send', 'title' => '2. Ajukan', 'text' => 'Masuk sebagai mahasiswa lalu isi tanggal dan keperluan.'],
            ['icon' => 'bi-check2-circle', 'title' => '3. Disetujui', 'text' => 'Laboran memeriksa dan menyetujui pengajuan.'],
            ['icon' => 'bi-arrow-repeat', 'title' => '4. Kembalikan', 'text' => 'Kembalikan alat tepat waktu agar stok kembali.'],
        ] as $step) : ?>
            <div class="col-6 col-lg-3">
                <div class="p-3">
                    <div class="step-icon mx-auto mb-3"><i class="bi <?= $step['icon'] ?>"></i></div>
                    <h3 class="h6 fw-bold"><?= esc($step['title']) ?></h3>
                    <p class="small text-muted mb-0"><?= esc($step['text']) ?></p>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>
<?= $this->endSection() ?>
