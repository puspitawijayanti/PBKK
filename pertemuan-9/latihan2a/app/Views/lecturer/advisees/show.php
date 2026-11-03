<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('lecturer/advisees') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Perwalian</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-2">
    <div class="col-md-8">
        <p class="mb-1"><strong><?= esc($student['name']) ?></strong> &middot; NIM <?= esc($student['student_number']) ?></p>
        <p class="small text-muted"><?= esc($student['degree'] . ' ' . $student['study_program_name']) ?> &middot; Angkatan <?= (int) $student['entry_year'] ?></p>
    </div>
    <div class="col-md-4 text-md-right">
        <span class="h4 font-weight-bold text-primary">IPK <?= format_gpa($summary['gpa']) ?></span>
        <div class="small text-muted"><?= $summary['gradedCredits'] ?> SKS dinilai dari <?= $summary['totalCredits'] ?> SKS</div>
    </div>
</div>

<?php if ($periods === []) : ?>
    <div class="card shadow"><div class="card-body text-center text-muted py-5">Mahasiswa ini belum memiliki riwayat KRS.</div></div>
<?php endif ?>
<?php foreach ($periods as $period) : ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><?= esc($period['label']) ?></h6>
            <span class="small">IP <strong><?= format_gpa($period['summary']['gpa']) ?></strong> &middot; <?= $period['summary']['totalCredits'] ?> SKS</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="thead-light"><tr><th>Kode</th><th>Mata Kuliah</th><th class="text-center">SKS</th><th class="text-center">Nilai</th><th class="text-center">Huruf</th></tr></thead>
                <tbody>
                    <?php foreach ($period['rows'] as $row) : ?>
                        <tr>
                            <td><code><?= esc($row['code']) ?></code></td>
                            <td><?= esc($row['course_name']) ?></td>
                            <td class="text-center"><?= (int) $row['credits'] ?></td>
                            <td class="text-center"><?= $row['score'] !== null ? number_format((float) $row['score'], 2) : '-' ?></td>
                            <td class="text-center"><?= grade_badge($row['grade']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach ?>
<?= $this->endSection() ?>
