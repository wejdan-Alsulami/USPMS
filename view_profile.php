<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - View</title>
    <link rel="stylesheet" href="profile_styles.css">
</head>
<body>
    <div class="container">
        <!-- Profile View Container -->
        <div class="profile-container" id="profileContainer">
            <h1>Student Profile</h1>
            <div class="profile-info">
                <p><b>Student ID:</b> <?php echo htmlspecialchars($_SESSION['student_id']); ?></p>
                <p><b>Name:</b> <?php echo htmlspecialchars($_SESSION['student_name']); ?></p>
                <p><b>Email:</b> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><b>Phone:</b> <?php echo htmlspecialchars($_SESSION['phone']); ?></p>
                <p><b>Specialization:</b> <?php echo htmlspecialchars($_SESSION['specialization']); ?></p>
                <p><b>Birthday:</b> <?php echo htmlspecialchars($_SESSION['birthday']); ?></p>
                <p><b>Gender:</b> <?php echo htmlspecialchars($_SESSION['gender']); ?></p>
                <p><b>Balance:</b> <?php echo htmlspecialchars(number_format($_SESSION['balance'], 2)); ?> SAR</p> <!-- Display Balance -->
            </div>

            <div class="button-container">
                <button onclick="location.href='edit_profile.php'">Edit Profile</button>
                <button onclick="location.href='change_password.html'">Change Password</button>
            </div>
        </div>
    </div>
</body>
</html>