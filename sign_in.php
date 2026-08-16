<?php
session_start();
require_once __DIR__ . '/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$login = trim((string)($_POST['email'] ?? $_POST['login'] ?? ''));
$pass  = (string)($_POST['pass'] ?? $_POST['password'] ?? '');

if ($login === '' || $pass === '') {
    http_response_code(400);
    echo 'Barcha maydonlarni to‘ldiring.';
    exit;
}

// Admin credentials can be changed in Render Environment Variables.
// Defaults are provided so the first deployment is immediately usable.
$adminLogin = getenv('ADMIN_LOGIN') ?: 'Daler';
$adminPass  = getenv('ADMIN_PASSWORD') ?: 'Lavash';

if (hash_equals($adminLogin, $login) && hash_equals($adminPass, $pass)) {
    session_regenerate_id(true);
    $_SESSION['unique_id'] = 900000001;
    $_SESSION['u_id'] = 0;
    $_SESSION['role_id'] = 1;
    $_SESSION['admin'] = true;
    $_SESSION['admin_login'] = $adminLogin;
    header('Location: admin/index.php');
    exit;
}

$email = filter_var($login, FILTER_VALIDATE_EMAIL);
if (!$email) {
    http_response_code(401);
    echo 'Login yoki parol noto‘g‘ri.';
    exit;
}

$stmt = $conn->prepare('SELECT u_id, unique_id, role_id, password FROM register WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result || !hash_equals((string)$result['password'], $pass)) {
    http_response_code(401);
    echo 'Login yoki parol noto‘g‘ri.';
    exit;
}

session_regenerate_id(true);
$_SESSION['unique_id'] = $result['unique_id'];
$_SESSION['u_id'] = $result['u_id'];
$_SESSION['role_id'] = $result['role_id'];

if ((int)$result['role_id'] === 1) {
    header('Location: admin/index.php');
} else {
    header('Location: index.php');
}
exit;
?>