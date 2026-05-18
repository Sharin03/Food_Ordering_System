<?php
class OrderModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getCartItems($cart) {
        $items = []; $total = 0;
        foreach ($cart as $item_id => $qty) {
            $stmt = $this->conn->prepare(
                "SELECT mi.*, mc.restaurant_id,
                        CASE WHEN d.is_active=1 AND CURDATE() BETWEEN d.valid_from AND d.valid_until
                             THEN ROUND(mi.price*(1-d.discount_pct/100),2)
                             ELSE mi.price END AS final_price
                 FROM menu_items mi
                 JOIN menu_categories mc ON mc.id=mi.category_id
                 LEFT JOIN discounts d ON d.menu_item_id=mi.id
                 WHERE mi.id=?"
            );
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $item = $stmt->get_result()->fetch_assoc();
            if ($item) {
                $item['quantity'] = $qty;
                $item['subtotal'] = $item['final_price'] * $qty;
                $total += $item['subtotal'];
                $items[] = $item;
            }
        }
        return ['items' => $items, 'total' => $total];
    }

    public function getRestaurantIdFromItem($item_id) {
        $stmt = $this->conn->prepare(
            "SELECT mc.restaurant_id FROM menu_items mi
             JOIN menu_categories mc ON mc.id=mi.category_id WHERE mi.id=?"
        );
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['restaurant_id'] ?? 0;
    }

    public function placeOrder($customer_id, $restaurant_id, $delivery_address,
                               $payment_method, $subtotal, $delivery_fee, $total) {
        $stmt = $this->conn->prepare(
            "INSERT INTO orders (customer_id,restaurant_id,delivery_address,payment_method,
             subtotal,delivery_fee,total_amount,status,estimated_delivery_minutes)
             VALUES (?,?,?,?,?,?,?,'pending',45)"
        );
        $stmt->bind_param("iissddd", $customer_id, $restaurant_id,
            $delivery_address, $payment_method, $subtotal, $delivery_fee, $total);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function addOrderItem($order_id, $item_id, $qty, $price) {
        $stmt = $this->conn->prepare(
            "INSERT INTO order_items (order_id,menu_item_id,quantity,unit_price) VALUES (?,?,?,?)"
        );
        $stmt->bind_param("iiid", $order_id, $item_id, $qty, $price);
        $stmt->execute();
    }

    public function getHistory($customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.*, r.name AS restaurant_name FROM orders o
             JOIN restaurants r ON r.id=o.restaurant_id
             WHERE o.customer_id=? ORDER BY o.created_at DESC"
        );
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($order_id, $customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.*, r.name AS restaurant_name FROM orders o
             JOIN restaurants r ON r.id=o.restaurant_id
             WHERE o.id=? AND o.customer_id=?"
        );
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cancel($order_id, $customer_id) {
        $stmt = $this->conn->prepare(
            "UPDATE orders SET status='cancelled'
             WHERE id=? AND customer_id=? AND status='pending'"
        );
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
    }

    public function getItemsByOrderId($order_id, $customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT oi.menu_item_id, oi.quantity FROM order_items oi
             JOIN orders o ON o.id=oi.order_id
             WHERE oi.order_id=? AND o.customer_id=?"
        );
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getStatus($order_id, $customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT status FROM orders WHERE id=? AND customer_id=?"
        );
        $stmt->bind_param("ii", $order_id, $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['status'] ?? 'unknown';
    }
}