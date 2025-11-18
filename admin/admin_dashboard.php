<?php
session_start();
include('../includes/config.php');

// Ensure the user is logged in and has an admin role
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user information to verify their role
$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if ($user_data['role'] !== 'admin') {
    echo "<p>You do not have permission to view this page.</p>";
    exit;
}

// Get dashboard statistics
$total_orders_query = "SELECT COUNT(*) as total_orders FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders = mysqli_fetch_assoc($total_orders_result)['total_orders'];

$total_users_query = "SELECT COUNT(*) as total_users FROM users WHERE role='user'";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

$total_products_query = "SELECT COUNT(*) as total_products FROM products";
$total_products_result = mysqli_query($conn, $total_products_query);
$total_products = mysqli_fetch_assoc($total_products_result)['total_products'];

$total_revenue_query = "SELECT SUM(total) as total_revenue FROM orders WHERE status IN ('completed', 'shipped', 'Delivered')";
$total_revenue_result = mysqli_query($conn, $total_revenue_query);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['total_revenue'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fumo Store Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px 0;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #6f42c1;
        }
        .stat-card h3 {
            color: #6f42c1;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        .stat-card .stat-icon {
            font-size: 40px;
            color: #6f42c1;
            opacity: 0.2;
            float: right;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .quick-links {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .quick-links h3 {
            margin-bottom: 20px;
            color: #333;
        }
        .quick-link-btn {
            display: block;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }
        .quick-link-btn:hover {
            background-color: #6f42c1;
            color: white;
            border-color: #6f42c1;
        }
        .quick-link-btn i {
            margin-right: 10px;
            width: 20px;
        }
        .page-title {
            margin-bottom: 30px;
        }
        .page-title h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .page-title p {
            color: #666;
            margin: 0;
        }
        .navbar {
            background-color: transparent;
        }
        .navbar-brand, .nav-link, .btn {
            color: black !important;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #d6bbfc !important;
        }
        .btn-outline-light {
            background-color: #6f42c1;
            color: black;
            border: none;
        }
        .btn-outline-light:hover {
            background-color: #00008B;
            color: white;
            border: none;
        }
        .btn-outline-light i {
            color: black;
        }
        .btn-outline-light:hover i {
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php">Fumo Store Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="../item/index.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin_orders.php">Orders</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="users.php">Users</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link text-danger" href="../user/logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="dashboard-container">
    <div class="page-title">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user_data['email']); ?>!</p>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card">
            <i class="fas fa-shopping-cart stat-icon"></i>
            <h3>Total Orders</h3>
            <div class="stat-value"><?php echo $total_orders; ?></div>
        </div>

        <div class="stat-card">
            <i class="fas fa-users stat-icon"></i>
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo $total_users; ?></div>
        </div>

        <div class="stat-card">
            <i class="fas fa-box stat-icon"></i>
            <h3>Total Products</h3>
            <div class="stat-value"><?php echo $total_products; ?></div>
        </div>

        <div class="stat-card">
            <i class="fas fa-dollar-sign stat-icon"></i>
            <h3>Total Revenue</h3>
            <div class="stat-value">₱<?php echo number_format($total_revenue, 2); ?></div>
        </div>
    </div>

    <div class="quick-links">
        <h3>Quick Links</h3>
        <a href="admin_orders.php" class="quick-link-btn">
            <i class="fas fa-list"></i> View Orders
        </a>
        <a href="users.php" class="quick-link-btn">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="../item/index.php" class="quick-link-btn">
            <i class="fas fa-box"></i> Manage Products
        </a>
    </div>
</div>

<footer style="background-color: #f8f9fa; padding: 20px; margin-top: 40px; text-align: center; color: #666;">
    <p>&copy; 2025 Fumo Store. All rights reserved.</p>
</footer>

</body>
</html>
