<link rel="stylesheet" href="/school_app/css/style.css">
<script src="/school_app/js/app.js" defer></script>
<?php

require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/config/database.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ======================================================
// GET ADMINISTRATOR
// ======================================================

$adminStmt = $conn->prepare(
    "SELECT full_name, email FROM admin WHERE id = ? LIMIT 1"
);
$adminId = (int)($_SESSION['admin_id'] ?? 0);
$adminStmt->bind_param("i", $adminId);
$adminStmt->execute();
$adminResult = $adminStmt->get_result();

$admin = $adminResult
    ? $adminResult->fetch_assoc()
    : null;


$adminName =
    $admin['full_name'] ?? 'Administrator';

$adminEmail =
    $admin['email'] ?? '';


$adminInitial =
    strtoupper(
        substr(
            $adminName,
            0,
            1
        )
    );

?>

<aside class="sidebar">

    <!-- LOGO -->

    <div class="sidebar-logo">

        <div class="sidebar-logo-icon">
            🏫
        </div>

        <div class="sidebar-logo-text">

            <strong>
                SCHOOL
            </strong>

            <span>
                MANAGEMENT
            </span>

        </div>

    </div>


    <!-- MAIN MENU -->

    <div class="sidebar-section-title">
        MAIN
    </div>


    <nav class="sidebar-menu">

        <a
            href="/school_app/index.php"
            class="sidebar-link active"
        >
            <span class="sidebar-icon">▣</span>
            <span>Dashboard</span>
        </a>


        <a
            href="/school_app/students/index.php"
            class="sidebar-link"
        >
            <span class="sidebar-icon">👨‍🎓</span>
            <span>Students</span>
        </a>


        <a
            href="/school_app/teachers/index.php"
            class="sidebar-link"
        >
            <span class="sidebar-icon">👨‍🏫</span>
            <span>Teachers</span>
        </a>


        <a
            href="/school_app/classes/index.php"
            class="sidebar-link"
        >
            <span class="sidebar-icon">🏫</span>
            <span>Classes</span>
        </a>


        <a
            href="/school_app/subjects/index.php"
            class="sidebar-link"
        >
            <span class="sidebar-icon">📚</span>
            <span>Subjects</span>
        </a>


        <a
            href="/school_app/grades/index.php"
            class="sidebar-link"
        >
            <span class="sidebar-icon">📝</span>
            <span>Grades</span>
        </a>


        <a
           href="/school_app/logout.php"
           class="sidebar-link logout-link"
        >
           <span class="sidebar-icon">🚪</span>
           <span>Logout</span>
        </a>

    </nav>


    <!-- MANAGEMENT -->

    <div class="sidebar-section-title">
        MANAGEMENT
    </div>


    <nav class="sidebar-menu">

        <a
            href="#"
            class="sidebar-link"
        >
            <span class="sidebar-icon">📊</span>
            <span>Reports</span>
        </a>


       <a
            href="/school_app/settings/index.php"
            class="sidebar-link"
        >
            <span class="sidebar-icon">⚙</span>
            <span>Settings</span>
        </a>

    </nav>

<!-- ==================================================
     USER
================================================== -->

<div class="sidebar-user">

<a
    href="/school_app/settings/profile.php"
    class="sidebar-user"
>

    <div class="user-avatar">

        <?php
        echo htmlspecialchars($adminInitial);
        ?>

    </div>


    <div class="user-info">

        <strong><?php echo htmlspecialchars($adminName); ?></strong>

        <span><?php echo htmlspecialchars($adminEmail); ?></span>

    </div>

</div>
</a>

</div>

</aside>