<?php
session_start();
include('../includes/config.php');

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Ensure the ID is an integer

    // Validate the product ID
    if ($product_id <= 0) {
        $_SESSION['error'] = "Invalid product ID.";
        header("Location: index.php");
        exit();
    }

    // Retrieve the product's image to delete it from the server
    $query = "SELECT image FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $imagePath = $product['image'];

        // Disable foreign key checks temporarily
        $conn->query("SET FOREIGN_KEY_CHECKS=0");

        // First, delete related order details to avoid foreign key constraint violation
        $deleteOrderDetailsQuery = "DELETE FROM order_details WHERE product_id = ?";
        $deleteOrderDetailsStmt = $conn->prepare($deleteOrderDetailsQuery);
        $deleteOrderDetailsStmt->bind_param("i", $product_id);
        if (!$deleteOrderDetailsStmt->execute()) {
            $_SESSION['error'] = "Error deleting order details: " . $deleteOrderDetailsStmt->error;
            $conn->query("SET FOREIGN_KEY_CHECKS=1");
            header("Location: index.php");
            exit();
        }
        $deleteOrderDetailsStmt->close();

        // Second, retrieve and delete product images to avoid foreign key constraint violation
        $getProductImagesQuery = "SELECT image FROM product_images WHERE product_id = ?";
        $getProductImagesStmt = $conn->prepare($getProductImagesQuery);
        $getProductImagesStmt->bind_param("i", $product_id);
        $getProductImagesStmt->execute();
        $imagesResult = $getProductImagesStmt->get_result();
        
        // Delete image files from server first
        while ($imageRow = $imagesResult->fetch_assoc()) {
            if (file_exists($imageRow['image'])) {
                unlink($imageRow['image']);
            }
        }
        $getProductImagesStmt->close();

        // Delete product images from database
        $deleteProductImagesQuery = "DELETE FROM product_images WHERE product_id = ?";
        $deleteProductImagesStmt = $conn->prepare($deleteProductImagesQuery);
        $deleteProductImagesStmt->bind_param("i", $product_id);
        if (!$deleteProductImagesStmt->execute()) {
            $_SESSION['error'] = "Error deleting product images: " . $deleteProductImagesStmt->error;
            $conn->query("SET FOREIGN_KEY_CHECKS=1");
            header("Location: index.php");
            exit();
        }
        $deleteProductImagesStmt->close();

        // Then, delete the product from the database
        $deleteQuery = "DELETE FROM products WHERE product_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $product_id);

        if ($deleteStmt->execute()) {
            // Check if the main product image file exists and delete it
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $_SESSION['success'] = "Product deleted successfully.";
            
            // Re-enable foreign key checks
            $conn->query("SET FOREIGN_KEY_CHECKS=1");
            
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to delete the product. Please try again.";
            $conn->query("SET FOREIGN_KEY_CHECKS=1");
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Product not found.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "No product ID provided.";
    header("Location: index.php");
    exit();
}
?>
