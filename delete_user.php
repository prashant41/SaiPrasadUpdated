<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];

    try {
        // Delete the user from the database
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Redirect to manage users page
        header("Location: manage_users.php");
        exit();
    } catch (PDOException $e) {
        // Handle query error
        echo "Error: " . $e->getMessage();
    }
}
?>
