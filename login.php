<?php
// DEBUGGING: Enable error reporting for InfinityFree
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include 'config/db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUser = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    if (empty($emailOrUser) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            // Allow login via Email OR Username (Case-Insensitive for Username)
            $sql = "SELECT id, username, password FROM users WHERE email = '$emailOrUser' OR username = '$emailOrUser'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['show_splash'] = true; // Trigger splash screen once
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No account found with that email or username.";
            }
        } catch (Throwable $e) {
            // CATCH DB ERRORS
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | QuickNote</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* Specific overrides for this page if needed */
        body {
            overflow: hidden;
            /* Lock scroll for full immersion on login */
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
                    <h2>Welcome Back</h2>
                    <p>Enter your details to access your second brain.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="auth-form-group">
                        <label class="auth-label">Email or Username</label>
                        <input type="text" name="email" class="auth-input" placeholder="diane@example.com / diane"
                            required>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="auth-input"
                                placeholder="••••••••" required>
                            <span class="toggle-password" onclick="togglePassword('password', this)">
                                <!-- Default: Eye (Show) - Since input is password type, logic starts here, but usually we want to "Show" so icon should imply "Reveal" which is the Eye. Wait, usually Eye means "I am watching" -> Visible. Users expect to click Eye to see. So Eye = Show. Eye Slash = Hide. -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <script>
                        function togglePassword(inputId, icon) {
                            const input = document.getElementById(inputId);
                            const eyeOpen = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                            const eyeClosed = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';

                            if (input.type === "password") {
                                input.type = "text";
                                icon.innerHTML = eyeClosed; // Showing text -> Click to hide
                            } else {
                                input.type = "password";
                                icon.innerHTML = eyeOpen; // Hidden text -> Click to show
                            }
                        }
                    </script>

                    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px; font-size: 0.9rem;">
                        <a href="forgot_password.php" style="color: #00d26a; text-decoration: none;">Forgot
                            Password?</a>
                    </div>

                    <button type="submit" class="auth-btn">Log In</button>
                </form>

                <div class="auth-footer" style="text-align: left;">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>
            </div>

            <!-- RIGHT SIDE: VISUAL -->
            <div class="auth-visual-side">
                <!-- 3D Model Container -->
                <div id="model-container"
                    style="position: absolute; top:0; left:0; width:100%; height:100%; z-index: 1;"></div>

                <div class="visual-content">
                    <h3>Capturing Moments,<br>Creating Memories.</h3>
                    <p>Experience the ultimate second brain designed for speed, clarity, and focus.</p>
                </div>
            </div>

        </div>

    </div>

    <!-- WebGL Background (Global) -->
    <script src="assets/js/webgl-background.js"></script>

    <!-- Three.js (For 3D Model in Card) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="assets/js/login-3d.js"></script>
</body>

</html>