<?php
session_start();

if (isset($_SESSION['unique_id']) && (int)$_SESSION['unique_id'] !== 900000001) {
    require_once __DIR__ . '/conf.php';
    try {
        $uniqueId = (string)$_SESSION['unique_id'];
        $stmt = $conn->prepare("UPDATE register SET status = 'signal_cellular_null' WHERE unique_id = ?");
        $stmt->bind_param('s', $uniqueId);
        $stmt->execute();
    } catch (Throwable $e) {
        error_log('Logout status update failed: ' . $e->getMessage());
    }
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: login.php');
exit;
?>