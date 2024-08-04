<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $counter_cash = $_POST["counter_cash"];
    $delivery_boy_cash = $_POST["delivery_boy_cash"];
    $book_cash = $_POST["book_cash"];
    $date = $_POST["date"];

    try {
        $sql = "INSERT INTO revenue (counter_cash, delivery_boy_cash, book_cash, date) VALUES (:counter_cash, :delivery_boy_cash, :book_cash, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':counter_cash' => $counter_cash,
            ':delivery_boy_cash' => $delivery_boy_cash,
            ':book_cash' => $book_cash,
            ':date' => $date
        ]);
        header("Location: manage_cash.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: manage_cash.php");
    exit();
}
?>
