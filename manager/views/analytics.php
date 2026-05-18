<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Sales Analytics</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="value">৳<?= number_format($revenue['today']??0,2) ?></div>
            <div class="label">Today's revenue</div>
        </div>
        <div class="stat-box">
            <div class="value">৳<?= number_format($revenue['this_week']??0,2) ?></div>
            <div class="label">This week</div>
        </div>
        <div class="stat-box">
            <div class="value">৳<?= number_format($revenue['this_month']??0,2) ?></div>
            <div class="label">This month</div>
        </div>
        <div class="stat-box">
            <div class="value"><?= $revenue['total_orders']??0 ?></div>
            <div class="label">Total delivered orders</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Top Ordered Items</h2>
    <?php if (empty($top_items)): ?>
        <p style="color:#888;">No data yet.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Item</th><th>Total Ordered</th></tr></thead>
            <tbody>
            <?php foreach ($top_items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['total_qty'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div></body></html>