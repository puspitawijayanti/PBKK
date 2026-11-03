<?php
$actions = [
    'approved' => ['label' => 'Setujui', 'class' => 'btn-success', 'icon' => 'fa-check', 'confirm' => 'Setujui pengajuan ini? Stok alat akan berkurang.'],
    'rejected' => ['label' => 'Tolak', 'class' => 'btn-outline-danger', 'icon' => 'fa-times', 'confirm' => 'Tolak pengajuan ini?'],
    'borrowed' => ['label' => 'Serahkan', 'class' => 'btn-primary', 'icon' => 'fa-hand-holding', 'confirm' => 'Tandai alat sudah diserahkan ke peminjam?'],
    'returned' => ['label' => 'Tandai Kembali', 'class' => 'btn-info', 'icon' => 'fa-undo', 'confirm' => 'Tandai alat sudah dikembalikan? Stok alat akan bertambah.'],
];
$available = \App\Models\LoanModel::TRANSITIONS[$loan['status']] ?? [];
?>
<?php if ($available === []) : ?>
    <span class="small text-muted">Selesai</span>
<?php else : ?>
    <div class="btn-group btn-group-sm" role="group">
        <?php foreach ($available as $target) : ?>
            <form action="<?= site_url('admin/loans/' . $loan['id'] . '/status') ?>" method="post" class="d-inline" onsubmit="return confirm('<?= esc($actions[$target]['confirm'], 'js') ?>')">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="<?= $target ?>">
                <button type="submit" class="btn btn-sm <?= $actions[$target]['class'] ?> ml-1" title="<?= esc($actions[$target]['label'], 'attr') ?>">
                    <i class="fas <?= $actions[$target]['icon'] ?>"></i> <?= esc($actions[$target]['label']) ?>
                </button>
            </form>
        <?php endforeach ?>
    </div>
<?php endif ?>
