<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
    session_start();
}

require_once __DIR__ . '/security.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /school_app/login.php');
    exit;
}
