<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $user["role"];

        switch ($user["role"]) {
            case "super_admin":
                header("Location:super_admin_dashboard.php"); // Redirect to super admin dashboard
                break;
            case "admin":
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                break;
            case "client":
                header("Location: client_dashboard.php"); // Redirect to client dashboard
                break;
            default:
                $loginError = "Invalid role assigned.";
                break;
        }
        exit();
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure this links to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 500px; /* Increase container width */
            background-color: #fff;
            padding: 60px 50px; /* Increase container padding */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #002147; /* Dark blue */
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            color: #002147; /* Dark blue */
            font-size: 18px; /* Make the label text bigger */
        }
        input[type="text"], input[type="password"], input[type="submit"] {
            padding: 15px; /* Increase padding for bigger input fields */
            margin-bottom: 20px; /* Increase space between fields */
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 18px; /* Increase font size for input text */
        }
        input[type="submit"] {
            background-color: #4CAF50; /* Green */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 18px; /* Increase font size for submit button */
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        p {
            text-align: center;
            margin-top: 10px;
            color: red; /* Adjust as per your preference */
        }
        p a {
            color: blue;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Login">
        </form>
        <?php if (isset($loginError)): ?>
            <p><?php echo $loginError; ?></p>
        <?php endif; ?>
    </div>
   
   
    
</body>
</html>
