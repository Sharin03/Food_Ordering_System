<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Delivery History</h2>
    <?php if (empty($history)): ?>
        <p style="color:#888;">No completed deliveries yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Restaurant</th>
                    <th>Drop-off area</th>
                    <th>Delivered at</th>
                    <th>Earned</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($history as $h): ?>
                <tr>
                    <td>#<?= $h['id'] ?></td>
                    <td><?= htmlspecialchars($h['restaurant_name']) ?></td>
                    <td><?= htmlspecialchars($h['city'] ?? $h['delivery_address']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($h['delivered_at'])) ?></td>
                    <td style="color:#27ae60;font-weight:bold;">৳<?= number_format($h['delivery_fee'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div></body></html>