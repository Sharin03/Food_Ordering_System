<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="max-width:560px;">
    <h2>Restaurant Profile</h2>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if (!empty($restaurant['logo_path'])): ?>
        <img src="<?= htmlspecialchars($restaurant['logo_path']) ?>"
             style="width:80px;height:80px;object-fit:cover;border-radius:8px;margin-bottom:16px;display:block;">
    <?php endif; ?>
    <form method="POST" action="index.php?page=profile" enctype="multipart/form-data">
        <label>Restaurant Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($restaurant['name']) ?>" required>
        <label>Description</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($restaurant['description']??'') ?></textarea>
        <label>Cuisine Type</label>
        <input type="text" name="cuisine_type" value="<?= htmlspecialchars($restaurant['cuisine_type']??'') ?>">
        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($restaurant['address']??'') ?>">
        <label>City</label>
        <input type="text" name="city" value="<?= htmlspecialchars($restaurant['city']??'') ?>">
        <label>Opening Hours</label>
        <input type="text" name="opening_hours" value="<?= htmlspecialchars($restaurant['opening_hours']??'') ?>" placeholder="e.g. 9am - 10pm">
        <label>Logo</label>
        <input type="file" name="logo" accept="image/*">
        <label style="display:flex;align-items:center;gap:8px;font-weight:normal;margin-bottom:14px;">
            <input type="checkbox" name="is_open" <?= ($restaurant['is_open']??0)?'checked':'' ?> style="width:auto;margin:0;">
            Restaurant is currently open
        </label>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</div></body></html>