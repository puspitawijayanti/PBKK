<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<?php if ($year !== '') : ?>
    <a href="<?= site_url('student/results/print?year=' . urlencode($year) . '&term=' . $term) ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-print fa-sm"></i> Cetak KHS</a>
<?php endif ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if ($periods === []) : ?>
    <div class="card shadow"><div class="card-body text-center text-muted py-5">Belum ada riwayat studi.</div></div>
<?php else : ?>
    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="m-0 font-weight-bold text-primary"><?= esc(period_label($year, $term)) ?></h6>
                    <form action="<?= site_url('student/results') ?>" method="get" class="form-inline">
                        <label class="sr-only" for="periodSelect">Pilih semester</label>
                        <select class="custom-select custom-select-sm" id="periodSelect" onchange="var parts = this.value.split('|'); window.location = '?year=' + encodeURIComponent(parts[0]) + '&term=' + parts[1];">
                            <?php foreach ($periods as $period) : ?>
                                <option value="<?= esc($period['academic_year'] . '|' . $period['term'], 'attr') ?>"<?= $period['academic_year'] === $year && $period['term'] === $term ? ' selected' : '' ?>><?= esc(period_label($period['academic_year'], $period['term'])) ?></option>
                            <?php endforeach ?>
                        </select>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="thead-light"><tr><th>Kode</th><th>Mata Kuliah</th><th class="text-center">SKS</th><th class="text-center">Nilai</th><th class="text-center">Huruf</th><th class="text-center">Bobot</th><th class="text-center">SKS &times; Bobot</th></tr></thead>
                        <tbody>
                            <?php foreach ($rows as $row) : ?>
                                <tr>
                                    <td><code><?= esc($row['code']) ?></code></td>
                                    <td><?= esc($row['course_name']) ?></td>
                                    <td class="text-center"><?= (int) $row['credits'] ?></td>
                                    <td class="text-center"><?= $row['score'] !== null ? number_format((float) $row['score'], 2) : '-' ?></td>
                                    <td class="text-center"><?= grade_badge($row['grade']) ?></td>
                                    <td class="text-center"><?= $row['weight'] !== null ? number_format((float) $row['weight'], 2) : '-' ?></td>
                                    <td class="text-center"><?= $row['weight'] !== null ? number_format((int) $row['credits'] * (float) $row['weight'], 2) : '-' ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="2" class="text-right">Total</td>
                                <td class="text-center"><?= $semester['totalCredits'] ?></td>
                                <td colspan="3" class="text-right">Mutu</td>
                                <td class="text-center"><?= number_format($semester['qualityPoints'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <span>IP Semester: <strong class="text-primary"><?= format_gpa($semester['gpa']) ?></strong></span>
                    <span>IPK: <strong class="text-primary"><?= format_gpa($cumulative['gpa']) ?></strong></span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Riwayat IP per Semester</h6></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light"><tr><th>Semester</th><th class="text-center">SKS</th><th class="text-center">IP</th></tr></thead>
                        <tbody>
                            <?php foreach ($history as $item) : ?>
                                <tr class="<?= $item['academic_year'] === $year && $item['term'] === $term ? 'table-primary' : '' ?>">
                                    <td><a href="?year=<?= urlencode($item['academic_year']) ?>&amp;term=<?= $item['term'] ?>"><?= esc(term_label($item['term']) . ' ' . $item['academic_year']) ?></a></td>
                                    <td class="text-center"><?= $item['summary']['totalCredits'] ?></td>
                                    <td class="text-center"><?= format_gpa($item['summary']['gpa']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold"><td>IPK</td><td class="text-center"><?= $cumulative['gradedCredits'] ?></td><td class="text-center"><?= format_gpa($cumulative['gpa']) ?></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
