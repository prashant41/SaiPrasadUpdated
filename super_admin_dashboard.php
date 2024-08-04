<?php
include("db.php");
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Get filter value from the request
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';

// Define date ranges based on filter
if ($filter == 'monthly') {
    $date_format = 'DATE_FORMAT(date, "%Y-%m")';
    $group_by = 'DATE_FORMAT(date, "%Y-%m")';
    $title_suffix = ' (Monthly)';
} elseif ($filter == 'yearly') {
    $date_format = 'DATE_FORMAT(date, "%Y")';
    $group_by = 'DATE_FORMAT(date, "%Y")';
    $title_suffix = ' (Yearly)';
} else {
    $date_format = 'DATE(date)';
    $group_by = 'DATE(date)';
    $title_suffix = ' (Daily)';
}

// Fetch raw materials data and aggregate amount
$material_query = $pdo->query("SELECT raw_material_name, SUM(amount) as total_amount FROM expenses_material GROUP BY raw_material_name");
$rawMaterials = $material_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch daily revenue data from the revenue table
$revenue_query = $pdo->query("
    SELECT $date_format as period, 
           SUM(counter_cash + delivery_boy_cash + book_cash) as total_revenue 
    FROM revenue 
    GROUP BY $group_by
");
$revenueData = $revenue_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch expenses by shop for bar charts
$shop_expense_query = $pdo->query("
    SELECT shop_name, SUM(amount) as total_expense 
    FROM orders 
    GROUP BY shop_name
");
$shop_expenses = $shop_expense_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch total salary and records data
$salary_query = $pdo->query("SELECT SUM(salary + bonus + extra_money) as total_salary FROM employees");
$salary = $salary_query->fetch(PDO::FETCH_ASSOC)['total_salary'];

$records_query = $pdo->query("SELECT SUM(amount) as total_records FROM records");
$records = $records_query->fetch(PDO::FETCH_ASSOC)['total_records'];

// Prepare data for charts
$revenuePeriods = array_column($revenueData, 'period');
$revenueAmounts = array_column($revenueData, 'total_revenue');

$shopNames = array_column($shop_expenses, 'shop_name');
$shopExpenseAmounts = array_column($shop_expenses, 'total_expense');

// Convert period to a format Plotly can handle
$formattedRevenuePeriods = array_map(function($date) {
    return date('Y-m-d', strtotime($date)); // ISO 8601 format
}, $revenuePeriods);

// Prepare data for charts
$rawMaterialNames = array_column($rawMaterials, 'raw_material_name');
$rawMaterialAmounts = array_column($rawMaterials, 'total_amount');

// Combine raw materials into a single category for the expense breakdown
$expenseNames = array('Total Salary', 'Total Records', 'Raw Material');
$expenseAmounts = array($salary, $records, array_sum($rawMaterialAmounts));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        nav {
            background-color: #002147; /* Dark blue */
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .content {
            margin-top: 56px; /* Space for top navigation */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            overflow: auto;
        }
        h2 {
            text-align: center;
            color: #002147; /* Dark blue */
            margin-top: 20px;
            font-weight: bold; /* Make the heading bold */
        }
        .plotly-chart {
            width: 100%; /* Responsive width */
            height: 500px; /* Fixed height for clarity */
        }
        .logo {
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logo:hover {
            background-color: #4CAF50;
        }
        .filter {
            text-align: center;
            margin: 20px 0;
        }
        .filter select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav>
    <a href="super_admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="pnl.php">PnL</a>
    <a href="manage_expense_material.php">Raw Materials</a>
    <a href="raw_material_list.php">Filter Raw Material</a>
    <a href="manage_users.php">Manage</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
</nav>

<!-- Main Content Area -->
<div class="content">
    <div class="filter">
        <label for="filter">Select Filter:</label>
        <select id="filter" onchange="applyFilter()">
            <option value="daily" <?php if ($filter == 'daily') echo 'selected'; ?>>Daily</option>
            <option value="monthly" <?php if ($filter == 'monthly') echo 'selected'; ?>>Monthly</option>
            <option value="yearly" <?php if ($filter == 'yearly') echo 'selected'; ?>>Yearly</option>
        </select>
    </div>

    <h2>Raw Material Quantities</h2>
    <div id="materialsChart" class="plotly-chart"></div>

    <h2>Revenue Per Day<?php echo $title_suffix; ?></h2>
    <div id="revenueChart" class="plotly-chart"></div>

    <h2>Expense Breakdown</h2>
    <div id="expensesChart" class="plotly-chart"></div>

    <h2>Sales based on Shop Name (Bar Chart)</h2>
    <div id="shopExpensesBarChart" class="plotly-chart"></div>
</div>

<script>
    function applyFilter() {
        var filter = document.getElementById('filter').value;
        window.location.href = "?filter=" + filter;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Raw Material Quantities Pie Chart
        var materialsData = {
            labels: <?php echo json_encode($rawMaterialNames); ?>,
            values: <?php echo json_encode($rawMaterialAmounts); ?>,
            type: 'pie',
            textinfo: 'label+percent',
            marker: {
                colors: ['#004B95', '#009596', '#C9190B', '#4CB140', '#5752D1'] // Example colors
            }
        };

        var materialsLayout = {
            title: 'Raw Material Quantities',
            autosize: true,
            responsive: true
        };

        Plotly.newPlot('materialsChart', [materialsData], materialsLayout);

        // Revenue Per Day Chart
        var revenueData = {
            x: <?php echo json_encode($formattedRevenuePeriods); ?>,
            y: <?php echo json_encode($revenueAmounts); ?>,
            type: 'bar',
            marker: {
                color: '#5752D1' // Use a color from the palette
            },
            text: <?php echo json_encode($revenueAmounts); ?>,
            textposition: 'auto'
        };

        var revenueLayout = {
            title: 'Revenue Per Day<?php echo $title_suffix; ?>',
            xaxis: { title: 'Period' },
            yaxis: { title: 'Total Revenue' },
            autosize: true,
            responsive: true
        };

        Plotly.newPlot('revenueChart', [revenueData], revenueLayout);

        // Expenses Breakdown Chart
        var expensesData = {
            labels: <?php echo json_encode($expenseNames); ?>,
            values: <?php echo json_encode($expenseAmounts); ?>,
            type: 'pie',
            textinfo: 'label+percent',
            marker: {
                colors: ['#004B95', '#009596', '#C9190B'] // Adjust colors if needed
            }
        };

        var expensesLayout = {
            title: 'Expenses Breakdown',
            autosize: true,
            responsive: true
        };

        Plotly.newPlot('expensesChart', [expensesData], expensesLayout);

        // Sales based on Shop Name (Bar Chart)
        var shopExpensesBarData = {
            x: <?php echo json_encode($shopNames); ?>,
            y: <?php echo json_encode($shopExpenseAmounts); ?>,
            type: 'bar',
            marker: {
                color: '#4CB140' // Use a color from the palette
            },
            text: <?php echo json_encode($shopExpenseAmounts); ?>,
            textposition: 'auto'
        };

        var shopExpensesBarLayout = {
            title: 'Sales based on Shop Name',
            xaxis: { title: 'Shop Name' },
            yaxis: { title: 'Total Sales' },
            autosize: true,
            responsive: true
        };

        Plotly.newPlot('shopExpensesBarChart', [shopExpensesBarData], shopExpensesBarLayout);
    });
</script>

</body>
</html>
