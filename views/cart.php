<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Your Cart</h2>
    <?php if (empty($items)): ?>
        <p style="color:#888;">Your cart is empty. <a href="index.php?page=restaurants">Browse restaurants</a>.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Item</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>৳<?= $item['final_price'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>৳<?= number_format($item['subtotal'],2) ?></td>
                <td>
                    <form method="POST" action="index.php?page=cart_remove">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="font-size:12px;">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align:right;margin-top:16px;">
            <p style="font-size:16px;">Subtotal: <strong>৳<?= number_format($total,2) ?></strong></p>
            <p style="font-size:14px;color:#888;">Delivery Fee: ৳30.00</p>
            <p style="font-size:18px;font-weight:bold;color:#e74c3c;">
                Total: ৳<?= number_format($total+30,2) ?>
            </p>
            <a href="index.php?page=checkout" class="btn btn-primary" style="margin-top:12px;">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>
</div>
</body>
</html>