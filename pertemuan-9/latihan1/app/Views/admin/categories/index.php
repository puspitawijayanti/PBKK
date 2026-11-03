<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/categories/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kategori</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/categories') ?>" method="get" class="form-inline">
            <input type="search" name="q" class="form-control form-control-sm mr-2" value="<?= esc($keyword, 'attr') ?>" placeholder="Cari kategori" aria-label="Cari kategori">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Kategori</th>
                        <th class="text-center">Jumlah Jenis Alat</th>
                        <th>Diperbarui</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories === []) : ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Kategori tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td class="font-weight-bold text-gray-800"><?= esc($category['name']) ?></td>
                            <td class="text-center"><a href="<?= site_url('admin/tools?category=' . $category['id']) ?>"><?= (int) $category['tool_count'] ?></a></td>
                            <td class="small text-muted"><?= date_id($category['updated_at'], true) ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Ubah</a>
                                <form action="<?= site_url('admin/categories/' . $category['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori <?= esc($category['name'], 'js') ?>?')">
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
    <div class="card-footer bg-white"><?= $pager->links('default', 'bootstrap') ?></div>
</div>
<?= $this->endSection() ?>
