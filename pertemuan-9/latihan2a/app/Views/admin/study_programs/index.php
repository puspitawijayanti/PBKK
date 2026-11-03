<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/study-programs/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Prodi</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Kode</th><th>Nama Program Studi</th><th>Jenjang</th><th class="text-center">Mahasiswa</th><th class="text-center">Dosen</th><th class="text-center">MK</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $program) : ?>
                        <tr>
                            <td><code><?= esc($program['code']) ?></code></td>
                            <td class="font-weight-bold text-gray-800"><?= esc($program['name']) ?></td>
                            <td><?= esc($program['degree']) ?></td>
                            <td class="text-center"><?= (int) $program['student_count'] ?></td>
                            <td class="text-center"><?= (int) $program['lecturer_count'] ?></td>
                            <td class="text-center"><?= (int) $program['course_count'] ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/study-programs/' . $program['id'] . '/edit') ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/study-programs/' . $program['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus program studi <?= esc($program['name'], 'js') ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger" aria-label="Hapus"><i class="fas fa-trash"></i></button>
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
