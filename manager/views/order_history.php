<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Order History</h2>
    <table>
        <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td style="font-size:13px;"><?= htmlspecialchars($o['items']) ?></td>
            <td>৳<?= number_format($o['total_amount'],2) ?></td>
            <td><span class="badge badge-blue"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span></td>
            <td style="font-size:13px;"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div></body></html>