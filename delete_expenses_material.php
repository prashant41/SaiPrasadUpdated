<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM expenses_material WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_expense_material.php");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
