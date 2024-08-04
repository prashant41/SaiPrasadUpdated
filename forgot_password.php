<?php
include("db.php"); // Adjust as per your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $token = bin2hex(random_bytes(50)); // Generate a secure random token
    $reset_time = date("Y-m-d H:i:s");

    $sql = "UPDATE users SET reset_token = ?, reset_time = ? WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token, $reset_time, $email]);

    if ($stmt->rowCount() > 0) {
        // Send reset link to user's email
        $reset_link = "http://localhost/authentication/reset_password.php?token=$token";
        mail($email, "Password Reset Request", "Click the following link to reset your password: $reset_link");
        echo "A password reset link has been sent to your email.";
    } else {
        echo "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
    <button onclick="goBack()">Go Back</button>
    <h2>Forgot Password</h2>
    <form action="forgot_password.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Reset Password">
    </form>

</body>
</html>
