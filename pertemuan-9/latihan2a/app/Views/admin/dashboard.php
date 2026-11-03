<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="alert alert-primary small">
    Periode aktif: <strong><?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?></strong>
    &middot; Batas SKS: <strong><?= $config->maxCredits ?></strong>
    &middot; Pengisian KRS: <strong><?= $config->enrollmentOpen ? 'dibuka' : 'ditutup' ?></strong>
    <span class="text-muted">(diatur di <code>app/Config/Academic.php</code>)</span>
</div>

<div class="row">
    <?php foreach ([
        ['label' => 'Program Studi', 'value' => $stats['programs'], 'icon' => 'fa-university', 'color' => 'primary', 'url' => 'admin/study-programs'],
        ['label' => 'Dosen', 'value' => $stats['lecturers'], 'icon' => 'fa-chalkboard-teacher', 'color' => 'success', 'url' => 'admin/lecturers'],
        ['label' => 'Mahasiswa', 'value' => $stats['students'], 'icon' => 'fa-user-graduate', 'color' => 'info', 'url' => 'admin/students'],
        ['label' => 'Mata Kuliah', 'value' => $stats['courses'], 'icon' => 'fa-book', 'color' => 'warning', 'url' => 'admin/courses'],
    ] as $card) : ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= site_url($card['url']) ?>" class="card border-left-<?= $card['color'] ?> shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $card['color'] ?> text-uppercase mb-1"><?= esc($card['label']) ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $card['value'] ?></div>
                        </div>
                        <div class="col-auto"><i class="fas <?= $card['icon'] ?> fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Ringkasan per Program Studi</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light"><tr><th>Program Studi</th><th class="text-center">Mahasiswa</th><th class="text-center">Dosen</th><th class="text-center">Mata Kuliah</th></tr></thead>
                        <tbody>
                            <?php foreach ($programs as $program) : ?>
                                <tr>
                                    <td><span class="text-muted"><?= esc($program['code']) ?></span> <?= esc($program['degree'] . ' ' . $program['name']) ?></td>
                                    <td class="text-center"><?= (int) $program['student_count'] ?></td>
                                    <td class="text-center"><?= (int) $program['lecturer_count'] ?></td>
                                    <td class="text-center"><?= (int) $program['course_count'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Perlu Perhatian</h6></div>
            <div class="card-body">
                <p class="mb-2"><i class="fas fa-clipboard-list text-gray-400 mr-2"></i> <strong><?= $stats['enrollments'] ?></strong> entri KRS periode aktif</p>
                <p class="mb-2">
                    <i class="fas fa-user-slash text-gray-400 mr-2"></i> <strong><?= $stats['noAdvisor'] ?></strong> mahasiswa belum punya dosen wali
                    <?php if ($stats['noAdvisor'] > 0) : ?><a href="<?= site_url('admin/advisors?unassigned=1') ?>" class="small">atur</a><?php endif ?>
                </p>
                <p class="mb-0"><i class="fas fa-book-dead text-gray-400 mr-2"></i> <strong><?= $stats['noLecturer'] ?></strong> mata kuliah belum ada pengampu</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
