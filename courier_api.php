<?php
session_start();
require_once __DIR__.'/conf.php';
header('Content-Type: application/json; charset=utf-8');

function out($data, $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$action = (string)($_REQUEST['action'] ?? '');

try {
    // Keep authentication independent from the orders schema. A broken/old
    // admin_orders table must never prevent a courier from logging in.
    $conn->query("CREATE TABLE IF NOT EXISTS couriers (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(190) NOT NULL,
        phone VARCHAR(60) NOT NULL UNIQUE,
        pin_hash VARCHAR(255) NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'offline',
        last_seen TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    try { $conn->query("ALTER TABLE couriers ADD COLUMN pin_hash VARCHAR(255) NULL"); } catch (Throwable $e) {}
    try { $conn->query("ALTER TABLE couriers ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT 'offline'"); } catch (Throwable $e) {}
    try { $conn->query("ALTER TABLE couriers ADD COLUMN last_seen TIMESTAMP NULL"); } catch (Throwable $e) {}

    // Ensure the requested test courier always exists.
    $defaultPhone = '+998998305603';
    $st = $conn->prepare('SELECT id, pin_hash FROM couriers WHERE phone=? LIMIT 1');
    $st->bind_param('s', $defaultPhone);
    $st->execute();
    $existing = $st->get_result()->fetch_assoc();
    if (!$existing) {
        $name = 'Daler Lavash';
        $hash = password_hash('Lavash', PASSWORD_DEFAULT);
        $st = $conn->prepare('INSERT INTO couriers(name,phone,pin_hash,status) VALUES(?,?,?,\'offline\')');
        $st->bind_param('sss', $name, $defaultPhone, $hash);
        $st->execute();
    } elseif (empty($existing['pin_hash'])) {
        $hash = password_hash('Lavash', PASSWORD_DEFAULT);
        $st = $conn->prepare('UPDATE couriers SET pin_hash=? WHERE id=?');
        $st->bind_param('si', $hash, $existing['id']);
        $st->execute();
    }

    if ($action === 'login') {
        $login = trim((string)($_POST['login'] ?? $_POST['phone'] ?? ''));
        $pin = (string)($_POST['password'] ?? $_POST['pin'] ?? '');
        if ($login === '' || $pin === '') {
            out(['ok'=>false, 'message'=>'Login va parolni kiriting.'], 422);
        }

        $phone = preg_replace('/[^0-9+]/', '', $login);
        if (mb_strtolower($login) === 'daler') {
            $phone = $defaultPhone;
        }

        $st = $conn->prepare('SELECT id,name,phone,pin_hash FROM couriers WHERE phone=? LIMIT 1');
        $st->bind_param('s', $phone);
        $st->execute();
        $courier = $st->get_result()->fetch_assoc();

        if (!$courier || empty($courier['pin_hash']) || !password_verify($pin, $courier['pin_hash'])) {
            out(['ok'=>false, 'message'=>'Login yoki parol noto‘g‘ri.'], 401);
        }

        session_regenerate_id(true);
        $_SESSION['courier_id'] = (int)$courier['id'];
        $_SESSION['courier_name'] = $courier['name'];

        $up = $conn->prepare('UPDATE couriers SET status=\'online\', last_seen=NOW() WHERE id=?');
        $cid = (int)$courier['id'];
        $up->bind_param('i', $cid);
        $up->execute();

        out(['ok'=>true, 'data'=>[
            'id'=>$cid,
            'name'=>$courier['name'],
            'phone'=>$courier['phone']
        ]]);
    }

    $id = (int)($_SESSION['courier_id'] ?? 0);
    if ($id <= 0) {
        out(['ok'=>false, 'message'=>'Kirish kerak.'], 401);
    }

    if ($action === 'logout') {
        $st = $conn->prepare('UPDATE couriers SET status=\'offline\' WHERE id=?');
        $st->bind_param('i', $id);
        $st->execute();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        out(['ok'=>true]);
    }

    // Keep the courier heartbeat independent from order-table migrations.
    $up = $conn->prepare('UPDATE couriers SET status=\'online\', last_seen=NOW() WHERE id=?');
    $up->bind_param('i', $id);
    $up->execute();

    if ($action === 'me') {
        $st = $conn->prepare('SELECT id,name,phone,status,last_seen FROM couriers WHERE id=? LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        if (!$row) out(['ok'=>false, 'message'=>'Kuryer topilmadi.'], 404);
        out(['ok'=>true, 'data'=>$row]);
    }

    // Migrate/ensure orders only when order-related actions are actually used.
    if (in_array($action, ['orders','status'], true)) {
        $conn->query("CREATE TABLE IF NOT EXISTS admin_orders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            source_table VARCHAR(128) NULL,
            source_key VARCHAR(191) NULL,
            customer_name VARCHAR(190) NOT NULL DEFAULT '',
            phone VARCHAR(60) NOT NULL DEFAULT '',
            address TEXT NULL,
            note TEXT NULL,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            payment_method VARCHAR(20) NOT NULL DEFAULT 'cash',
            lat DECIMAL(10,7) NULL,
            lng DECIMAL(10,7) NULL,
            status VARCHAR(40) NOT NULL DEFAULT 'new',
            courier_id BIGINT UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY source_ref(source_table,source_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        try { $conn->query("ALTER TABLE admin_orders ADD COLUMN payment_method VARCHAR(20) NOT NULL DEFAULT 'cash'"); } catch (Throwable $e) {}
        try { $conn->query("ALTER TABLE admin_orders ADD COLUMN lat DECIMAL(10,7) NULL"); } catch (Throwable $e) {}
        try { $conn->query("ALTER TABLE admin_orders ADD COLUMN lng DECIMAL(10,7) NULL"); } catch (Throwable $e) {}
    }

    if ($action === 'orders') {
        $st = $conn->prepare("SELECT * FROM admin_orders WHERE courier_id=? AND status IN('confirmed','preparing','delivering') ORDER BY created_at DESC");
        $st->bind_param('i', $id);
        $st->execute();
        $rows = [];
        $q = $st->get_result();
        while ($row = $q->fetch_assoc()) $rows[] = $row;
        out(['ok'=>true, 'data'=>$rows]);
    }

    if ($action === 'status') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = (string)($_POST['status'] ?? 'delivering');
        $allowed = ['preparing','delivering','completed'];
        if (!$orderId || !in_array($status, $allowed, true)) out(['ok'=>false,'message'=>'Status noto‘g‘ri.'], 422);
        $st = $conn->prepare('UPDATE admin_orders SET status=? WHERE id=? AND courier_id=?');
        $st->bind_param('sii', $status, $orderId, $id);
        $st->execute();
        out(['ok'=>true]);
    }

    out(['ok'=>false, 'message'=>'Unknown action'], 404);
} catch (Throwable $e) {
    error_log('Courier API: '.$e->getMessage());
    out(['ok'=>false, 'message'=>'Server xatosi. Iltimos qayta urinib ko‘ring.'], 500);
}
?>
