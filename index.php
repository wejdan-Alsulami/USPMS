<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In and Sign Up Form</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
    <link rel="stylesheet" href="./style.css">
    <style>
        #popup {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 20px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div id="popup"></div>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form id="signup-form">
                <h1>Sign Up</h1>
                <input type="text" id="std_id" name="std_id" placeholder="Enter your Student ID" required />
                <input type="text" id="std_name" name="std_name" placeholder="Enter your Full Name" required />
                <input type="tel" id="phone" name="phone" placeholder="Enter your Phone Number" required />
                <input type="email" id="email" name="email" placeholder="Enter your Email" required />
                <input type="password" id="password" name="password" placeholder="Enter your Password" required />
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
                <input type="text" id="specialization" name="specialization" placeholder="Enter your Specialization" required />
                <input type="date" id="birthday" name="birthday" required />
                <div class="gender-container">
                    <label>Gender:</label>
                    <label>
                        <input type="radio" name="gender" value="Male" required /> Male
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Female" required /> Female
                    </label>
                </div>
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form id="login-form">
                <h1>Log In</h1>
                <input type="text" name="std_id" placeholder="Student ID" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit">Log In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Let's Register Account!</h1>
                    <p>Enter your information to create an account.</p>
                    <span>Already have an account?</span>
                    <a class="ghost" id="signIn">Log In</a>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Welcome Back!</h1>
                    <p>Enter your credentials to login.</p>
                    <span>Don't have an account yet?</span>
                    <a class="ghost" id="signUp">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="./script.js"></script>
    <script>
    // Show popup message for success or error
    function showPopup(message, isError = false, redirect = null) {
        var popup = document.getElementById('popup');
        popup.textContent = message;
        popup.style.backgroundColor = isError ? '#ffcccc' : '#ccffcc';
        popup.style.display = 'block';

        // Set timeout for 5 seconds (error) or 15 seconds (success)
        var timeoutDuration = isError ? 3000 : 5000;

        setTimeout(function() {
            popup.style.display = 'none'; // Hide the popup after the timeout
            if (redirect) {
                window.location.href = redirect; // Redirect after hiding the popup
            }
        }, timeoutDuration);
    }

    // Handle login form submission
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showPopup(data.message, !data.success, data.redirect);
            // Clear the form fields after successful login
            if (data.success) {
                document.getElementById('login-form').reset();
            }
        });
    });

    // Handle signup form submission
    document.getElementById('signup-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('signup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showPopup(data.message, !data.success, data.redirect);
            // If signup is successful, redirect to login section and clear the form
            if (data.success) {
                setTimeout(function() {
                    // Reset the signup form fields
                    document.getElementById('signup-form').reset();
                    // Redirect to login section
                    window.location.hash = '#signIn'; // Redirect to login section
                }, 5000); // Wait for 5 seconds before redirecting to login section
            }
        });
    });

    <?php
    // If session message exists, show popup with message
    if (isset($_SESSION['message'])) {
        $isError = strpos($_SESSION['message'], 'Error') !== false;
        $redirect = $isError ? null : ($_SESSION['redirect'] ?? null);
        echo "showPopup(" . json_encode($_SESSION['message']) . ", $isError, " . json_encode($redirect) . ");";
        unset($_SESSION['message']);
        unset($_SESSION['redirect']);
    }
    ?>
    </script>
</body>
</html>
