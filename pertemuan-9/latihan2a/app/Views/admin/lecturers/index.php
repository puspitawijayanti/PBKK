<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/lecturers/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Dosen</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= view('admin/_filter', ['action' => 'admin/lecturers', 'placeholder' => 'Nama atau NIDN', 'keyword' => $keyword, 'programs' => $programs, 'programId' => $programId]) ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>NIDN</th><th>Nama</th><th>Program Studi</th><th>Kontak</th><th class="text-center">MK Diampu</th><th class="text-center">Perwalian</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if ($lecturers === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Data dosen tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($lecturers as $lecturer) : ?>
                        <tr>
                            <td><code><?= esc($lecturer['lecturer_number']) ?></code></td>
                            <td class="font-weight-bold text-gray-800"><?= esc($lecturer['name']) ?></td>
                            <td class="small"><?= esc($lecturer['degree'] . ' ' . $lecturer['study_program_name']) ?></td>
                            <td class="small"><?= esc($lecturer['email'] ?? '-') ?><div class="text-muted"><?= esc($lecturer['phone'] ?? '') ?></div></td>
                            <td class="text-center"><?= (int) $lecturer['course_count'] ?></td>
                            <td class="text-center"><?= (int) $lecturer['advisee_count'] ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/lecturers/' . $lecturer['id'] . '/edit') ?>" class="btn btn-sm btn-warning" aria-label="Ubah"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/lecturers/' . $lecturer['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus dosen <?= esc($lecturer['name'], 'js') ?> beserta akunnya?')">
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
