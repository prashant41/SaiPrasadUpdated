<?php
session_start();
include("db.php"); // Ensure this file sets up $pdo correctly

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_name = trim($_POST['shop_name']);

    if (!empty($shop_name)) {
        try {
            $sql = "INSERT INTO shop_names (name) VALUES (:shop_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':shop_name', $shop_name);
            $stmt->execute();
            header("Location: manage_shop_names.php?success=1");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Shop name cannot be empty.";
    }
} else {
    header("Location: manage_shop_names.php");
    exit();
}
?>
