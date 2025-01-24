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
$goals = [];

// Fetch all goals for the logged-in student
$query = "SELECT * FROM Goal WHERE student_id = ? ORDER BY created_at ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}

$stmt->close();

// Add Goal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_goal'])) {
    $description = trim($_POST['description']);
    $target_amount = trim($_POST['target_amount']);

    if (empty($description) || empty($target_amount)) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Goal (student_id, description, target_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $student_id, $description, $target_amount);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Goal added successfully!";
            header("Location: manage_goals.php"); // Redirect to refresh the page
            exit();
        } else {
            $_SESSION['error_message'] = "Error adding goal: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Edit Goal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_goal'])) {
    $goal_id = $_POST['goal_id'];
    $description = trim($_POST['description']);
    $target_amount = trim($_POST['target_amount']);

    if (empty($description) || empty($target_amount)) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("UPDATE Goal SET description = ?, target_amount = ? WHERE goal_id = ?");
        $stmt->bind_param("sii", $description, $target_amount, $goal_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Goal updated successfully!";
            header("Location: manage_goals.php"); // Redirect to refresh the page
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating goal: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Delete Goal
if (isset($_GET['delete_id'])) {
    $goal_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Goal WHERE goal_id = ?");
    $stmt->bind_param("i", $goal_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Goal deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting goal: " . $stmt->error;
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
    <title>Manage Goals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            padding: 30px;
        }
        .goal-card {
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
    <h2 class="text-center mb-4">Manage Goals</h2>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" id="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" id="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="goal-card">
        <h4>Add New Goal</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="description" class="form-label required">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="target_amount" class="form-label required">Target Amount (SAR):</label>
                <input type="number" step="0.01" class="form-control" id="target_amount" name="target_amount" required>
            </div>
            <button type="submit" name="add_goal" class="btn btn-primary">Add Goal</button>
        </form>
    </div>

    <h4 class="mt-4">Your Goals</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Target Amount (SAR)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($goals as $goal): ?>
                <tr>
                    <td><?php echo htmlspecialchars($goal['goal_id']); ?></td>
                    <td><?php echo htmlspecialchars($goal['description']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($goal['target_amount'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($goal['status']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $goal['goal_id']; ?>">Edit</button>
                        <a href="?delete_id=<?php echo $goal['goal_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this goal?');">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $goal['goal_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Goal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="goal_id" value="<?php echo $goal['goal_id']; ?>">
                                    <div class="mb-3">
                                        <label for="description" class="form-label required">Description:</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($goal['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="target_amount" class="form-label required">Target Amount (SAR):</label>
                                        <input type="number" step="0.01" class="form-control" id="target_amount" name="target_amount" value="<?php echo htmlspecialchars($goal['target_amount']); ?>" required>
                                    </div>
                                    <button type="submit" name="edit_goal" class="btn btn-primary">Update Goal</button>
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