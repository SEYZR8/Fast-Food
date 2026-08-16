<?php
session_start();
if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 1) {
    header('Location: admin/index.php');
    exit;
}
$error = isset($_GET['error']) ? 'Login yoki parol noto‘g‘ri.' : '';
?>
<!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Fast Food — Kirish</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;font-family:Arial,sans-serif;background:linear-gradient(135deg,#111827,#ff2b8a);padding:20px}.card{width:min(430px,100%);background:#fff;border-radius:24px;padding:34px;box-shadow:0 20px 60px #0005}.logo{font-size:30px;font-weight:800;text-align:center;margin-bottom:8px}.muted{text-align:center;color:#6b7280;margin-bottom:25px}label{display:block;font-weight:700;margin:14px 0 7px}input{width:100%;padding:14px;border:1px solid #ddd;border-radius:12px;font-size:16px}button{width:100%;border:0;border-radius:12px;padding:14px;margin-top:20px;background:#ff2b8a;color:#fff;font-size:17px;font-weight:700;cursor:pointer}button:hover{filter:brightness(.95)}.error{background:#fee2e2;color:#991b1b;border-radius:10px;padding:12px;margin-bottom:15px}.back{text-align:center;margin-top:18px}.back a{color:#ff2b8a;text-decoration:none;font-weight:700}
</style>
</head>
<body>
<div class="card">
<div class="logo">🍔 Fast Food</div>
<div class="muted">Admin panelga kirish</div>
<?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form action="sign_in.php" method="post" autocomplete="on">
<label for="login">Login</label>
<input id="login" name="login" type="text" placeholder="Login" required autocomplete="username">
<label for="password">Parol</label>
<input id="password" name="password" type="password" placeholder="Parol" required autocomplete="current-password">
<button type="submit">Kirish</button>
</form>
<div class="back"><a href="index.php">← Saytga qaytish</a></div>
</div>
</body>
</html>