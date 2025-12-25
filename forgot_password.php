<?php
include 'config/db.php';
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));

    if (empty($email)) {
        $error = "Please enter your email.";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            // Generate Token
            $token = bin2hex(random_bytes(50));
            // Expire in 1 hour (Use SQL time to avoid timezone mismatch)

            // Check if columns exist (Quick fix for development flow)
            // In production, run migration script. Here we attempt to update.
            $update = "UPDATE users SET reset_token='$token', reset_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email='$email'";

            if ($conn->query($update)) {
                // In a real app, send email here.
                // For this demo/mini-proj, we'll simulate it or show a link.
                $success = "Password reset link has been sent to your email. <br><small>(Simulated: <a href='reset_password.php?token=$token' style='color:#00d26a'>Reset Link</a>)</small>";
            } else {
                $error = "Database error. Please try again.";
            }
        } else {
            $error = "No account found with this email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | QuickNote</title>
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

        /* Reuse auth styles */
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <div class="auth-container">

        <div class="auth-split-card">

            <!-- LEFT SIDE: FORM -->
            <div class="auth-form-side">
                <a href="login.php" class="auth-back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Login
                </a>

                <div class="auth-header" style="text-align: left;">
                    <h2>Reset Password</h2>
                    <p>Enter your email to receive a recovery link.</p>
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

                <form method="POST" action="">
                    <div class="auth-form-group">
                        <label class="auth-label">Email Address</label>
                        <input type="email" name="email" class="auth-input" placeholder="diane@example.com" required>
                    </div>

                    <button type="submit" class="auth-btn">RESET PASSWORD</button>

                    <a href="login.php" class="auth-btn"
                        style="background: transparent; border: 1px solid rgba(255,255,255,0.1); color: #fff; margin-top: 15px; display: block; text-align: center; text-decoration: none;">
                        Back to Login
                    </a>
                </form>
            </div>

            <!-- RIGHT SIDE: VISUAL -->
            <div class="auth-visual-side">
                <!-- 3D Model Container -->
                <div id="model-container"
                    style="position: absolute; top:0; left:0; width:100%; height:100%; z-index: 1;"></div>

                <div class="visual-content">
                    <h3>Secure Account Recovery</h3>
                    <p>We'll help you get back to your second brain in no time.</p>
                </div>
            </div>

        </div>

    </div>

    <script src="assets/js/webgl-background.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="assets/js/login-3d.js"></script>
</body>

</html>