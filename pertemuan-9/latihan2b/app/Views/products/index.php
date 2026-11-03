<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold">Kategori</h2>
                    <div class="list-group list-group-flush small">
                        <a href="<?= site_url('products?' . http_build_query(array_filter(['q' => $keyword, 'sort' => $sort]))) ?>" class="list-group-item list-group-item-action d-flex justify-content-between<?= $categoryId === 0 ? ' active' : '' ?>">Semua</a>
                        <?php foreach ($categories as $category) : ?>
                            <a href="<?= site_url('products?' . http_build_query(array_filter(['q' => $keyword, 'category' => $category['id'], 'sort' => $sort]))) ?>" class="list-group-item list-group-item-action d-flex justify-content-between<?= $categoryId === (int) $category['id'] ? ' active' : '' ?>">
                                <?= esc($category['name']) ?> <span class="badge rounded-pill text-bg-light"><?= (int) $category['product_count'] ?></span>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </aside>
        <div class="col-lg-9">
            <form action="<?= site_url('products') ?>" method="get" class="row g-2 align-items-end mb-3">
                <?php if ($categoryId > 0) : ?>
                    <input type="hidden" name="category" value="<?= $categoryId ?>">
                <?php endif ?>
                <div class="col-md-6">
                    <label for="q" class="form-label small text-muted">Cari</label>
                    <input type="search" id="q" name="q" class="form-control" value="<?= esc($keyword, 'attr') ?>" placeholder="Nama kopi, daerah asal, atau catatan rasa">
                </div>
                <div class="col-md-4">
                    <label for="sort" class="form-label small text-muted">Urutkan</label>
                    <select id="sort" name="sort" class="form-select" onchange="this.form.submit()">
                        <?php foreach (\App\Models\ProductModel::SORTS as $value => $label) : ?>
                            <option value="<?= $value ?>"<?= $sort === $value ? ' selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-coffee"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>

            <p class="small text-muted"><?= $pager->getTotal() ?> produk ditemukan<?= $keyword !== '' ? ' untuk "' . esc($keyword) . '"' : '' ?>.</p>

            <?php if ($products === []) : ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cup display-4"></i>
                    <p class="mt-3">Belum ada kopi yang cocok. Coba kata kunci lain.</p>
                </div>
            <?php else : ?>
                <div class="row g-4 mb-4">
                    <?php foreach ($products as $product) : ?>
                        <div class="col-6 col-md-4"><?= view('products/_card', ['product' => $product]) ?></div>
                    <?php endforeach ?>
                </div>
                <?= $pager->links('default', 'bootstrap') ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
