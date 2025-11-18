<?php
session_start();
include('../includes/config.php');

// Verify admin access
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

// Ensure the product ID is passed in the URL
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Product ID not provided.";
    header("Location: index.php");
    exit();
}

$productId = intval($_GET['id']); // Ensure the ID is an integer

// Fetch product details from the database
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['message'] = "Product not found.";
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    // Get form data
    $productName = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = $_FILES['image']['name'];
    
    // Handle main product image upload
    $imagePath = $product['image']; // Default to the old image if no new image is uploaded

    // Check if a new product image is uploaded
    if ($image) {
        // Validate file type (e.g., jpeg, png)
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = $_FILES['image']['type'];

        if (in_array($fileType, $allowedTypes)) {
            // Validate file size (max 5 MB)
            if ($_FILES['image']['size'] <= 5000000) { // 5 MB max size
                // Define the image directory
                $imageDir = '../item/images';

                // Check if the image directory is writable
                if (is_writable($imageDir)) {
                    // Sanitize and create a unique filename for the image
                    $imageFileName = time() . '_' . basename($image); // Prefix with timestamp to ensure uniqueness
                    $imagePath = $imageDir . '/' . $imageFileName;

                    // Move the uploaded file to the server directory
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                        $_SESSION['message'] = "Error uploading the image.";
                        header("Location: edit.php?id=$productId");
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "The directory is not writable.";
                    header("Location: edit.php?id=$productId");
                    exit();
                }
            } else {
                $_SESSION['message'] = "File size exceeds the maximum allowed size of 5 MB.";
                header("Location: edit.php?id=$productId");
                exit();
            }
        } else {
            $_SESSION['message'] = "Invalid file type. Only JPEG and PNG files are allowed.";
            header("Location: edit.php?id=$productId");
            exit();
        }
    }

    // Update product details in the database
    $updateSql = "UPDATE products SET 
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    stock = ?, 
                    image = ? 
                  WHERE product_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssdiss", $productName, $description, $price, $stock, $imagePath, $productId);

    if ($stmt->execute()) {
        // If product updated successfully, handle additional image uploads
        if (isset($_FILES['additional_image']) && !empty($_FILES['additional_image']['name'])) {
            $additionalImage = $_FILES['additional_image'];
            
            // Validate additional image
            $imageName = $additionalImage['name'];
            $fileTmpName = $additionalImage['tmp_name'];
            $allowedTypes = ['image/jpeg', 'image/png'];
            $fileType = $additionalImage['type'];

            if (in_array($fileType, $allowedTypes)) {
                // Validate file size (max 5 MB)
                if ($additionalImage['size'] <= 5000000) { // 5 MB max size
                    $imageFileName = time() . '_' . basename($imageName); // Prefix with timestamp to ensure uniqueness
                    $imagePath = '../item/images/' . $imageFileName;

                    // Move the uploaded file to the server directory
                    if (move_uploaded_file($fileTmpName, $imagePath)) {
                        // Insert new image record into product_images table
                        $insertImageSql = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
                        $stmt = $conn->prepare($insertImageSql);
                        $stmt->bind_param("is", $productId, $imagePath);
                        $stmt->execute();
                    } else {
                        $_SESSION['message'] = "Error uploading additional image.";
                        header("Location: edit.php?id=$productId");
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "Additional image file size exceeds the maximum allowed size of 5 MB.";
                    header("Location: edit.php?id=$productId");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Invalid file type for additional image. Only JPEG and PNG files are allowed.";
                header("Location: edit.php?id=$productId");
                exit();
            }
        }

        $_SESSION['message'] = "Product updated successfully.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Error updating product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Fumo Store Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f5f5f5;
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
        .container {
            background-color: white;
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: Arial, sans-serif;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .btn-primary {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-primary:hover {
            background-color: #00008B;
            border-color: #00008B;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #cce5ff;
            border-radius: 4px;
            background-color: #d1ecf1;
            color: #0c5460;
        }
        img {
            margin: 10px 0;
            border-radius: 4px;
        }
        small {
            display: block;
            color: #666;
            margin-bottom: 10px;
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
          <a class="nav-link" href="index.php">Products</a>
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

<body>
    <div class="container">
        <h2>Edit Product</h2>

        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Edit Product Form -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>" required>

                <label for="description">Product Description</label>
                <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($product['description'], ENT_QUOTES) ?></textarea>

                <label for="price">Product Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price'], ENT_QUOTES) ?>" required>

                <label for="stock">Product Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($product['stock'], ENT_QUOTES) ?>" required>

                <label for="image">Product Thumbnail</label>
                <input type="file" class="form-control" id="image" name="image">
                <small>Leave empty to keep the current image</small><br>

                <!-- Display current image -->
                <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>" width="150" height="150" alt="Current Product Image" /><br>

                <label for="additional_image">Upload Additional Image</label>
                <input type="file" class="form-control" id="additional_image" name="additional_image" multiple><br>

                <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>

    <a href="index.php" class="btn btn-secondary">Cancel</a>

</body>
</html>
