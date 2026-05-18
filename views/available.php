<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Available Deliveries</h2>
    <?php if (!$is_online): ?>
        <div class="alert alert-info">You are offline. Go online from your dashboard to accept deliveries.</div>
    <?php elseif (empty($orders)): ?>
        <p style="color:#888;">No orders available right now. Check back soon.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Restaurant</th>
                    <th>Drop-off</th>
                    <th>Amount</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['restaurant_name']) ?><br>
                        <small style="color:#888;"><?= htmlspecialchars($o['restaurant_address']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($o['delivery_address']) ?></td>
                    <td>৳<?= number_format($o['total_amount'], 2) ?></td>
                    <td style="font-size:13px;color:#888;"><?= date('h:i A', strtotime($o['created_at'])) ?></td>
                    <td>
                        <form method="POST" action="index.php?page=accept" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <button type="submit" class="btn btn-success">Accept</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>

setInterval(() => location.reload(), 20000);
</script>
</div></body></html>