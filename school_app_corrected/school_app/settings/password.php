<?php

require_once "../config/auth.php";
require_once "../config/database.php";

$message = '';
$messageType = '';


// ======================================================
// CHANGE PASSWORD
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $currentPassword =
        $_POST['current_password'] ?? '';

    $newPassword =
        $_POST['new_password'] ?? '';

    $confirmPassword =
        $_POST['confirm_password'] ?? '';


    // ==================================================
    // VALIDATION
    // ==================================================

    if (
        $currentPassword === '' ||
        $newPassword === '' ||
        $confirmPassword === ''
    ) {

        $message =
            "Please fill in all password fields.";

        $messageType =
            "error";

    } elseif (strlen($newPassword) < 8) {

    $message =
        "Password must contain at least 8 characters.";

    $messageType =
        "error";

}

elseif (!preg_match('/[A-Z]/', $newPassword)) {

    $message =
        "Password must contain at least one uppercase letter.";

    $messageType =
        "error";

}

elseif (!preg_match('/[a-z]/', $newPassword)) {

    $message =
        "Password must contain at least one lowercase letter.";

    $messageType =
        "error";

}

elseif (!preg_match('/[0-9]/', $newPassword)) {

    $message =
        "Password must contain at least one number.";

    $messageType =
        "error";

}

elseif (!preg_match('/[\W_]/', $newPassword)) {

    $message =
        "Password must contain at least one special character.";

    $messageType =
        "error";

}

elseif ($newPassword !== $confirmPassword) {

    $message =
        "The new passwords do not match.";

    $messageType =
        "error";

} else {


        // ==================================================
        // GET CURRENT PASSWORD
        // ==================================================

        $stmt = $conn->prepare(
            "SELECT password
             FROM admin
             WHERE id = ?
             LIMIT 1"
        );

        $adminId = (int) $_SESSION['admin_id'];
        $stmt->bind_param("i", $adminId);
        $stmt->execute();

        $result = $stmt->get_result();

        $admin = $result->fetch_assoc();

        $stmt->close();


        if (!$admin) {

            $message =
                "Administrator account not found.";

            $messageType =
                "error";

        } else {


            // ==================================================
            // VERIFY CURRENT PASSWORD
            // ==================================================

            if (
                !password_verify(
                    $currentPassword,
                    $admin['password']
                )
            ) {

                $message =
                    "The current password is incorrect.";

                $messageType =
                    "error";

            } else {


                // ==================================================
                // HASH NEW PASSWORD
                // ==================================================

                $hashedPassword =
                    password_hash(
                        $newPassword,
                        PASSWORD_DEFAULT
                    );


                // ==================================================
                // UPDATE PASSWORD
                // ==================================================

                $stmt = $conn->prepare(
                    "UPDATE admin
                     SET password = ?
                     WHERE id = ?"
                );


                $adminId = (int) $_SESSION['admin_id'];
                $stmt->bind_param(
                    "si",
                    $hashedPassword,
                    $adminId
                );


                if ($stmt->execute()) {

                    $message =
                        "Password changed successfully.";

                    $messageType =
                        "success";

                } else {

                    $message =
                        "Unable to change password.";

                    $messageType =
                        "error";
                }


                $stmt->close();
            }
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
        Change Password
    </title>


    <link
        rel="stylesheet"
        href="/school_app/css/style.css"
    >

</head>


<body>


<?php include "../menu.php"; ?>


<main class="dashboard settings-page">


    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="settings-page-header">

        <div>

            <h1>
                Change Password
            </h1>

            <p>
                Keep your administrator account secure.
            </p>

        </div>

    </div>


    <!-- ==================================================
         MESSAGE
    ================================================== -->

    <?php if ($message !== ''): ?>

        <div class="settings-message <?php echo $messageType; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <!-- ==================================================
         PASSWORD CARD
    ================================================== -->

    <section class="settings-card">


        <div class="settings-card-header">

            <div class="settings-card-icon">
                🔐
            </div>

            <div>

                <h2>
                    Password Security
                </h2>

                <p>
                    Update your administrator password.
                </p>

            </div>

        </div>


        <form
            method="POST"
            class="settings-form"
        >
            <?php echo csrf_field(); ?>


            <!-- CURRENT PASSWORD -->

            <div class="form-group">

                <label for="current_password">
                    Current Password
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    placeholder="Enter current password"
                    required
                >

            </div>


            <!-- NEW PASSWORD -->

            <div class="form-group">

                <label for="new_password">
                    New Password
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="Enter new password"
                    minlength="8"
                    required
                >

<div class="password-strength">

    <div class="password-strength-bar">

        <div
            id="passwordStrengthBar"
            class="password-strength-fill"
        ></div>

    </div>

    <span id="passwordStrengthText">
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
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    minlength="8"
                    required
                >

            </div>


            <!-- BUTTON -->

            <div class="settings-form-footer">

                <button
                    type="submit"
                    class="settings-save-button"
                >
                    🔐 Change Password
                </button>

            </div>


        </form>


    </section>


    <!-- BACK -->

    <a
        href="profile.php"
        class="back-settings-link"
    >
        ← Back to Profile
    </a>


</main>


<script
    src="/school_app/js/app.js"
    defer
></script>


</body>

</html>