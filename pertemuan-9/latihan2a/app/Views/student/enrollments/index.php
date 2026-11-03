<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('student/enrollments/print') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-print fa-sm"></i> Cetak KRS</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $percentage = min(100, round($credits / max(1, $config->maxCredits) * 100)) ?>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap mb-2">
            <span><strong><?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?></strong> &middot; <?= esc($student['study_program_name']) ?></span>
            <span>Total SKS: <strong><?= $credits ?></strong> / <?= $config->maxCredits ?></span>
        </div>
        <div class="progress" role="progressbar" aria-label="Total SKS" aria-valuenow="<?= $credits ?>" aria-valuemin="0" aria-valuemax="<?= $config->maxCredits ?>">
            <div class="progress-bar<?= $percentage >= 100 ? ' bg-danger' : '' ?>" style="width: <?= $percentage ?>%"></div>
        </div>
        <?php if (! $config->enrollmentOpen) : ?>
            <div class="alert alert-warning small mt-3 mb-0">Masa pengisian KRS sudah ditutup. KRS hanya dapat dilihat dan dicetak.</div>
        <?php endif ?>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">KRS Saya (<?= count($selected) ?> mata kuliah)</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th>Mata Kuliah</th><th class="text-center">SKS</th><th></th></tr></thead>
                    <tbody>
                        <?php if ($selected === []) : ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada mata kuliah yang dipilih.</td></tr>
                        <?php endif ?>
                        <?php foreach ($selected as $row) : ?>
                            <tr>
                                <td><?= esc($row['course_name']) ?><div class="small text-muted"><?= esc($row['code']) ?> &middot; <?= esc($row['schedule'] ?? '-') ?></div></td>
                                <td class="text-center"><?= (int) $row['credits'] ?></td>
                                <td class="text-right">
                                    <?php if ($config->enrollmentOpen && $row['score'] === null) : ?>
                                        <form action="<?= site_url('student/enrollments/' . $row['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Batalkan <?= esc($row['course_name'], 'js') ?> dari KRS?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-minus"></i> Batal</button>
                                        </form>
                                    <?php else : ?>
                                        <?= grade_badge($row['grade']) ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Mata Kuliah Ditawarkan</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th>Mata Kuliah</th><th class="text-center">SKS</th><th class="text-center">Kuota</th><th></th></tr></thead>
                    <tbody>
                        <?php if ($offered === []) : ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada mata kuliah yang ditawarkan.</td></tr>
                        <?php endif ?>
                        <?php foreach ($offered as $course) : ?>
                            <?php
                            $taken = in_array((int) $course['id'], $selectedIds, true);
                            $full = (int) $course['enrolled_count'] >= (int) $course['quota'];
                            $overLimit = $credits + (int) $course['credits'] > $config->maxCredits;
                            ?>
                            <tr class="<?= $taken ? 'table-success' : '' ?>">
                                <td>
                                    <?= esc($course['name']) ?>
                                    <div class="small text-muted"><?= esc($course['code']) ?> &middot; Smt <?= (int) $course['semester'] ?> &middot; <?= esc($course['schedule'] ?? '-') ?> &middot; <?= esc($course['lecturer_name'] ?? 'Pengampu belum ada') ?></div>
                                </td>
                                <td class="text-center"><?= (int) $course['credits'] ?></td>
                                <td class="text-center small<?= $full ? ' text-danger font-weight-bold' : '' ?>"><?= (int) $course['enrolled_count'] ?>/<?= (int) $course['quota'] ?></td>
                                <td class="text-right">
                                    <?php if ($taken) : ?>
                                        <span class="badge badge-success">Diambil</span>
                                    <?php elseif ($config->enrollmentOpen) : ?>
                                        <form action="<?= site_url('student/enrollments') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-primary"<?= $full || $overLimit ? ' title="' . ($full ? 'Kuota penuh' : 'Melebihi batas SKS') . '"' : '' ?>><i class="fas fa-plus"></i> Ambil</button>
                                        </form>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
