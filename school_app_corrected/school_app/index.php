<?php

require_once "config/database.php";
require_once "config/auth.php";



// ======================================================
// TOTAL STUDENTS
// ======================================================

$resultStudents = $conn->query(
    "SELECT COUNT(*) AS total
     FROM students"
);

$totalStudents = 0;

if ($resultStudents) {
    $totalStudents =
        $resultStudents->fetch_assoc()['total'];
}


// ======================================================
// TOTAL TEACHERS
// ======================================================

$resultTeachers = $conn->query(
    "SELECT COUNT(*) AS total
     FROM teachers"
);

$totalTeachers = 0;

if ($resultTeachers) {
    $totalTeachers =
        $resultTeachers->fetch_assoc()['total'];
}


// ======================================================
// TOTAL CLASSES
// ======================================================

$resultClasses = $conn->query(
    "SELECT COUNT(*) AS total
     FROM classes"
);

$totalClasses = 0;

if ($resultClasses) {
    $totalClasses =
        $resultClasses->fetch_assoc()['total'];
}


// ======================================================
// TOTAL SUBJECTS
// ======================================================

$resultSubjects = $conn->query(
    "SELECT COUNT(*) AS total
     FROM subjects"
);

$totalSubjects = 0;

if ($resultSubjects) {
    $totalSubjects =
        $resultSubjects->fetch_assoc()['total'];
}


// ======================================================
// TOTAL GRADES
// ======================================================

$resultGrades = $conn->query(
    "SELECT COUNT(*) AS total
     FROM grades"
);

$totalGrades = 0;

if ($resultGrades) {
    $totalGrades =
        $resultGrades->fetch_assoc()['total'];
}


// ======================================================
// STUDENTS PER CLASS
// ======================================================

$classOverview = $conn->query(
    "SELECT
        classes.id,
        classes.name,
        COUNT(students.id) AS student_count

     FROM classes

     LEFT JOIN students
        ON students.class = classes.name

     GROUP BY
        classes.id,
        classes.name

     ORDER BY classes.id ASC"
);


// ======================================================
// RECENT STUDENTS
// ======================================================

$recentStudents = $conn->query(
    "SELECT
        id,
        matricule,
        first_name,
        last_name,
        class,
        phone

     FROM students

     ORDER BY id DESC

     LIMIT 5"
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
        School Management System
    </title>

    <link
        rel="stylesheet"
        href="/school_app/css/style.css?v=3"
    >

</head>


<body>


<?php include "menu.php"; ?>


<!-- ==================================================
     TOP HEADER
=================================================== -->

<header class="top-header">

    <button class="top-menu-toggle" id="menuToggle">
        ☰
    </button>

    <div class="search-box">

        <span class="search-icon">🔍</span>

        <input
            type="text"
            placeholder="Search students, teachers, classes..."
        >

    </div>


    <div class="top-header-right">

        <button class="notification-btn">
            🔔
        </button>

        <div class="admin-profile">

            <div class="admin-avatar">
                A
            </div>

            <div class="admin-info">

                <strong>Administrator</strong>

                <span>School Admin</span>

            </div>

        </div>

    </div>

</header>

 <div class="header-actions">


        <!-- NOTIFICATIONS -->

        <button
            type="button"
            class="header-icon-button"
            title="Notifications"
        >

            🔔

            <span class="notification-badge">
                3
            </span>

        </button>



        <!-- PROFILE -->

        <div class="profile-container">


            <button
                type="button"
                class="header-profile"
                id="profileButton"
            >

                <div class="header-avatar">
                    A
                </div>


                <div class="header-user-info">

                    <strong>
                        Administrator
                    </strong>

                    <span>
                        School Admin
                    </span>

                </div>


                <span class="profile-arrow">
                    ▾
                </span>

            </button>



            <!-- PROFILE DROPDOWN -->

            <div
                class="profile-dropdown"
                id="profileDropdown"
            >


                <a href="#">

                    <span>
                        👤
                    </span>

                    <span>
                        My Profile
                    </span>

                </a>



                <a href="/school_app/settings/index.php">

                    <span>
                        ⚙️
                    </span>

                    <span>
                        Settings
                    </span>

                </a>



                <div class="dropdown-divider"></div>



                <a
                    href="#"
                    class="logout-link"
                >

                    <span>
                        🚪
                    </span>

                    <span>
                        Logout
                    </span>

                </a>


            </div>


        </div>


    </div>


</header>


<!-- ==================================================
     MAIN DASHBOARD
=================================================== -->

<main class="dashboard">


    <!-- ==================================================
         PAGE TITLE
    =================================================== -->

    <div class="page-header">

        <div>

            <h2>
                Overview
            </h2>

            <p>
                Here's what's happening in your school today.
            </p>

        </div>

    </div>


    <!-- ==================================================
         STATISTICS CARDS
    =================================================== -->

    <div class="dashboard-cards">


        <!-- STUDENTS -->

        <div class="dashboard-card">

            <div class="card-icon">
                👨‍🎓
            </div>

            <div class="card-content">

                <span class="card-title">
                    Total Students
                </span>

                <strong class="card-number">
                    <?php echo $totalStudents; ?>
                </strong>

                <span class="card-info">
                    Active students
                </span>

            </div>

        </div>


        <!-- TEACHERS -->

        <div class="dashboard-card">

            <div class="card-icon">
                👨‍🏫
            </div>

            <div class="card-content">

                <span class="card-title">
                    Teachers
                </span>

                <strong class="card-number">
                    <?php echo $totalTeachers; ?>
                </strong>

                <span class="card-info">
                    Active teachers
                </span>

            </div>

        </div>


        <!-- CLASSES -->

        <div class="dashboard-card">

            <div class="card-icon">
                🏫
            </div>

            <div class="card-content">

                <span class="card-title">
                    Classes
                </span>

                <strong class="card-number">
                    <?php echo $totalClasses; ?>
                </strong>

                <span class="card-info">
                    Active classes
                </span>

            </div>

        </div>


        <!-- SUBJECTS -->

        <div class="dashboard-card">

            <div class="card-icon">
                📚
            </div>

            <div class="card-content">

                <span class="card-title">
                    Subjects
                </span>

                <strong class="card-number">
                    <?php echo $totalSubjects; ?>
                </strong>

                <span class="card-info">
                    Active subjects
                </span>

            </div>

        </div>


    </div>


    <!-- ==================================================
         MAIN TWO COLUMNS
    =================================================== -->

    <div class="dashboard-main-grid">


        <!-- ==================================================
             STUDENTS PER CLASS
        =================================================== -->

        <section class="dashboard-panel students-class-panel">


            <div class="panel-header">

                <div>

                    <h2>
                        Students per Class
                    </h2>

                    <p>
                        Number of students in each class
                    </p>

                </div>


                <a
                    href="classes/index.php"
                    class="panel-link"
                >
                    View all
                </a>

            </div>


            <div class="class-chart">

                <?php

                if (
                    $classOverview &&
                    $classOverview->num_rows > 0
                ) {

                    while (
                        $class =
                        $classOverview->fetch_assoc()
                    ) {

                        $studentCount =
                            (int) $class['student_count'];

                        /*
                         * Maximum visual height:
                         * 150px
                         */

                        $barHeight =
                            $studentCount > 0
                            ? min(
                                150,
                                max(
                                    15,
                                    $studentCount * 5
                                )
                            )
                            : 5;

                ?>

                    <div class="chart-column">


                        <div class="chart-value">

                            <?php
                            echo $studentCount;
                            ?>

                        </div>


                        <div class="chart-bar-container">

                            <div
                                class="chart-bar"
                                style="
                                    height:
                                    <?php echo $barHeight; ?>px;
                                "
                            ></div>

                        </div>


                        <div class="chart-label">

                            <?php

                            echo htmlspecialchars(
                                $class['name']
                            );

                            ?>

                        </div>


                    </div>

                <?php

                    }

                } else {

                ?>

                    <p class="empty-message">
                        No classes found.
                    </p>

                <?php

                }

                ?>

            </div>


        </section>



        <!-- ==================================================
             RECENT ACTIVITIES
        =================================================== -->

        <section class="dashboard-panel activities-panel">


            <div class="panel-header">

                <div>

                    <h2>
                        Recent Activities
                    </h2>

                    <p>
                        Latest school activities
                    </p>

                </div>


                <a
                    href="#"
                    class="panel-link"
                >
                    View all
                </a>

            </div>


            <div class="activities-list">


                <!-- ACTIVITY 1 -->

                <div class="activity-item">

                    <div class="activity-icon">
                        👨‍🎓
                    </div>

                    <div class="activity-content">

                        <strong>
                            Student Management
                        </strong>

                        <span>
                            Student information updated
                        </span>

                        <small>
                            Recently
                        </small>

                    </div>

                </div>


                <!-- ACTIVITY 2 -->

                <div class="activity-item">

                    <div class="activity-icon">
                        📝
                    </div>

                    <div class="activity-content">

                        <strong>
                            Grade Management
                        </strong>

                        <span>
                            A student's grade was updated
                        </span>

                        <small>
                            Recently
                        </small>

                    </div>

                </div>


                <!-- ACTIVITY 3 -->

                <div class="activity-item">

                    <div class="activity-icon">
                        👨‍🏫
                    </div>

                    <div class="activity-content">

                        <strong>
                            Teacher Management
                        </strong>

                        <span>
                            Teacher information updated
                        </span>

                        <small>
                            Recently
                        </small>

                    </div>

                </div>


                <!-- ACTIVITY 4 -->

                <div class="activity-item">

                    <div class="activity-icon">
                        📚
                    </div>

                    <div class="activity-content">

                        <strong>
                            Subject Management
                        </strong>

                        <span>
                            Subject information updated
                        </span>

                        <small>
                            Recently
                        </small>

                    </div>

                </div>


            </div>


        </section>


    </div>


    <!-- ==================================================
         QUICK ACTIONS
    =================================================== -->

    <section class="dashboard-section quick-actions-section">


        <div class="section-header">

            <div>

                <h2>
                    Quick Actions
                </h2>

                <p>
                    Quickly access the main functions.
                </p>

            </div>

        </div>


        <div class="quick-actions">


            <a
                href="/school_app/students/add.php"
                class="quick-action"
            >
                <span>➕</span>
                Add Student
            </a>


            <a
                href="/school_app/grades/add.php"
                class="quick-action"
            >
                <span>📝</span>
                Add Grade
            </a>


            <a
                href="/school_app/students/index.php"
                class="quick-action"
            >
                <span>👨‍🎓</span>
                View Students
            </a>


            <a
                href="/school_app/grades/index.php"
                class="quick-action"
            >
                <span>📊</span>
                View Grades
            </a>


        </div>


    </section>


    <!-- ==================================================
         RECENT STUDENTS
    =================================================== -->

    <section class="dashboard-section recent-students-section">


        <div class="section-header">

            <div>

                <h2>
                    Recent Students
                </h2>

                <p>
                    Recently added students.
                </p>

            </div>


            <a
                href="/school_app/students/index.php"
                class="panel-link"
            >
                View all
            </a>

        </div>


        <div class="table-container">


            <table>


                <thead>

                    <tr>

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

                    </tr>

                </thead>


                <tbody>

                    <?php

                    if (
                        $recentStudents &&
                        $recentStudents->num_rows > 0
                    ) {

                        while (
                            $student =
                            $recentStudents->fetch_assoc()
                        ) {

                    ?>

                        <tr>

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
                                    $student['class']
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $student['phone']
                                );

                                ?>

                            </td>

                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td
                                colspan="5"
                                class="empty-table"
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


    </section>


</main>


<script
    src="/school_app/js/app.js?v=3"
    defer
></script>


</body>

</html>