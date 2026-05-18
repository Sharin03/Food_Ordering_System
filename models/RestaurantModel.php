<?php
class RestaurantModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getAll($search = '', $cuisine = '', $city = '') {
        $sql = "SELECT r.*, COALESCE(AVG(rv.rating),0) AS avg_rating
                FROM restaurants r
                LEFT JOIN reviews rv ON rv.restaurant_id=r.id
                WHERE r.is_approved=1";
        $params = []; $types = '';

        if ($search) { $sql .= " AND r.name LIKE ?"; $params[] = "%$search%"; $types .= 's'; }
        if ($cuisine) { $sql .= " AND r.cuisine_type=?"; $params[] = $cuisine; $types .= 's'; }
        if ($city)    { $sql .= " AND r.city=?"; $params[] = $city; $types .= 's'; }

        $sql .= " GROUP BY r.id ORDER BY avg_rating DESC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT r.*, COALESCE(AVG(rv.rating),0) AS avg_rating
             FROM restaurants r
             LEFT JOIN reviews rv ON rv.restaurant_id=r.id
             WHERE r.id=? AND r.is_approved=1 GROUP BY r.id"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getMenu($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT mc.name AS category, mi.*,
                    d.discount_pct,
                    CASE WHEN d.is_active=1 AND CURDATE() BETWEEN d.valid_from AND d.valid_until
                         THEN ROUND(mi.price*(1-d.discount_pct/100),2)
                         ELSE mi.price END AS final_price
             FROM menu_categories mc
             JOIN menu_items mi ON mi.category_id=mc.id
             LEFT JOIN discounts d ON d.menu_item_id=mi.id
             WHERE mc.restaurant_id=? AND mi.is_available=1
             ORDER BY mc.display_order, mi.name"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $menu = [];
        foreach ($rows as $row) {
            $menu[$row['category']][] = $row;
        }
        return $menu;
    }

    public function isFavourite($user_id, $restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM saved_restaurants WHERE customer_id=? AND restaurant_id=?"
        );
        $stmt->bind_param("ii", $user_id, $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function addFavourite($user_id, $restaurant_id) {
        $stmt = $this->conn->prepare(
            "INSERT IGNORE INTO saved_restaurants (customer_id,restaurant_id) VALUES (?,?)"
        );
        $stmt->bind_param("ii", $user_id, $restaurant_id);
        $stmt->execute();
    }

    public function removeFavourite($user_id, $restaurant_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM saved_restaurants WHERE customer_id=? AND restaurant_id=?"
        );
        $stmt->bind_param("ii", $user_id, $restaurant_id);
        $stmt->execute();
    }

    public function getFavourites($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT r.* FROM saved_restaurants sr
             JOIN restaurants r ON r.id=sr.restaurant_id
             WHERE sr.customer_id=?"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCuisines() {
        return $this->conn->query(
            "SELECT DISTINCT cuisine_type FROM restaurants WHERE is_approved=1"
        )->fetch_all(MYSQLI_ASSOC);
    }
}