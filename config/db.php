<?php

$envPath = __DIR__ . '/../.env';
$env = [];

if (is_file($envPath)) {
    $env = parse_ini_file($envPath, true, INI_SCANNER_TYPED);
    if ($env === false) {
        $env = [];
    }
}

$host = $env['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$dbname = $env['DB_NAME'] ?? getenv('DB_NAME') ?: 'Auth_task';
$username = $env['DB_USER'] ?? getenv('DB_USER') ?: 'root';
$password = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: $env['PASSWORD'] ?? getenv('PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
