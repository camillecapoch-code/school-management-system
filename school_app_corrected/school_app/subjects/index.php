<?php

require_once "../config/database.php";
require_once "../config/auth.php";
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
        Subjects - School App
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>

<body>

<?php include "../menu.php"; ?>


<main class="dashboard">

    <div class="page-header">

        <div>

            <h1>
                Subjects
            </h1>

            <p>
                Manage school subjects.
            </p>

        </div>

    </div>


    <section class="dashboard-section">

        <h2>
            School Subjects
        </h2>


        <div class="quick-actions">

            <div class="quick-action">
                Mathematics
            </div>

            <div class="quick-action">
                French
            </div>

            <div class="quick-action">
                English
            </div>

            <div class="quick-action">
                Science
            </div>

            <div class="quick-action">
                History
            </div>

            <div class="quick-action">
                Geography
            </div>

        </div>

    </section>

</main>


<script src="../js/app.js"></script>

</body>

</html>