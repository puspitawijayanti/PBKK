<?php foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $cls): ?>
    <?php if (session()->getFlashdata($key)): ?>
        <div class="alert alert-<?= $cls ?> alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata($key)) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<?php if (session('errors')): ?>
    <div class="alert alert-danger" role="alert">
        Periksa kembali isian Anda, ada yang belum sesuai.
    </div>
<?php endif; ?>
