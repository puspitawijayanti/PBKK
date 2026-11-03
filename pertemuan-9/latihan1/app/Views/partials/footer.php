<?php $site = config('Site') ?>
<footer class="bg-dark text-white-50 pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h5 class="text-white"><i class="bi bi-eyedropper"></i> <?= esc($site->appName) ?></h5>
                <p class="small mb-0"><?= esc($site->tagline) ?>. Cari alat, ajukan peminjaman, dan pantau statusnya secara daring.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white">Navigasi</h6>
                <ul class="list-unstyled small">
                    <li><a class="link-light link-underline-opacity-0" href="<?= site_url('tools') ?>">Katalog Alat</a></li>
                    <li><a class="link-light link-underline-opacity-0" href="<?= site_url('about') ?>">Tentang</a></li>
                    <li><a class="link-light link-underline-opacity-0" href="<?= site_url('contact') ?>">Kontak</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white">Laboratorium</h6>
                <p class="small mb-1"><i class="bi bi-geo-alt"></i> <?= esc($site->address) ?></p>
                <p class="small mb-1"><i class="bi bi-clock"></i> <?= esc($site->officeHours) ?></p>
                <p class="small mb-0"><i class="bi bi-envelope"></i> <?= esc($site->email) ?></p>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="small text-center mb-0">&copy; <?= date('Y') ?> <?= esc($site->appName) ?>. Tugas PBKK Pertemuan 9.</p>
    </div>
</footer>
