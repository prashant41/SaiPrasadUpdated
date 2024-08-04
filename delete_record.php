<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $recordId = $_GET['id'];

    // Prepare SQL statement to delete the record
    $sql = "DELETE FROM records WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$recordId]);

    // Redirect to manage_records.php after deletion
    header("Location: manage_records.php");
    exit();
} else {
    echo "Record ID not specified.";
}
?>
