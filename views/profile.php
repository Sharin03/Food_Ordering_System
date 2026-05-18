<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="max-width:520px;">
    <h2>My Profile</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($user['profile_pic'])): ?>
        <img src="<?= htmlspecialchars($user['profile_pic']) ?>"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:16px;display:block;">
    <?php endif; ?>
    <form method="POST" action="index.php?page=profile" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="new_password">
        <label>Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*">
        <label>Email (cannot change)</label>
        <input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#f5f5f5;">
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
    <div style="margin-top:16px;">
        <a href="index.php?page=addresses" class="btn btn-secondary">Manage Addresses</a>
        <a href="index.php?page=reviews" class="btn btn-secondary" style="margin-left:8px;">My Reviews</a>
        <a href="index.php?page=complaints" class="btn btn-secondary" style="margin-left:8px;">Complaints</a>
    </div>
</div>
</div></body></html>