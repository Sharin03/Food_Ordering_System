<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="text-align:center;">
    <h2 style="color:#27ae60;">✅ Order Placed!</h2>
    <p style="font-size:18px;margin:12px 0;">Order #<?= $order['id'] ?></p>
    <p>Estimated delivery: <strong><?= $order['estimated_delivery_minutes'] ?> minutes</strong></p>
    <p style="margin-top:8px;">Total: <strong>৳<?= number_format($order['total_amount'],2) ?></strong></p>
    <div style="margin-top:20px;display:flex;gap:12px;justify-content:center;">
        <a href="index.php?page=track_order&id=<?= $order['id'] ?>" class="btn btn-primary">Track Order</a>
        <a href="index.php?page=restaurants" class="btn btn-secondary">Order More</a>
    </div>
</div>
</div></body></html>