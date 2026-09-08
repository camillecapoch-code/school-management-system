<?php

require_once "../config/database.php";
require_once "../config/auth.php";

// ======================================================
// GET GRADES
// ======================================================

$sql = "SELECT
            grades.id,
            grades.homework,
            grades.test,
            grades.exam,

            students.id AS student_id,
            students.matricule,
            students.first_name,
            students.last_name,

            subjects.name AS subject_name,
            subjects.coefficient

        FROM grades

        INNER JOIN students
            ON grades.student_id = students.id

        INNER JOIN subjects
            ON grades.subject_id = subjects.id

        ORDER BY grades.id DESC";

$result = $conn->query($sql);

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
        Grades - School Management System
    </title>

    <!-- MAIN CSS -->
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

<main class="grades-page">


    <!-- ==================================================
         ALERTS
    ================================================== -->

    <?php if (isset($_GET['success'])): ?>

        <div class="grades-alert grades-alert-success">

            <?php

            if ($_GET['success'] === 'grade_added') {

                echo "Grade added successfully.";

            } elseif (
                $_GET['success'] === 'grade_updated'
            ) {

                echo "Grade updated successfully.";

            } elseif (
                $_GET['success'] === 'grade_deleted'
            ) {

                echo "Grade deleted successfully.";

            }

            ?>

        </div>

    <?php endif; ?>


    <!-- ==================================================
         HEADER
    ================================================== -->

    <div class="grades-header">

        <div>

            <h1>
                Grades
            </h1>

            <p>
                Manage homework, test and exam grades.
            </p>

        </div>


        <a
            href="/school_app/grades/add.php"
            class="grades-add-button"
        >

            <span>+</span>

            Add Grade

        </a>

    </div>


    <!-- ==================================================
         SEARCH BAR
    ================================================== -->

    <div class="grades-toolbar">

        <div class="grades-search">

            <span class="grades-search-icon">
                🔎
            </span>

            <input
                type="text"
                id="gradeSearch"
                placeholder="Search student or subject..."
                autocomplete="off"
            >

        </div>

    </div>


    <!-- ==================================================
         TABLE CARD
    ================================================== -->

    <div class="grades-table-card">


        <div class="grades-table-wrapper">

            <table
                class="grades-table"
                id="gradesTable"
            >


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Matricule
                        </th>

                        <th>
                            Student
                        </th>

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
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $result &&
                    $result->num_rows > 0
                ):

                    while (
                        $row = $result->fetch_assoc()
                    ):

                        // =================================
                        // CALCULATE AVERAGE
                        // =================================

                        $homework = (float) $row['homework'];

                        $test = (float) $row['test'];

                        $exam = (float) $row['exam'];

                        $average = (
                            $homework
                            + $test
                            + $exam
                        ) / 3;

                ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php
                            echo (int) $row['id'];
                            ?>

                        </td>


                        <!-- MATRICULE -->

                        <td>

                            <span class="grades-matricule">

                                <?php

                                echo htmlspecialchars(
                                    $row['matricule']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- STUDENT -->

                        <td>

                            <div class="grades-student">

                                <div class="grades-student-avatar">

                                    <?php

                                    echo strtoupper(
                                        substr(
                                            $row['first_name'],
                                            0,
                                            1
                                        )
                                    );

                                    ?>

                                </div>


                                <div>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $row['first_name']
                                            . " "
                                            . $row['last_name']
                                        );

                                        ?>

                                    </strong>

                                </div>

                            </div>

                        </td>


                        <!-- SUBJECT -->

                        <td>

                            <span class="grades-subject">

                                <?php

                                echo htmlspecialchars(
                                    $row['subject_name']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- HOMEWORK -->

                        <td>

                            <span class="grade-value">

                                <?php

                                echo number_format(
                                    $homework,
                                    2
                                );

                                ?>

                            </span>

                        </td>


                        <!-- TEST -->

                        <td>

                            <span class="grade-value">

                                <?php

                                echo number_format(
                                    $test,
                                    2
                                );

                                ?>

                            </span>

                        </td>


                        <!-- EXAM -->

                        <td>

                            <span class="grade-value">

                                <?php

                                echo number_format(
                                    $exam,
                                    2
                                );

                                ?>

                            </span>

                        </td>


                        <!-- AVERAGE -->

                        <td>

                            <span
                                class="grade-average
                                <?php

                                if ($average >= 10) {

                                    echo 'average-success';

                                } else {

                                    echo 'average-danger';

                                }

                                ?>"
                            >

                                <?php

                                echo number_format(
                                    $average,
                                    2
                                );

                                ?>

                            </span>

                        </td>


                        <!-- COEFFICIENT -->

                        <td>

                            <span class="grade-coefficient">

                                <?php

                                echo htmlspecialchars(
                                    $row['coefficient']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- ACTIONS -->

                        <td>

                            <div class="grades-actions">


                                <a
                                    href="edit.php?id=<?php echo (int) $row['id']; ?>"
                                    class="grade-action grade-edit"
                                >
                                    Edit
                                </a>


                                <form method="POST" action="delete.php" class="inline-delete-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <button type="submit" class="grade-action grade-delete" onclick="return confirmDelete();">
                                        Delete
                                    </button>
                                </form>


                                <a
                                    href="student-grades.php?student_id=<?php echo (int) $row['student_id']; ?>"
                                    class="grade-action grade-bulletin"
                                >
                                    Bulletin
                                </a>


                            </div>

                        </td>


                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <tr>

                        <td
                            colspan="10"
                            class="grades-empty"
                        >

                            No grades found.

                        </td>

                    </tr>


                <?php

                endif;

                ?>


                </tbody>

            </table>

        </div>

    </div>


</main>


<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script src="/school_app/js/app.js"></script>


<script>

// ======================================================
// SEARCH GRADES
// ======================================================

const gradeSearch =
    document.getElementById("gradeSearch");

const gradesTable =
    document.getElementById("gradesTable");


if (gradeSearch && gradesTable) {

    gradeSearch.addEventListener(
        "input",
        function () {

            const search =
                this.value.toLowerCase().trim();

            const rows =
                gradesTable
                .querySelectorAll("tbody tr");


            rows.forEach(function (row) {

                const text =
                    row.textContent.toLowerCase();

                if (text.includes(search)) {

                    row.style.display = "";

                } else {

                    row.style.display = "none";

                }

            });

        }
    );

}


// ======================================================
// DELETE CONFIRMATION
// ======================================================

function confirmDelete() {

    return confirm(
        "Are you sure you want to delete this grade?"
    );

}

</script>


</body>

</html>