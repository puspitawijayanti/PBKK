<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<form class="card card-body border-0 shadow-sm mb-3" method="get" action="<?= url_to('admin.users') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small" for="q">Cari nama atau email</label>
            <input class="form-control" type="search" id="q" name="q" value="<?= esc($keyword, 'attr') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small" for="role">Role</label>
            <select class="form-select" id="role" name="role">
                <option value="">Semua</option>
                <?php foreach (\App\Models\UserModel::ROLES as $value) : ?>
                    <option value="<?= $value ?>" <?= $role === $value ? 'selected' : '' ?>><?= role_label($value) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Cari</button></div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Terdaftar</th><th scope="col">Status</th><th scope="col" class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><strong><?= esc($user['name']) ?></strong><div class="small text-body-secondary"><?= esc($user['phone'] ?? '-') ?></div></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= role_label($user['role']) ?></td>
                        <td class="small"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td><?= $user['is_active'] ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>' ?></td>
                        <td class="text-end">
                            <?php if ($user['id'] !== current_user()['id']) : ?>
                                <form action="<?= url_to('admin.user.toggle', $user['id']) ?>" method="post" data-confirm="<?= $user['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> akun <?= esc($user['name'], 'attr') ?>?">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm <?= $user['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" type="submit"><?= $user['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                                </form>
                            <?php else : ?>
                                <span class="small text-body-secondary">Akun Anda</span>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?= $pagerLinks ?></div>
<?= $this->endSection() ?>
