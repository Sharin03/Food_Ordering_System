<?php $current = $_GET['page'] ?? 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering — Customer</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        nav { background: #e74c3c; color: white; padding: 0 20px; display: flex; align-items: center; gap: 16px; }
        nav .brand { font-size: 18px; font-weight: bold; padding: 14px 0; margin-right: auto; }
        nav a { color: #ffcdd2; text-decoration: none; padding: 16px 10px; font-size: 14px; display:inline-block; }
        nav a:hover, nav a.active { color: white; border-bottom: 3px solid white; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 16px; color: #c0392b; }
        .btn { display: inline-block; padding: 9px 18px; border-radius: 5px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; }
        .btn-primary { background: #e74c3c; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-danger  { background: #c0392b; color: white; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn:hover { opacity: 0.88; }
        .alert { padding: 12px 16px; border-radius: 5px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error   { background: #f8d7da; color: #721c24; }
        .alert-info    { background: #d1ecf1; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        .badge { display:inline-block; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:bold; }
        .badge-green  { background:#d4edda; color:#155724; }
        .badge-blue   { background:#cce5ff; color:#004085; }
        .badge-orange { background:#fff3cd; color:#856404; }
        .badge-red    { background:#f8d7da; color:#721c24; }
        .badge-gray   { background:#e2e3e5; color:#383d41; }
        form label { display:block; margin-bottom:4px; font-size:14px; font-weight:600; color:#555; }
        form input, form select, form textarea {
            width:100%; padding:9px 12px; margin-bottom:14px;
            border:1px solid #ccc; border-radius:5px; font-size:14px;
        }
        .restaurant-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
        .restaurant-card { background:white; border-radius:8px; padding:16px; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
        .restaurant-card h3 { margin-bottom:6px; color:#c0392b; }
        .cart-badge { background:white; color:#e74c3c; border-radius:50%; padding:1px 7px; font-size:12px; font-weight:bold; margin-left:4px; }
    </style>
</head>
<body>
<nav>
    <span class="brand">🍔 FoodOrder</span>
    <a href="index.php?page=dashboard"     class="<?= $current==='dashboard'?'active':'' ?>">Home</a>
    <a href="index.php?page=restaurants"   class="<?= $current==='restaurants'?'active':'' ?>">Restaurants</a>
    <a href="index.php?page=cart"          class="<?= $current==='cart'?'active':'' ?>">
        Cart <span class="cart-badge"><?= count($_SESSION['cart'] ?? []) ?></span>
    </a>
    <a href="index.php?page=order_history" class="<?= $current==='order_history'?'active':'' ?>">Orders</a>
    <a href="index.php?page=favourites"    class="<?= $current==='favourites'?'active':'' ?>">Favourites</a>
    <a href="index.php?page=profile"       class="<?= $current==='profile'?'active':'' ?>">Profile</a>
    <a href="index.php?page=logout">Logout</a>
</nav>
<div class="container">