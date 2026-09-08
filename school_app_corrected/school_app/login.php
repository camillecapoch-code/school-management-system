<?php

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
session_start();

require_once "config/database.php";
require_once "config/security.php";


// ======================================================
// IF ALREADY LOGGED IN
// ======================================================

if (isset($_SESSION['admin_id'])) {

    header("Location: index.php");

    exit;
}


$message = '';


// ======================================================
// LOGIN
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $email =
        trim($_POST['email'] ?? '');

    $password =
        $_POST['password'] ?? '';


    // ==================================================
    // VALIDATION
    // ==================================================

    if ($email === '' || $password === '') {

        $message =
            "Please enter your email and password.";

    } else {


        // ==================================================
        // FIND ADMIN
        // ==================================================

        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password
             FROM admin
             WHERE email = ?
             LIMIT 1"
        );


        $stmt->bind_param(
            "s",
            $email
        );


        $stmt->execute();


        $result =
            $stmt->get_result();


        $admin =
            $result->fetch_assoc();


        $stmt->close();


        // ==================================================
        // VERIFY
        // ==================================================

        if (
            $admin &&
            password_verify(
                $password,
                $admin['password']
            )
        ) {


            // ==================================================
            // CREATE SESSION
            // ==================================================

            session_regenerate_id(true);


            $_SESSION['admin_id'] =
                $admin['id'];

            $_SESSION['admin_name'] =
                $admin['full_name'];

            $_SESSION['admin_email'] =
                $admin['email'];


            // ==================================================
            // REDIRECT
            // ==================================================

            header(
                "Location: index.php"
            );

            exit;


        } else {

            $message =
                "Invalid email or password.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Login - School Management System
    </title>


    <link
        rel="stylesheet"
        href="/school_app/css/style.css"
    >

</head>


<body class="login-body">


<div class="login-container">


    <!-- LOGO -->

    <div class="login-logo">

        <div class="login-logo-icon">
            🏫
        </div>

        <h1>
            SCHOOL APP
        </h1>

        <p>
            SCHOOL MANAGEMENT SYSTEM
        </p>

    </div>


    <!-- LOGIN CARD -->

    <div class="login-card">


        <div class="login-header">

            <h2>
                Welcome Back
            </h2>

            <p>
                Sign in to your administrator account.
            </p>

        </div>


        <?php if ($message !== ''): ?>

            <div class="login-error">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="login-form"
        >
            <?php echo csrf_field(); ?>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="admin@school.com"
                    autocomplete="email"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="login-button"
            >

                🔐 Sign In

            </button>


        </form>


    </div>


    <p class="login-footer">

        School Management System

    </p>


</div>


</body>

</html>