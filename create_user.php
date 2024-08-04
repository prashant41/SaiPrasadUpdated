<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert the user into the database
        $sql = "INSERT INTO users (username, email, password, phone, role) VALUES (:username, :email, :password, :phone, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':phone' => $phone,
            ':role' => $role
        ]);

        // Redirect to manage users page
        header("Location: manage_users.php");
        exit();
    } catch (PDOException $e) {
        // Handle query error
        echo "Error: " . $e->getMessage();
    }
}
?>
