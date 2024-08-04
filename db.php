<?php

$servername = "localhost";
$username = "root";
$password = "root";
$database = "saiprasad";

$dsn = "mysql:host=$servername;dbname=$database";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    // No output here
} catch (PDOException $e) {
    // Log error instead of displaying it
    error_log($e->getMessage());
    die("Database connection failed. Please try again later.");
}

?>
