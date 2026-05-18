<?php
class RestaurantController {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT o.*,r.name AS restaurant_name FROM orders o
             JOIN restaurants r ON r.id=o.restaurant_id
             WHERE o.customer_id=? ORDER BY o.created_at DESC LIMIT 5"
        );
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $recent_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt2 = $this->conn->prepare("SELECT * FROM restaurants WHERE is_approved=1 AND is_open=1 LIMIT 6");
        $stmt2->execute();
        $restaurants = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/dashboard.php';
    }

    public function browse() {
        $search  = trim($_GET['search'] ?? '');
        $cuisine = trim($_GET['cuisine'] ?? '');
        $city    = trim($_GET['city'] ?? '');

        $sql = "SELECT r.*,COALESCE(AVG(rv.rating),0) AS avg_rating
                FROM restaurants r LEFT JOIN reviews rv ON rv.restaurant_id=r.id
                WHERE r.is_approved=1";
        $params = []; $types = '';
        if ($search)  { $sql .= ' AND r.name LIKE ?';         $params[] = "%$search%"; $types .= 's'; }
        if ($cuisine) { $sql .= ' AND r.cuisine_type=?';      $params[] = $cuisine;    $types .= 's'; }
        if ($city)    { $sql .= ' AND r.city=?';              $params[] = $city;       $types .= 's'; }
        $sql .= ' GROUP BY r.id ORDER BY avg_rating DESC';

        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $restaurants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $cuisines = $this->conn->query(
            "SELECT DISTINCT cuisine_type FROM restaurants WHERE is_approved=1"
        )->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/restaurants.php';
    }

    public function detail() {
        $restaurant_id = (int)($_GET['id'] ?? 0);
        $user_id       = $_SESSION['user_id'];

        $stmt = $this->conn->prepare(
            "SELECT r.*,COALESCE(AVG(rv.rating),0) AS avg_rating
             FROM restaurants r LEFT JOIN reviews rv ON rv.restaurant_id=r.id
             WHERE r.id=? AND r.is_approved=1 GROUP BY r.id"
        );
        $stmt->bind_param('i', $restaurant_id); $stmt->execute();
        $restaurant = $stmt->get_result()->fetch_assoc();
        if (!$restaurant) { header('Location: index.php?page=restaurants'); exit; }

        $stmt2 = $this->conn->prepare(
            "SELECT mc.name AS category,mi.*,d.discount_pct,
                    CASE WHEN d.is_active=1 AND CURDATE() BETWEEN d.valid_from AND d.valid_until
                         THEN ROUND(mi.price*(1-d.discount_pct/100),2) ELSE mi.price END AS final_price
             FROM menu_categories mc
             JOIN menu_items mi ON mi.category_id=mc.id
             LEFT JOIN discounts d ON d.menu_item_id=mi.id
             WHERE mc.restaurant_id=? AND mi.is_available=1
             ORDER BY mc.display_order,mi.name"
        );
        $stmt2->bind_param('i', $restaurant_id); $stmt2->execute();
        $items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $menu = [];
        foreach ($items as $item) $menu[$item['category']][] = $item;

        $stmt3 = $this->conn->prepare(
            "SELECT rv.*,u.name AS customer_name FROM reviews rv
             JOIN users u ON u.id=rv.customer_id
             WHERE rv.restaurant_id=? ORDER BY rv.created_at DESC LIMIT 10"
        );
        $stmt3->bind_param('i', $restaurant_id); $stmt3->execute();
        $reviews = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt4 = $this->conn->prepare(
            "SELECT id FROM saved_restaurants WHERE customer_id=? AND restaurant_id=?"
        );
        $stmt4->bind_param('ii', $user_id, $restaurant_id); $stmt4->execute();
        $is_fav = $stmt4->get_result()->num_rows > 0;
        include __DIR__ . '/../views/restaurant_detail.php';
    }

    public function favourites() {
        $user_id = $_SESSION['user_id'];
        $stmt = $this->conn->prepare(
            "SELECT r.* FROM saved_restaurants sr JOIN restaurants r ON r.id=sr.restaurant_id WHERE sr.customer_id=?"
        );
        $stmt->bind_param('i', $user_id); $stmt->execute();
        $restaurants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        include __DIR__ . '/../views/favourites.php';
    }

    public function toggleFavourite() {
        $user_id       = $_SESSION['user_id'];
        $restaurant_id = (int)($_POST['restaurant_id'] ?? 0);
        $stmt = $this->conn->prepare(
            "SELECT id FROM saved_restaurants WHERE customer_id=? AND restaurant_id=?"
        );
        $stmt->bind_param('ii', $user_id, $restaurant_id); $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $d = $this->conn->prepare("DELETE FROM saved_restaurants WHERE customer_id=? AND restaurant_id=?");
            $d->bind_param('ii', $user_id, $restaurant_id); $d->execute();
        } else {
            $i = $this->conn->prepare("INSERT INTO saved_restaurants (customer_id,restaurant_id) VALUES (?,?)");
            $i->bind_param('ii', $user_id, $restaurant_id); $i->execute();
        }
        header("Location: index.php?page=restaurant_detail&id=$restaurant_id"); exit;
    }
}
