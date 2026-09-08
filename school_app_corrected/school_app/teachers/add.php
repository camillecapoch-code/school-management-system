<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ======================================================
// VARIABLES
// ======================================================

$message = '';
$messageType = '';

$firstName = '';
$lastName = '';
$phone = '';
$email = '';


// ======================================================
// FORM SUBMISSION
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');


    // ==================================================
    // REQUIRED FIELDS
    // ==================================================

    if ($firstName === '' || $lastName === '') {

        $message =
            "First name and last name are required.";

        $messageType = "error";

    }


    // ==================================================
    // EMAIL VALIDATION
    // ==================================================

    elseif (
        $email !== '' &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $message =
            "Please enter a valid email address.";

        $messageType = "error";

    }


    else {

        // ==================================================
        // INSERT TEACHER
        // ==================================================

        $stmt = $conn->prepare(
            "INSERT INTO teachers
            (first_name, last_name, phone, email)
            VALUES (?, ?, ?, ?)"
        );


        $stmt->bind_param(
            "ssss",
            $firstName,
            $lastName,
            $phone,
            $email
        );


        if ($stmt->execute()) {

            $message =
                "Teacher added successfully.";

            $messageType = "success";


            // Clear form

            $firstName = '';
            $lastName = '';
            $phone = '';
            $email = '';

        } else {

            $message =
                "Unable to add teacher.";

            $messageType = "error";

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
        Add Teacher - School Management System
    </title>


    <link
        rel="stylesheet"
        href="/school_app/css/style.css"
    >

</head>


<body>


<?php include "../menu.php"; ?>


<main class="dashboard">


    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="page-header">

        <div>

            <h1>
                Add Teacher
            </h1>

            <p>
                Create a new teacher profile.
            </p>

        </div>


        <a
            href="index.php"
            class="secondary-button"
        >
            ← Back to Teachers
        </a>

    </div>


    <!-- ==================================================
         FORM CARD
    ================================================== -->

    <section class="dashboard-section">


        <div class="form-card">


            <div class="form-card-header">

                <h2>
                    Teacher Information
                </h2>

                <p>
                    Enter the teacher's information below.
                </p>

            </div>


            <!-- MESSAGE -->

            <?php if ($message !== ''): ?>

                <div
                    class="<?php
                        echo $messageType === 'success'
                            ? 'form-success'
                            : 'form-error';
                    ?>"
                >

                    <?php

                    echo htmlspecialchars(
                        $message
                    );

                    ?>

                </div>

            <?php endif; ?>


            <!-- FORM -->

            <form
                method="POST"
                class="teacher-form"
            >
            <?php echo csrf_field(); ?>


                <!-- FIRST NAME -->

                <div class="form-group">

                    <label for="first_name">
                        First Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="<?php
                            echo htmlspecialchars(
                                $firstName
                            );
                        ?>"
                        placeholder="Enter first name"
                        required
                    >

                </div>


                <!-- LAST NAME -->

                <div class="form-group">

                    <label for="last_name">
                        Last Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="<?php
                            echo htmlspecialchars(
                                $lastName
                            );
                        ?>"
                        placeholder="Enter last name"
                        required
                    >

                </div>


                <!-- PHONE -->

                <div class="form-group">

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?php
                            echo htmlspecialchars(
                                $phone
                            );
                        ?>"
                        placeholder="Enter phone number"
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php
                            echo htmlspecialchars(
                                $email
                            );
                        ?>"
                        placeholder="teacher@example.com"
                    >

                </div>


                <!-- BUTTONS -->

                <div class="form-actions">

                    <a
                        href="index.php"
                        class="secondary-button"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="primary-button"
                    >
                        + Add Teacher
                    </button>

                </div>


            </form>


        </div>


    </section>


</main>


<script
    src="/school_app/js/app.js"
    defer
></script>


</body>

</html>