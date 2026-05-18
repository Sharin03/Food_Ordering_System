<?php

class OrderModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // =========================================================
    // AVAILABLE ORDERS
    // =========================================================
    public function getAvailableOrders() {

        /*
         FIX:
         Show all manager-approved orders
         that are not yet assigned to agents.
        */

        $stmt = $this->conn->prepare(
            "SELECT
                o.id,
                o.delivery_address,
                o.total_amount,
                o.created_at,
                o.status,
                r.name AS restaurant_name,
                r.address AS restaurant_address
             FROM orders o
             JOIN restaurants r
             ON r.id = o.restaurant_id
             WHERE o.agent_id IS NULL
             AND o.status IN
             ('confirmed','accepted','preparing','ready')
             ORDER BY o.created_at ASC"
        );

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // =========================================================
    // ACCEPT ORDER
    // =========================================================
    public function acceptOrder($order_id, $agent_id) {

        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET agent_id=?, status='picked_up'
             WHERE id=? AND agent_id IS NULL"
        );

        $stmt->bind_param("ii", $agent_id, $order_id);

        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            return false;
        }

        $stmt2 = $this->conn->prepare(
            "INSERT INTO delivery_assignments
            (order_id, agent_id, status)
            VALUES (?,?,'assigned')"
        );

        $stmt2->bind_param(
            "ii",
            $order_id,
            $agent_id
        );

        $stmt2->execute();

        return true;
    }

    // =========================================================
    // DECLINE ORDER
    // =========================================================
    public function declineOrder($order_id) {

        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET agent_id=NULL, status='ready'
             WHERE id=?"
        );

        $stmt->bind_param("i", $order_id);

        $stmt->execute();
    }

    // =========================================================
    // UPDATE DELIVERY STATUS
    // =========================================================
    public function updateStatus($order_id, $agent_id, $new_status) {

        $allowed = ['on_the_way', 'delivered'];

        if (!in_array($new_status, $allowed)) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET status=?
             WHERE id=? AND agent_id=?"
        );

        $stmt->bind_param(
            "sii",
            $new_status,
            $order_id,
            $agent_id
        );

        $stmt->execute();

        // DELIVERED
        if ($new_status === 'delivered') {

            $stmt2 = $this->conn->prepare(
                "UPDATE delivery_assignments
                 SET delivered_at=NOW(),
                     status='delivered'
                 WHERE order_id=? AND agent_id=?"
            );

            $stmt2->bind_param(
                "ii",
                $order_id,
                $agent_id
            );

            $stmt2->execute();

            $stmt3 = $this->conn->prepare(
                "UPDATE delivery_agents
                 SET total_earnings =
                 total_earnings +
                 (SELECT delivery_fee
                  FROM orders
                  WHERE id=?)
                 WHERE id=?"
            );

            $stmt3->bind_param(
                "ii",
                $order_id,
                $agent_id
            );

            $stmt3->execute();
        }

        // ON THE WAY
        if ($new_status === 'on_the_way') {

            $stmt2 = $this->conn->prepare(
                "UPDATE delivery_assignments
                 SET picked_up_at=NOW(),
                     status='on_the_way'
                 WHERE order_id=? AND agent_id=?"
            );

            $stmt2->bind_param(
                "ii",
                $order_id,
                $agent_id
            );

            $stmt2->execute();
        }

        return true;
    }

    // =========================================================
    // ACTIVE ORDER
    // =========================================================
    public function getActiveOrder($agent_id) {

        $stmt = $this->conn->prepare(
            "SELECT
                o.id,
                o.delivery_address,
                o.status,
                o.total_amount,
                o.delivery_fee,
                r.name AS restaurant_name,
                r.address AS restaurant_address,
                GROUP_CONCAT(
                    CONCAT(mi.name, ' x', oi.quantity)
                    SEPARATOR ', '
                ) AS items
             FROM orders o
             JOIN restaurants r
             ON r.id = o.restaurant_id
             JOIN order_items oi
             ON oi.order_id = o.id
             JOIN menu_items mi
             ON mi.id = oi.menu_item_id
             WHERE o.agent_id = ?
             AND o.status NOT IN
             ('delivered','cancelled')
             GROUP BY o.id
             LIMIT 1"
        );

        $stmt->bind_param("i", $agent_id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // =========================================================
    // DELIVERY HISTORY
    // =========================================================
    public function getDeliveryHistory($agent_id) {

        $stmt = $this->conn->prepare(
            "SELECT
                o.id,
                o.delivery_address,
                o.delivery_fee,
                o.total_amount,
                o.created_at,
                r.name AS restaurant_name,
                r.city,
                da.delivered_at
             FROM orders o
             JOIN restaurants r
             ON r.id = o.restaurant_id
             JOIN delivery_assignments da
             ON da.order_id = o.id
             WHERE da.agent_id = ?
             AND da.status = 'delivered'
             ORDER BY da.delivered_at DESC"
        );

        $stmt->bind_param("i", $agent_id);

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}