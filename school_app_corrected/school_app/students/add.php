<?php

require_once "../config/database.php";
require_once "../config/auth.php";


// ======================================================
// RÉCUPÉRER LES CLASSES
// ======================================================

$classes = $conn->query(
    "SELECT id, name
     FROM classes
     ORDER BY id ASC"
);


// ======================================================
// AJOUTER L'ÉLÈVE
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verify_csrf();

    $matricule  = trim($_POST["matricule"]);
    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $class_id   = (int) $_POST["class_id"];
    $phone      = trim($_POST["phone"]);

    $sql = "INSERT INTO students
            (
                matricule,
                first_name,
                last_name,
                class,
                class_id,
                phone
            )
            VALUES (?, ?, ?, ?, ?, ?)";


    /*
     * Nous récupérons le nom de la classe
     * correspondant au class_id.
     */

    $classStmt = $conn->prepare(
        "SELECT name
         FROM classes
         WHERE id = ?"
    );

    $classStmt->bind_param("i", $class_id);

    $classStmt->execute();

    $classResult = $classStmt->get_result();

    $classData = $classResult->fetch_assoc();


    if (!$classData) {

        $error = "Invalid class selected.";

    } else {

        $class_name = $classData["name"];


        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssis",
            $matricule,
            $first_name,
            $last_name,
            $class_name,
            $class_id,
            $phone
        );


    if ($stmt->execute()) {

    // ==================================================
    // ID DU NOUVEL ÉLÈVE
    // ==================================================

    $student_id = $conn->insert_id;


    // ==================================================
    // RÉCUPÉRER TOUTES LES MATIÈRES
    // ==================================================

    $subjectsResult = $conn->query(
        "SELECT id FROM subjects ORDER BY id ASC"
    );


    // ==================================================
    // PRÉPARER UNE LIGNE DE NOTE POUR CHAQUE MATIÈRE
    // ==================================================

    $gradeStmt = $conn->prepare(
        "INSERT IGNORE INTO grades
        (
            student_id,
            subject_id,
            grade,
            homework,
            test,
            exam
        )
        VALUES (?, ?, 0, 0, 0, 0)"
    );


    while ($subject = $subjectsResult->fetch_assoc()) {

        $subject_id = (int) $subject["id"];

        $gradeStmt->bind_param(
            "ii",
            $student_id,
            $subject_id
        );

        $gradeStmt->execute();
    }


    // ==================================================
    // RETOUR À LA LISTE DES ÉLÈVES
    // ==================================================

    header(
        "Location: /school_app/students/index.php?success=student_added"
    );

    exit;

} else {

    $error = "Unable to add student.";

}

}
}

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
        Add Student - School App
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
                Add Student
            </h1>

            <p>
                Add a new student to the school.
            </p>

        </div>

    </div>


    <?php if (isset($error)): ?>

        <div class="alert alert-error">

            <?php echo $error; ?>

        </div>

    <?php endif; ?>


    <section class="dashboard-section">


        <form
            method="POST"
            class="student-form"
        >
            <?php echo csrf_field(); ?>


            <!-- MATRICULE -->

            <div class="form-group">

                <label for="matricule">
                    Matricule
                </label>

                <input
                    type="text"
                    id="matricule"
                    name="matricule"
                    placeholder="Example: CI002"
                    required
                >

            </div>


            <!-- FIRST NAME -->

            <div class="form-group">

                <label for="first_name">
                    First Name
                </label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    placeholder="First name"
                    required
                >

            </div>


            <!-- LAST NAME -->

            <div class="form-group">

                <label for="last_name">
                    Last Name
                </label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    placeholder="Last name"
                    required
                >

            </div>


            <!-- CLASS -->

            <div class="form-group">


                <div class="form-group">

                  <label for="class_id">
                     Class
                  </label>

                    <select
                       id="class_id"
                       name="class_id"
                       required
                    >

                    <option value="">
                       Select a class
                    </option>


                      <?php while ($class = $classes->fetch_assoc()): ?>

                    <option
                         value="<?php echo $class['id']; ?>"
                    >

                      <?php echo htmlspecialchars($class['name']); ?>

                    </option>

                    <?php endwhile; ?>

                  </select>

                </div>
            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    placeholder="Phone number"
                >

            </div>


            <!-- BUTTONS -->

            <div class="form-actions">

                <a
                    href="/school_app/students/index.php"
                    class="btn"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="btn"
                >
                    Save Student
                </button>

            </div>


        </form>

    </section>

</main>


<script src="../js/app.js"></script>

</body>

</html>