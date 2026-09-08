<?php
require_once "../config/auth.php";
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /school_app/students/index.php');
    exit;
}

verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid ID.');
}

$stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    header('Location: index.php');
    exit;
}

http_response_code(500);
exit('Unable to delete the record.');
