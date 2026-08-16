<?php
// Database configuration for both local Docker/MariaDB and Render.
// On Render, set DB_HOST, DB_USER, DB_PASSWORD, DB_NAME and DB_PORT
// in the service Environment variables. For the bundled MariaDB,
// the defaults below are used automatically.
$server = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'id18044649_food_website';
$port = (int) (getenv('DB_PORT') ?: 3306);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($server, $username, $password, $database, $port);
    mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed. Please check the database environment variables.');
}
?>
