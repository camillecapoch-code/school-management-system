<?php

require_once "../config/database.php";
require_once "../config/auth.php";

// ======================================================
// GET SETTINGS
// ======================================================

$result = $conn->query(
    "SELECT *
     FROM settings
     WHERE id = 1
     LIMIT 1"
);

$settings = $result->fetch_assoc();


// ======================================================
// DEFAULT VALUES
// ======================================================

$schoolName = $settings['school_name'] ?? 'School Management System';
$schoolEmail = $settings['school_email'] ?? '';
$phone = $settings['phone'] ?? '';
$address = $settings['address'] ?? '';

$notificationsEnabled =
    isset($settings['notifications_enabled'])
    ? (int)$settings['notifications_enabled']
    : 1;

$studentNotifications =
    isset($settings['student_notifications'])
    ? (int)$settings['student_notifications']
    : 1;

$gradeNotifications =
    isset($settings['grade_notifications'])
    ? (int)$settings['grade_notifications']
    : 1;

$teacherNotifications =
    isset($settings['teacher_notifications'])
    ? (int)$settings['teacher_notifications']
    : 1;


$message = '';
$messageType = '';


// ======================================================
// SAVE SETTINGS
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $schoolName =
        trim($_POST['school_name'] ?? '');

    $schoolEmail =
        trim($_POST['school_email'] ?? '');

    $phone =
        trim($_POST['phone'] ?? '');

    $address =
        trim($_POST['address'] ?? '');


    // Checkboxes

    $notificationsEnabled =
        isset($_POST['notifications_enabled']) ? 1 : 0;

    $studentNotifications =
        isset($_POST['student_notifications']) ? 1 : 0;

    $gradeNotifications =
        isset($_POST['grade_notifications']) ? 1 : 0;

    $teacherNotifications =
        isset($_POST['teacher_notifications']) ? 1 : 0;


    // ==================================================
    // VALIDATION
    // ==================================================

    if ($schoolName === '') {

        $message = "School name is required.";

        $messageType = "error";

    } else {


        // ==================================================
        // UPDATE DATABASE
        // ==================================================

        $stmt = $conn->prepare(
            "UPDATE settings
             SET
                school_name = ?,
                school_email = ?,
                phone = ?,
                address = ?,
                notifications_enabled = ?,
                student_notifications = ?,
                grade_notifications = ?,
                teacher_notifications = ?
             WHERE id = 1"
        );


        $stmt->bind_param(
            "ssssiiii",
            $schoolName,
            $schoolEmail,
            $phone,
            $address,
            $notificationsEnabled,
            $studentNotifications,
            $gradeNotifications,
            $teacherNotifications
        );


        if ($stmt->execute()) {

            $message =
                "Settings saved successfully.";

            $messageType =
                "success";

        } else {

            $message =
                "Unable to save settings.";

            $messageType =
                "error";
        }


        $stmt->close();
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
        Settings - School Management System
    </title>


    <link
        rel="stylesheet"
        href="/school_app/css/style.css"
    >

</head>


<body>


<?php include "../menu.php"; ?>


<!-- ==================================================
     MAIN CONTENT
================================================== -->

<main class="dashboard settings-page">


    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="settings-page-header">

        <div>

            <h1>
                Settings
            </h1>

            <p>
                Manage your school system preferences.
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
         GENERAL SETTINGS
    ================================================== -->

    <section class="settings-card">

        <div class="settings-card-header">

            <div class="settings-card-icon">
                🏫
            </div>

            <div>

                <h2>
                    General Settings
                </h2>

                <p>
                    Basic information about your school.
                </p>

            </div>

        </div>


        <form
            method="POST"
            action=""
            class="settings-form"
        >
            <?php echo csrf_field(); ?>


            <!-- SCHOOL NAME -->

            <div class="form-group">

                <label for="school_name">
                    School Name
                </label>

                <input
                    type="text"
                    id="school_name"
                    name="school_name"
                    value="<?php echo htmlspecialchars($schoolName); ?>"
                    placeholder="Enter school name"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="school_email">
                    School Email
                </label>

                <input
                    type="email"
                    id="school_email"
                    name="school_email"
                    value="<?php echo htmlspecialchars($schoolEmail); ?>"
                    placeholder="school@example.com"
                >

            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    placeholder="Enter phone number"
                >

            </div>


            <!-- ADDRESS -->

            <div class="form-group">

                <label for="address">
                    Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    placeholder="Enter school address"
                ><?php echo htmlspecialchars($address); ?></textarea>

            </div>


            <div class="settings-form-footer">

                <button
                    type="submit"
                    class="settings-save-button"
                >
                    💾 Save Changes
                </button>

            </div>


        </form>

    </section>



    <!-- ==================================================
         APPEARANCE
    ================================================== -->

    <section class="settings-card">

        <div class="settings-card-header">

            <div class="settings-card-icon">
                🎨
            </div>

            <div>

                <h2>
                    Appearance
                </h2>

                <p>
                    Customize the appearance of the application.
                </p>

            </div>

        </div>


        <div class="theme-selector">

            <div class="theme-selector-info">

                <strong>
                    Theme
                </strong>

                <span>
                    Choose between light and dark mode.
                </span>

            </div>


    <div class="theme-buttons">

     <button id="themeToggle" class="theme-toggle" type="button" aria-label="Changer le thème">  
      <!-- Soleil -->
     <svg id="sunIcon" class="theme-icon sun-icon"
         viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round">

        <circle cx="12" cy="12" r="4"></circle>

        <line x1="12" y1="2" x2="12" y2="4"></line>
        <line x1="12" y1="20" x2="12" y2="22"></line>

        <line x1="4.93" y1="4.93" x2="6.34" y2="6.34"></line>
        <line x1="17.66" y1="17.66" x2="19.07" y2="19.07"></line>

        <line x1="2" y1="12" x2="4" y2="12"></line>
        <line x1="20" y1="12" x2="22" y2="12"></line>

        <line x1="4.93" y1="19.07" x2="6.34" y2="17.66"></line>
        <line x1="17.66" y1="6.34" x2="19.07" y2="4.93"></line>
    </svg>

      <!-- Lune -->
    <svg id="moonIcon" class="theme-icon moon-icon"
         viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round">

          <path d="M21 12.79A9 9 0 1 1 11.21 3
                 7 7 0 0 0 21 12.79Z"></path>
    </svg>
 
    </button>
    </div>


    </div>

    </section>



    <!-- ==================================================
         NOTIFICATIONS
    ================================================== -->

    <section class="settings-card">

        <div class="settings-card-header">

            <div class="settings-card-icon">
                🔔
            </div>

            <div>

                <h2>
                    Notifications
                </h2>

                <p>
                    Manage system notifications.
                </p>

            </div>

        </div>


        <!-- SYSTEM -->

        <div class="settings-option">

            <div>

                <strong>
                    System Notifications
                </strong>

                <span>
                    Enable or disable all system notifications.
                </span>

            </div>


            <label class="switch">

                <input
                    type="checkbox"
                    name="notifications_enabled"
                    form="notificationForm"
                    <?php
                    echo $notificationsEnabled ? 'checked' : '';
                    ?>
                >

                <span class="slider"></span>

            </label>

        </div>



        <!-- STUDENTS -->

        <div class="settings-option">

            <div>

                <strong>
                    Student Notifications
                </strong>

                <span>
                    Notify when a new student is registered.
                </span>

            </div>


            <label class="switch">

                <input
                    type="checkbox"
                    name="student_notifications"
                    form="notificationForm"
                    <?php
                    echo $studentNotifications ? 'checked' : '';
                    ?>
                >

                <span class="slider"></span>

            </label>

        </div>



        <!-- GRADES -->

        <div class="settings-option">

            <div>

                <strong>
                    Grade Notifications
                </strong>

                <span>
                    Notify when a new grade is recorded.
                </span>

            </div>


            <label class="switch">

                <input
                    type="checkbox"
                    name="grade_notifications"
                    form="notificationForm"
                    <?php
                    echo $gradeNotifications ? 'checked' : '';
                    ?>
                >

                <span class="slider"></span>

            </label>

        </div>



        <!-- TEACHERS -->

        <div class="settings-option">

            <div>

                <strong>
                    Teacher Notifications
                </strong>

                <span>
                    Notify when a new teacher is registered.
                </span>

            </div>


            <label class="switch">

                <input
                    type="checkbox"
                    name="teacher_notifications"
                    form="notificationForm"
                    <?php
                    echo $teacherNotifications ? 'checked' : '';
                    ?>
                >

                <span class="slider"></span>

            </label>

        </div>


        <!-- SAVE NOTIFICATIONS -->

        <form
            method="POST"
            id="notificationForm"
            class="notification-save-form"
        >
            <?php echo csrf_field(); ?>

            <input
                type="hidden"
                name="school_name"
                value="<?php echo htmlspecialchars($schoolName); ?>"
            >

            <input
                type="hidden"
                name="school_email"
                value="<?php echo htmlspecialchars($schoolEmail); ?>"
            >

            <input
                type="hidden"
                name="phone"
                value="<?php echo htmlspecialchars($phone); ?>"
            >

            <input
                type="hidden"
                name="address"
                value="<?php echo htmlspecialchars($address); ?>"
            >

            <button
                type="submit"
                class="settings-save-button"
            >
                💾 Save Notification Settings
            </button>

        </form>

    </section>


</main>


<script
    src="/school_app/js/app.js"
    defer
></script>


</body>

</html>