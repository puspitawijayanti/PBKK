<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/users/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-user-plus fa-sm text-white-50"></i> Tambah Pengguna</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/users') ?>" method="get" class="form-inline">
            <input type="search" name="q" class="form-control form-control-sm mr-2 mb-2 mb-sm-0" value="<?= esc($keyword, 'attr') ?>" placeholder="Cari nama atau username" aria-label="Cari pengguna">
            <select name="role" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Filter peran">
                <option value="">Semua peran</option>
                <?php foreach (['admin', 'borrower'] as $item) : ?>
                    <option value="<?= $item ?>"<?= $role === $item ? ' selected' : '' ?>><?= esc(role_label($item)) ?></option>
                <?php endforeach ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Peran</th>
                        <th>Terdaftar</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users === []) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Pengguna tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td class="font-weight-bold text-gray-800">
                                <?= esc($user['name']) ?>
                                <?php if ((int) $user['id'] === (int) session('user_id')) : ?>
                                    <span class="badge badge-secondary">Anda</span>
                                <?php endif ?>
                            </td>
                            <td><code><?= esc($user['username']) ?></code></td>
                            <td><span class="badge badge-<?= $user['role'] === 'admin' ? 'primary' : 'light' ?>"><?= esc(role_label($user['role'])) ?></span></td>
                            <td class="small text-muted"><?= date_id($user['created_at']) ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Ubah</a>
                                <?php if ((int) $user['id'] !== (int) session('user_id')) : ?>
                                    <form action="<?= site_url('admin/users/' . $user['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus pengguna <?= esc($user['name'], 'js') ?>? Riwayat peminjamannya ikut terhapus.')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                <?php endif ?>
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
