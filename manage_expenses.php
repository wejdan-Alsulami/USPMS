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
$expenses = [];

// Fetch all expenses for the logged-in student, ordered by expense_date ascending
$query = "SELECT * FROM Expense WHERE student_id = ? ORDER BY expense_date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $expenses[] = $row;
}

$stmt->close();

// Fetch student balance
$query_balance = "SELECT balance FROM students WHERE student_id = ?";
$stmt_balance = $conn->prepare($query_balance);
$stmt_balance->bind_param("s", $student_id);
$stmt_balance->execute();
$stmt_balance->bind_result($balance);
$stmt_balance->fetch();
$stmt_balance->close();

// Add Expense
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_expense'])) {
    $amount = trim($_POST['amount']);
    $category = trim($_POST['category']);
    $expense_date = trim($_POST['expense_date']);
    $description = trim($_POST['description']);

    if (empty($amount) || empty($category) || empty($expense_date)) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    } else if ($amount > $balance) {
        $_SESSION['error_message'] = "Insufficient balance to add this expense.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Expense (student_id, amount, category, expense_date, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $student_id, $amount, $category, $expense_date, $description);

        if ($stmt->execute()) {
            // Update balance
            $new_balance = $balance - $amount;
            $stmt_update = $conn->prepare("UPDATE students SET balance = ? WHERE student_id = ?");
            $stmt_update->bind_param("is", $new_balance, $student_id);
            $stmt_update->execute();
            $stmt_update->close();

            $_SESSION['success_message'] = "Expense added successfully!";
            header("Location: manage_expenses.php"); // Redirect to refresh the page
            exit();
        } else {
            $_SESSION['error_message'] = "Error adding expense: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Edit Expense
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_expense'])) {
    $expense_id = $_POST['expense_id'];
    $amount = trim($_POST['amount']);
    $category = trim($_POST['category']);
    $expense_date = trim($_POST['expense_date']);
    $description = trim($_POST['description']);

    if (empty($amount) || empty($category) || empty($expense_date)) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("UPDATE Expense SET amount = ?, category = ?, expense_date = ?, description = ? WHERE expense_id = ?");
        $stmt->bind_param("dsssi", $amount, $category, $expense_date, $description, $expense_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Expense updated successfully!";
            header("Location: manage_expenses.php"); // Redirect to refresh the page
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating expense: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Delete Expense
if (isset($_GET['delete_id'])) {
    $expense_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Expense WHERE expense_id = ?");
    $stmt->bind_param("i", $expense_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Expense deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting expense: " . $stmt->error;
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
    <title>Manage Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            padding: 30px;
        }
        .expense-card {
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
    <h2 class="text-center mb-4">Manage Expenses</h2>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" id="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" id="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="expense-card">
        <h4>Add New Expense</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="amount" class="form-label required">Amount (SAR):</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label required">Category:</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="mb-3">
                <label for="expense_date" class="form-label required">Expense Date:</label>
                <input type="date" class="form-control" id="expense_date" name="expense_date" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description (optional):</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
        </form>
    </div>

    <h4 class="mt-4">Your Expenses</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount (SAR)</th>
                <th>Category</th>
                <th>Expense Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense): ?>
                <tr>
                    <td><?php echo htmlspecialchars($expense['expense_id']); ?></td>
                    <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                    <td><?php echo htmlspecialchars($expense['category']); ?></td>
                    <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $expense['expense_id']; ?>">Edit</button>
                        <a href="?delete_id=<?php echo $expense['expense_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $expense['expense_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="expense_id" value="<?php echo $expense['expense_id']; ?>">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label required">Amount (SAR):</label>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($expense['amount']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="form-label required">Category:</label>
                                        <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($expense['category']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expense_date" class="form-label required">Expense Date:</label>
                                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description (optional):</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($expense['description']); ?></textarea>
                                    </div>
                                    <button type="submit" name="edit_expense" class="btn btn-primary">Update Expense</button>
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