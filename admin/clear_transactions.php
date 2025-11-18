<?php
session_start();
include('../includes/config.php');

// Check if user is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if ($user_data['role'] !== 'admin') {
    echo "<p>You do not have permission to perform this action.</p>";
    exit;
}

// Check if confirmation is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        // Disable foreign key checks temporarily
        $conn->query("SET FOREIGN_KEY_CHECKS=0");

        // Delete all transaction data while keeping table structure intact
        // 1. Delete order details records (line items in orders)
        $result1 = $conn->query("DELETE FROM order_details");
        
        // 2. Delete order records
        $result2 = $conn->query("DELETE FROM orders");
        
        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        
        // Verify deletions were successful
        if ($result1 === false || $result2 === false) {
            throw new Exception("Failed to delete transaction data");
        }

        $_SESSION['success'] = "All transaction data has been cleared successfully.";
        header("Location: admin_orders.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error clearing transaction data: " . $e->getMessage();
        header("Location: admin_orders.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Clear Transaction Data - Fumo Store Admin</title>
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-danger mb-4">⚠️ Clear All Transaction Data</h2>
    
    <div class="alert alert-warning" role="alert">
        <strong>Warning!</strong> This action will permanently delete all orders and transaction records. This cannot be undone!
    </div>

    <form method="POST">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="confirmCheckbox" name="confirm" value="yes" required>
            <label class="form-check-label" for="confirmCheckbox">
                I understand this will delete all transaction data permanently.
            </label>
        </div>

        <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
            <a href="admin_orders.php" class="btn btn-secondary btn-sm">Cancel</a>
            <button type="submit" class="btn btn-danger btn-sm" disabled id="submitBtn">Clear All Data</button>
        </div>
    </form>
</div>

<script>
    // Enable submit button only when checkbox is checked
    document.getElementById('confirmCheckbox').addEventListener('change', function() {
        document.getElementById('submitBtn').disabled = !this.checked;
    });
</script>
</body>
</html>
