<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ======================================================
// CHECK ID
// ======================================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {

    header("Location: index.php");
    exit;
}


// ======================================================
// GET TEACHER
// ======================================================

$stmt = $conn->prepare(
    "SELECT
        id,
        first_name,
        last_name,
        phone,
        email
     FROM teachers
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$teacher = $result->fetch_assoc();

$stmt->close();


if (!$teacher) {

    header("Location: index.php");
    exit;
}


// ======================================================
// VARIABLES
// ======================================================

$message = '';
$messageType = '';

$firstName = $teacher['first_name'];
$lastName  = $teacher['last_name'];
$phone     = $teacher['phone'] ?? '';
$email     = $teacher['email'] ?? '';


// ======================================================
// UPDATE TEACHER
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $firstName = trim(
        $_POST['first_name'] ?? ''
    );

    $lastName = trim(
        $_POST['last_name'] ?? ''
    );

    $phone = trim(
        $_POST['phone'] ?? ''
    );

    $email = trim(
        $_POST['email'] ?? ''
    );


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
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $message =
            "Please enter a valid email address.";

        $messageType = "error";

    }


    else {

        // ==================================================
        // UPDATE
        // ==================================================

        $stmt = $conn->prepare(
            "UPDATE teachers
             SET
                first_name = ?,
                last_name = ?,
                phone = ?,
                email = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssi",
            $firstName,
            $lastName,
            $phone,
            $email,
            $id
        );


        if ($stmt->execute()) {

            $message =
                "Teacher updated successfully.";

            $messageType = "success";

        } else {

            $message =
                "Unable to update teacher.";

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
        Edit Teacher - School Management System
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
                Edit Teacher
            </h1>

            <p>
                Update the teacher's information.
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
         FORM
    ================================================== -->

    <section class="dashboard-section">


        <div class="form-card">


            <div class="form-card-header">

                <h2>
                    Teacher Information
                </h2>

                <p>
                    Modify the information below.
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
                    >

                </div>


                <!-- ACTIONS -->

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
                        Save Changes
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