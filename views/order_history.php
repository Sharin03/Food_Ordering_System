<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Order History</h2>
    <?php if (empty($orders)): ?>
        <p style="color:#888;">No orders yet.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Order #</th><th>Restaurant</th><th>Total</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['restaurant_name']) ?></td>
                <td>৳<?= number_format($o['total_amount'],2) ?></td>
                <td style="font-size:13px;"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                <td><span class="badge badge-blue"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span></td>
                <td style="display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="index.php?page=track_order&id=<?= $o['id'] ?>" class="btn btn-secondary" style="font-size:12px;">Track</a>

                    <?php if ($o['status'] === 'pending'): ?>
                    <form method="POST" action="index.php?page=cancel_order">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="font-size:12px;">Cancel</button>
                    </form>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=reorder">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <button type="submit" class="btn btn-primary" style="font-size:12px;">Reorder</button>
                    </form>

                    <?php if ($o['status'] === 'delivered'): ?>
                    <button class="btn btn-success" style="font-size:12px;"
                        onclick="document.getElementById('review-<?= $o['id'] ?>').style.display='block'">
                        Review
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($o['status'] === 'delivered'): ?>
            <tr id="review-<?= $o['id'] ?>" style="display:none;">
                <td colspan="6" style="background:#f9f9f9;padding:16px;">
                    <form method="POST" action="index.php?page=submit_review">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <input type="hidden" name="restaurant_id" value="<?= $o['restaurant_id'] ?>">
                        <label>Rating</label>
                        <select name="rating" style="width:auto;margin-bottom:8px;">
                            <option value="5">⭐⭐⭐⭐⭐ 5</option>
                            <option value="4">⭐⭐⭐⭐ 4</option>
                            <option value="3">⭐⭐⭐ 3</option>
                            <option value="2">⭐⭐ 2</option>
                            <option value="1">⭐ 1</option>
                        </select>
                        <label>Comment</label>
                        <textarea name="comment" rows="2" required></textarea>
                        <button type="submit" class="btn btn-success" style="font-size:13px;">Submit Review</button>
                    </form>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div></body></html>