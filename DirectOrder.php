<?php
session_start();
require_once __DIR__ . '/conf.php';
header('Content-Type: text/html; charset=utf-8');

function clean(string $key): string {
    return trim((string)($_POST[$key] ?? ''));
}
function fail(string $message, int $code = 400): void {
    http_response_code($code);
    echo '<div class="alert alert-danger" role="alert">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>';
    exit;
}

$name = clean('name');
$number = preg_replace('/[^0-9+]/', '', clean('number'));
$email = clean('email');
$address = clean('address');
$orderName = clean('order');
$additional = clean('additional');
$qty = (int)(clean('qty') !== '' ? clean('qty') : clean('quantity'));
$proId = clean('p_id');
$catId = clean('category');
$dateInput = clean('date');

if ($name === '' || $number === '' || $address === '' || $qty < 1) {
    fail('Ism, telefon, manzil va mahsulot sonini to‘ldiring.');
}
if (mb_strlen($address) > 100) {
    fail('Manzil juda uzun. Qisqaroq manzil kiriting.');
}
if (!preg_match('/^\+?[0-9]{9,15}$/', $number)) {
    fail('Telefon raqami noto‘g‘ri. Masalan: +998998305603');
}

// Find product either by its id (cart/AJAX flow) or by the name from the quick-order form.
$product = null;
if ($proId !== '') {
    $stmt = $conn->prepare('SELECT P_id, cat_id, scat_id, p_title, p_prize FROM product WHERE P_id = ? LIMIT 1');
    $stmt->bind_param('s', $proId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}
if (!$product && $orderName !== '') {
    $like = '%' . $orderName . '%';
    $stmt = $conn->prepare('SELECT P_id, cat_id, scat_id, p_title, p_prize FROM product WHERE p_title LIKE ? LIMIT 1');
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}
if (!$product) {
    fail('Mahsulot topilmadi. Menyudan mahsulot tanlang yoki nomini to‘g‘ri yozing.');
}

// Reuse the logged-in customer when available; otherwise create a lightweight guest customer.
$uId = (int)($_SESSION['u_id'] ?? 0);
if ($uId <= 0) {
    $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : ('guest' . preg_replace('/\D+/', '', $number) . '@fastfood.local');
    $stmt = $conn->prepare('SELECT u_id, unique_id, role_id FROM register WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    if ($existing) {
        $uId = (int)$existing['u_id'];
        $_SESSION['u_id'] = $uId;
        $_SESSION['unique_id'] = $existing['unique_id'];
        $_SESSION['role_id'] = $existing['role_id'];
    } else {
        $uniqueId = random_int(100000000, 1999999999);
        $guestPassword = bin2hex(random_bytes(8));
        $status = 'signal_cellular_null';
        $roleId = 2;
        $image = 'pic.png';
        $stmt = $conn->prepare('INSERT INTO register (unique_id, Name, email, password, image, status, role_id, address, number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssssiss', $uniqueId, $name, $email, $guestPassword, $image, $status, $roleId, $address, $number);
        $stmt->execute();
        $uId = $conn->insert_id;
        $_SESSION['u_id'] = $uId;
        $_SESSION['unique_id'] = $uniqueId;
        $_SESSION['role_id'] = 2;
    }
}

$invId = 'inv-' . strtoupper(bin2hex(random_bytes(4)));
$catIdFinal = $catId !== '' ? $catId : (string)$product['cat_id'];
$scatId = (string)$product['scat_id'];
$price = (float)$product['p_prize'];
$status = 'pending';
$date = $dateInput !== '' ? date('Y-m-d H:i:s', strtotime($dateInput) ?: time()) : date('Y-m-d H:i:s');

$stmt = $conn->prepare('INSERT INTO card (inv_id, cat_id, scat_id, pro_id, u_id, qty, prize, tax, date, status, number, address) VALUES (?, ?, ?, ?, ?, ?, ?, 3, ?, ?, ?, ?)');
$stmt->bind_param('ssssiidssss', $invId, $catIdFinal, $scatId, $product['P_id'], $uId, $qty, $price, $date, $status, $number, $address);
$stmt->execute();

$total = $price * $qty;
$extra = $additional !== '' ? ' Qo‘shimcha: ' . $additional : '';

// AJAX clients expect a truthy response; this message is also useful for direct form submissions.
echo '<div class="alert alert-success" role="alert"><strong>Buyurtma qabul qilindi!</strong><br>Buyurtma: '.htmlspecialchars($invId).' · '.htmlspecialchars($product['p_title']).' × '.(int)$qty.'<br>Jami: '.number_format($total, 0, '.', ' ').' · Manzil: '.htmlspecialchars($address).htmlspecialchars($extra).'<br>Tez orada siz bilan <a href="tel:+998998305603">+998 99 830 56 03</a> orqali bog‘lanamiz.</div>';
?>