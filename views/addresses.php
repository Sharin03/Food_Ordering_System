<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Saved Addresses</h2>
    <?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <?php if (empty($addresses)): ?>
        <p style="color:#888;margin-bottom:16px;">No saved addresses yet.</p>
    <?php else: ?>
        <table style="margin-bottom:20px;">
            <thead><tr><th>Label</th><th>Address</th><th>City</th><th>Default</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($addresses as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['label']) ?></td>
                <td><?= htmlspecialchars($a['address_line']) ?></td>
                <td><?= htmlspecialchars($a['city']) ?></td>
                <td><?= $a['is_default'] ? '✅' : '' ?></td>
                <td style="display:flex;gap:6px;">
                    <?php if (!$a['is_default']): ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="set_default">
                        <input type="hidden" name="addr_id" value="<?= $a['id'] ?>">
                        <button type="submit" class="btn btn-secondary" style="font-size:12px;">Set Default</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="addr_id" value="<?= $a['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="font-size:12px;">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3 style="margin-bottom:12px;">Add New Address</h3>
    <form method="POST" style="max-width:400px;">
        <input type="hidden" name="action" value="add">
        <label>Label (e.g. Home, Office)</label>
        <input type="text" name="label" required>
        <label>Address</label>
        <input type="text" name="address_line" required>
        <label>City</label>
        <input type="text" name="city" required>
        <button type="submit" class="btn btn-primary">Add Address</button>
    </form>
</div>
</div></body></html>