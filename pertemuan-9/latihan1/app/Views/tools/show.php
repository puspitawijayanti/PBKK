<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('tools') ?>">Katalog</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('tools?category=' . $tool['category_id']) ?>"><?= esc($tool['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($tool['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-5">
            <img src="<?= tool_photo_url($tool['photo']) ?>" class="img-fluid rounded-4 shadow-sm w-100 tool-photo" alt="Foto <?= esc($tool['name'], 'attr') ?>">
        </div>
        <div class="col-lg-7">
            <span class="badge text-bg-primary mb-2"><?= esc($tool['category_name']) ?></span>
            <h1 class="fw-bold"><?= esc($tool['name']) ?></h1>

            <div class="row g-3 my-3">
                <div class="col-4">
                    <div class="border rounded-3 p-3 text-center">
                        <div class="small text-muted">Stok tersedia</div>
                        <div class="fs-4 fw-bold <?= (int) $tool['stock'] > 0 ? 'text-success' : 'text-danger' ?>"><?= (int) $tool['stock'] ?></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded-3 p-3 text-center">
                        <div class="small text-muted">Kondisi</div>
                        <div class="fs-6 fw-bold mt-2"><?= esc(condition_label($tool['condition'])) ?></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded-3 p-3 text-center">
                        <div class="small text-muted">Sedang dipinjam</div>
                        <div class="fs-4 fw-bold"><?= (int) $activeLoans ?></div>
                    </div>
                </div>
            </div>

            <h2 class="h6 fw-bold">Deskripsi</h2>
            <p class="text-muted"><?= nl2br(esc((string) $tool['description'])) ?></p>

            <?php if ((int) $tool['stock'] < 1) : ?>
                <button type="button" class="btn btn-secondary btn-lg" disabled>Stok sedang habis</button>
            <?php elseif (session('user_role') === 'admin') : ?>
                <a href="<?= site_url('admin/tools/' . $tool['id'] . '/edit') ?>" class="btn btn-outline-primary btn-lg"><i class="bi bi-pencil"></i> Ubah di panel laboran</a>
            <?php else : ?>
                <a href="<?= site_url('borrower/loans/new/' . $tool['id']) ?>" class="btn btn-primary btn-lg"><i class="bi bi-cart-plus"></i> Ajukan Peminjaman</a>
                <?php if (session('user_role') === null) : ?>
                    <p class="small text-muted mt-2">Anda akan diminta masuk sebagai mahasiswa terlebih dahulu.</p>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>

    <?php if ($relatedTools !== []) : ?>
        <h2 class="h5 fw-bold mt-5 mb-3">Alat lain di kategori ini</h2>
        <div class="row g-4">
            <?php foreach ($relatedTools as $related) : ?>
                <div class="col-sm-6 col-lg-4">
                    <?= view('tools/_card', ['tool' => $related]) ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
