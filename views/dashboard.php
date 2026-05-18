<?php include __DIR__ . '/layout.php'; ?>

<div class="card">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
    <p style="color:#888;font-size:14px;">Here's your overview for today.</p>
</div>

<div class="card">
    <h2>Availability</h2>
    <p style="margin-bottom:12px;font-size:14px;">
        Status: <span id="status-text" style="font-weight:bold;color:<?= $profile['is_online'] ? '#27ae60' : '#e74c3c' ?>">
            <?= $profile['is_online'] ? 'Online' : 'Offline' ?>
        </span>
    </p>
    <button id="toggle-btn" class="btn <?= $profile['is_online'] ? 'btn-danger' : 'btn-success' ?>"
            onclick="toggleOnline(<?= $profile['is_online'] ? 0 : 1 ?>)">
        <?= $profile['is_online'] ? 'Go Offline' : 'Go Online' ?>
    </button>
</div>


<div class="card">
    <h2>Current Delivery</h2>
    <?php if ($active): ?>
        <p><strong>Order #<?= $active['id'] ?></strong> — <?= htmlspecialchars($active['restaurant_name']) ?></p>
        <p style="color:#888;font-size:13px;margin:4px 0;">Drop-off: <?= htmlspecialchars($active['delivery_address']) ?></p>
        <p>Status: <span class="badge badge-blue"><?= ucfirst(str_replace('_',' ',$active['status'])) ?></span></p>
        <a href="index.php?page=active" class="btn btn-primary" style="margin-top:12px;">Manage Delivery</a>
    <?php else: ?>
        <p style="color:#888;">No active delivery. <a href="index.php?page=available">Browse available orders</a>.</p>
    <?php endif; ?>
</div>


<div class="card">
    <h2>Performance</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="value"><?= $stats['total_deliveries'] ?></div>
            <div class="label">Total deliveries</div>
        </div>
        <div class="stat-box">
            <div class="value"><?= $stats['avg_time'] ?> min</div>
            <div class="label">Avg delivery time</div>
        </div>
        <div class="stat-box">
            <div class="value">৳<?= number_format($profile['total_earnings'], 2) ?></div>
            <div class="label">Total earned</div>
        </div>
    </div>
</div>

<script>
function toggleOnline(newStatus) {
    fetch('api/toggle_availability.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({is_online: newStatus})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
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