<?php
// Fast Food database configuration.
// The Render image runs MariaDB inside the same container, so the web app
// uses the local database and does not depend on external DB variables.
$dbExternal = getenv('DB_EXTERNAL') === '1';

$server = $dbExternal ? (getenv('DB_HOST') ?: '127.0.0.1') : '127.0.0.1';
$username = $dbExternal ? (getenv('DB_USER') ?: 'root') : 'root';
$password = $dbExternal ? (getenv('DB_PASSWORD') ?: '') : '';
$database = getenv('DB_NAME') ?: 'id18044649_food_website';
$port = (int) (getenv('DB_PORT') ?: 3306);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($server, $username, $password, $database, $port);
    mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Ma\'lumotlar bazasiga ulanishda xatolik yuz berdi.');
}
?>
