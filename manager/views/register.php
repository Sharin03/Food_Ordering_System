<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="max-width:520px;margin:40px auto;">
    <h2>Register Restaurant</h2>
    <?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="POST" action="index.php?role=manager&amp;page=register">
        <h3 style="margin-bottom:12px;">Your Account</h3>
        <label>Full Name</label><input type="text" name="name" required>
        <label>Email</label><input type="email" name="email" required>
        <label>Phone</label><input type="text" name="phone" required>
        <label>Password</label><input type="password" name="password" required>
        <label>Confirm Password</label><input type="password" name="confirm_password" required>

        <h3 style="margin:16px 0 12px;">Restaurant Details</h3>
        <label>Restaurant Name</label><input type="text" name="restaurant_name" required>
        <label>Cuisine Type</label><input type="text" name="cuisine_type" placeholder="e.g. Fast Food, Chinese" required>
        <label>Address</label><input type="text" name="address" required>
        <label>City</label><input type="text" name="city" required>

        <button type="submit" class="btn btn-primary" style="width:100%">Submit for Approval</button>
    </form>
</div>
</div></body></html>