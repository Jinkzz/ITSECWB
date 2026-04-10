<?php
require 'core.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// If user doesn't exist in DB anymore, clear session
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - <?= htmlspecialchars($user['full_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .profile-card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .profile-img { width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card profile-card p-4 text-center">
                <div class="mb-4">
                    <?php if ($user['profile_photo']): ?>
                        <img src="uploads/<?= $user['profile_photo'] ?>" class="rounded-circle profile-img" alt="Profile Picture">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/150" class="rounded-circle profile-img" alt="Default Picture">
                    <?php endif; ?>
                </div>

                <h2 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name']) ?></h2>
                <p class="text-muted mb-4"><?= htmlspecialchars($user['email']) ?></p>

                <div class="row text-start mb-4">
                    <div class="col-12 border-bottom py-2">
                        <strong>Phone:</strong> <span class="float-end"><?= htmlspecialchars($user['phone']) ?></span>
                    </div>
                    <div class="col-12 border-bottom py-2">
                        <strong>Role:</strong> 
                        <span class="float-end badge <?= $user['role'] == 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                            <?= strtoupper($user['role']) ?>
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="items.php" class="btn btn-success">Manage My Items</a>

                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="admin_page.php" class="btn btn-primary">Go to Administration Panel</a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Security: Detects if the page is loaded from the "Back" cache
    // This helps with the session timeout and logout security requirements
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

</body>
</html>