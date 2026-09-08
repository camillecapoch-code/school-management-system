<?php

require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../includes/grade_functions.php";


// ======================================================
// GET CLASS ID
// ======================================================

if (!isset($_GET['class_id'])) {

    die("Class ID is missing.");

}

$class_id = (int) $_GET['class_id'];


// ======================================================
// GET CLASS INFORMATION
// ======================================================

$sqlClass = "SELECT
                 id,
                 name

             FROM classes

             WHERE id = ?";

$stmtClass = $conn->prepare($sqlClass);

$stmtClass->bind_param(
    "i",
    $class_id
);

$stmtClass->execute();

$classResult = $stmtClass->get_result();


if ($classResult->num_rows == 0) {

    die("Class not found.");

}

$class = $classResult->fetch_assoc();


// ======================================================
// GET STUDENTS
// ======================================================

$sqlStudents = "SELECT
                    id,
                    matricule,
                    first_name,
                    last_name

                FROM students

                WHERE class_id = ?

                ORDER BY last_name ASC";


$stmtStudents = $conn->prepare($sqlStudents);

$stmtStudents->bind_param(
    "i",
    $class_id
);

$stmtStudents->execute();

$studentsResult = $stmtStudents->get_result();


// ======================================================
// CREATE RANKING ARRAY
// ======================================================

$ranking = [];


while (
    $student =
    $studentsResult->fetch_assoc()
) {

    $student_id = $student['id'];


    // ==================================================
    // GET STUDENT GRADES
    // ==================================================

    $sqlGrades = "SELECT

                      grades.homework,
                      grades.test,
                      grades.exam,

                      subjects.coefficient

                  FROM grades

                  JOIN subjects

                      ON grades.subject_id =
                         subjects.id

                  WHERE grades.student_id = ?";


    $stmtGrades =
        $conn->prepare($sqlGrades);


    $stmtGrades->bind_param(
        "i",
        $student_id
    );


    $stmtGrades->execute();


    $gradesResult =
        $stmtGrades->get_result();


    // ==================================================
    // TOTALS
    // ==================================================

    $totalPoints = 0;

    $totalCoefficients = 0;


    while (
        $grade =
        $gradesResult->fetch_assoc()
    ) {


        $average = calculateAverage(

            (float) $grade['homework'],

            (float) $grade['test'],

            (float) $grade['exam']

        );


        $coefficient =
            (float) $grade['coefficient'];


        $points =
            $average * $coefficient;


        $totalPoints += $points;

        $totalCoefficients += $coefficient;

    }


    // ==================================================
    // GENERAL AVERAGE
    // ==================================================

    if ($totalCoefficients > 0) {

        $generalAverage =
            $totalPoints /
            $totalCoefficients;

    } else {

        $generalAverage = 0;

    }


    // ==================================================
    // SAVE STUDENT RESULT
    // ==================================================

    $ranking[] = [

        'id' =>
            $student['id'],

        'matricule' =>
            $student['matricule'],

        'first_name' =>
            $student['first_name'],

        'last_name' =>
            $student['last_name'],

        'total_points' =>
            $totalPoints,

        'total_coefficients' =>
            $totalCoefficients,

        'average' =>
            $generalAverage

    ];

}


// ======================================================
// SORT STUDENTS
// ======================================================

usort(

    $ranking,

    function ($a, $b) {

        return
            $b['average']
            <=>
            $a['average'];

    }

);

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
        Class Ranking
    </title>

</head>


<body>


<?php include "../menu.php"; ?>


<h1>
    Class Ranking
</h1>


<h2>

    <?php

    echo htmlspecialchars(
        $class['name']
    );

    ?>

</h2>


<br>


<table border="1">

    <thead>

        <tr>

            <th>Rank</th>

            <th>Matricule</th>

            <th>Student</th>

            <th>Total Coefficients</th>

            <th>Total Points</th>

            <th>Average</th>

            <th>Bulletin</th>

        </tr>

    </thead>


    <tbody>


        <?php

        $rank = 1;


        foreach ($ranking as $student) {

        ?>


            <tr>


                <td>

                    <strong>

                        <?php

                        echo $rank;

                        ?>

                    </strong>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $student['matricule']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo htmlspecialchars(
                        $student['first_name']
                        . " "
                        . $student['last_name']
                    );

                    ?>

                </td>


                <td>

                    <?php

                    echo $student[
                        'total_coefficients'
                    ];

                    ?>

                </td>


                <td>

                    <?php

                    echo number_format(
                        $student['total_points'],
                        2
                    );

                    ?>

                </td>


                <td>

                    <strong>

                        <?php

                        echo number_format(
                            $student['average'],
                            2
                        );

                        ?>

                    </strong>

                    /20

                </td>


                <td>

                    <a
                        href="student-grades.php?student_id=<?php echo $student['id']; ?>"
                    >

                        View Bulletin

                    </a>

                </td>


            </tr>


        <?php

            $rank++;

        }

        ?>


    </tbody>

</table>


<br>


<a href="../index.php">

    Back to Dashboard

</a>


</body>

</html>