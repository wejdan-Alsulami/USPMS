<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

// Database connection (replace with your actual connection code)
//require 'db_connection.php'; // Include your database connection file
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['student_id'];
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Fetch the current hashed password from the database
    $query = "SELECT password FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the old password
        if (password_verify($old_password, $hashed_password)) {
            // Check if new passwords match
            if ($new_password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => "Error: Passwords do not match!"]);
                exit();
            }

            // Hash the new password
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_query = "UPDATE students SET password = ? WHERE student_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ss", $new_hashed_password, $student_id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => "Password changed successfully!"]);
            } else {
                echo json_encode(['success' => false, 'message' => "Error: Could not update password."]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Error: Old password is incorrect."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Error: User not found."]);
    }

    $stmt->close();
    $conn->close();
}