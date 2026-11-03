<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <?php foreach ([
        ['label' => 'IPK', 'value' => format_gpa($summary['gpa']), 'icon' => 'fa-star', 'color' => 'primary', 'url' => 'student/results'],
        ['label' => 'SKS Lulus Dinilai', 'value' => $summary['gradedCredits'], 'icon' => 'fa-check-double', 'color' => 'success', 'url' => 'student/results'],
        ['label' => 'SKS Semester Ini', 'value' => $currentCredits . ' / ' . $config->maxCredits, 'icon' => 'fa-clipboard-list', 'color' => 'info', 'url' => 'student/enrollments'],
    ] as $card) : ?>
        <div class="col-md-4 mb-4">
            <a href="<?= site_url($card['url']) ?>" class="card border-left-<?= $card['color'] ?> shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $card['color'] ?> text-uppercase mb-1"><?= esc($card['label']) ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc((string) $card['value']) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas <?= $card['icon'] ?> fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Profil</h6></div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="small text-muted">Nama</dt><dd><?= esc($student['name']) ?></dd>
                    <dt class="small text-muted">NIM</dt><dd><?= esc($student['student_number']) ?></dd>
                    <dt class="small text-muted">Program Studi</dt><dd><?= esc($student['degree'] . ' ' . $student['study_program_name']) ?></dd>
                    <dt class="small text-muted">Angkatan</dt><dd><?= (int) $student['entry_year'] ?></dd>
                    <dt class="small text-muted">Dosen Wali</dt><dd class="mb-0"><?= esc($student['advisor_name'] ?? 'Belum ditentukan') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Jadwal <?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?></h6>
                <a href="<?= site_url('student/enrollments') ?>" class="small">Kelola KRS</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th>Mata Kuliah</th><th class="text-center">SKS</th><th>Jadwal</th><th>Dosen</th></tr></thead>
                    <tbody>
                        <?php if ($current === []) : ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">KRS semester ini masih kosong.</td></tr>
                        <?php endif ?>
                        <?php foreach ($current as $row) : ?>
                            <tr>
                                <td><?= esc($row['course_name']) ?><div class="small text-muted"><?= esc($row['code']) ?></div></td>
                                <td class="text-center"><?= (int) $row['credits'] ?></td>
                                <td class="small"><?= esc($row['schedule'] ?? '-') ?><div class="text-muted"><?= esc($row['room'] ?? '') ?></div></td>
                                <td class="small"><?= esc($row['lecturer_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
