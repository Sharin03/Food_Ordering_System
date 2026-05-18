<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Customer Reviews</h2>
    <?php if (empty($reviews)): ?>
        <p style="color:#888;">No reviews yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $rv): ?>
        <div style="border-bottom:1px solid #eee;padding:14px 0;">
            <strong><?= htmlspecialchars($rv['customer_name']) ?></strong>
            <span style="color:#f39c12;margin-left:8px;">⭐ <?= $rv['rating'] ?>/5</span>
            <span style="font-size:12px;color:#aaa;margin-left:8px;"><?= date('d M Y', strtotime($rv['created_at'])) ?></span>
            <p style="margin-top:6px;font-size:14px;"><?= htmlspecialchars($rv['comment']) ?></p>

            <?php if ($rv['manager_reply']): ?>
                <p style="margin-top:8px;font-size:13px;background:#f0f8ff;padding:8px;border-radius:4px;color:#0c5460;">
                    Your reply: <?= htmlspecialchars($rv['manager_reply']) ?>
                </p>
            <?php else: ?>
                <form method="POST" action="index.php?page=reply_review" style="margin-top:8px;display:flex;gap:8px;">
                    <input type="hidden" name="review_id" value="<?= $rv['id'] ?>">
                    <input type="text" name="reply" placeholder="Write a reply..." required style="margin:0;flex:1;">
                    <button type="submit" class="btn btn-primary" style="font-size:12px;white-space:nowrap;">Reply</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</div></body></html>