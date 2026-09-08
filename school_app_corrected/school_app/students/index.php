<?php

require_once "../config/database.php";
require_once "../config/auth.php";

// ======================================================
// GET STUDENTS
// ======================================================

$sql = "SELECT
            id,
            matricule,
            first_name,
            last_name,
            class,
            phone

        FROM students

        ORDER BY id DESC";


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
        Students - School App
    </title>

</head>


<body>


<?php include "../menu.php"; ?>


<main class="dashboard students-page">


    <!-- ==================================================
         PAGE HEADER
    =================================================== -->

    <div class="page-header">

        <div>

            <h1>
                Students
            </h1>

            <p>
                Manage all students registered in the school.
            </p>

        </div>


        <a
            href="/school_app/students/add.php"
            class="btn"
        >

            + Add Student

        </a>

    </div>


    <!-- ==================================================
         SEARCH
    =================================================== -->

    <div class="search-box">

        <input
            type="text"
            id="studentSearch"
            placeholder="Search student..."
        >

    </div>


    <!-- ==================================================
         STUDENTS TABLE
    =================================================== -->

    <div class="table-container">

        <table id="studentsTable">


            <thead>

                <tr>

                    <th>
                        ID
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
                        Class
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


                <?php

                if ($result->num_rows > 0) {


                    while (
                        $row =
                        $result->fetch_assoc()
                    ) {

                ?>


                    <tr>


                        <td>

                            <?php

                            echo $row['id'];

                            ?>

                        </td>


                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row['matricule']
                                );

                                ?>

                            </strong>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['first_name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['last_name']
                            );

                            ?>

                        </td>


                        <td>

                            <span class="class-badge">

                                <?php

                                echo htmlspecialchars(
                                    $row['class']
                                );

                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['phone']
                            );

                            ?>

                        </td>


                        <td>


                            <a
                                href="edit.php?id=<?php echo $row['id']; ?>"
                                class="btn btn-edit"
                            >

                                Edit

                            </a>


                            <form method="POST" action="delete.php" class="inline-delete-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                    <button type="submit" class="btn btn-delete" onclick="return confirmDelete();">
                                        Delete
                                    </button>
                                </form>


                            <a
                                href="/school_app/grades/student-grades.php?student_id=<?php echo $row['id']; ?>"
                                class="btn btn-view"
                            >

                                Bulletin

                            </a>


                        </td>


                    </tr>


                <?php

                    }


                } else {

                ?>


                    <tr>

                        <td
                            colspan="7"
                            class="empty-message"
                        >

                            No students found.

                        </td>

                    </tr>


                <?php

                }

                ?>


            </tbody>


        </table>

    </div>


</main>


</body>

</html>