<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= url_to('home') ?>">Beranda</a></li>
            <li class="breadcrumb-item<?= $activeCategory ? '' : ' active' ?>"<?= $activeCategory ? '' : ' aria-current="page"' ?>>
                <?= $activeCategory ? '<a href="' . url_to('products') . '">Katalog</a>' : 'Katalog' ?>
            </li>
            <?php if ($activeCategory) : ?>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($activeCategory['name']) ?></li>
            <?php endif ?>
        </ol>
    </nav>

    <div class="row g-4">
        <aside class="col-lg-3">
            <form method="get" action="<?= url_to('products') ?>" class="card card-body sticky-lg-top filter-panel">
                <h2 class="h6 fw-bold mb-3">Filter produk</h2>

                <label class="form-label small" for="filterQ">Kata kunci</label>
                <input class="form-control mb-3" type="search" id="filterQ" name="q" value="<?= esc($keyword, 'attr') ?>" placeholder="Nama produk">

                <label class="form-label small" for="filterKategori">Kategori</label>
                <select class="form-select mb-3" id="filterKategori" name="category">
                    <option value="">Semua kategori</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= esc($category['slug'], 'attr') ?>" <?= ($activeCategory['slug'] ?? '') === $category['slug'] ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?> (<?= $category['product_count'] ?>)
                        </option>
                    <?php endforeach ?>
                </select>

                <label class="form-label small" for="filterSort">Urutkan</label>
                <select class="form-select mb-3" id="filterSort" name="sort">
                    <?php foreach ($sorts as $value => $label) : ?>
                        <option value="<?= $value ?>" <?= $sort === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach ?>
                </select>

                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                <?php if ($keyword !== '' || $activeCategory) : ?>
                    <a class="btn btn-link btn-sm mt-2" href="<?= url_to('products') ?>">Reset filter</a>
                <?php endif ?>
            </form>
        </aside>

        <section class="col-lg-9" aria-labelledby="judulKatalog">
            <div class="d-flex flex-wrap justify-content-between align-items-baseline mb-3 gap-2">
                <h1 id="judulKatalog" class="h4 fw-bold mb-0"><?= esc($title) ?></h1>
                <p class="text-body-secondary small mb-0">
                    <?= $total ?> produk<?= $keyword !== '' ? ' untuk "<strong>' . esc($keyword) . '</strong>"' : '' ?>
                </p>
            </div>

            <?php if ($products === []) : ?>
                <div class="text-center py-5 border rounded-3 bg-white">
                    <i class="bi bi-search display-5 text-body-secondary"></i>
                    <p class="mt-3 mb-1 fw-semibold">Produk tidak ditemukan</p>
                    <p class="text-body-secondary small">Coba kata kunci lain atau hapus filter kategori.</p>
                    <a class="btn btn-outline-primary" href="<?= url_to('products') ?>">Lihat semua produk</a>
                </div>
            <?php else : ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                    <?php foreach ($products as $product) : ?>
                        <div class="col"><?= view('frontend/products/_card', ['product' => $product]) ?></div>
                    <?php endforeach ?>
                </div>
                <div class="mt-4"><?= $pagerLinks ?></div>
            <?php endif ?>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
