<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Menu Items</h2>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <table style="margin-bottom:24px;">
        <thead><tr><th>Item</th><th>Category</th><th>Price</th><th>Available</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['category_name'] ?? '—') ?></td>
            <td>৳<?= $item['price'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="is_available" value="<?= $item['is_available']?0:1 ?>">
                    <button type="submit" class="btn <?= $item['is_available']?'btn-success':'btn-secondary' ?>" style="font-size:12px;">
                        <?= $item['is_available']?'Available':'Unavailable' ?>
                    </button>
                </form>
            </td>
            <td>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn btn-danger" style="font-size:12px;">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="margin-bottom:12px;">Add New Item</h3>
    <form method="POST" enctype="multipart/form-data" style="max-width:500px;">
        <input type="hidden" name="action" value="add">
        <label>Name</label><input type="text" name="name" required>
        <label>Description</label><textarea name="description" rows="2"></textarea>
        <label>Price (৳)</label><input type="number" name="price" step="0.01" required>
        <label>Category</label>
        <select name="category_id" required>
            <option value="">-- Select --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Image</label><input type="file" name="image" accept="image/*">
        <label style="display:flex;align-items:center;gap:8px;font-weight:normal;">
            <input type="checkbox" name="is_available" checked style="width:auto;margin:0;"> Available immediately
        </label>
        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Add Item</button>
    </form>
</div>
</div></body></html>