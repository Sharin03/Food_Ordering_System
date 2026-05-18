<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>My Favourites</h2>
    <?php if (empty($restaurants)): ?>
        <p style="color:#888;">No saved restaurants yet. Browse and save some!</p>
    <?php else: ?>
    <div class="restaurant-grid">
        <?php foreach ($restaurants as $r): ?>
        <div class="restaurant-card">
            <h3><?= htmlspecialchars($r['name']) ?></h3>
            <p style="font-size:13px;color:#888;"><?= htmlspecialchars($r['cuisine_type']) ?> — <?= htmlspecialchars($r['city']) ?></p>
            <a href="index.php?page=restaurant_detail&id=<?= $r['id'] ?>" class="btn btn-primary" style="margin-top:10px;font-size:13px;">View Menu</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</div></body></html>