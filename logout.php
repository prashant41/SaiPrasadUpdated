<?php
include("db.php");
session_start();

// Check if session is set
if (isset($_SESSION["username"])) {
    // Unset all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
} else {
    // If session is not set, redirect to login page directly
    header("Location: login.php");
    exit();
}
?>
