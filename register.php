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
    <title>Register | QuickNote</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #121212;
        }

        .auth-card {
            background: #1e1e1e;
            padding: 40px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        .auth-card h2 {
            color: #eee;
            text-align: center;
            margin-bottom: 20px;
        }

        .auth-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background: #2d2d2d;
            border: 1px solid #444;
            color: #fff;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .auth-btn {
            width: 100%;
            padding: 12px;
            background: var(--accent-green);
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }

        .auth-btn:hover {
            background: #1e8e3e;
        }

        .auth-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #888;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .auth-link:hover {
            color: #ccc;
        }

        .error-msg {
            background: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <h2>Create Account</h2>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" class="auth-input" placeholder="Username" required>
            <input type="email" name="email" class="auth-input" placeholder="Email Address" required>
            <input type="password" name="password" class="auth-input" placeholder="Password" required>
            <input type="password" name="confirm_password" class="auth-input" placeholder="Confirm Password" required>
            <button type="submit" class="auth-btn">Sign Up</button>
        </form>
        <a href="login.php" class="auth-link">Already have an account? Login</a>
    </div>
</body>

</html>