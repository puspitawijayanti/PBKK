<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <form action="<?= site_url('admin/users') ?>" method="get" class="form-inline">
            <input type="search" name="q" class="form-control form-control-sm mr-2 mb-2 mb-sm-0" value="<?= esc($keyword, 'attr') ?>" placeholder="Nama atau username" aria-label="Cari akun">
            <select name="role" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Filter peran">
                <option value="">Semua peran</option>
                <?php foreach (\App\Models\UserModel::ROLES as $item) : ?>
                    <option value="<?= $item ?>"<?= $role === $item ? ' selected' : '' ?>><?= esc(role_label($item)) ?></option>
                <?php endforeach ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light"><tr><th>Username</th><th>Nama</th><th>Peran</th><th>Diperbarui</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><code><?= esc($user['username']) ?></code></td>
                            <td><?= esc($user['name']) ?></td>
                            <td><span class="badge badge-light"><?= esc(role_label($user['role'])) ?></span></td>
                            <td class="small text-muted"><?= date_id($user['updated_at'], true) ?></td>
                            <td class="text-right">
                                <form action="<?= site_url('admin/users/' . $user['id'] . '/reset-password') ?>" method="post" onsubmit="return confirm('Reset password <?= esc($user['name'], 'js') ?> menjadi username-nya?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-undo"></i> Reset Password</button>
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
