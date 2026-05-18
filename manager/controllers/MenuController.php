<?php

class MenuController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // =========================================================
    // MENU CATEGORIES
    // =========================================================
    public function categories() {

        $error = '';
        $success = '';

        $restaurant_id = $_SESSION['restaurant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            // ADD CATEGORY
            if ($action === 'add') {

                $name = trim($_POST['name'] ?? '');

                if (!$name) {

                    $error = 'Name required.';
                }

                else {

                    $s = $this->conn->prepare(
                        "INSERT INTO menu_categories (restaurant_id,name)
                         VALUES (?,?)"
                    );

                    $s->bind_param('is', $restaurant_id, $name);
                    $s->execute();

                    $success = 'Category added.';
                }
            }

            // DELETE CATEGORY
            elseif ($action === 'delete') {

                $id = (int)$_POST['cat_id'];

                $s = $this->conn->prepare(
                    "DELETE FROM menu_categories
                     WHERE id=? AND restaurant_id=?"
                );

                $s->bind_param('ii', $id, $restaurant_id);
                $s->execute();

                $success = 'Category deleted.';
            }

            // RENAME CATEGORY
            elseif ($action === 'rename') {

                $id   = (int)$_POST['cat_id'];
                $name = trim($_POST['name'] ?? '');

                $s = $this->conn->prepare(
                    "UPDATE menu_categories
                     SET name=?
                     WHERE id=? AND restaurant_id=?"
                );

                $s->bind_param('sii', $name, $id, $restaurant_id);
                $s->execute();

                $success = 'Category renamed.';
            }
        }

        $stmt = $this->conn->prepare(
            "SELECT *
             FROM menu_categories
             WHERE restaurant_id=?
             ORDER BY display_order"
        );

        $stmt->bind_param('i', $restaurant_id);
        $stmt->execute();

        $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        include __DIR__ . '/../views/menu_categories.php';
    }

    // =========================================================
    // MENU ITEMS
    // =========================================================
    public function items() {

        $error = '';
        $success = '';

        $restaurant_id = $_SESSION['restaurant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            // ADD ITEM
            if ($action === 'add') {

                $name   = trim($_POST['name'] ?? '');
                $desc   = trim($_POST['description'] ?? '');
                $price  = (float)($_POST['price'] ?? 0);
                $cat_id = (int)($_POST['category_id'] ?? 0);

                // AUTO AVAILABLE ENABLED
                $avail = 1;

                $image = null;

                // IMAGE UPLOAD
                if (!empty($_FILES['image']['name'])) {

                    $ext = strtolower(
                        pathinfo(
                            $_FILES['image']['name'],
                            PATHINFO_EXTENSION
                        )
                    );

                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {

                        $dir = __DIR__ . '/../../uploads/';

                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }

                        $fn = 'item_' . time() . '.' . $ext;

                        move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $dir . $fn
                        );

                        $image = 'uploads/' . $fn;
                    }
                }

                $s = $this->conn->prepare(
                    "INSERT INTO menu_items
                    (restaurant_id,category_id,name,description,price,image_path,is_available)
                    VALUES (?,?,?,?,?,?,?)"
                );

                $s->bind_param(
                    'iissdsi',
                    $restaurant_id,
                    $cat_id,
                    $name,
                    $desc,
                    $price,
                    $image,
                    $avail
                );

                $s->execute();

                $success = 'Item added successfully.';
            }

            // DELETE ITEM
            elseif ($action === 'delete') {

                $id = (int)$_POST['item_id'];

                $s = $this->conn->prepare(
                    "DELETE FROM menu_items
                     WHERE id=? AND restaurant_id=?"
                );

                $s->bind_param('ii', $id, $restaurant_id);

                $s->execute();

                $success = 'Item deleted.';
            }

            // TOGGLE ITEM
            elseif ($action === 'toggle') {

                $id    = (int)$_POST['item_id'];
                $avail = (int)$_POST['is_available'];

                $s = $this->conn->prepare(
                    "UPDATE menu_items
                     SET is_available=?
                     WHERE id=? AND restaurant_id=?"
                );

                $s->bind_param('iii', $avail, $id, $restaurant_id);

                $s->execute();

                $success = 'Item updated.';
            }
        }

        // FETCH ITEMS
        $stmt = $this->conn->prepare(
            "SELECT
                mi.*,
                mc.name AS category_name
             FROM menu_items mi
             LEFT JOIN menu_categories mc
             ON mc.id = mi.category_id
             WHERE mi.restaurant_id=?
             ORDER BY mc.name, mi.name"
        );

        $stmt->bind_param('i', $restaurant_id);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // FETCH CATEGORIES
        $stmt2 = $this->conn->prepare(
            "SELECT *
             FROM menu_categories
             WHERE restaurant_id=?"
        );

        $stmt2->bind_param('i', $restaurant_id);
        $stmt2->execute();

        $categories = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        include __DIR__ . '/../views/menu_items.php';
    }

    // =========================================================
    // DISCOUNTS
    // =========================================================
    public function discounts() {

        $error = '';
        $success = '';

        $restaurant_id = $_SESSION['restaurant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            // ADD DISCOUNT
            if ($action === 'add') {

                $item_id = (int)$_POST['menu_item_id'];
                $pct     = (float)$_POST['discount_pct'];
                $from    = $_POST['valid_from'];
                $until   = $_POST['valid_until'];

                $s = $this->conn->prepare(
                    "INSERT INTO discounts
                    (menu_item_id,restaurant_id,discount_pct,valid_from,valid_until,is_active)
                    VALUES (?,?,?,?,?,1)"
                );

                $s->bind_param(
                    'iidss',
                    $item_id,
                    $restaurant_id,
                    $pct,
                    $from,
                    $until
                );

                $s->execute();

                $success = 'Discount created.';
            }

            // TOGGLE DISCOUNT
            elseif ($action === 'toggle') {

                $id     = (int)$_POST['discount_id'];
                $status = (int)$_POST['is_active'];

                $s = $this->conn->prepare(
                    "UPDATE discounts
                     SET is_active=?
                     WHERE id=? AND restaurant_id=?"
                );

                $s->bind_param('iii', $status, $id, $restaurant_id);

                $s->execute();

                $success = 'Updated.';
            }

            // DELETE DISCOUNT
            elseif ($action === 'delete') {

                $id = (int)$_POST['discount_id'];

                $s = $this->conn->prepare(
                    "DELETE FROM discounts
                     WHERE id=? AND restaurant_id=?"
                );

                $s->bind_param('ii', $id, $restaurant_id);

                $s->execute();

                $success = 'Deleted.';
            }
        }

        // FETCH DISCOUNTS
        $stmt = $this->conn->prepare(
            "SELECT
                d.*,
                mi.name AS item_name
             FROM discounts d
             JOIN menu_items mi
             ON mi.id=d.menu_item_id
             WHERE d.restaurant_id=?
             ORDER BY d.valid_until DESC"
        );

        $stmt->bind_param('i', $restaurant_id);
        $stmt->execute();

        $discounts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // FETCH MENU ITEMS
        $stmt2 = $this->conn->prepare(
            "SELECT id,name
             FROM menu_items
             WHERE restaurant_id=?"
        );

        $stmt2->bind_param('i', $restaurant_id);
        $stmt2->execute();

        $items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        include __DIR__ . '/../views/discounts.php';
    }
}