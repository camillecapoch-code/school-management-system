<?php

require_once "../config/database.php";
require_once "../config/auth.php";


// ======================================================
// VÉRIFIER LE CLASS_ID
// ======================================================

if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {

    header("Location: /school_app/classes/index.php");

    exit;
}


$class_id = (int) $_GET['class_id'];


// ======================================================
// RÉCUPÉRER LA CLASSE
// ======================================================

$classStmt = $conn->prepare(
    "SELECT id, name
     FROM classes
     WHERE id = ?"
);

$classStmt->bind_param("i", $class_id);

$classStmt->execute();

$classResult = $classStmt->get_result();

$class = $classResult->fetch_assoc();


if (!$class) {

    header("Location: /school_app/classes/index.php");

    exit;
}


// ======================================================
// RÉCUPÉRER LES ÉLÈVES DE LA CLASSE
// ======================================================

$studentStmt = $conn->prepare(
    "SELECT
        id,
        matricule,
        first_name,
        last_name,
        phone

     FROM students

     WHERE class_id = ?

     ORDER BY last_name ASC, first_name ASC"
);

$studentStmt->bind_param("i", $class_id);

$studentStmt->execute();

$students = $studentStmt->get_result();

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

        <?php echo htmlspecialchars($class['name']); ?>

        - Students

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
    =================================================== -->

    <div class="page-header">

        <div>

            <h1>

                Class

                <?php

                echo htmlspecialchars(
                    $class['name']
                );

                ?>

            </h1>


            <p>

                Students in this class.

            </p>

        </div>


        <a
            href="/school_app/classes/index.php"
            class="quick-action"
        >

            ← Back to Classes

        </a>

    </div>


    <!-- ==================================================
         STUDENTS
    =================================================== -->

    <section class="dashboard-section">


        <div class="section-header">

            <h2>

                Students

                (<?php echo $students->num_rows; ?>)

            </h2>


            <a
                href="/school_app/students/add.php"
                class="quick-action"
            >

                ➕ Add Student

            </a>

        </div>


        <div class="table-container">


            <table>

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Matricule
                        </th>

                        <th>
                            First Name
                        </th>

                        <th>
                            Last Name
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if ($students->num_rows > 0): ?>


                    <?php

                    $number = 1;

                    while (
                        $student =
                        $students->fetch_assoc()
                    ):

                    ?>


                        <tr>

                            <td>

                                <?php

                                echo $number++;

                                ?>

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
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $student['last_name']
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $student['phone'] ?? ''
                                );

                                ?>

                            </td>


                            <td>


                                <a
                                    href="/school_app/students/edit.php?id=<?php echo $student['id']; ?>"
                                    class="action-edit"
                                >

                                    Edit

                                </a>


                                <a
                                    href="/school_app/grades/student-grades.php?student_id=<?php echo $student['id']; ?>"
                                    class="action-view"
                                >

                                    Bulletin

                                </a>


                            </td>

                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center;"
                        >

                            No students in this class.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>


        </div>

    </section>


</main>


<script src="/school_app/js/app.js"></script>


</body>

</html>