<?php
$alerts = [
    'success' => session()->getFlashdata('success'),
    'danger'  => session()->getFlashdata('error'),
    'info'    => session()->getFlashdata('info'),
];
$errors = session('errors') ?? [];
?>
<?php foreach ($alerts as $class => $message) : ?>
    <?php if (! empty($message)) : ?>
        <div class="alert alert-<?= $class ?> alert-dismissible fade show" role="<?= $class === 'danger' ? 'alert' : 'status' ?>">
            <?= esc($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
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
