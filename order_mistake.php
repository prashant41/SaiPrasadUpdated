<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $username = $_SESSION["username"];
    
    // Fetch order details based on order_id
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Send notification to admin
        $admin_message = "Mistake reported by $username for order ID: $order_id. Please review.";
        
        // Insert notification into notifications table
        $stmt = $pdo->prepare("INSERT INTO notifications (message) VALUES (:message)");
        $stmt->execute(['message' => $admin_message]);

        // Respond to AJAX request
        echo json_encode(array("status" => "success"));
    } else {
        // Handle error if order not found
        echo json_encode(array("status" => "error", "message" => "Order not found."));
    }
} else {
    // Handle invalid request
    echo json_encode(array("status" => "error", "message" => "Invalid request."));
}
?>
