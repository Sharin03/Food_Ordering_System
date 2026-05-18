<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Menu Categories</h2>
    <?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <?php if (!empty($categories)): ?>
    <table style="margin-bottom:20px;">
        <thead><tr><th>Category</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr>
            <td><?= htmlspecialchars($cat['name']) ?></td>
            <td style="display:flex;gap:6px;">
                <form method="POST" style="display:flex;gap:6px;">
                    <input type="hidden" name="action" value="rename">
                    <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" style="width:160px;margin:0;padding:6px;">
                    <button type="submit" class="btn btn-warning" style="font-size:12px;">Rename</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                    <button type="submit" class="btn btn-danger" style="font-size:12px;">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <h3 style="margin-bottom:12px;">Add New Category</h3>
    <form method="POST" style="display:flex;gap:10px;max-width:400px;">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" placeholder="Category name" required style="margin:0;">
        <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Add</button>
    </form>
</div>
</div></body></html>