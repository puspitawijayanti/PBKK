<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <?php if ($courses === []) : ?>
        <div class="col-12"><div class="card shadow"><div class="card-body text-center text-muted py-5">Anda belum mengampu mata kuliah.</div></div></div>
    <?php endif ?>
    <?php foreach ($courses as $course) : ?>
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="small text-muted"><?= esc($course['code']) ?> &middot; Semester <?= (int) $course['semester'] ?> &middot; <?= (int) $course['credits'] ?> SKS</div>
                    <h5 class="font-weight-bold text-gray-800 mt-1"><?= esc($course['name']) ?></h5>
                    <p class="small mb-1"><i class="far fa-clock text-gray-400"></i> <?= esc($course['schedule'] ?? 'Jadwal belum diatur') ?></p>
                    <p class="small mb-3"><i class="fas fa-door-open text-gray-400"></i> <?= esc($course['room'] ?? '-') ?></p>
                    <div class="small mb-1">Peserta <?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?>: <strong><?= (int) $course['enrolled_count'] ?> / <?= (int) $course['quota'] ?></strong></div>
                    <div class="progress progress-sm mb-3">
                        <div class="progress-bar" role="progressbar" style="width: <?= min(100, round((int) $course['enrolled_count'] / max(1, (int) $course['quota']) * 100)) ?>%"></div>
                    </div>
                    <a href="<?= site_url('lecturer/classes/' . $course['id']) ?>" class="btn btn-primary btn-sm btn-block">Daftar Peserta &amp; Nilai</a>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>
