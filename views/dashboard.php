<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <p style="color:#888;">What would you like to eat today?</p>
    <a href="index.php?page=restaurants" class="btn btn-primary" style="margin-top:12px;">Browse Restaurants</a>
</div>

<?php if (!empty($restaurants)): ?>
<div class="card">
    <h2>Open Restaurants</h2>
    <div class="restaurant-grid">
        <?php foreach ($restaurants as $r): ?>
        <div class="restaurant-card">
            <h3><?= htmlspecialchars($r['name']) ?></h3>
            <p style="color:#888;font-size:13px;"><?= htmlspecialchars($r['cuisine_type']) ?> — <?= htmlspecialchars($r['city']) ?></p>
            <a href="index.php?page=restaurant_detail&id=<?= $r['id'] ?>" class="btn btn-primary" style="margin-top:10px;font-size:13px;">View Menu</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($recent_orders)): ?>
<div class="card">
    <h2>Recent Orders</h2>
    <table>
        <thead><tr><th>Order #</th><th>Restaurant</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($recent_orders as $o): ?>
        <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['restaurant_name']) ?></td>
            <td>৳<?= number_format($o['total_amount'],2) ?></td>
            <td><span class="badge badge-blue"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span></td>
            <td><a href="index.php?page=track_order&id=<?= $o['id'] ?>" class="btn btn-secondary" style="font-size:12px;">Track</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
</div></body></html>