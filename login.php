<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['std_id']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT student_name, password, phone, email, specialization, birthday, gender, balance FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($student_name, $stored_password, $phone, $email, $specialization, $birthday, $gender, $balance);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $stored_password)) {
            // Login successful
            $first_name = explode(" ", $student_name)[0];

            // Store student information in session
            $_SESSION['student_id'] = $student_id;
            $_SESSION['student_name'] = $student_name;
            $_SESSION['phone'] = $phone;
            $_SESSION['email'] = $email;
            $_SESSION['specialization'] = $specialization;
            $_SESSION['birthday'] = $birthday;
            $_SESSION['gender'] = $gender;
            $_SESSION['balance'] = $balance;

            echo json_encode(['success' => true, 'message' => "Login successful! Welcome, $first_name", 'redirect' => 'dashboard.php']);
        } else {
            // Incorrect password
            echo json_encode(['success' => false, 'message' => "Error: Incorrect password!"]);
        }
    } else {
        // User not found
        echo json_encode(['success' => false, 'message' => "Error: User not found!"]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>