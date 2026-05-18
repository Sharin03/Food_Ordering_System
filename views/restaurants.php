<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Browse Restaurants</h2>
    <form method="GET" action="index.php" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
        <input type="hidden" name="page" value="restaurants">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($_GET['search']??'') ?>" style="flex:1;margin:0;">
        <select name="cuisine" style="margin:0;">
            <option value="">All cuisines</option>
            <?php foreach ($cuisines as $c): ?>
                <option value="<?= $c['cuisine_type'] ?>" <?= ($_GET['cuisine']??'')===$c['cuisine_type']?'selected':'' ?>>
                    <?= htmlspecialchars($c['cuisine_type']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary" style="margin:0;">Search</button>
    </form>

    <?php if (empty($restaurants)): ?>
        <p style="color:#888;">No restaurants found.</p>
    <?php else: ?>
    <div class="restaurant-grid">
        <?php foreach ($restaurants as $r): ?>
        <div class="restaurant-card">
            <h3><?= htmlspecialchars($r['name']) ?></h3>
            <p style="font-size:13px;color:#888;"><?= htmlspecialchars($r['cuisine_type']) ?> — <?= htmlspecialchars($r['city']) ?></p>
            <p style="font-size:13px;margin:6px 0;">⭐ <?= number_format($r['avg_rating'],1) ?></p>
            <p style="font-size:13px;color:<?= $r['is_open']?'#27ae60':'#e74c3c' ?>;">
                <?= $r['is_open'] ? 'Open' : 'Closed' ?>
            </p>
            <a href="index.php?page=restaurant_detail&id=<?= $r['id'] ?>" class="btn btn-primary" style="margin-top:10px;font-size:13px;">View Menu</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</div></body></html>