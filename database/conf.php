<?php
// Render runs MariaDB inside the same container for this deployment.
$server = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'id18044649_food_website';
$port = 3306;

$conn = mysqli_connect($server, $username, $password, $database, $port);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
?>
