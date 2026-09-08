<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ======================================================
// GET TEACHERS
// ======================================================

$teachers = $conn->query(
    "SELECT
        id,
        first_name,
        last_name,
        phone,
        email,
        created_at
     FROM teachers
     ORDER BY id DESC"
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
        Teachers - School Management System
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
                Teachers
            </h1>

            <p>
                Manage teachers in your school.
            </p>

        </div>


        <a
            href="add.php"
            class="primary-button"
        >
            + Add Teacher
        </a>

    </div>


    <!-- ==================================================
         TEACHERS TABLE
    ================================================== -->

    <section class="dashboard-section">


        <div class="section-header">

            <h2>
                All Teachers
            </h2>

            <span class="table-count">

                <?php

                echo $teachers
                    ? $teachers->num_rows
                    : 0;

                ?>

                teachers

            </span>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            #
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
                            Email
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php

                if (
                    $teachers &&
                    $teachers->num_rows > 0
                ) {

                    $number = 1;


                    while (
                        $teacher =
                        $teachers->fetch_assoc()
                    ) {

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
                                $teacher['first_name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $teacher['last_name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $teacher['phone'] ?? ''
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $teacher['email'] ?? ''
                            );

                            ?>

                        </td>


                        <td>

                            <div class="table-actions">

                                <a
                                    href="edit.php?id=<?php echo (int) $teacher['id']; ?>"
                                    class="action-edit"
                                >
                                    Edit
                                </a>


                                <form method="POST" action="delete.php" class="inline-delete-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $teacher['id']; ?>">
                                    <button type="submit" class="action-delete" onclick="return confirmDelete();">
                                        Delete
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center;"
                        >

                            No teachers found.

                        </td>

                    </tr>

                <?php

                }

                ?>

                </tbody>

            </table>

        </div>

    </section>


</main>


<script
    src="/school_app/js/app.js"
    defer
></script>


</body>

</html>