<?php

$config = [];
$localConfig = __DIR__ . '/database.local.php';

if (is_file($localConfig)) {
    $config = require $localConfig;
    if (!is_array($config)) {
        $config = [];
    }
}

$host = $config['host'] ?? (getenv('DB_HOST') ?: 'localhost');
$user = $config['user'] ?? (getenv('DB_USER') ?: 'root');
$password = $config['password'] ?? (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '');
$database = $config['database'] ?? (getenv('DB_NAME') ?: 'school_app');

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die('Database connection failed.');
}

$conn->set_charset('utf8mb4');
