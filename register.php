<?php
include 'config/db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check duplication
        $check = $conn->query("SELECT id FROM users WHERE email='$email' OR username='$username'");
        if ($check->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hash')";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | QuickNote</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        body {
            overflow: hidden;
        }
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <div class="auth-container">

        <div class="auth-split-card">

            <!-- LEFT SIDE: FORM -->
            <div class="auth-form-side">
                <a href="index.php" class="auth-back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Home
                </a>

                <div class="auth-header" style="text-align: left;">
                    <h2>Create Account</h2>
                    <p>Join the second brain revolution.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    
                    <div class="auth-grid-row">
                        <div class="auth-form-group">
                            <label class="auth-label">Username</label>
                            <input type="text" name="username" class="auth-input" placeholder="diane" required>
                        </div>

                        <div class="auth-form-group">
                            <label class="auth-label">Email Address</label>
                            <input type="email" name="email" class="auth-input" placeholder="diane@example.com" required>
                        </div>
                    </div>

                    <div class="auth-grid-row">
                        <div class="auth-form-group">
                            <label class="auth-label">Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" class="auth-input" placeholder="••••••••" required>
                                <span class="toggle-password" onclick="togglePassword('password', this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </span>
                            </div>
                        </div>

                        <div class="auth-form-group">
                            <label class="auth-label">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="confirm_password" id="confirm_password" class="auth-input" placeholder="••••••••" required>
                                <span class="toggle-password" onclick="togglePassword('confirm_password', this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Sign Up</button>
                </form>

                <div class="auth-footer" style="text-align: left;">
                    Already have an account? <a href="login.php">Log In</a>
                </div>
            </div>

            <!-- RIGHT SIDE: VISUAL -->
            <div class="auth-visual-side">
                <!-- 3D Model Container -->
                <div id="model-container"
                    style="position: absolute; top:0; left:0; width:100%; height:100%; z-index: 1;"></div>

                <div class="visual-content">
                    <h3>Unlock Your Potential</h3>
                    <p>Start capturing ideas at the speed of thought.</p>
                </div>
            </div>

        </div>

    </div>

    <script src="assets/js/webgl-background.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="assets/js/register-3d.js"></script>
    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            const eyeOpen = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            const eyeClosed = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';

            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML = eyeClosed;
            } else {
                input.type = "password";
                icon.innerHTML = eyeOpen;
            }
        }
    </script>
</body>

</html>