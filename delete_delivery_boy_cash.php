<?php
include("db.php");
session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_record'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM delivery_boy_cash WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: delivery_boy_data.php"); // Redirect to avoid re-submission
    exit();
}
?>
