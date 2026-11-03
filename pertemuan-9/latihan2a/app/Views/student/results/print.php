<?= $this->extend('layouts/print') ?>

<?= $this->section('content') ?>
<h1 class="sheet-title">Kartu Hasil Studi</h1>
<?= view('student/_identity', ['student' => $student, 'periodLabel' => $year === '' ? '-' : period_label($year, $term)]) ?>

<table class="table table-bordered">
    <thead class="table-light">
        <tr><th class="text-center">No</th><th>Kode</th><th>Mata Kuliah</th><th class="text-center">SKS</th><th class="text-center">Nilai</th><th class="text-center">Huruf</th><th class="text-center">Bobot</th><th class="text-center">Mutu</th></tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $index => $row) : ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><?= esc($row['code']) ?></td>
                <td><?= esc($row['course_name']) ?></td>
                <td class="text-center"><?= (int) $row['credits'] ?></td>
                <td class="text-center"><?= $row['score'] !== null ? number_format((float) $row['score'], 2) : '-' ?></td>
                <td class="text-center"><?= esc($row['grade'] ?? '-') ?></td>
                <td class="text-center"><?= $row['weight'] !== null ? number_format((float) $row['weight'], 2) : '-' ?></td>
                <td class="text-center"><?= $row['weight'] !== null ? number_format((int) $row['credits'] * (float) $row['weight'], 2) : '-' ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr><th colspan="3" class="text-end">Jumlah</th><th class="text-center"><?= $semester['totalCredits'] ?></th><th colspan="3"></th><th class="text-center"><?= number_format($semester['qualityPoints'], 2) ?></th></tr>
    </tfoot>
</table>

<table class="table table-sm w-50">
    <tbody>
        <tr><th>Indeks Prestasi Semester (IP)</th><td><?= format_gpa($semester['gpa']) ?></td></tr>
        <tr><th>Indeks Prestasi Kumulatif (IPK)</th><td><?= format_gpa($cumulative['gpa']) ?></td></tr>
        <tr><th>Total SKS Dinilai</th><td><?= $cumulative['gradedCredits'] ?></td></tr>
    </tbody>
</table>

<div class="signature">
    <div></div>
    <div><?= date_id(date('Y-m-d')) ?><br>Dosen Wali<div class="line"><?= esc($student['advisor_name'] ?? '....................') ?></div></div>
</div>
<?= $this->endSection() ?>
