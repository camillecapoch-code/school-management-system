<?php

require_once "../config/database.php";
require_once "../config/auth.php";


// ======================================================
// CHECK TEACHER ID
// ======================================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Teacher ID is missing.");
}

$teacher_id = (int) $_GET['id'];


// ======================================================
// GET TEACHER
// ======================================================

$stmt = $conn->prepare("
    SELECT id, first_name, last_name
    FROM teachers
    WHERE id = ?
");

$stmt->bind_param("i", $teacher_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Teacher not found.");
}

$teacher = $result->fetch_assoc();


// ======================================================
// SAVE ASSIGNMENT
// ======================================================

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $class_id = (int) $_POST['class_id'];
    $subject_id = (int) $_POST['subject_id'];


    if ($class_id <= 0 || $subject_id <= 0) {

        $error = "Please select a class and a subject.";

    } else {

        $check = $conn->prepare("
            SELECT id
            FROM teacher_assignments
            WHERE teacher_id = ?
              AND class_id = ?
              AND subject_id = ?
        ");

        $check->bind_param(
            "iii",
            $teacher_id,
            $class_id,
            $subject_id
        );

        $check->execute();

        $checkResult = $check->get_result();


        if ($checkResult->num_rows > 0) {

            $error = "This assignment already exists.";

        } else {

            $insert = $conn->prepare("
                INSERT INTO teacher_assignments
                    (teacher_id, class_id, subject_id)
                VALUES
                    (?, ?, ?)
            ");

            $insert->bind_param(
                "iii",
                $teacher_id,
                $class_id,
                $subject_id
            );


            if ($insert->execute()) {

                $message = "Assignment added successfully.";

            } else {

                $error = "Unable to save the assignment.";
            }
        }
    }
}


// ======================================================
// GET CLASSES
// ======================================================

$classesResult = $conn->query("
    SELECT id, name
    FROM classes
    ORDER BY id
");


// ======================================================
// GET SUBJECTS
// ======================================================

$subjectsResult = $conn->query("
    SELECT id, name, coefficient
    FROM subjects
    ORDER BY name
");


// ======================================================
// GET ASSIGNMENTS
// ======================================================

$assignments = $conn->prepare("
    SELECT
        teacher_assignments.id,
        classes.name AS class_name,
        subjects.name AS subject_name,
        subjects.coefficient

    FROM teacher_assignments

    JOIN classes
        ON teacher_assignments.class_id = classes.id

    JOIN subjects
        ON teacher_assignments.subject_id = subjects.id

    WHERE teacher_assignments.teacher_id = ?

    ORDER BY classes.id, subjects.name
");

$assignments->bind_param(
    "i",
    $teacher_id
);

$assignments->execute();

$assignmentsResult = $assignments->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Teacher Assignments</title>

    <link
        rel="stylesheet"
        href="teachers.css"
    >

</head>


<body>


<div class="container">


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div class="top-bar">

        <div>

            <h1>
                Teacher Assignments
            </h1>

            <p>

                <?php
                echo htmlspecialchars(
                    $teacher['first_name']
                );
                ?>

                <?php
                echo htmlspecialchars(
                    $teacher['last_name']
                );
                ?>

            </p>

        </div>


        <a
            href="index.php"
            class="btn-back"
        >
            ← Back
        </a>

    </div>


    <!-- ==================================================
         MESSAGES
    =================================================== -->

    <?php if ($message !== ""): ?>

        <div class="success">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- ==================================================
         ADD ASSIGNMENT
    =================================================== -->

    <h2>
        Add Assignment
    </h2>


    <form method="POST">
            <?php echo csrf_field(); ?>


        <div class="form-group">

            <label for="class_id">
                Class
            </label>

            <select
                name="class_id"
                id="class_id"
                required
            >

                <option value="">
                    Select a class
                </option>

                <?php while (
                    $class =
                    $classesResult->fetch_assoc()
                ): ?>

                    <option
                        value="<?php echo $class['id']; ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $class['name']
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>


        <div class="form-group">

            <label for="subject_id">
                Subject
            </label>

            <select
                name="subject_id"
                id="subject_id"
                required
            >

                <option value="">
                    Select a subject
                </option>

                <?php while (
                    $subject =
                    $subjectsResult->fetch_assoc()
                ): ?>

                    <option
                        value="<?php echo $subject['id']; ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $subject['name']
                        );
                        ?>

                        —
                        Coef.
                        <?php
                        echo htmlspecialchars(
                            $subject['coefficient']
                        );
                        ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>


        <button
            type="submit"
            class="btn-save"
        >
            Add Assignment
        </button>


    </form>


    <!-- ==================================================
         CURRENT ASSIGNMENTS
    =================================================== -->

    <h2>
        Current Assignments
    </h2>


    <?php if ($assignmentsResult->num_rows > 0): ?>

        <table>

            <thead>

                <tr>

                    <th>
                        Class
                    </th>

                    <th>
                        Subject
                    </th>

                    <th>
                        Coefficient
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php while (
                    $assignment =
                    $assignmentsResult->fetch_assoc()
                ): ?>

                    <tr>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $assignment['class_name']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $assignment['subject_name']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $assignment['coefficient']
                            );
                            ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>


    <?php else: ?>

        <p>
            No assignments yet.
        </p>

    <?php endif; ?>


</div>


</body>

</html>