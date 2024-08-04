<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Move the order to the backup table
        $sql = "INSERT INTO orders_backup (id, order_date, item_name, quantity, amount, shop_name, delivery_boy_name, admin_name, error_status, created_at)
                SELECT id, order_date, item_name, quantity, amount, shop_name, delivery_boy_name, admin_name, error_status, created_at
                FROM orders WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);

        // Delete the order from the main table
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);

        // Commit the transaction
        $pdo->commit();

        header("Location: display_orders.php?message=Order+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $pdo->rollBack();
        echo "Error deleting order: " . $e->getMessage();
    }
} else {
    echo "Order ID not specified.";
}
?>
