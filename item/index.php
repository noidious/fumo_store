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
    echo "<p>You do not have permission to view this page.</p>";
    exit;
}

// Check if a search term is provided
if (isset($_GET['search'])) {
    $keyword = strtolower(trim($_GET['search']));
} else {
    $keyword = '';
}

// Prepare the SQL query based on the search keyword
if ($keyword) {
    $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$keyword}%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
}

// Get the total number of items
$itemCount = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Fumo Store Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f5f5f5;
            padding-top: 0;
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
        .main-container {
            background: #fff;
            margin: 30px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }
        .container-title {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card img {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            max-height: 200px;
            width: 100%;
            object-fit: contain;
        }
        .btn-primary {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        .btn-primary:hover {
            background-color: #00008B;
            border-color: #00008B;
            color: white;
        }
        .btn-outline-primary {
            color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-outline-primary:hover {
            background-color: #6f42c1;
            color: #fff;
        }
        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="../admin/admin_dashboard.php">Fumo Store Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="../item/index.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/admin_orders.php">Orders</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/users.php">Users</a>
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
<div class="main-container">
    <h2 class="container-title">Number of items: <?= $itemCount ?> </h2>
    <a href="create.php" class="btn btn-primary btn-lg mb-4" role="button">Add Products</a>
    <div class="row">
        <?php
        // Check if there are any results
        if ($itemCount > 0) {
            // Loop through and display each product
            while ($row = $result->fetch_assoc()) {
        ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <img src="<?= $row['image'] ?>" class="card-img-top" alt="Product Image">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                    <p class="card-text"><strong>Price:</strong> <?= htmlspecialchars($row['price']) ?></p>
                    <p class="card-text"><strong>Stock:</strong> <?= htmlspecialchars($row['stock']) ?></p>
                    <div class="d-flex justify-content-between">
                        <a href="edit.php?id=<?= $row['product_id'] ?>" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                        <a href="delete.php?id=<?= $row['product_id'] ?>" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
