<?php
include 'config/db.php';
session_start();

$error = "";
$success = "";
$token_valid = false;

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    // Check if token exists and is not expired
    $sql = "SELECT * FROM users WHERE reset_token='$token' AND reset_expiry > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $token_valid = true;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            // Fetch current password hash
            $result->data_seek(0); // Reset result pointer because we fetched
            $row = $result->fetch_assoc();
            $current_hash = $row['password'];

            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    if (password_verify($new_password, $current_hash)) {
                        $error = "New password cannot be the same as your old password.";
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Update password and clear token
                        $update = "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'";

                        if ($conn->query($update)) {
                            $success = "Password updated successfully! <a href='login.php' style='color:#00d26a'>Login now</a>";
                            $token_valid = false; // Hide form
                        } else {
                            $error = "Database error updating password.";
                        }
                    }
                } else {
                    $error = "Password must be at least 6 characters.";
                }
            } else {
                $error = "Passwords do not match.";
            }
        }
    } else {
        $error = "This password reset link is invalid or has expired.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | QuickNote</title>
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
                <a href="login.php" class="auth-back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back to Login
                </a>

                <div class="auth-header" style="text-align: left;">
                    <h2>Set New Password</h2>
                    <p>Secure your account with a fresh password.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="error-msg"
                        style="background: rgba(0, 210, 106, 0.1); border-color: rgba(0, 210, 106, 0.2); color: #00d26a;">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($token_valid): ?>
                    <form method="POST" action="">
                        <div class="auth-form-group">
                            <label class="auth-label">New Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="new_password" id="new_password" class="auth-input"
                                    placeholder="••••••••" required>
                                <span class="toggle-password" onclick="togglePassword('new_password', this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="auth-form-group">
                            <label class="auth-label">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" name="confirm_password" id="confirm_password" class="auth-input"
                                    placeholder="••••••••" required>
                                <span class="toggle-password" onclick="togglePassword('confirm_password', this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="auth-btn">Reset Password</button>
                    </form>

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
                <?php endif; ?>

                <?php if (!$token_valid && empty($success)): ?>
                    <a href="forgot_password.php" class="auth-btn">Request New Link</a>
                <?php endif; ?>
            </div>

            <!-- RIGHT SIDE: VISUAL -->
            <div class="auth-visual-side">
                <!-- 3D Model Container -->
                <div id="model-container"
                    style="position: absolute; top:0; left:0; width:100%; height:100%; z-index: 1;"></div>

                <div class="visual-content">
                    <h3>Fresh Start</h3>
                    <p>Your security is our priority.</p>
                </div>
            </div>

        </div>

    </div>

    <script src="assets/js/webgl-background.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="assets/js/login-3d.js"></script>
</body>

</html>