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
$savings_accounts = [];

// Fetch savings account for the logged-in student
$query = "SELECT * FROM SavingsAccount WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $savings_accounts[] = $row;
}

$stmt->close();

// Add Savings Account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_account'])) {
    $monthly_saving_goal = trim($_POST['monthly_saving_goal']);

    $stmt = $conn->prepare("INSERT INTO SavingsAccount (student_id, monthly_saving_goal) VALUES (?, ?)");
    $stmt->bind_param("sd", $student_id, $monthly_saving_goal);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Savings account created successfully!";
        header("Location: manage_savings_account.php"); // Redirect to refresh the page
        exit();
    } else {
        $_SESSION['error_message'] = "Error creating savings account: " . $stmt->error;
    }

    $stmt->close();
}

// Update Savings Account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_account'])) {
    $account_id = $_POST['account_id'];
    $monthly_saving_goal = trim($_POST['monthly_saving_goal']);

    $stmt = $conn->prepare("UPDATE SavingsAccount SET monthly_saving_goal = ? WHERE account_id = ?");
    $stmt->bind_param("di", $monthly_saving_goal, $account_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Savings account updated successfully!";
        header("Location: manage_savings_account.php"); // Redirect to refresh the page
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating savings account: " . $stmt->error;
    }

    $stmt->close();
}

// Delete Savings Account
if (isset($_GET['delete_id'])) {
    $account_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM SavingsAccount WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Savings account deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting savings account: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Savings Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            padding: 30px;
        }
        .account-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .alert {
            margin-bottom: 20px;
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Manage Savings Account</h2>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" id="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" id="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="account-card">
        <h4>Add New Savings Account</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="monthly_saving_goal" class="form-label required">Monthly Saving Goal (SAR):</label>
                <input type="number" step="0.01" class="form-control" id="monthly_saving_goal" name="monthly_saving_goal" required>
            </div>
            <button type="submit" name="add_account" class="btn btn-primary">Create Savings Account</button>
        </form>
    </div>

    <h4 class="mt-4">Your Savings Accounts</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Account ID</th>
                <th>Balance (SAR)</th>
                <th>Monthly Saving Goal (SAR)</th>
                <th>Opening Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($savings_accounts as $account): ?>
                <tr>
                    <td><?php echo htmlspecialchars($account['account_id']); ?></td>
                    <td><?php echo htmlspecialchars($account['balance']); ?></td>
                    <td><?php echo htmlspecialchars($account['monthly_saving_goal']); ?></td>
                    <td><?php echo htmlspecialchars($account['opening_date']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $account['account_id']; ?>">Edit</button>
                        <a href="?delete_id=<?php echo $account['account_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this account?');">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $account['account_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Savings Account</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="account_id" value="<?php echo $account['account_id']; ?>">
                                    <div class="mb-3">
                                        <label for="monthly_saving_goal" class="form-label required">Monthly Saving Goal (SAR):</label>
                                        <input type="number" step="0.01" class="form-control" id="monthly_saving_goal" name="monthly_saving_goal" value="<?php echo htmlspecialchars($account['monthly_saving_goal']); ?>" required>
                                    </div>
                                    <button type="submit" name="edit_account" class="btn btn-primary">Update Savings Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Function to hide messages after 10 seconds
    setTimeout(function() {
        const errorMessage = document.getElementById('error-message');
        const successMessage = document.getElementById('success-message');

        if (errorMessage) {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 500); // Wait for fade-out transition to complete
        }

        if (successMessage) {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500); // Wait for fade-out transition to complete
        }
    }, 10000); // 10 seconds
</script>
</body>
</html>