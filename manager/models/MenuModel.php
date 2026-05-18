<?php
class MenuModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    public function getCategories($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM menu_categories WHERE restaurant_id=? ORDER BY display_order"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addCategory($restaurant_id, $name) {
        $stmt = $this->conn->prepare(
            "INSERT INTO menu_categories (restaurant_id,name) VALUES (?,?)"
        );
        $stmt->bind_param("is", $restaurant_id, $name);
        $stmt->execute();
    }

    public function renameCategory($cat_id, $restaurant_id, $name) {
        $stmt = $this->conn->prepare(
            "UPDATE menu_categories SET name=? WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("sii", $name, $cat_id, $restaurant_id);
        $stmt->execute();
    }

    public function deleteCategory($cat_id, $restaurant_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM menu_categories WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("ii", $cat_id, $restaurant_id);
        $stmt->execute();
    }

    public function getItems($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT mi.*, mc.name AS category_name FROM menu_items mi
             LEFT JOIN menu_categories mc ON mc.id=mi.category_id
             WHERE mi.restaurant_id=? ORDER BY mc.name, mi.name"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addItem($restaurant_id, $cat_id, $name, $desc, $price, $image, $avail) {
        $stmt = $this->conn->prepare(
            "INSERT INTO menu_items
             (restaurant_id,category_id,name,description,price,image_path,is_available)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->bind_param("iissdsi",
            $restaurant_id,$cat_id,$name,$desc,$price,$image,$avail);
        $stmt->execute();
    }

    public function deleteItem($item_id, $restaurant_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM menu_items WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("ii", $item_id, $restaurant_id);
        $stmt->execute();
    }

    public function toggleItem($item_id, $restaurant_id, $avail) {
        $stmt = $this->conn->prepare(
            "UPDATE menu_items SET is_available=? WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("iii", $avail, $item_id, $restaurant_id);
        $stmt->execute();
    }

    public function getDiscounts($restaurant_id) {
        $stmt = $this->conn->prepare(
            "SELECT d.*, mi.name AS item_name FROM discounts d
             JOIN menu_items mi ON mi.id=d.menu_item_id
             WHERE d.restaurant_id=? ORDER BY d.valid_until DESC"
        );
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addDiscount($item_id, $restaurant_id, $pct, $from, $until) {
        $stmt = $this->conn->prepare(
            "INSERT INTO discounts
             (menu_item_id,restaurant_id,discount_pct,valid_from,valid_until,is_active)
             VALUES (?,?,?,?,?,1)"
        );
        $stmt->bind_param("iidss", $item_id,$restaurant_id,$pct,$from,$until);
        $stmt->execute();
    }

    public function toggleDiscount($discount_id, $restaurant_id, $status) {
        $stmt = $this->conn->prepare(
            "UPDATE discounts SET is_active=? WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("iii", $status, $discount_id, $restaurant_id);
        $stmt->execute();
    }

    public function deleteDiscount($discount_id, $restaurant_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM discounts WHERE id=? AND restaurant_id=?"
        );
        $stmt->bind_param("ii", $discount_id, $restaurant_id);
        $stmt->execute();
    }
}