<?php

require_once "../config/database.php";
require_once "../config/auth.php";
$classQuery = "SELECT * FROM classes ORDER BY id ASC";
$classResult = $conn->query($classQuery);
// Vérifier que l'ID existe
if (!isset($_GET["id"])) {
    die("Student ID is missing.");
}

$id = $_GET["id"];

// Récupérer les informations de l'élève
$sql = "SELECT * FROM students WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

$stmt->close();


// Modifier l'élève
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verify_csrf();

    $matricule = $_POST["matricule"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $class_id = (int) ($_POST["class_id"] ?? 0);
    $phone = trim($_POST["phone"] ?? '');

    $classLookup = $conn->prepare("SELECT name FROM classes WHERE id = ? LIMIT 1");
    $classLookup->bind_param("i", $class_id);
    $classLookup->execute();
    $classData = $classLookup->get_result()->fetch_assoc();
    $classLookup->close();

    if (!$classData) {
        $error = "Invalid class selected.";
    } else {
    $className = $classData['name'];
    $sql = "UPDATE students SET matricule = ?, first_name = ?, last_name = ?, class = ?, class_id = ?, phone = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssisi", $matricule, $first_name, $last_name, $className, $class_id, $phone, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Student</title>
</head>

<body>

    <h1>Edit Student</h1>

    <form method="POST">
            <?php echo csrf_field(); ?>

        <label>Matricule:</label>
        <br>
        <input
            type="text"
            name="matricule"
            value="<?php echo htmlspecialchars($student['matricule']); ?>"
            required
        >

        <br><br>

        <label>First Name:</label>
        <br>
        <input
            type="text"
            name="first_name"
            value="<?php echo htmlspecialchars($student['first_name']); ?>"
            required
        >

        <br><br>

        <label>Last Name:</label>
        <br>
        <input
            type="text"
            name="last_name"
            value="<?php echo htmlspecialchars($student['last_name']); ?>"
            required
        >

        <br><br>

        <label>Class:</label>
        <br>

    <select name="class_id" required>

        <option value="">Select class</option>

        <?php while ($class = $classResult->fetch_assoc()) { ?>

        <option
            value="<?php echo $class['id']; ?>"
            <?php
            if ($class['id'] == $student['class_id']) {
                echo "selected";
            }
            ?>
        >
            <?php echo htmlspecialchars($class['name']); ?>
        </option>

        <?php } ?>

    </select>

        <br><br>

        <label>Phone:</label>
        <br>
        <input
            type="text"
            name="phone"
            value="<?php echo htmlspecialchars($student['phone']); ?>"
        >

        <br><br>

        <button type="submit">
            Update Student
        </button>

    </form>

</body>

</html>