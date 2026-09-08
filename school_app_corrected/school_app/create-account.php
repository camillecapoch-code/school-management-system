<?php

require_once "config/database.php";
require_once "config/security.php";

// ======================================================
// CHECK IF AN ADMIN ALREADY EXISTS
// ======================================================

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM admin"
);

$adminCount =
    (int) $result->fetch_assoc()['total'];


// ======================================================
// IF AN ADMIN ALREADY EXISTS
// ======================================================

if ($adminCount > 0) {

    header("Location: login.php");

    exit;
}

$message = '';
$messageType = '';


// ======================================================
// CREATE ADMIN ACCOUNT
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $fullName =
        trim($_POST['full_name'] ?? '');

    $email =
        trim($_POST['email'] ?? '');

    $password =
        $_POST['password'] ?? '';

    $confirmPassword =
        $_POST['confirm_password'] ?? '';


    // ==================================================
    // BASIC VALIDATION
    // ==================================================

    if (
        $fullName === '' ||
        $email === '' ||
        $password === '' ||
        $confirmPassword === ''
    ) {

        $message =
            "Please fill in all fields.";

        $messageType =
            "error";

    }

    // ==================================================
    // EMAIL
    // ==================================================

    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $message =
            "Please enter a valid email address.";

        $messageType =
            "error";

    }

    // ==================================================
    // PASSWORD LENGTH
    // ==================================================

    elseif (strlen($password) < 8) {

        $message =
            "Password must contain at least 8 characters.";

        $messageType =
            "error";

    }

    // ==================================================
    // LOWERCASE
    // ==================================================

    elseif (!preg_match('/[a-z]/', $password)) {

        $message =
            "Password must contain at least one lowercase letter.";

        $messageType =
            "error";

    }

    // ==================================================
    // UPPERCASE
    // ==================================================

    elseif (!preg_match('/[A-Z]/', $password)) {

        $message =
            "Password must contain at least one uppercase letter.";

        $messageType =
            "error";

    }

    // ==================================================
    // NUMBER
    // ==================================================

    elseif (!preg_match('/[0-9]/', $password)) {

        $message =
            "Password must contain at least one number.";

        $messageType =
            "error";

    }

    // ==================================================
    // SPECIAL CHARACTER
    // ==================================================

    elseif (!preg_match('/[\W_]/', $password)) {

        $message =
            "Password must contain at least one special character.";

        $messageType =
            "error";

    }

    // ==================================================
    // CONFIRM PASSWORD
    // ==================================================

    elseif ($password !== $confirmPassword) {

        $message =
            "Passwords do not match.";

        $messageType =
            "error";

    }

    else {


        // ==================================================
        // CHECK EMAIL
        // ==================================================

        $stmt = $conn->prepare(
            "SELECT id
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

        $existingAdmin =
            $result->fetch_assoc();

        $stmt->close();


        if ($existingAdmin) {

            $message =
                "This email address is already registered.";

            $messageType =
                "error";

        } else {


            // ==================================================
            // HASH PASSWORD
            // ==================================================

            $hashedPassword =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            // ==================================================
            // INSERT ADMIN
            // ==================================================

            $stmt = $conn->prepare(
                "INSERT INTO admin
                (full_name, email, password)
                VALUES (?, ?, ?)"
            );


            $stmt->bind_param(
                "sss",
                $fullName,
                $email,
                $hashedPassword
            );


            if ($stmt->execute()) {

                $message =
                    "Administrator account created successfully.";

                $messageType =
                    "success";

                $fullName = '';
                $email = '';

            } else {

                $message =
                    "Unable to create administrator account.";

                $messageType =
                    "error";
            }


            $stmt->close();
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
        Create Administrator Account
    </title>

   <link
    rel="stylesheet"
    href="/school_app/css/create-account.css"
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


    <!-- CARD -->

    <div class="login-card">


        <div class="login-header">

            <h2>
                Create Administrator Account
            </h2>

            <p>
                Set up your administrator account.
            </p>

        </div>


        <!-- MESSAGE -->

        <?php if ($message !== ''): ?>

            <div
                class="<?php
                    echo $messageType === 'success'
                        ? 'login-success'
                        : 'login-error';
                ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <!-- FORM -->

        <form
            method="POST"
            class="login-form"
        >
            <?php echo csrf_field(); ?>


            <!-- FULL NAME -->

            <div class="form-group">

                <label for="full_name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?php
                    echo htmlspecialchars(
                        $_POST['full_name'] ?? ''
                    );
                    ?>"
                    placeholder="Administrator name"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php
                    echo htmlspecialchars(
                        $_POST['email'] ?? ''
                    );
                    ?>"
                    placeholder="example@gmail.com"
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
                    placeholder="Create your password"
                    required
                >


                <div class="password-strength">

                    <div class="password-strength-bar">

                        <div
                            id="createPasswordStrengthBar"
                            class="password-strength-fill"
                        ></div>

                    </div>


                    <span id="createPasswordStrengthText">
                        Password strength
                    </span>

                </div>


                <small class="password-help">

                    Password must contain:

                    <br>

                    • At least 8 characters

                    <br>

                    • One uppercase letter

                    <br>

                    • One lowercase letter

                    <br>

                    • One number

                    <br>

                    • One special character

                </small>

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required
                >

            </div>


            <!-- BUTTON -->

            <button
                type="submit"
                class="login-button"
            >

                👤 Create Account

            </button>


        </form>


    </div>


    <!-- LOGIN -->

    <p class="login-footer">

        Already have an account?

        <a
            href="/school_app/login.php"
            class="back-settings-link"
        >
            Sign in
        </a>

    </p>


</div>


<script>

const createPassword =
    document.getElementById(
        "password"
    );

const createStrengthBar =
    document.getElementById(
        "createPasswordStrengthBar"
    );

const createStrengthText =
    document.getElementById(
        "createPasswordStrengthText"
    );


if (
    createPassword &&
    createStrengthBar &&
    createStrengthText
) {

    createPassword.addEventListener(
        "input",
        function () {

            const password =
                createPassword.value;

            let score = 0;


            if (password.length >= 8) {
                score++;
            }

            if (password.length >= 12) {
                score++;
            }

            if (/[A-Z]/.test(password)) {
                score++;
            }

            if (/[a-z]/.test(password)) {
                score++;
            }

            if (/[0-9]/.test(password)) {
                score++;
            }

            if (/[\W_]/.test(password)) {
                score++;
            }


            if (password.length === 0) {

                createStrengthBar.style.width =
                    "0%";

                createStrengthText.textContent =
                    "Password strength";

            }

            else if (score <= 2) {

                createStrengthBar.style.width =
                    "25%";

                createStrengthText.textContent =
                    "Weak";

            }

            else if (score <= 4) {

                createStrengthBar.style.width =
                    "50%";

                createStrengthText.textContent =
                    "Medium";

            }

            else if (score === 5) {

                createStrengthBar.style.width =
                    "75%";

                createStrengthText.textContent =
                    "Strong";

            }

            else {

                createStrengthBar.style.width =
                    "100%";

                createStrengthText.textContent =
                    "Very Strong";

            }

        }
    );

}

</script>


</body>

</html>