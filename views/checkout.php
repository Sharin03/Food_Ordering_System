<?php include __DIR__ . '/layout.php'; ?>
<div class="card">
    <h2>Checkout</h2>
    <form method="POST" action="index.php?page=order_confirm">

        <label>Delivery Address</label>
        <?php if (!empty($addresses)): ?>
            <select name="delivery_address" required style="margin-bottom:8px;">
                <?php foreach ($addresses as $a): ?>
                    <option value="<?= htmlspecialchars($a['address_line'].', '.$a['city']) ?>"
                        <?= $a['is_default']?'selected':'' ?>>
                        <?= htmlspecialchars($a['label'].': '.$a['address_line'].', '.$a['city']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p style="font-size:13px;margin-bottom:12px;">Or enter a new address:</p>
        <?php endif; ?>
        <input type="text" name="delivery_address" placeholder="Enter delivery address" <?= empty($addresses)?'required':'' ?>>

        <label>Payment Method</label>
        <select name="payment_method">
            <option value="cash">Cash on Delivery</option>
            <option value="card">Card</option>
        </select>

        <h3 style="margin-bottom:10px;">Order Summary</h3>
        <table>
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>৳<?= number_format($item['subtotal'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align:right;margin-top:12px;">
            <p>Subtotal: ৳<?= number_format($subtotal,2) ?></p>
            <p>Delivery Fee: ৳<?= $delivery_fee ?></p>
            <p style="font-size:18px;font-weight:bold;color:#e74c3c;">Total: ৳<?= number_format($total,2) ?></p>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px;">Place Order</button>
    </form>
</div>
</div></body></html>