<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Active Orders</h2>
    <?php if (empty($orders)): ?>
        <p style="color:#888;">No active orders right now.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td style="font-size:13px;"><?= htmlspecialchars($o['items']) ?></td>
                <td>৳<?= number_format($o['total_amount'],2) ?></td>
                <td><span class="badge badge-orange"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span></td>
                <td style="display:flex;gap:6px;flex-wrap:wrap;">
                    <?php if ($o['status']==='pending'): ?>
                        <form method="POST" action="index.php?page=accept_order">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <button type="submit" class="btn btn-success" style="font-size:12px;">Accept</button>
                        </form>
                        <form method="POST" action="index.php?page=reject_order">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <button type="submit" class="btn btn-danger" style="font-size:12px;">Reject</button>
                        </form>
                    <?php elseif ($o['status']==='accepted'): ?>
                        <form method="POST" action="index.php?page=update_order">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <input type="hidden" name="status" value="preparing">
                            <button type="submit" class="btn btn-warning" style="font-size:12px;">Preparing</button>
                        </form>
                    <?php elseif ($o['status']==='preparing'): ?>
                        <form method="POST" action="index.php?page=update_order">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" class="btn btn-primary" style="font-size:12px;">Ready for Pickup</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div></body></html>