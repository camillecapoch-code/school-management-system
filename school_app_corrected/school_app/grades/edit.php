<?php

require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../includes/grade_functions.php";

// ======================================================
// CHECK ID
// ======================================================

if (!isset($_GET['id'])) {
    die("Grade ID is missing.");
}

$id = (int) $_GET['id'];


// ======================================================
// GET GRADE
// ======================================================

$sql = "SELECT
            grades.id,
            grades.student_id,
            grades.subject_id,
            grades.homework,
            grades.test,
            grades.exam,

            students.first_name,
            students.last_name,
            students.matricule,

            subjects.name AS subject_name,
            subjects.coefficient

        FROM grades

        JOIN students
            ON grades.student_id = students.id

        JOIN subjects
            ON grades.subject_id = subjects.id

        WHERE grades.id = ?";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    die("Grade not found.");
}


$grade = $result->fetch_assoc();


// ======================================================
// UPDATE
// ======================================================

$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $homework = (float) $_POST['homework'];
    $test = (float) $_POST['test'];
    $exam = (float) $_POST['exam'];


    if (
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

    $grade =
    ($homework * 0.20)
    +
    ($test * 0.30)
    +
    ($exam * 0.50);
 
     $grade = round($grade, 2);

                $updateSql = "UPDATE grades

              SET
                  homework = ?,
                  test = ?,
                  exam = ?,
                  grade = ?

              WHERE id = ?";

        $updateStmt =
            $conn->prepare($updateSql);


        $updateStmt->bind_param(
        "ddddi",
        $homework,
        $test,
        $exam,
        $grade,
        $id
        );


        if ($updateStmt->execute()) {

            header(
                "Location: index.php?success=grade_updated"
            );

            exit;

        } else {

            $error =
                "Unable to update the grade.";

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
        Edit Grade - School App
    </title>

</head>


<body>


<?php include "../menu.php"; ?>


<main>


    <div class="page-header">

        <div>

            <h1>
                Edit Grade
            </h1>

            <p>
                Modify the student's academic results.
            </p>

        </div>

    </div>


    <?php if (!empty($error)) { ?>

        <div class="alert alert-error">

            <?php

            echo htmlspecialchars($error);

            ?>

        </div>

    <?php } ?>


    <!-- ==================================================
         STUDENT INFORMATION
    =================================================== -->

    <div class="student-information">

        <div>

            <span>
                Student
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $grade['first_name']
                    . " "
                    . $grade['last_name']
                );

                ?>

            </strong>

        </div>


        <div>

            <span>
                Matricule
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $grade['matricule']
                );

                ?>

            </strong>

        </div>


        <div>

            <span>
                Subject
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $grade['subject_name']
                );

                ?>

            </strong>

        </div>

    </div>


    <!-- ==================================================
         FORM
    =================================================== -->

    <form
        method="POST"
        action=""
    >
            <?php echo csrf_field(); ?>


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

                echo htmlspecialchars(
                    $grade['homework']
                );

            ?>"
            required
        >


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

                echo htmlspecialchars(
                    $grade['test']
                );

            ?>"
            required
        >


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

                echo htmlspecialchars(
                    $grade['exam']
                );

            ?>"
            required
        >


        <br>


        <button
            type="submit"
            class="btn"
        >

            Update Grade

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