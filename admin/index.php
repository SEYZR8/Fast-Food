<?php
session_start();

if (!isset($_SESSION['role_id']) || (int) $_SESSION['role_id'] !== 1) {
    header('Location: ../sign_in.php');
    exit;
}

require_once __DIR__ . '/../database/conf.php';

function countRows(mysqli $conn, string $table): int {
    $allowed = ['product', 'catagory', 'sub_category', 'register', 'banner', 'card'];
    if (!in_array($table, $allowed, true)) return 0;
    $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
    return (int) ($result->fetch_assoc()['total'] ?? 0);
}

$products = countRows($conn, 'product');
$categories = countRows($conn, 'catagory');
$subCategories = countRows($conn, 'sub_category');
$users = countRows($conn, 'register');
$banners = countRows($conn, 'banner');
$orders = countRows($conn, 'card');
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fast Food — Admin panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <style>
      body{background:#f5f6fa;font-family:Arial,sans-serif}
      .top{background:#fff;padding:18px 28px;box-shadow:0 2px 12px #0001;display:flex;justify-content:space-between;align-items:center}
      .brand{font-size:24px;font-weight:700}
      .brand span{color:#ff2b8a}
      .wrap{max-width:1200px;margin:30px auto;padding:0 18px}
      .stat{background:#fff;border-radius:16px;padding:22px;box-shadow:0 4px 18px #0000000d;height:100%}
      .stat small{color:#777}.stat b{display:block;font-size:32px;margin-top:8px}
      .menu-card{background:#fff;border-radius:16px;padding:22px;box-shadow:0 4px 18px #0000000d;height:100%}
      .menu-card a{display:block;text-decoration:none;padding:13px 15px;border-radius:10px;margin:7px 0;background:#f7f7f9;color:#222}
      .menu-card a:hover{background:#ff2b8a;color:#fff}
    </style>
</head>
<body>
<header class="top">
  <div class="brand">🍔 <span>Fast Food</span> — Admin</div>
  <a class="btn btn-outline-danger" href="../logout.php">Chiqish</a>
</header>

<main class="wrap">
  <h1 class="mb-4">Boshqaruv paneli</h1>

  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-2"><div class="stat"><small>Mahsulotlar</small><b><?= $products ?></b></div></div>
    <div class="col-6 col-lg-2"><div class="stat"><small>Kategoriyalar</small><b><?= $categories ?></b></div></div>
    <div class="col-6 col-lg-2"><div class="stat"><small>Bo'limlar</small><b><?= $subCategories ?></b></div></div>
    <div class="col-6 col-lg-2"><div class="stat"><small>Mijozlar</small><b><?= $users ?></b></div></div>
    <div class="col-6 col-lg-2"><div class="stat"><small>Bannerlar</small><b><?= $banners ?></b></div></div>
    <div class="col-6 col-lg-2"><div class="stat"><small>Buyurtmalar</small><b><?= $orders ?></b></div></div>
  </div>

  <div class="row g-4">
    <div class="col-md-6 col-lg-4"><div class="menu-card"><h3>Mahsulotlar</h3><a href="../products.php">Mahsulotlar</a><a href="../proStock.php">Ombor</a><a href="../proSell.php">Sotuvlar</a></div></div>
    <div class="col-md-6 col-lg-4"><div class="menu-card"><h3>Kategoriyalar</h3><a href="../cat.php">Kategoriyalar</a><a href="../Subcat.php">Ichki kategoriyalar</a><a href="../banners.php">Bannerlar</a></div></div>
    <div class="col-md-6 col-lg-4"><div class="menu-card"><h3>Mijozlar va hisob</h3><a href="../users.php">Foydalanuvchilar</a><a href="../money.php">Moliya</a><a href="../inv.php">Buyurtmalar</a></div></div>
  </div>
</main>
</body>
</html>
