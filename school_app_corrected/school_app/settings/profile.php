<?php
require_once "../config/auth.php";
require_once "../config/database.php";

$adminId = (int) $_SESSION['admin_id'];
$message = '';
$messageType = '';

function loadAdmin(mysqli $conn, int $adminId): array {
    $stmt = $conn->prepare("SELECT id, full_name, email FROM admin WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc() ?: [];
    $stmt->close();
    return $admin;
}

$admin = loadAdmin($conn, $adminId);
if (!$admin) {
    session_destroy();
    header("Location: /school_app/login.php");
    exit;
}

$fullName = $admin['full_name'];
$email = $admin['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($fullName === '' || $email === '') {
        $message = 'Please fill in all fields.'; $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.'; $messageType = 'error';
    } else {
        $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $email, $adminId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($existing) {
            $message = 'This email address is already in use.'; $messageType = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE admin SET full_name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fullName, $email, $adminId);
            if ($stmt->execute()) {
                $_SESSION['admin_name'] = $fullName;
                $_SESSION['admin_email'] = $email;
                $admin['full_name'] = $fullName; $admin['email'] = $email;
                $message = 'Profile updated successfully.'; $messageType = 'success';
            } else {
                $message = 'Unable to update profile.'; $messageType = 'error';
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
        Administrator Profile
    </title>

    <link
        rel="stylesheet"
        href="/school_app/css/style.css"
    >

</head>


<body>


<?php include "../menu.php"; ?>


<main class="dashboard settings-page">


    <!-- HEADER -->

    <div class="settings-page-header">

        <div>

            <h1>
                Administrator Profile
            </h1>

            <p>
                Manage your administrator account.
            </p>

        </div>

    </div>


    <!-- MESSAGE -->

    <?php if ($message !== ''): ?>

        <div class="settings-message <?php echo $messageType; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <!-- PROFILE -->

    <section class="settings-card">


        <div class="settings-card-header">

            <div class="settings-card-icon">
                👤
            </div>

            <div>

                <h2>
                    Profile Information
                </h2>

                <p>
                    Update your administrator information.
                </p>

            </div>

        </div>


        <!-- PROFILE PREVIEW -->

        <div class="profile-preview">

            <div class="profile-avatar">

                <?php
                echo strtoupper(
                    substr($fullName, 0, 1)
                );
                ?>

            </div>


            <div>

                <strong>
                    <?php
                    echo htmlspecialchars($fullName);
                    ?>
                </strong>

                <span>
                    School Administrator
                </span>

            </div>

        </div>


        <!-- FORM -->

<form method="POST" class="settings-form">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="settings-form-footer">
                <button type="submit" class="settings-save-button">💾 Save Profile</button>
            </div>

        </form>


    </section>


    <!-- SECURITY -->

    <section class="settings-card">

        <div class="settings-card-header">

            <div class="settings-card-icon">
                🔐
            </div>

            <div>

                <h2>
                    Security
                </h2>

                <p>
                    Manage your account security.
                </p>

            </div>

        </div>


        <div class="settings-option">

            <div>

                <strong>
                    Password
                </strong>

                <span>
                    Change your administrator password.
                </span>

            </div>


            <a
                href="password.php"
                class="settings-action-link"
            >
                Change Password →
            </a>

        </div>

    </section>


</main>


<script
    src="/school_app/js/app.js"
    defer
></script>

</body>

</html>