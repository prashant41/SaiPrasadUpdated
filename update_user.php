<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "UPDATE users SET username = :username, email = :email, phone = :phone, role = :role";

    // Check if password is provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
    }

    $sql .= " WHERE id = :id";

    // Update the user in the database
    try {
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':phone' => $phone,
            ':role' => $role,
            ':id' => $id
        ];

        if (!empty($password)) {
            $params[':password'] = $hashedPassword;
        }

        $stmt->execute($params);

        // Redirect to manage users page
        header("Location: manage_users.php");
        exit();
    } catch (PDOException $e) {
        // Handle query error
        echo "Error: " . $e->getMessage();
    }
} else {
    // Fetch user details
    $id = $_GET['id'];
    try {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle query error
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="style.css">
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
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 500px;
            position: relative;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px 15px;
            background-color: #4CAF50; /* Green */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #45a049; /* Darker green */
        }
        h2 {
            text-align: center;
            color: #002147; /* Dark blue */
            margin-top: 0;
        }
        .update-form {
            width: 100%;
        }
        .update-form label {
            display: block;
            margin-top: 10px;
        }
        .update-form input, .update-form select {
            padding: 8px;
            margin: 5px 0;
            width: calc(100% - 16px); /* Adjust width to account for padding */
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .update-form input[type="submit"] {
            background-color: #4CAF50; /* Green */
            color: white;
            cursor: pointer;
            border: none;
            padding: 10px;
            font-size: 16px;
        }
        .update-form input[type="submit"]:hover {
            background-color: #45a049; /* Darker green */
        }
    </style>
</head>
<body>

<!-- Main Content Area -->
<div class="container">
    <a href="manage_users.php" class="back-button">Go Back</a>
    <h2>Update User</h2>
    <div class="update-form">
        <form action="update_user.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="super_admin" <?php echo $user['role'] == 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="client" <?php echo $user['role'] == 'client' ? 'selected' : ''; ?>>Client</option>
            </select>
            <label for="password">Password (leave blank to keep current):</label>
            <input type="password" id="password" name="password">
            <input type="submit" value="Update User">
        </form>
    </div>
</div>

</body>
</html>
