<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php if ($messages === []) : ?>
    <div class="card shadow"><div class="card-body text-center text-muted py-5">Belum ada pesan masuk.</div></div>
<?php endif ?>
<?php foreach ($messages as $message) : ?>
    <div class="card shadow mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap">
                <div>
                    <h6 class="font-weight-bold text-gray-800 mb-1"><?= esc($message['subject']) ?></h6>
                    <div class="small text-muted mb-2">
                        <?= esc($message['name']) ?> &lt;<a href="mailto:<?= esc($message['email'], 'attr') ?>"><?= esc($message['email']) ?></a>&gt;
                        &middot; <?= date_id($message['created_at'], true) ?>
                    </div>
                </div>
                <form action="<?= site_url('admin/messages/' . $message['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus pesan ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                </form>
            </div>
            <p class="mb-0"><?= nl2br(esc($message['body'])) ?></p>
        </div>
    </div>
<?php endforeach ?>
<?= $pager->links('default', 'bootstrap') ?>
<?= $this->endSection() ?>
