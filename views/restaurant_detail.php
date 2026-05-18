<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
    <p style="color:#888;"><?= htmlspecialchars($restaurant['cuisine_type']) ?> — <?= htmlspecialchars($restaurant['city']) ?></p>
    <p>⭐ <?= number_format($restaurant['avg_rating'],1) ?> &nbsp;|&nbsp;
       <?= htmlspecialchars($restaurant['opening_hours'] ?? 'N/A') ?></p>
    <p style="margin-top:8px;"><?= htmlspecialchars($restaurant['description'] ?? '') ?></p>

    <form method="POST" action="index.php?page=toggle_favourite" style="display:inline;">
        <input type="hidden" name="restaurant_id" value="<?= $restaurant['id'] ?>">
        <button type="submit" class="btn <?= $is_fav ? 'btn-danger' : 'btn-secondary' ?>" style="margin-top:12px;">
            <?= $is_fav ? '❤️ Unsave' : '🤍 Save' ?>
        </button>
    </form>
</div>

<?php foreach ($menu as $category => $items): ?>
<div class="card">
    <h2><?= htmlspecialchars($category) ?></h2>
    <table>
        <thead><tr><th>Item</th><th>Price</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                <small style="color:#888;"><?= htmlspecialchars($item['description'] ?? '') ?></small>
            </td>
            <td>
                <?php if ($item['discount_pct'] && $item['final_price'] < $item['price']): ?>
                    <span style="text-decoration:line-through;color:#aaa;">৳<?= $item['price'] ?></span><br>
                    <span style="color:#27ae60;font-weight:bold;">৳<?= $item['final_price'] ?></span>
                    <span style="background:#d4edda;color:#155724;font-size:11px;padding:2px 6px;border-radius:10px;"><?= $item['discount_pct'] ?>% off</span>
                <?php else: ?>
                    ৳<?= $item['price'] ?>
                <?php endif; ?>
            </td>
            <td>
                <form method="POST" action="index.php?page=cart_add" style="display:flex;gap:6px;align-items:center;">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" style="width:50px;margin:0;padding:6px;">
                    <button type="submit" class="btn btn-primary" style="font-size:12px;">Add</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<?php if (!empty($reviews)): ?>
<div class="card">
    <h2>Customer Reviews</h2>
    <?php foreach ($reviews as $rv): ?>
    <div style="border-bottom:1px solid #eee;padding:12px 0;">
        <strong><?= htmlspecialchars($rv['customer_name']) ?></strong>
        <span style="color:#f39c12;">⭐ <?= $rv['rating'] ?></span>
        <p style="margin-top:4px;font-size:14px;"><?= htmlspecialchars($rv['comment']) ?></p>
        <?php if ($rv['manager_reply']): ?>
            <p style="margin-top:6px;font-size:13px;color:#555;background:#f8f9fa;padding:8px;border-radius:4px;">
                <em>Restaurant replied: <?= htmlspecialchars($rv['manager_reply']) ?></em>
            </p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div></body></html>