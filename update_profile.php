<?php
session_start();
include 'db_config.php';

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['std_id'];
    $student_name = trim($_POST['std_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $birthday = trim($_POST['birthday']);
    $gender = trim($_POST['gender']);

    $stmt = $conn->prepare("UPDATE students SET student_name = ?, phone = ?, email = ?, specialization = ?, birthday = ?, gender = ? WHERE student_id = ?");
    $stmt->bind_param("ssssssi", $student_name, $phone, $email, $specialization, $birthday, $gender, $student_id);

    if ($stmt->execute()) {
        // Update session data after successful update
        $_SESSION['student_name'] = $student_name;
        $_SESSION['phone'] = $phone;
        $_SESSION['email'] = $email;
        $_SESSION['specialization'] = $specialization;
        $_SESSION['birthday'] = $birthday;
        $_SESSION['gender'] = $gender;

        // Set success message
        $_SESSION['message'] = "Profile updated successfully!";
    } else {
        // Set error message
        $_SESSION['message'] = "Error: Could not update profile. Cause: " . htmlspecialchars($conn->error);
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the edit profile page
    header("Location: edit_profile.php");
    exit();

}
?>