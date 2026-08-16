<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require __DIR__ . '/database/conf.php';
    $conn->query('SELECT 1');
    echo json_encode([
        'ok' => true,
        'service' => 'Fast Food',
        'database' => 'connected',
        'time' => date(DATE_ATOM)
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode([
        'ok' => false,
        'service' => 'Fast Food',
        'database' => 'error'
    ], JSON_UNESCAPED_UNICODE);
}
