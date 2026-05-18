<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="max-width:520px;">
    <h2>My Profile</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($profile['profile_pic'])): ?>
        <img src="<?= htmlspecialchars($profile['profile_pic']) ?>"
             style="width:90px;height:90px;border-radius:50%;object-fit:cover;margin-bottom:16px;display:block;">
    <?php endif; ?>

    <form method="POST" action="index.php?page=profile" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($profile['name']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" required>

        <label>Vehicle Type</label>
        <select name="vehicle_type" required>
            <?php foreach (['Motorcycle','Bicycle','Car','Scooter'] as $v): ?>
                <option value="<?= $v ?>" <?= $profile['vehicle_type']===$v?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>

        <label>Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*">

        <label>Email (cannot change)</label>
        <input type="text" value="<?= htmlspecialchars($profile['email']) ?>" disabled style="background:#f5f5f5;">

        <button type="submit" class="btn btn-primary" style="margin-top:6px;">Save Changes</button>
    </form>
</div>
</div></body></html>