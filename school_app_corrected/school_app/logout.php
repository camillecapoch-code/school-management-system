<?php

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
session_start();


// ======================================================
// DESTROY SESSION
// ======================================================

$_SESSION = [];


// Delete session cookie

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}


// Destroy session

session_destroy();


// Return to login

header(
    "Location: /school_app/login.php"
);

exit;

?>