<?php
$isBootstrap5 = ($theme ?? 'bootstrap5') === 'bootstrap5';
$alerts = [
    'success' => ['class' => 'success', 'message' => session()->getFlashdata('success')],
    'error'   => ['class' => 'danger', 'message' => session()->getFlashdata('error')],
    'info'    => ['class' => 'info', 'message' => session()->getFlashdata('info')],
];
$errors = session('errors') ?? [];
?>
<?php foreach ($alerts as $alert) : ?>
    <?php if (! empty($alert['message'])) : ?>
        <div class="alert alert-<?= $alert['class'] ?> alert-dismissible fade show" role="alert">
            <?= esc($alert['message']) ?>
            <?php if ($isBootstrap5) : ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            <?php else : ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
            <?php endif ?>
        </div>
    <?php endif ?>
<?php endforeach ?>
<?php if ($errors !== []) : ?>
    <div class="alert alert-danger" role="alert">
        <strong>Periksa kembali isian Anda:</strong>
        <ul class="mb-0">
            <?php foreach ($errors as $message) : ?>
                <li><?= esc($message) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>
