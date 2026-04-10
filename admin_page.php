<?php
require 'core.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    writeLog('AUTH_VIOLATION', 'Unauthorized admin access attempt.');
    header("Location: login.php");
    exit;
}

// Admin Action 1: Delete User
if (isset($_GET['delete_user'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_user']]);
    writeLog('ADMIN', "Deleted user ID: " . $_GET['delete_user']);
    header("Location: admin_page.php");
    exit;
}

// Admin Action 2: Change Role
if (isset($_GET['toggle_role'])) {
    $uid = $_GET['toggle_role'];
    $current_role = $_GET['current'] == 'admin' ? 'user' : 'admin';
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$current_role, $uid]);
    writeLog('ADMIN', "Changed role of user ID $uid to $current_role");
    header("Location: admin_page.php");
    exit;
}

// Admin Action 3: Unlock Account (reset login attempts)
if (isset($_GET['unlock_user'])) {
    $uid = (int)$_GET['unlock_user'];
    $stmt = $pdo->prepare("UPDATE users SET login_attempts = 0, last_attempt = NULL WHERE id = ?");
    $stmt->execute([$uid]);
    writeLog('ADMIN', "Unlocked user account ID: $uid");
    header("Location: admin_page.php");
    exit;
}

$users = $pdo->query("SELECT id, full_name, email, role, login_attempts FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Admin Panel</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Admin Dashboard</span>
        <div>
            <a href="profile.php" class="btn btn-outline-info btn-sm">My Profile</a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Manage Users</div>
        <table class="table table-hover mb-0">
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Attempts</th><th>Admin Actions</th></tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= strtoupper($u['role']) ?></td>
                <td><?= (int)$u['login_attempts'] ?></td>
                <td>
                    <a href="?toggle_role=<?= $u['id'] ?>&current=<?= $u['role'] ?>" class="btn btn-sm btn-warning">Change Role</a>
                    <a href="?unlock_user=<?= $u['id'] ?>" class="btn btn-sm btn-info">Unlock</a>
                    <a href="?delete_user=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">System Logs (app.log)</div>
        <div class="card-body bg-light" style="max-height: 300px; overflow-y: scroll;">
            <pre><?php 
                if(file_exists('app.log')) echo htmlspecialchars(file_get_contents('app.log')); 
                else echo "No logs generated yet."; 
            ?></pre>
        </div>
    </div>
</div>
</body>
</html>

<script>
    // Security: Detects if the page is loaded from the "Back" cache
    // This helps with the session timeout and logout security requirements
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
