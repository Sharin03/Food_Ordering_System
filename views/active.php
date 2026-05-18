<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Active Delivery</h2>
    <?php if (!$active): ?>
        <p style="color:#888;">No active delivery. <a href="index.php?page=available">Browse available orders</a>.</p>
    <?php else: ?>
        <table style="margin-bottom:20px;">
            <tr><th>Order #</th><td>#<?= $active['id'] ?></td></tr>
            <tr><th>Restaurant</th><td><?= htmlspecialchars($active['restaurant_name']) ?><br>
                <small><?= htmlspecialchars($active['restaurant_address']) ?></small></td></tr>
            <tr><th>Items</th><td><?= htmlspecialchars($active['items']) ?></td></tr>
            <tr><th>Drop-off</th><td><?= htmlspecialchars($active['delivery_address']) ?></td></tr>
            <tr><th>Total</th><td>৳<?= number_format($active['total_amount'], 2) ?></td></tr>
            <tr><th>Delivery Fee</th><td>৳<?= number_format($active['delivery_fee'] ?? 0, 2) ?></td></tr>
            <tr><th>Status</th><td><span class="badge badge-blue" id="status-badge">
                <?= ucfirst(str_replace('_',' ',$active['status'])) ?>
            </span></td></tr>
        </table>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <?php if ($active['status'] === 'picked_up'): ?>
                <button class="btn btn-warning" onclick="updateStatus('on_the_way')">Mark On the Way</button>
            <?php endif; ?>
            <?php if (in_array($active['status'], ['picked_up','on_the_way'])): ?>
                <button class="btn btn-success" onclick="updateStatus('delivered')">Mark Delivered</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
const orderId = <?= json_encode($active['id'] ?? null) ?>;

function updateStatus(status) {
    if (!orderId) return;
    fetch('api/update_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order_id: orderId, status: status})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('status-badge').textContent = status.replace('_',' ');
            if (status === 'delivered') {
                setTimeout(() => { location.href = 'index.php?page=history'; }, 1000);
            } else {
                location.reload();
            }
        } else {
            alert('Could not update status. Try again.');
        }
    });
}


setInterval(() => {
    fetch('api/check_assignments.php')
    .then(r => r.json())
    .then(data => {
        document.getElementById('notify-bar').style.display = data.count > 0 ? 'block' : 'none';
    });
}, 15000);
</script>
</div></body></html>