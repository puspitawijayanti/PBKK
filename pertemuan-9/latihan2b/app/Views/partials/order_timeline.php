<?php
$steps = [
    'awaiting_payment'      => ['label' => 'Dibuat', 'time' => $order['created_at']],
    'awaiting_verification' => ['label' => 'Dibayar', 'time' => $order['paid_at']],
    'processing'            => ['label' => 'Diverifikasi', 'time' => $order['verified_at']],
    'shipped'               => ['label' => 'Dikirim', 'time' => $order['shipped_at']],
    'completed'             => ['label' => 'Selesai', 'time' => $order['completed_at']],
];
$keys = array_keys($steps);
$position = array_search($order['status'], $keys, true);
?>
<?php if ($order['status'] === 'cancelled') : ?>
    <div class="alert alert-danger mb-0">Pesanan dibatalkan pada <?= date_id($order['cancelled_at'], true) ?>. Stok produk sudah dikembalikan.</div>
<?php else : ?>
    <ol class="order-timeline list-unstyled d-flex mb-0">
        <?php foreach ($steps as $key => $step) : ?>
            <?php $done = $position !== false && array_search($key, $keys, true) <= $position ?>
            <li class="flex-fill text-center<?= $done ? ' done' : '' ?>">
                <span class="dot"></span>
                <div class="small fw-semibold mt-1"><?= esc($step['label']) ?></div>
                <div class="text-muted timeline-time"><?= $done && $step['time'] ? date_id($step['time'], true) : '&nbsp;' ?></div>
            </li>
        <?php endforeach ?>
    </ol>
<?php endif ?>
