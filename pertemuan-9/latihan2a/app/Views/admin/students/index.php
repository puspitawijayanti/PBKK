<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/students/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-user-plus fa-sm text-white-50"></i> Tambah Mahasiswa</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= view('admin/_filter', ['action' => 'admin/students', 'placeholder' => 'Nama atau NIM', 'keyword' => $keyword, 'programs' => $programs, 'programId' => $programId]) ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>NIM</th><th>Nama</th><th>Program Studi</th><th>Angkatan</th><th>Dosen Wali</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if ($students === []) : ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Data mahasiswa tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($students as $student) : ?>
                        <tr>
                            <td><code><?= esc($student['student_number']) ?></code></td>
                            <td class="font-weight-bold text-gray-800"><?= esc($student['name']) ?></td>
                            <td class="small"><?= esc($student['degree'] . ' ' . $student['study_program_name']) ?></td>
                            <td><?= (int) $student['entry_year'] ?></td>
                            <td class="small"><?= $student['advisor_name'] !== null ? esc($student['advisor_name']) : '<span class="badge badge-warning">Belum ada</span>' ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/students/' . $student['id'] . '/edit') ?>" class="btn btn-sm btn-warning" aria-label="Ubah"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/students/' . $student['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus mahasiswa <?= esc($student['name'], 'js') ?> beserta akun dan seluruh KRS-nya?')">
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
    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
        <span class="small text-muted">Total <?= $pager->getTotal() ?> mahasiswa</span>
        <?= $pager->links('default', 'bootstrap') ?>
    </div>
</div>
<?= $this->endSection() ?>
