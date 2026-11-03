<table class="identity mb-3">
    <tbody>
        <tr><td>Nama</td><td>: <strong><?= esc($student['name']) ?></strong></td><td class="ps-4">Program Studi</td><td>: <?= esc($student['degree'] . ' ' . $student['study_program_name']) ?></td></tr>
        <tr><td>NIM</td><td>: <?= esc($student['student_number']) ?></td><td class="ps-4">Dosen Wali</td><td>: <?= esc($student['advisor_name'] ?? '-') ?></td></tr>
        <tr><td>Angkatan</td><td>: <?= (int) $student['entry_year'] ?></td><td class="ps-4">Periode</td><td>: <?= esc($periodLabel) ?></td></tr>
    </tbody>
</table>
