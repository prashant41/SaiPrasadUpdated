<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Get the selected month from the dropdown
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');

// Define the commission percentage
$commission_percentage = 0.16; // 16%

// Fetch total revenue for the selected month
try {
    $sql = "SELECT SUM(counter_cash + delivery_boy_cash + book_cash) AS total_revenue FROM revenue WHERE DATE_FORMAT(date, '%Y-%m') = :selected_month";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['selected_month' => $selected_month]);
    $revenue_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_revenue = $revenue_result['total_revenue'] ?? 0;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch total expenses for the selected month from `expenses_material`
try {
    $sql = "SELECT SUM(amount) AS total_material_expenses FROM expenses_material WHERE DATE_FORMAT(date, '%Y-%m') = :selected_month";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['selected_month' => $selected_month]);
    $material_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_material_expenses = $material_result['total_material_expenses'] ?? 0;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch total expenses for the selected month from `records`
try {
    $sql = "SELECT SUM(amount) AS total_records_expenses FROM records WHERE DATE_FORMAT(date_paid, '%Y-%m') = :selected_month";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['selected_month' => $selected_month]);
    $records_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_records_expenses = $records_result['total_records_expenses'] ?? 0;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch total order amount and calculate commission for the selected month using delivery_boy_cash from revenue table
try {
    $sql = "SELECT SUM(delivery_boy_cash) AS total_delivery_boy_cash FROM revenue WHERE DATE_FORMAT(date, '%Y-%m') = :selected_month";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['selected_month' => $selected_month]);
    $revenue_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_delivery_boy_cash = $revenue_result['total_delivery_boy_cash'] ?? 0;
    $total_commission_expenses = $total_delivery_boy_cash * $commission_percentage;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


// Fetch total salary expenses (ignoring the month)
try {
    $sql = "SELECT SUM(salary + bonus + extra_money) AS total_salary_expenses FROM employees";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $salary_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_salary_expenses = $salary_result['total_salary_expenses'] ?? 0;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Calculate total expenses including salaries
$total_expenses = $total_material_expenses + $total_records_expenses + $total_salary_expenses + $total_commission_expenses;

// Calculate profit
$profit = $total_revenue - $total_expenses;

// Determine color and shadow based on profit or loss
$border_color = $profit >= 0 ? 'green' : 'red';
$shadow_color = $profit >= 0 ? '0 0 15px rgba(0, 255, 0, 0.5)' : '0 0 15px rgba(255, 0, 0, 0.5)';

// Get the distinct months for the dropdown
try {
    $sql = "SELECT DISTINCT DATE_FORMAT(date, '%Y-%m') AS month FROM revenue ORDER BY month DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $months = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Loss Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        nav {
            background-color: #002147;
            overflow: hidden;
            color: white;
        }
        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
        }
        nav a:hover {
            background-color: #ddd;
            color: black;
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid;
            border-radius: 8px;
            box-shadow: 0 0 15px;
            background-color: white;
            border-color: <?php echo $border_color; ?>;
            box-shadow: <?php echo $shadow_color; ?>;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .container:hover {
            box-shadow: 0 0 65px <?php echo $shadow_color; ?>; /* Enhance shadow on hover */
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #002147;
        }
        .form-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        form {
            margin: 0;
            display: flex;
            align-items: center;
        }
        form label {
            font-weight: bold;
            margin-right: 10px;
        }
        form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            margin-right: 10px;
        }
        form select:hover {
            background-color: #f9f9f9; /* Lighten background on hover */
            border-color: #aaa; /* Darker border on hover */
        }
        .button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .profit-report {
            font-size: 18px;
            line-height: 1.6;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .profit-report p {
            margin: 0;
            padding: 10px 0;
        }
        .profit-report span {
            font-weight: bold;
        }
        .profit-report .profit {
            color: green;
        }
        .profit-report .loss {
            color: red;
        }
        .logo {
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .logo:hover {
            background-color: #4CAF50; /* Green on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>

<nav>
    <a href="super_admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="pnl.php">PnL</a>
    <a href="manage_expense_material.php">Raw Materials</a>
    <a href="raw_material_list.php">Filter Raw Materials</a>
    <a href="manage_users.php">Manage</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
</nav>

<div class="container">
    <h1>Profit and Loss Statement</h1>
    <div class="form-container">
        <form method="POST">
            <label for="month">Select Month:</label>
            <select id="month" name="month" required>
                <?php foreach ($months as $month) : ?>
                    <option value="<?php echo $month['month']; ?>" <?php echo $month['month'] === $selected_month ? 'selected' : ''; ?>>
                        <?php echo date('F Y', strtotime($month['month'] . '-01')); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button">Update</button>
        </form>
        <button id="screenshotBtn" class="button">Download Report</button>
    </div>
    <div class="profit-report">
        <p><span>Total Revenue:</span> ₹<?php echo number_format($total_revenue, 2); ?></p>
        <p><span>Total Material Expenses:</span> ₹<?php echo number_format($total_material_expenses, 2); ?></p>
        <p><span>Total Records Expenses:</span> ₹<?php echo number_format($total_records_expenses, 2); ?></p>
        <p><span>Total Salary Expenses:</span> ₹<?php echo number_format($total_salary_expenses, 2); ?></p>
        <p><span>Total Commission Expenses:</span> ₹<?php echo number_format($total_commission_expenses, 2); ?></p>
        <p class="<?php echo $profit >= 0 ? 'profit' : 'loss'; ?>">
            <span><?php echo $profit >= 0 ? 'Profit:' : 'Loss:'; ?></span> ₹<?php echo number_format($profit, 2); ?>
        </p>
    </div>
</div>

<script>
document.getElementById('screenshotBtn').addEventListener('click', function() {
    html2canvas(document.querySelector('.container')).then(canvas => {
        const link = document.createElement('a');
        link.download = 'profit_loss_statement.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
});
</script>

</body>
</html>
