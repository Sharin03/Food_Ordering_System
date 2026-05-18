<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Complaints Related to Your Restaurant</h2>
    <?php if (empty($complaints)): ?>
        <p style="color:#888;">No complaints found.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>From</th><th>Subject</th><th>Description</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($complaints as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['submitter_name']) ?></td>
                <td><?= htmlspecialchars($c['subject']) ?></td>
                <td style="font-size:13px;"><?= htmlspecialchars($c['description']) ?></td>
                <td><span class="badge <?= $c['status']==='resolved'?'badge-green':'badge-orange' ?>"><?= ucfirst($c['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div></body></html>