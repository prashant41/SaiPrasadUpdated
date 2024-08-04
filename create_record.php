<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $utilityName = filter_input(INPUT_POST, 'utility_name', FILTER_SANITIZE_STRING);
    $month = filter_input(INPUT_POST, 'month', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $datePaid = filter_input(INPUT_POST, 'date_paid', FILTER_SANITIZE_STRING);

    // Check if the inputs are valid
    if ($utilityName && $month && $amount !== false && $datePaid) {
        try {
            // Prepare SQL statement to insert a new record
            $sql = "INSERT INTO records (utility_name, month, amount, date_paid) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters and execute the statement
            $stmt->execute([$utilityName, $month, $amount, $datePaid]);

            // Redirect to manage_records.php after creation
            header("Location: manage_records.php");
            exit();
        } catch (PDOException $e) {
            // Handle SQL error
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid input. Please check your data and try again.";
    }
}
?>
