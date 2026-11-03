<form action="<?= site_url($action) ?>" method="get" class="form-inline">
    <input type="search" name="q" class="form-control form-control-sm mr-2 mb-2 mb-sm-0" value="<?= esc($keyword ?? '', 'attr') ?>" placeholder="<?= esc($placeholder, 'attr') ?>" aria-label="Cari">
    <select name="program" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Filter program studi">
        <option value="">Semua program studi</option>
        <?php foreach ($programs as $id => $name) : ?>
            <option value="<?= $id ?>"<?= (int) $programId === (int) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
        <?php endforeach ?>
    </select>
    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
</form>
