<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <?php foreach ([
        ['label' => 'Kelas Diampu', 'value' => count($courses), 'icon' => 'fa-chalkboard', 'color' => 'primary', 'url' => 'lecturer/classes'],
        ['label' => 'Nilai Belum Diisi', 'value' => $ungraded, 'icon' => 'fa-pen', 'color' => 'warning', 'url' => 'lecturer/classes'],
        ['label' => 'Mahasiswa Perwalian', 'value' => $adviseeCount, 'icon' => 'fa-user-friends', 'color' => 'success', 'url' => 'lecturer/advisees'],
    ] as $card) : ?>
        <div class="col-md-4 mb-4">
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

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Jadwal Mengajar &middot; <?= esc(period_label($config->currentAcademicYear, $config->currentTerm)) ?></h6>
        <span class="small text-muted">NIDN <?= esc($lecturer['lecturer_number']) ?> &middot; <?= esc($lecturer['study_program_name']) ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr><th>Kode</th><th>Mata Kuliah</th><th>Jadwal</th><th>Ruang</th><th class="text-center">Peserta</th><th></th></tr></thead>
                <tbody>
                    <?php if ($courses === []) : ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Anda belum mengampu mata kuliah.</td></tr>
                    <?php endif ?>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td><code><?= esc($course['code']) ?></code></td>
                            <td><?= esc($course['name']) ?></td>
                            <td class="small"><?= esc($course['schedule'] ?? '-') ?></td>
                            <td class="small"><?= esc($course['room'] ?? '-') ?></td>
                            <td class="text-center"><?= (int) $course['enrolled_count'] ?> / <?= (int) $course['quota'] ?></td>
                            <td class="text-right"><a href="<?= site_url('lecturer/classes/' . $course['id']) ?>" class="btn btn-sm btn-primary">Input Nilai</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
