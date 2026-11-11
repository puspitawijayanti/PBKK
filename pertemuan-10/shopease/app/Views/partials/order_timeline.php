<?php
/**
 * Riwayat status pesanan dari order_logs (BR-012).
 *
 * @var array $logs
 */
?>
<ol class="timeline list-unstyled mb-0">
    <?php foreach ($logs as $log) : ?>
        <li class="timeline-item">
            <div class="d-flex flex-wrap justify-content-between gap-1">
                <?= status_badge('order', $log['to_status']) ?>
                <small class="text-body-secondary"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></small>
            </div>
            <p class="small mb-0 mt-1">
                <?= esc($log['note']) ?>
                <?php if (! empty($log['user_name'])) : ?>
                    <span class="text-body-secondary">&mdash; <?= esc($log['user_name']) ?> (<?= role_label($log['user_role']) ?>)</span>
                <?php endif ?>
            </p>
        </li>
    <?php endforeach ?>
</ol>
