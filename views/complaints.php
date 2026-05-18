<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Submit a Complaint</h2>
    <?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="POST" style="max-width:500px;">
        <label>Subject</label>
        <input type="text" name="subject" required>
        <label>Description</label>
        <textarea name="description" rows="4" required></textarea>
        <button type="submit" class="btn btn-primary">Submit Complaint</button>
    </form>

    <?php if (!empty($complaints)): ?>
    <h3 style="margin-top:24px;margin-bottom:12px;">Your Past Complaints</h3>
    <table>
        <thead><tr><th>Subject</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($complaints as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['subject']) ?></td>
            <td><span class="badge <?= $c['status']==='resolved'?'badge-green':'badge-orange' ?>">
                <?= ucfirst($c['status']) ?>
            </span></td>
            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</div></body></html>