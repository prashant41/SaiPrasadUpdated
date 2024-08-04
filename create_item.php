<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];

    try {
        $sql = "INSERT INTO items (name, price) VALUES (:name, :price)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'price' => $price]);
        header("Location: manage_items.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: manage_items.php");
    exit();
}
?>
