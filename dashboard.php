<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
include 'db_config.php'; // Include your database connection file

$student_id = $_SESSION['student_id'];

// Fetch the student's balance from the students table
$query = "SELECT balance FROM students WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($student_balance);
$stmt->fetch();
$stmt->close();

// Fetch the student's savings goal target amount from the Goal table
$query = "SELECT target_amount FROM Goal WHERE student_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($target_amount);
$stmt->fetch();
$stmt->close();

// Fetch the student's balance from the SavingsAccount table
$query = "SELECT balance FROM savingsaccount WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($savings_balance);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, #004aad, #0066cc, #a1c4fd, #c2e9fb);
            background-size: 200% 200%;
            animation: waveAnimation 6s ease infinite;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        @keyframes waveAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-header img {
            width: 60%;
            margin-bottom: 10px;
        }

        .sidebar-header h2 {
            font-size: 22px;
            font-weight: 600;
            margin: 0;
            padding: 5px 0;
            border-bottom: 2px solid #003366; /* خط تحت النص */
            color: #e5f01d; /* لون النص الجديد (أحمر غامق) */
        }

        .sidebar ul {
            padding: 0;
            margin-top: 30px;
            width: 100%;
        }

        .sidebar ul li {
            list-style: none;
            padding: 15px 10px;
            width: 100%;
            position: relative;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            display: flex;
            align-items: center;
            padding-left: 30px;
            transition: 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .sidebar ul li a i {
            margin-right: 15px;
            font-size: 20px;
            color: #003366; /* اللون الكحلي */
        }

        .content {
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 30px;
            font-weight: 700;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .card-text {
            font-size: 24px;
            color: #007bff;
        }

        .chart-container {
            width: 400px;
            height: 400px;
            margin: 20px auto;
        }


        .highlight {
            color: #dc3545; /* لون أحمر */
            font-weight: bold; /* نص غامق */
}

    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <img src="images/logo.png" alt="USPM Logo" class="logo">
            <h2>University Student Payroll Management System</h2>
        </div>
        <ul class="list-unstyled components">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="view_profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="manage_goals.php"><i class="fas fa-bullseye"></i> Goals</a></li>
            <li><a href="manage_expenses.php"><i class="fas fa-exchange-alt"></i> Expense</a></li>
            <li><a href="manage_savings_account.php"><i class="fas fa-money-bill-wave"></i> Savings Account</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <header class="header">
            <h1>Dashboard</h1>
        </header>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Account Balance</h5>
                        <p class="card-text"><?php echo htmlspecialchars($student_balance) . ' SAR'; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h5>Savings Goal Progress</h5>
        <div class="chart-container">
            <canvas id="savingsGoalChart"></canvas>
        </div>
        <p class="mt-3"><span class="highlight">Goal:</span> <?php echo htmlspecialchars($target_amount) . ' SAR'; ?></p>
        <p><span class="highlight">Current Savings:</span> <?php echo htmlspecialchars($savings_balance) . ' SAR'; ?></p>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('savingsGoalChart').getContext('2d');
        const savingsGoalChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Current Savings', 'Remaining Goal'],
                datasets: [{
                    label: 'Savings Progress',
                    data: [<?php echo htmlspecialchars($savings_balance); ?>, <?php echo htmlspecialchars($target_amount - $savings_balance); ?>],
                    backgroundColor: ['#007bff', '#e0e0e0'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Savings Goal Progress'
                    }
                }
            }
        });
    </script>
</body>
</html>