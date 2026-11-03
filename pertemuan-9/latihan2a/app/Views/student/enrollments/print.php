<?= $this->extend('layouts/print') ?>

<?= $this->section('content') ?>
<h1 class="sheet-title">Kartu Rencana Studi</h1>
<?= view('student/_identity', ['student' => $student, 'periodLabel' => period_label($config->currentAcademicYear, $config->currentTerm)]) ?>

<table class="table table-bordered">
    <thead class="table-light">
        <tr><th class="text-center">No</th><th>Kode</th><th>Mata Kuliah</th><th class="text-center">SKS</th><th>Jadwal</th><th>Ruang</th><th>Dosen</th></tr>
    </thead>
    <tbody>
        <?php if ($selected === []) : ?>
            <tr><td colspan="7" class="text-center text-muted">Belum ada mata kuliah yang dipilih.</td></tr>
        <?php endif ?>
        <?php foreach ($selected as $index => $row) : ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><?= esc($row['code']) ?></td>
                <td><?= esc($row['course_name']) ?></td>
                <td class="text-center"><?= (int) $row['credits'] ?></td>
                <td><?= esc($row['schedule'] ?? '-') ?></td>
                <td><?= esc($row['room'] ?? '-') ?></td>
                <td><?= esc($row['lecturer_name'] ?? '-') ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr><th colspan="3" class="text-end">Jumlah SKS</th><th class="text-center"><?= $credits ?></th><th colspan="3">Batas maksimal <?= $config->maxCredits ?> SKS</th></tr>
    </tfoot>
</table>

<div class="signature">
    <div>Mengetahui,<br>Dosen Wali<div class="line"><?= esc($student['advisor_name'] ?? '....................') ?></div></div>
    <div><?= date_id(date('Y-m-d')) ?><br>Mahasiswa<div class="line"><?= esc($student['name']) ?></div></div>
</div>
<?= $this->endSection() ?>
