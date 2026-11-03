<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="p-4 p-lg-5 mb-4 rounded-4 welcome-banner text-white">
        <p class="text-uppercase small fw-semibold mb-2 text-warning">Latihan 1 &middot; Instalasi dan Konfigurasi</p>
        <h1 class="display-6 fw-bold">Selamat datang di <?= esc($site->appName) ?>!</h1>
        <p class="lead mb-0">CodeIgniter <?= esc($runtime['Versi CodeIgniter']) ?> berhasil terpasang dan terhubung ke database.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="bi bi-person-vcard"></i> Biodata Mahasiswa</div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <?php foreach ($biodata as $label => $value) : ?>
                                <tr>
                                    <th class="text-muted fw-normal w-40"><?= esc($label) ?></th>
                                    <td class="fw-semibold">
                                        <?php if ($value === '') : ?>
                                            <span class="text-muted fst-italic">belum diisi</span>
                                        <?php else : ?>
                                            <?= esc($value) ?>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                    <p class="small text-muted mt-3 mb-0">Nilai biodata dibaca dari <code>app/Config/Site.php</code> dan dapat ditimpa melalui <code>.env</code> (<code>site.studentName</code>, <code>site.studentNumber</code>).</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="bi bi-gear"></i> Konfigurasi Aplikasi</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <?php foreach ($runtime as $label => $value) : ?>
                                <tr>
                                    <th class="text-muted fw-normal"><?= esc($label) ?></th>
                                    <td><code><?= esc($value) ?></code></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="bi bi-folder2-open"></i> Struktur Direktori CodeIgniter 4</div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($directories as $path => $description) : ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <code class="fw-bold"><?= esc($path) ?></code>
                                    <p class="small text-muted mb-0 mt-1"><?= esc($description) ?></p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
