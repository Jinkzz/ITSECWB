<?php
require 'core.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Use a Prepared Statement to find the user by email.
    // This prevents SQL Injection attacks.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();


    // Check the login_attempts column in the database. If the number is 5 or more, we stop the login immediately.
    if ($user && $user['login_attempts'] >= 5) {
        writeLog('AUTH', "Login blocked for locked account. User ID: {$user['id']}, Attempts: {$user['login_attempts']}");
        $error = "Account locked. Contact admin.";
    } 
    
    // password_verify() takes the plain text password from the user and compares it to the secure hash stored in the database.
    elseif ($user && password_verify($pass, $user['password_hash'])) {
        
        // SReset the login attempts to 0 since they got it right.
        $pdo->prepare("UPDATE users SET login_attempts = 0 WHERE id = ?")->execute([$user['id']]);
        
        // Save user info into the Session (Server memory)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        writeLog('AUTH', 'User successfully logged in.');
        
        header("Location: profile.php");
        exit;
    } 
    

    else {
        // If the email exists but the password was wrong we add +1 to the 'login_attempts' count in the database.
        if ($user) {
            $newAttempts = (int)$user['login_attempts'] + 1;
            $pdo->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?")->execute([$user['id']]);

            writeLog('AUTH', "Failed login attempt for user ID {$user['id']} (Attempt {$newAttempts}/5)");
            if ($newAttempts >= 5) {
                writeLog('AUTH', "Account locked after 5 failed login attempts. User ID: {$user['id']}");
                $error = "Account locked. Contact admin.";
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            writeLog('AUTH', "Failed login attempt for non-existent email: {$email}");
            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4 card p-4 shadow-sm">
            <h3 class="text-center mb-4">Login</h3>
            <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                <button type="submit" class="btn btn-primary w-100">Sign In</button>
                <div class="mb-3">
                    <p class="text-center mt-3">Don't have an account? <a href="registration.php">Register</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>