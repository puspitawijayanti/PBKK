<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1 class="fw-bold mb-1">Kontak</h1>
    <p class="text-muted mb-4">Ada pertanyaan tentang alat atau jadwal laboratorium? Kirim pesan kepada laboran.</p>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold">Laboratorium Terpadu</h2>
                    <ul class="list-unstyled small mb-0 contact-list">
                        <li><i class="bi bi-geo-alt text-primary"></i> <?= esc($site->address) ?></li>
                        <li><i class="bi bi-telephone text-primary"></i> <?= esc($site->phone) ?></li>
                        <li><i class="bi bi-envelope text-primary"></i> <?= esc($site->email) ?></li>
                        <li><i class="bi bi-clock text-primary"></i> <?= esc($site->officeHours) ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="<?= site_url('contact') ?>" method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name') ?>" required>
                                <?= field_error('name') ?>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control<?= invalid_class('email') ?>" id="email" name="email" value="<?= old('email') ?>" required>
                                <?= field_error('email') ?>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Subjek</label>
                                <input type="text" class="form-control<?= invalid_class('subject') ?>" id="subject" name="subject" value="<?= old('subject') ?>" required>
                                <?= field_error('subject') ?>
                            </div>
                            <div class="col-12">
                                <label for="body" class="form-label">Pesan</label>
                                <textarea class="form-control<?= invalid_class('body') ?>" id="body" name="body" rows="5" required><?= old('body') ?></textarea>
                                <?= field_error('body') ?>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim Pesan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
