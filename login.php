<?php
include 'db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password.";
    } else {
        // Allow login by Username OR Email
        $sql = "SELECT * FROM users WHERE email='$email' OR username='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | QuickNote</title>
    <link rel="stylesheet" href="style.css">
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

        .success-msg {
            background: var(--accent-green);
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
        <h2>Welcome Back</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-msg"><?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="email" class="auth-input" placeholder="Email or Username" required>
            <input type="password" name="password" class="auth-input" placeholder="Password" required>
            <button type="submit" class="auth-btn">Login</button>
        </form>
        <a href="register.php" class="auth-link">Create an account</a>
    </div>
</body>

</html>