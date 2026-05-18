<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Discount Campaigns</h2>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <table style="margin-bottom:24px;">
        <thead><tr><th>Item</th><th>Discount</th><th>Valid From</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($discounts as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['item_name']) ?></td>
            <td><?= $d['discount_pct'] ?>%</td>
            <td><?= $d['valid_from'] ?></td>
            <td><?= $d['valid_until'] ?></td>
            <td><span class="badge <?= $d['is_active']?'badge-green':'badge-red' ?>"><?= $d['is_active']?'Active':'Inactive' ?></span></td>
            <td style="display:flex;gap:6px;">
                <form method="POST">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="discount_id" value="<?= $d['id'] ?>">
                    <input type="hidden" name="is_active" value="<?= $d['is_active']?0:1 ?>">
                    <button type="submit" class="btn btn-warning" style="font-size:12px;"><?= $d['is_active']?'Deactivate':'Activate' ?></button>
                </form>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="discount_id" value="<?= $d['id'] ?>">
                    <button type="submit" class="btn btn-danger" style="font-size:12px;">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="margin-bottom:12px;">Create New Discount</h3>
    <form method="POST" style="max-width:460px;">
        <input type="hidden" name="action" value="add">
        <label>Menu Item</label>
        <select name="menu_item_id" required>
            <option value="">-- Select item --</option>
            <?php foreach ($items as $item): ?>
                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Discount %</label>
        <input type="number" name="discount_pct" min="1" max="100" required>
        <label>Valid From</label>
        <input type="date" name="valid_from" required>
        <label>Valid Until</label>
        <input type="date" name="valid_until" required>
        <button type="submit" class="btn btn-primary">Create Discount</button>
    </form>
</div>
</div></body></html>