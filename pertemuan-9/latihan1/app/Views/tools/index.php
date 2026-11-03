<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="fw-bold mb-1">Katalog Alat</h1>
    <p class="text-muted mb-4">Daftar alat laboratorium yang dapat dipinjam mahasiswa.</p>

    <form action="<?= site_url('tools') ?>" method="get" class="card card-body shadow-sm mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label small text-muted">Kata kunci</label>
                <input type="search" class="form-control" id="q" name="q" value="<?= esc($keyword, 'attr') ?>" placeholder="Nama atau deskripsi alat">
            </div>
            <div class="col-md-4">
                <label for="category" class="form-label small text-muted">Kategori</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Semua kategori</option>
                    <?php foreach ($categories as $id => $name) : ?>
                        <option value="<?= $id ?>"<?= $categoryId === (int) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
            </div>
        </div>
    </form>

    <?php if ($keyword !== '' || $categoryId > 0) : ?>
        <p class="small text-muted">
            Menampilkan <?= $pager->getTotal() ?> alat
            <?= $keyword !== '' ? 'untuk kata kunci "<strong>' . esc($keyword) . '</strong>"' : '' ?>
            <?= $categoryId > 0 && isset($categories[$categoryId]) ? 'pada kategori <strong>' . esc($categories[$categoryId]) . '</strong>' : '' ?>.
            <a href="<?= site_url('tools') ?>">Hapus filter</a>
        </p>
    <?php endif ?>

    <?php if ($tools === []) : ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-3">Tidak ada alat yang cocok dengan pencarian Anda.</p>
        </div>
    <?php else : ?>
        <div class="row g-4 mb-4">
            <?php foreach ($tools as $tool) : ?>
                <div class="col-sm-6 col-lg-4">
                    <?= view('tools/_card', ['tool' => $tool]) ?>
                </div>
            <?php endforeach ?>
        </div>
        <?= $pager->links('default', 'bootstrap') ?>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
