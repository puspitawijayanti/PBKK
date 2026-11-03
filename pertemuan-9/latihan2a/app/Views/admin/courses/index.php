<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/courses/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Mata Kuliah</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= view('admin/_filter', ['action' => 'admin/courses', 'placeholder' => 'Kode atau nama MK', 'keyword' => $keyword, 'programs' => $programs, 'programId' => $programId]) ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Kode</th><th>Mata Kuliah</th><th class="text-center">SKS</th><th class="text-center">Smt</th><th>Pengampu</th><th>Jadwal</th><th class="text-center">Peserta / Kuota</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if ($courses === []) : ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Mata kuliah tidak ditemukan.</td></tr>
                    <?php endif ?>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td><code><?= esc($course['code']) ?></code></td>
                            <td>
                                <div class="font-weight-bold text-gray-800"><?= esc($course['name']) ?></div>
                                <div class="small text-muted"><?= esc($course['study_program_name']) ?></div>
                            </td>
                            <td class="text-center"><?= (int) $course['credits'] ?></td>
                            <td class="text-center"><?= (int) $course['semester'] ?></td>
                            <td class="small"><?= $course['lecturer_name'] !== null ? esc($course['lecturer_name']) : '<span class="badge badge-warning">Belum ada</span>' ?></td>
                            <td class="small"><?= esc($course['schedule'] ?? '-') ?><div class="text-muted"><?= esc($course['room'] ?? '') ?></div></td>
                            <td class="text-center"><?= (int) $course['enrolled_count'] ?> / <?= (int) $course['quota'] ?></td>
                            <td class="text-right text-nowrap">
                                <a href="<?= site_url('admin/courses/' . $course['id'] . '/edit') ?>" class="btn btn-sm btn-warning" aria-label="Ubah"><i class="fas fa-edit"></i></a>
                                <form action="<?= site_url('admin/courses/' . $course['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus mata kuliah <?= esc($course['name'], 'js') ?>?')">
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
        <span class="small text-muted">Peserta dihitung untuk <?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?></span>
        <?= $pager->links('default', 'bootstrap') ?>
    </div>
</div>
<?= $this->endSection() ?>
