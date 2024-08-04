<?php
include("db.php"); // Adjust as per your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $new_password = $_POST["new_password"];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_time = NULL WHERE reset_token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hashed_password, $token]);

    if ($stmt->rowCount() > 0) {
        echo "Password reset successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Invalid or expired token.";
    }
} else {
    $token = $_GET["token"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form action="reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>
        
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
