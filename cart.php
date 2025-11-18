<?php
session_start();
include 'includes/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to add items to your cart.";
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: user/login.php");
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION["cart_products"])) {
    $_SESSION["cart_products"] = [];
}

// Ensure product ID and quantity are set
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Validate quantity
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Fetch product details
    $stmt = $conn->prepare("SELECT product_id, name, description, price, stock, image FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Check stock availability
        if ($quantity > $product['stock']) {
            $_SESSION['message'] = "Only " . $product['stock'] . " items available in stock.";
            header("Location: index.php");
            exit();
        }

        // Check if the product is already in the cart
        if (isset($_SESSION["cart_products"][$product_id])) {
            // Update the quantity if the product is already in the cart
            $_SESSION["cart_products"][$product_id]['quantity'] += $quantity;
        } else {
            // Add the product to the cart
            $_SESSION["cart_products"][$product_id] = [
                "name" => $product['name'],
                "description" => $product['description'],
                "price" => $product['price'],
                "stock" => $product['stock'],
                "image" => $product['image'],
                "quantity" => $quantity
            ];
        }

        $_SESSION['message'] = $product['name'] . " added to cart!";
        header("Location: view_cart.php");
        exit();
    } else {
        $_SESSION['message'] = "Product not found.";
    }
} else {
    $_SESSION['message'] = "Invalid product.";
}

header("Location: index.php");
exit();
?>
