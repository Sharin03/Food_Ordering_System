<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Earnings Summary</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="value" style="color:#27ae60;">৳<?= number_format($summary['today'] ?? 0, 2) ?></div>
            <div class="label">Today</div>
        </div>
        <div class="stat-box">
            <div class="value" style="color:#3498db;">৳<?= number_format($summary['this_week'] ?? 0, 2) ?></div>
            <div class="label">This week</div>
        </div>
        <div class="stat-box">
            <div class="value" style="color:#9b59b6;">৳<?= number_format($summary['this_month'] ?? 0, 2) ?></div>
            <div class="label">This month</div>
        </div>
        <div class="stat-box">
            <div class="value" style="color:#e67e22;">৳<?= number_format($summary['all_time'] ?? 0, 2) ?></div>
            <div class="label">All time</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Recent Earnings</h2>
    <?php if (empty($history)): ?>
        <p style="color:#888;">No earnings yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Order #</th><th>Restaurant</th><th>Date</th><th>Earned</th></tr>
            </thead>
            <tbody>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td>#<?= $h['id'] ?></td>
                    <td><?= htmlspecialchars($h['restaurant_name']) ?></td>
                    <td><?= date('d M Y', strtotime($h['delivered_at'])) ?></td>
                    <td style="color:#27ae60;font-weight:bold;">৳<?= number_format($h['delivery_fee'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div></body></html>