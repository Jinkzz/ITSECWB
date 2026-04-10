<?php
require 'core.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action_msg = "";

// Action 1: POST / CREATE Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $stmt = $pdo->prepare("INSERT INTO items (user_id, item_name, description, price, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $_POST['item_name'], $_POST['description'], $_POST['price'], $_POST['quantity']]);
    writeLog('TRANSACTION', "Created new item: " . $_POST['item_name']);
    $action_msg = "Item added successfully.";
}

// Action 2: DELETE Item
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    writeLog('TRANSACTION', "Deleted item ID: " . $_GET['delete']);
    header("Location: items.php");
    exit;
}

// Action 3: EDIT Item (Simple inline update for demonstration)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {
    $stmt = $pdo->prepare("UPDATE items SET price = ?, quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['new_price'], $_POST['new_quantity'], $_POST['item_id'], $user_id]);
    writeLog('TRANSACTION', "Updated item ID: " . $_POST['item_id']);
    $action_msg = "Item updated successfully.";
}

// Fetch user's items
$items = $pdo->prepare("SELECT * FROM items WHERE user_id = ?");
$items->execute([$user_id]);
$items = $items->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>My Items</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Manage My Items</h2>
    <a href="profile.php" class="btn btn-secondary mb-3">Back to Profile</a>
    
    <?php if($action_msg) echo "<div class='alert alert-success'>$action_msg</div>"; ?>

    <div class="card p-3 mb-4">
        <h4>Add New Item</h4>
        <form method="POST">
            <input type="text" name="item_name" class="form-control mb-2" placeholder="Item Name (Text)" required>
            <textarea name="description" class="form-control mb-2" placeholder="Description (Text)" required></textarea>
            <div class="row">
                <div class="col"><input type="number" step="0.01" name="price" class="form-control mb-2" placeholder="Price (Numeric)" required></div>
                <div class="col"><input type="number" name="quantity" class="form-control mb-2" placeholder="Quantity (Numeric)" required></div>
            </div>
            <button type="submit" name="add_item" class="btn btn-primary">Save Item</button>
        </form>
    </div>

    <h4>My Inventory</h4>
    <table class="table table-bordered bg-white">
        <tr><th>Name</th><th>Desc</th><th>Price</th><th>Qty</th><th>Actions</th></tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= htmlspecialchars($item['description']) ?></td>
            <td>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    $<input type="number" step="0.01" name="new_price" value="<?= $item['price'] ?>" style="width: 70px;">
            </td>
            <td>
                    <input type="number" name="new_quantity" value="<?= $item['quantity'] ?>" style="width: 60px;">
            </td>
            <td>
                    <button type="submit" name="edit_item" class="btn btn-sm btn-success">Update</button>
                </form>
                <a href="items.php?delete=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
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
