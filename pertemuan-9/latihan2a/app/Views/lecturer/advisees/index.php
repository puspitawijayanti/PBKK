<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light"><tr><th>NIM</th><th>Nama</th><th>Angkatan</th><th class="text-center">SKS Semester Ini</th><th class="text-center">SKS Dinilai</th><th class="text-center">IPK</th><th></th></tr></thead>
                <tbody>
                    <?php if ($students === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada mahasiswa perwalian.</td></tr>
                    <?php endif ?>
                    <?php foreach ($students as $student) : ?>
                        <tr>
                            <td><code><?= esc($student['student_number']) ?></code></td>
                            <td><?= esc($student['name']) ?><div class="small text-muted"><?= esc($student['study_program_name']) ?></div></td>
                            <td><?= (int) $student['entry_year'] ?></td>
                            <td class="text-center"><?= $student['current_credits'] ?></td>
                            <td class="text-center"><?= $student['summary']['gradedCredits'] ?></td>
                            <td class="text-center font-weight-bold <?= $student['summary']['gpa'] !== null && $student['summary']['gpa'] < 2 ? 'text-danger' : 'text-gray-800' ?>"><?= format_gpa($student['summary']['gpa']) ?></td>
                            <td class="text-right"><a href="<?= site_url('lecturer/advisees/' . $student['id']) ?>" class="btn btn-sm btn-outline-primary">Transkrip</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
