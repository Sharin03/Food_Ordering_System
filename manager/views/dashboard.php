<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
</div>
<div class="card">
    <h2>Today's Stats</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="value"><?= $today['total'] ?? 0 ?></div>
            <div class="label">Orders delivered today</div>
        </div>
        <div class="stat-box">
            <div class="value">৳<?= number_format($today['revenue'] ?? 0, 2) ?></div>
            <div class="label">Revenue today</div>
        </div>
        <div class="stat-box">
            <div class="value"><?= count($active_orders) ?></div>
            <div class="label">Active orders</div>
        </div>
    </div>
</div>

<?php if (!empty($active_orders)): ?>
<div class="card">
    <h2>Active Orders</h2>
    <table>
        <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($active_orders as $o): ?>
        <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td>৳<?= number_format($o['total_amount'],2) ?></td>
            <td><span class="badge badge-orange"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span></td>
            <td><a href="index.php?page=orders" class="btn btn-primary" style="font-size:12px;">Manage</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
</div></body></html>