<?php

require_once "../config/database.php";
require_once "../config/auth.php";

// ======================================================
// GET STUDENT ID
// ======================================================

$studentId = filter_input(
    INPUT_GET,
    'student_id',
    FILTER_VALIDATE_INT
);

if (!$studentId) {

    header("Location: index.php");
    exit;
}


// ======================================================
// GET STUDENT INFORMATION
// ======================================================

$stmt = $conn->prepare(
    "SELECT
        students.id,
        students.matricule,
        students.first_name,
        students.last_name,
        students.phone,
        classes.name AS class_name

     FROM students

     LEFT JOIN classes
        ON students.class_id = classes.id

     WHERE students.id = ?

     LIMIT 1"
);

$stmt->bind_param("i", $studentId);

$stmt->execute();

$studentResult = $stmt->get_result();

$student = $studentResult->fetch_assoc();

$stmt->close();


if (!$student) {

    header("Location: index.php");
    exit;
}


// ======================================================
// GET STUDENT GRADES
// ======================================================

$stmt = $conn->prepare(
    "SELECT
        grades.id,
        grades.homework,
        grades.test,
        grades.exam,

        subjects.name AS subject_name,
        subjects.coefficient

     FROM grades

     INNER JOIN subjects
        ON grades.subject_id = subjects.id

     WHERE grades.student_id = ?

     ORDER BY subjects.name ASC"
);

$stmt->bind_param("i", $studentId);

$stmt->execute();

$gradesResult = $stmt->get_result();

$stmt->close();


// ======================================================
// CALCULATIONS
// ======================================================

$totalWeighted = 0;

$totalCoefficients = 0;

$totalSubjects = 0;

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
        Student Bulletin - School Management System
    </title>

    <link
        rel="stylesheet"
        href="/school_app/css/style.css"
    >

    <link
        rel="stylesheet"
        href="/school_app/grades/student-grades.css"
    >

</head>


<body>


<?php include "../menu.php"; ?>


<main class="bulletin-page">


    <!-- ==================================================
         TOP ACTIONS
    ================================================== -->

    <div class="bulletin-actions">

        <a
            href="index.php"
            class="bulletin-back"
        >
            ← Back to Grades
        </a>


        <button
            type="button"
            class="bulletin-print"
            onclick="window.print()"
        >
            🖨 Print Bulletin
        </button>

    </div>


    <!-- ==================================================
         BULLETIN
    ================================================== -->

    <section class="bulletin">


        <!-- ==================================================
             SCHOOL HEADER
        ================================================== -->

        <div class="bulletin-school">

            <div class="bulletin-logo">
                🏫
            </div>

            <div>

                <h1>
                    SCHOOL MANAGEMENT SYSTEM
                </h1>

                <p>
                    Academic Report Card
                </p>

            </div>

        </div>


        <div class="bulletin-line"></div>


        <!-- ==================================================
             STUDENT INFORMATION
        ================================================== -->

        <div class="student-information">


            <div class="student-info-item">

                <span>
                    Student
                </span>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $student['first_name']
                        . " "
                        . $student['last_name']
                    );

                    ?>

                </strong>

            </div>


            <div class="student-info-item">

                <span>
                    Matricule
                </span>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $student['matricule']
                    );

                    ?>

                </strong>

            </div>


            <div class="student-info-item">

                <span>
                    Class
                </span>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $student['class_name'] ?? 'N/A'
                    );

                    ?>

                </strong>

            </div>


            <div class="student-info-item">

                <span>
                    Academic Year
                </span>

                <strong>
                    2025 - 2026
                </strong>

            </div>


        </div>


        <!-- ==================================================
             GRADES TABLE
        ================================================== -->

        <div class="bulletin-table-wrapper">

            <table class="bulletin-table">

                <thead>

                    <tr>

                        <th>
                            Subject
                        </th>

                        <th>
                            Homework
                        </th>

                        <th>
                            Test
                        </th>

                        <th>
                            Exam
                        </th>

                        <th>
                            Average
                        </th>

                        <th>
                            Coef.
                        </th>

                        <th>
                            Weighted
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if ($gradesResult->num_rows > 0):

                    while (
                        $grade =
                        $gradesResult->fetch_assoc()
                    ):


                        // ==================================
                        // VALUES
                        // ==================================

                        $homework =
                            (float) $grade['homework'];

                        $test =
                            (float) $grade['test'];

                        $exam =
                            (float) $grade['exam'];

                        $coefficient =
                            (float) $grade['coefficient'];


                        // ==================================
                        // SIMPLE AVERAGE
                        // ==================================

                        $average = (
                            $homework
                            + $test
                            + $exam
                        ) / 3;


                        // ==================================
                        // WEIGHTED VALUE
                        // ==================================

                        $weighted =
                            $average * $coefficient;


                        // ==================================
                        // TOTALS
                        // ==================================

                        $totalWeighted += $weighted;

                        $totalCoefficients +=
                            $coefficient;

                        $totalSubjects++;

                ?>


                    <tr>


                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $grade['subject_name']
                                );

                                ?>

                            </strong>

                        </td>


                        <td>

                            <?php

                            echo number_format(
                                $homework,
                                2
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo number_format(
                                $test,
                                2
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo number_format(
                                $exam,
                                2
                            );

                            ?>

                        </td>


                        <td>

                            <span class="bulletin-average">

                                <?php

                                echo number_format(
                                    $average,
                                    2
                                );

                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo number_format(
                                $coefficient,
                                0
                            );

                            ?>

                        </td>


                        <td>

                            <strong>

                                <?php

                                echo number_format(
                                    $weighted,
                                    2
                                );

                                ?>

                            </strong>

                        </td>


                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <tr>

                        <td
                            colspan="7"
                            class="bulletin-empty"
                        >

                            No grades available for this student.

                        </td>

                    </tr>


                <?php

                endif;

                ?>


                </tbody>

            </table>

        </div>


        <!-- ==================================================
             RESULTS
        ================================================== -->

        <?php

        if ($totalCoefficients > 0) {

            $generalAverage =
                $totalWeighted
                / $totalCoefficients;

        } else {

            $generalAverage = 0;

        }

        ?>


        <div class="bulletin-results">


            <div class="result-box">

                <span>
                    Subjects
                </span>

                <strong>
                    <?php echo $totalSubjects; ?>
                </strong>

            </div>


            <div class="result-box">

                <span>
                    Total Coefficient
                </span>

                <strong>
                    <?php

                    echo number_format(
                        $totalCoefficients,
                        0
                    );

                    ?>
                </strong>

            </div>


            <div class="result-box result-main">

                <span>
                    General Average
                </span>

                <strong>

                    <?php

                    echo number_format(
                        $generalAverage,
                        2
                    );

                    ?>

                    / 20

                </strong>

            </div>


        </div>


        <!-- ==================================================
             DECISION
        ================================================== -->

        <div class="bulletin-decision">


            <?php

            if ($generalAverage >= 10) {

                echo "PASS";

                $decisionClass = "decision-pass";

            } else {

                echo "FAIL";

                $decisionClass = "decision-fail";

            }

            ?>

        </div>


        <!-- ==================================================
             SIGNATURES
        ================================================== -->

        <div class="bulletin-signatures">


            <div>

                <span>
                    Class Teacher
                </span>

                <div class="signature-line"></div>

            </div>


            <div>

                <span>
                    School Administration
                </span>

                <div class="signature-line"></div>

            </div>


        </div>


    </section>


</main>


<script src="/school_app/js/app.js"></script>


</body>

</html>