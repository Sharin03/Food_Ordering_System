<?php
$current = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Agent — <?= ucfirst($current) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        nav { background: #2c3e50; color: white; padding: 0 20px; display: flex; align-items: center; gap: 20px; }
        nav .brand { font-size: 18px; font-weight: bold; padding: 14px 0; margin-right: auto; }
        nav a { color: #ccc; text-decoration: none; padding: 16px 10px; display: inline-block; font-size: 14px; }
        nav a:hover, nav a.active { color: white; border-bottom: 3px solid #3498db; }
        .container { max-width: 960px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 16px; font-size: 20px; color: #2c3e50; }
        .btn { display: inline-block; padding: 9px 18px; border-radius: 5px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-danger  { background: #e74c3c; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn:hover { opacity: 0.88; }
        .alert { padding: 12px 16px; border-radius: 5px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info    { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #555; font-weight: 600; }
        tr:hover td { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-green  { background: #d4edda; color: #155724; }
        .badge-blue   { background: #cce5ff; color: #004085; }
        .badge-orange { background: #fff3cd; color: #856404; }
        .badge-gray   { background: #e2e3e5; color: #383d41; }
        form label { display: block; margin-bottom: 4px; font-size: 14px; font-weight: 600; color: #555; }
        form input, form select { width: 100%; padding: 9px 12px; margin-bottom: 14px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        form input:focus, form select:focus { outline: none; border-color: #3498db; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
        .stat-box { background: #f8f9fa; border-radius: 8px; padding: 18px; text-align: center; }
        .stat-box .value { font-size: 28px; font-weight: bold; color: #2c3e50; }
        .stat-box .label { font-size: 13px; color: #888; margin-top: 4px; }
        #notify-bar { display:none; background:#27ae60; color:white; padding:10px 20px; text-align:center; font-size:14px; }
    </style>
</head>
<body>
<div id="notify-bar">New delivery available! <a href="index.php?page=available" style="color:white;font-weight:bold;">View now</a></div>
<nav>
    <span class="brand">Delivery Agent</span>
    <a href="index.php?page=dashboard"  class="<?= $current==='dashboard'?'active':'' ?>">Dashboard</a>
    <a href="index.php?page=available"  class="<?= $current==='available'?'active':'' ?>">Available</a>
    <a href="index.php?page=active"     class="<?= $current==='active'?'active':'' ?>">Active</a>
    <a href="index.php?page=history"    class="<?= $current==='history'?'active':'' ?>">History</a>
    <a href="index.php?page=earnings"   class="<?= $current==='earnings'?'active':'' ?>">Earnings</a>
    <a href="index.php?page=profile"    class="<?= $current==='profile'?'active':'' ?>">Profile</a>
    <a href="index.php?page=logout">Logout</a>
</nav>
<div class="container">