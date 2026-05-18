<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="max-width:480px;margin:40px auto;">
    <h2>Register as Delivery Agent</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php?role=agent&amp;page=register">
        <label>Full Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Phone</label>
        <input type="text" name="phone" required>
        <label>Vehicle Type</label>
        <select name="vehicle_type" required>
            <option value="">-- Select --</option>
            <option value="Motorcycle">Motorcycle</option>
            <option value="Bicycle">Bicycle</option>
            <option value="Car">Car</option>
            <option value="Scooter">Scooter</option>
        </select>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
        <button type="submit" class="btn btn-primary" style="width:100%">Register</button>
    </form>
    <p style="margin-top:14px;font-size:14px;text-align:center;">
        Already have an account? <a href="index.php?role=agent&amp;page=login">Log in</a>
    </p>
</div>
</div></body></html>