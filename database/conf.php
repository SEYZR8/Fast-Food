<?php

$server = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'food_website';
$port = (int)(getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect($server, $username, $password, $database, $port)
    or die("<script>alert('connection failed')</script>");

mysqli_set_charset($conn, 'utf8mb4');

?>
