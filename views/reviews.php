<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>My Reviews</h2>
    <?php if (empty($reviews)): ?>
        <p style="color:#888;">You haven't submitted any reviews yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $rv): ?>
        <div style="border-bottom:1px solid #eee;padding:12px 0;">
            <strong><?= htmlspecialchars($rv['restaurant_name']) ?></strong>
            <span style="color:#f39c12;margin-left:8px;">⭐ <?= $rv['rating'] ?>/5</span>
            <p style="margin-top:4px;font-size:14px;"><?= htmlspecialchars($rv['comment']) ?></p>
            <p style="font-size:12px;color:#aaa;"><?= date('d M Y', strtotime($rv['created_at'])) ?></p>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</div></body></html>