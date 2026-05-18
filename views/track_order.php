<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Track Order #<?= $order['id'] ?></h2>
    <p>Restaurant: <strong><?= htmlspecialchars($order['restaurant_name']) ?></strong></p>
    <p>Delivery to: <?= htmlspecialchars($order['delivery_address']) ?></p>
    <p style="margin-top:12px;">Status: <span class="badge badge-blue" id="status-badge">
        <?= ucfirst(str_replace('_',' ',$order['status'])) ?>
    </span></p>

    
    <?php
    $steps   = ['pending','accepted','preparing','ready','picked_up','on_the_way','delivered'];
    $current = array_search($order['status'], $steps);
    ?>
    <div style="display:flex;gap:4px;margin-top:20px;flex-wrap:wrap;">
        <?php foreach ($steps as $i => $step): ?>
        <div style="flex:1;text-align:center;font-size:11px;padding:6px 2px;
                    background:<?= $i<=$current?'#e74c3c':'#eee' ?>;
                    color:<?= $i<=$current?'white':'#888' ?>;
                    border-radius:4px;">
            <?= ucfirst(str_replace('_',' ',$step)) ?>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!in_array($order['status'], ['delivered','cancelled'])): ?>
    <p style="margin-top:16px;font-size:13px;color:#888;">Page auto-refreshes every 15 seconds.</p>
    <?php endif; ?>
</div>

<script>
const orderId = <?= $order['id'] ?>;
const statusBadge = document.getElementById('status-badge');

function checkStatus() {
    fetch('api/track_order.php?order_id=' + orderId)
    .then(r => r.json())
    .then(data => {
        if (data.status) {
            statusBadge.textContent = data.status.replace(/_/g,' ');
            if (data.status === 'delivered') {
                clearInterval(poller);
                statusBadge.style.background = '#d4edda';
                statusBadge.style.color = '#155724';
            }
        }
    });
}

const poller = setInterval(checkStatus, 15000);
</script>
</div></body></html>