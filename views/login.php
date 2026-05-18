<?php include __DIR__ . '/layout.php'; ?>
<div class="card" style="max-width:420px;margin:60px auto;">
    <h2>Delivery Agent Login</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php?role=agent&amp;page=login">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn btn-primary" style="width:100%">Log In</button>
    </form>
    <p style="margin-top:14px;font-size:14px;text-align:center;">
        No account? <a href="index.php?role=agent&amp;page=register">Register here</a>
    </p>
</div>
</div></body></html>