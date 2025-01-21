<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['std_id']);
    $student_name = trim($_POST['std_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $specialization = trim($_POST['specialization']);
    $birthday = trim($_POST['birthday']);
    $gender = trim($_POST['gender']);

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => "Error: Passwords do not match!"]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate a random balance between 0 and 1500
    $balance = rand(0, 1500);

    $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => "Error: Student ID already exists!"]);
        exit();
    }
    $stmt->close();

    // Update the INSERT statement to include the balance
    $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, phone, email, password, specialization, birthday, gender, balance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $student_id, $student_name, $phone, $email, $hashed_password, $specialization, $birthday, $gender, $balance);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Account created successfully!", 'redirect' => 'index.php#signIn']);
    } else {
        echo json_encode(['success' => false, 'message' => "Error: Could not register. Cause: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>