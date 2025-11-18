<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the base path for navigation links based on current file location
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$is_user_page = strpos($current_dir, '/user') !== false;
$is_admin_page = strpos($current_dir, '/admin') !== false;
$is_item_page = strpos($current_dir, '/item') !== false;

// Set base href for navigation
if ($is_user_page) {
    $home_href = '../index.php';
    $profile_href = 'profile.php';
    $myorders_href = 'myorders.php';
    $cart_href = '../view_cart.php';
    $search_action = '../search.php';
    $item_href = '../item/index.php';
    $admin_orders = '../admin/admin_orders.php';
    $admin_users = '../admin/users.php';
} elseif ($is_admin_page) {
    $home_href = '../index.php';
    $item_href = '../item/index.php';
    $admin_orders = 'admin_orders.php';
    $admin_users = 'users.php';
    $profile_href = '../user/profile.php';
    $myorders_href = '../user/myorders.php';
    $cart_href = '../view_cart.php';
    $search_action = '../search.php';
} elseif ($is_item_page) {
    $home_href = '../index.php';
    $item_href = 'index.php';
    $admin_orders = '../admin/admin_orders.php';
    $admin_users = '../admin/users.php';
    $profile_href = '../user/profile.php';
    $myorders_href = '../user/myorders.php';
    $cart_href = '../view_cart.php';
    $search_action = '../search.php';
} else {
    // Main index.php
    $home_href = 'index.php';
    $profile_href = 'user/profile.php';
    $myorders_href = 'user/myorders.php';
    $cart_href = 'view_cart.php';
    $search_action = 'search.php';
    $item_href = 'item/index.php';
    $admin_orders = 'admin/admin_orders.php';
    $admin_users = 'admin/users.php';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="includes/style/style.css" rel="stylesheet" type="text/css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <title>Fumo Store</title>
</head>

<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <!-- Logo and Name -->
        <a class="navbar-brand" href="<?php echo $home_href; ?>">
            <img src="../item/images/fumo-logo.png" alt="Fumo Store" class="logo" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?php echo $home_href; ?>">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Menu
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo $item_href; ?>">Item</a></li>
                                <li><a class="dropdown-item" href="<?php echo $admin_orders; ?>">Orders</a></li>
                                <li><a class="dropdown-item" href="<?php echo $admin_users; ?>">Users</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?php echo $profile_href; ?>">Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo $myorders_href; ?>">My Orders</a></li>
                            <?php endif; ?>
                            <!-- Add View Cart option here -->
                            <li><a class="dropdown-item" href="<?php echo $cart_href; ?>">View Cart</a></li>
                        </ul>
                    <?php endif; ?>
                </li>
            </ul>
            <form action="<?php echo $search_action; ?>" method="GET" class="d-flex">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search">
                <button class="btn btn-blue my-2 my-sm-0" type="submit">
                     <i class="fas fa-search"></i>
                </button>


            </form>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="navbar-nav ms-auto">
                    <a href="<?php echo ($is_user_page || $is_admin_page || $is_item_page) ? '../' : ''; ?>user/login.php" class="nav-item nav-link">Login</a>
                </div>
            <?php else: ?>
                <li class="nav-item">
                    <p><?= htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                </li>
                <div class="navbar-nav ms-auto">
                    <a href="<?php echo ($is_user_page || $is_admin_page || $is_item_page) ? '../' : ''; ?>user/logout.php" class="nav-item nav-link">Logout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>