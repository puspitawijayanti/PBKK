<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/tools/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Alat</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/tools') ?>" method="get" class="form-inline">
            <input type="search" name="q" class="form-control form-control-sm mr-2 mb-2 mb-sm-0" value="<?= esc($keyword, 'attr') ?>" placeholder="Cari nama alat" aria-label="Cari alat">
            <select name="category" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $id => $name) : ?>
                    <option value="<?= $id ?>"<?= $categoryId === (int) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                <?php endforeach ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
            <?php if ($keyword !== '' || $categoryId > 0) : ?>
                <a href="<?= site_url('admin/tools') ?>" class="btn btn-sm btn-link">Reset</a>
            <?php endif ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th>Kondisi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tools === []) : ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Data alat tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($tools as $tool) : ?>
                        <tr>
                            <td><img src="<?= tool_photo_url($tool['photo']) ?>" alt="Foto <?= esc($tool['name'], 'attr') ?>" class="table-thumb rounded"></td>
                            <td>
                                <div class="font-weight-bold text-gray-800"><?= esc($tool['name']) ?></div>
                                <a href="<?= site_url('tools/' . $tool['slug']) ?>" class="small text-muted" target="_blank" rel="noopener">/tools/<?= esc($tool['slug']) ?></a>
                            </td>
                            <td><?= esc($tool['category_name']) ?></td>
                            <td class="text-center"><span class="badge badge-<?= (int) $tool['stock'] > 2 ? 'success' : ((int) $tool['stock'] > 0 ? 'warning' : 'danger') ?>"><?= (int) $tool['stock'] ?></span></td>
                            <td><?= esc(condition_label($tool['condition'])) ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/tools/' . $tool['id'] . '/edit') ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Ubah</a>
                                <form action="<?= site_url('admin/tools/' . $tool['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus alat <?= esc($tool['name'], 'js') ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
        <span class="small text-muted">Total <?= $pager->getTotal() ?> alat</span>
        <?= $pager->links('default', 'bootstrap') ?>
    </div>
</div>
<?= $this->endSection() ?>
