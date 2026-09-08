<?php

require_once "../config/database.php";
require_once "../config/auth.php";


// ======================================================
// VARIABLES
// ======================================================

$error = "";


// ======================================================
// GET STUDENTS
// ======================================================

$students = $conn->query(
    "SELECT id, matricule, first_name, last_name
     FROM students
     ORDER BY last_name, first_name"
);


// ======================================================
// GET SUBJECTS
// ======================================================

$subjects = $conn->query(
    "SELECT id, name, coefficient
     FROM subjects
     ORDER BY name"
);


// ======================================================
// ADD GRADE
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $student_id = (int) $_POST['student_id'];
    $subject_id = (int) $_POST['subject_id'];

    $homework = (float) $_POST['homework'];
    $test = (float) $_POST['test'];
    $exam = (float) $_POST['exam'];


    // ==================================================
    // VALIDATION
    // ==================================================

    if ($student_id <= 0 || $subject_id <= 0) {

        $error =
            "Please select a student and a subject.";

    } elseif (
        $homework < 0 ||
        $homework > 20 ||
        $test < 0 ||
        $test > 20 ||
        $exam < 0 ||
        $exam > 20
    ) {

        $error =
            "All grades must be between 0 and 20.";

    } else {


        // ==================================================
        // CHECK DUPLICATE
        // ==================================================

        $checkSql = "SELECT id

                     FROM grades

                     WHERE student_id = ?
                     AND subject_id = ?";


        $checkStmt =
            $conn->prepare($checkSql);


        $checkStmt->bind_param(
            "ii",
            $student_id,
            $subject_id
        );


        $checkStmt->execute();


        $checkResult =
            $checkStmt->get_result();


        if ($checkResult->num_rows > 0) {


            // ==============================================
            // GRADE ALREADY EXISTS
            // ==============================================

            $existingGrade =
                $checkResult->fetch_assoc();


            $error =
                "This student already has a grade "
                . "for this subject. "
                . "Please edit the existing grade "
                . "instead.";

        } else {


            // ==================================================
            // INSERT GRADE
            // ==================================================

            $sql = "INSERT INTO grades
                        (
                            student_id,
                            subject_id,
                            homework,
                            test,
                            exam
                        )

                    VALUES
                        (?, ?, ?, ?, ?)";


            $stmt =
                $conn->prepare($sql);


            $stmt->bind_param(
                "iiddd",
                $student_id,
                $subject_id,
                $homework,
                $test,
                $exam
            );


            if ($stmt->execute()) {


                // ==========================================
                // SUCCESS
                // ==========================================

                header(
                    "Location: index.php?success=grade_added"
                );

                exit;


            } else {


                $error =
                    "Unable to add the grade.";

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
        Add Grade - School App
    </title>

</head>


<body>


<?php include "../menu.php"; ?>


<main>


    <!-- ==================================================
         HEADER
    =================================================== -->

    <div class="page-header">

        <div>

            <h1>
                Add Grade
            </h1>

            <p>
                Enter a student's academic results.
            </p>

        </div>

    </div>


    <!-- ==================================================
         ERROR MESSAGE
    =================================================== -->

    <?php if (!empty($error)) { ?>

        <div class="alert alert-error">

            <?php

            echo htmlspecialchars($error);

            ?>

        </div>

    <?php } ?>


    <!-- ==================================================
         FORM
    =================================================== -->

    <form
        method="POST"
        action=""
    >
            <?php echo csrf_field(); ?>


        <!-- STUDENT -->

        <label for="student_id">

            Student

        </label>


        <select
            name="student_id"
            id="student_id"
            required
        >

            <option value="">

                -- Select Student --

            </option>


            <?php

            while (
                $student =
                $students->fetch_assoc()
            ) {

            ?>

                <option
                    value="<?php echo $student['id']; ?>"
                    <?php

                    if (
                        isset($_POST['student_id'])
                        &&
                        $_POST['student_id']
                        == $student['id']
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    <?php

                    echo htmlspecialchars(
                        $student['matricule']
                        . " - "
                        . $student['first_name']
                        . " "
                        . $student['last_name']
                    );

                    ?>

                </option>

            <?php

            }

            ?>

        </select>


        <!-- SUBJECT -->

        <label for="subject_id">

            Subject

        </label>


        <select
            name="subject_id"
            id="subject_id"
            required
        >

            <option value="">

                -- Select Subject --

            </option>


            <?php

            while (
                $subject =
                $subjects->fetch_assoc()
            ) {

            ?>

                <option
                    value="<?php echo $subject['id']; ?>"
                    <?php

                    if (
                        isset($_POST['subject_id'])
                        &&
                        $_POST['subject_id']
                        == $subject['id']
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    <?php

                    echo htmlspecialchars(
                        $subject['name']
                    );

                    ?>

                    -
                    Coef.

                    <?php

                    echo $subject['coefficient'];

                    ?>

                </option>

            <?php

            }

            ?>

        </select>


        <!-- HOMEWORK -->

        <label for="homework">

            Homework /20

        </label>


        <input
            type="number"
            name="homework"
            id="homework"
            min="0"
            max="20"
            step="0.01"
            value="<?php

                echo isset($_POST['homework'])
                    ? htmlspecialchars(
                        $_POST['homework']
                    )
                    : '';

            ?>"
            required
        >


        <!-- TEST -->

        <label for="test">

            Test /20

        </label>


        <input
            type="number"
            name="test"
            id="test"
            min="0"
            max="20"
            step="0.01"
            value="<?php

                echo isset($_POST['test'])
                    ? htmlspecialchars(
                        $_POST['test']
                    )
                    : '';

            ?>"
            required
        >


        <!-- EXAM -->

        <label for="exam">

            Exam /20

        </label>


        <input
            type="number"
            name="exam"
            id="exam"
            min="0"
            max="20"
            step="0.01"
            value="<?php

                echo isset($_POST['exam'])
                    ? htmlspecialchars(
                        $_POST['exam']
                    )
                    : '';

            ?>"
            required
        >


        <br>


        <button
            type="submit"
            class="btn"
        >

            Save Grade

        </button>


        <a
            href="index.php"
            class="btn"
        >

            Cancel

        </a>


    </form>


</main>


</body>

</html>