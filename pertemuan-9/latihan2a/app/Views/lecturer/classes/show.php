<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('lecturer/classes') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kelas Saya</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="m-0 font-weight-bold text-primary"><?= esc(period_label($year, $term)) ?> &middot; <?= count($roster) ?> peserta</h6>
                <?php if (count($periods) > 1) : ?>
                    <form action="<?= site_url('lecturer/classes/' . $course['id']) ?>" method="get" class="form-inline">
                        <select class="custom-select custom-select-sm" name="period" aria-label="Pilih periode" onchange="var parts = this.value.split('|'); window.location = '?year=' + encodeURIComponent(parts[0]) + '&term=' + parts[1];">
                            <?php foreach ($periods as $period) : ?>
                                <option value="<?= esc($period['academic_year'] . '|' . $period['term'], 'attr') ?>"<?= $period['academic_year'] === $year && $period['term'] === $term ? ' selected' : '' ?>><?= esc(period_label($period['academic_year'], $period['term'])) ?></option>
                            <?php endforeach ?>
                        </select>
                    </form>
                <?php endif ?>
            </div>
            <form action="<?= site_url('lecturer/classes/' . $course['id'] . '/scores') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light"><tr><th>No</th><th>NIM</th><th>Nama</th><th style="width: 140px">Nilai Angka</th><th class="text-center">Huruf</th><th class="text-center">Bobot</th></tr></thead>
                            <tbody>
                                <?php if ($roster === []) : ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada mahasiswa yang mengambil kelas ini pada periode ini.</td></tr>
                                <?php endif ?>
                                <?php foreach ($roster as $index => $row) : ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><code><?= esc($row['student_number']) ?></code></td>
                                        <td><?= esc($row['student_name']) ?></td>
                                        <td>
                                            <label for="score_<?= $row['id'] ?>" class="sr-only">Nilai <?= esc($row['student_name']) ?></label>
                                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm score-input<?= invalid_class('score_' . $row['id']) ?>" id="score_<?= $row['id'] ?>" name="scores[<?= $row['id'] ?>]" value="<?= esc(old('scores.' . $row['id'], $row['score'] ?? ''), 'attr') ?>">
                                            <?= field_error('score_' . $row['id']) ?>
                                        </td>
                                        <td class="text-center grade-cell"><?= grade_badge($row['grade']) ?></td>
                                        <td class="text-center weight-cell"><?= $row['weight'] !== null ? number_format((float) $row['weight'], 2) : '-' ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($roster !== []) : ?>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Kosongkan nilai untuk mahasiswa yang belum dinilai.</span>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Nilai</button>
                    </div>
                <?php endif ?>
            </form>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="small text-muted"><?= esc($course['study_program_name']) ?></div>
                <h5 class="font-weight-bold text-gray-800"><?= esc($course['name']) ?></h5>
                <p class="mb-0 small"><?= (int) $course['credits'] ?> SKS &middot; Semester <?= (int) $course['semester'] ?> &middot; <?= esc($course['schedule'] ?? '-') ?> &middot; <?= esc($course['room'] ?? '-') ?></p>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Skala Nilai</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" id="gradeScale">
                    <thead class="thead-light"><tr><th>Nilai Minimal</th><th>Huruf</th><th>Bobot</th></tr></thead>
                    <tbody>
                        <?php foreach ($config->gradeScale as $level) : ?>
                            <tr data-min="<?= $level['min'] ?>" data-grade="<?= $level['grade'] ?>" data-weight="<?= $level['weight'] ?>">
                                <td>&ge; <?= $level['min'] ?></td><td><?= $level['grade'] ?></td><td><?= number_format($level['weight'], 2) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/grade-preview.js') ?>"></script>
<?= $this->endSection() ?>
