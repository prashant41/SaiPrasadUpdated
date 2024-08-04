<?php
session_start();
include("db.php"); // Adjust as per your database connection file

// Check if user is logged in as admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php"); // Redirect unauthorized users
    exit();
}

// Check if order ID is provided via GET request
if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Mark the order as not deleted
    $sql = "UPDATE orders SET deleted = 0 WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$orderId])) {
        header("Location: display_orders.php?message=Order+restored+successfully");
        exit();
    } else {
        echo "Error restoring order.";
    }
} else {
    echo "Order ID not specified.";
}
?>
