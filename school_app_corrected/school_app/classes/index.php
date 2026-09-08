<?php

require_once "../config/database.php";
require_once "../config/auth.php";

// ======================================================
// RÉCUPÉRER LES CLASSES AVEC LE NOMBRE D'ÉLÈVES
// ======================================================

$sql = "
    SELECT
        classes.id,
        classes.name,
        COUNT(students.id) AS student_count

    FROM classes

    LEFT JOIN students
        ON students.class_id = classes.id

    GROUP BY
        classes.id,
        classes.name

    ORDER BY classes.id ASC
";


$classes = $conn->query($sql);

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
        Classes - School App
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
                Classes
            </h1>

            <p>
                Manage school classes and students.
            </p>

        </div>

    </div>


    <!-- ==================================================
         CLASSES
    =================================================== -->

    <section class="dashboard-section">

        <div class="section-header">

            <h2>
                School Classes
            </h2>

        </div>


        <div class="classes-grid">


            <?php if ($classes && $classes->num_rows > 0): ?>


                <?php while ($class = $classes->fetch_assoc()): ?>


                    <a
                        href="students.php?class_id=<?php echo $class['id']; ?>"
                        class="class-card"
                    >

                        <div class="class-icon">
                            🏫
                        </div>


                        <div class="class-info">

                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $class['name']
                                );

                                ?>

                            </h3>


                            <p>

                                <?php

                                echo $class['student_count'];

                                ?>

                                student<?php

                                echo (
                                    $class['student_count'] != 1
                                )
                                ? 's'
                                : '';

                                ?>

                            </p>

                        </div>


                        <div class="class-arrow">

                            →

                        </div>

                    </a>


                <?php endwhile; ?>


            <?php else: ?>


                <p>
                    No classes found.
                </p>


            <?php endif; ?>


        </div>

    </section>


</main>


<script src="/school_app/js/app.js"></script>

</body>

</html>