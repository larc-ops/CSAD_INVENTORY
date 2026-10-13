<?php
session_start();
require_once "connection.php";

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === "" || $password === "") {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                // ✅ Login successful
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Username not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Inventory System</title>
<style>
    * { box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
    body {
        margin: 0; min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #3498db, #2c3e50);
    }
    .login-box {
        background: #fff; padding: 40px 35px; border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25); width: 100%; max-width: 380px;
    }
    .login-box h1 { margin: 0 0 25px; text-align: center; color: #2c3e50; font-size: 24px; }
    .form-group { margin-bottom: 15px; }
    label { display: block; font-size: 13px; color: #555; margin-bottom: 5px; font-weight: 600; }
    input {
        width: 100%; padding: 11px 13px; border: 1px solid #ccc;
        border-radius: 6px; font-size: 14px;
    }
    input:focus { outline: none; border-color: #3498db; box-shadow: 0 0 5px rgba(52,152,219,0.4); }
    button {
        width: 100%; padding: 12px; margin-top: 10px;
        background: #3498db; color: #fff; border: none;
        border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer;
    }
    button:hover { background: #2980b9; }
    .error {
        background: #f8d7da; color: #721c24; padding: 10px 14px;
        border-radius: 6px; margin-bottom: 15px; font-size: 14px;
        border-left: 4px solid #dc3545;
    }
    .footer { text-align: center; margin-top: 20px; font-size: 13px; color: #666; }
    .footer a { color: #3498db; text-decoration: none; font-weight: 600; }
</style>
</head>
<body>
    <div class="login-box">
        <h1>📦 Inventory Login</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Log In</button>
        </form>

        <div class="footer">
            No account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>