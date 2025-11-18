<?php
session_start();
include('includes/header.php');
include('includes/config.php');
include('includes/functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to continue with checkout.";
    header("Location: user/login.php");
    exit;
}

// Check if cart is not empty
if (empty($_SESSION['cart_products'])) {
    $_SESSION['error'] = "Your cart is empty. Please add products to your cart before checking out.";
    header("Location: view_cart.php");
    exit;
}

// Check if checkout form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    try {
        // Start a transaction
        mysqli_query($conn, 'START TRANSACTION');

        // Get the customer ID associated with the logged-in user
        $sql = "SELECT customer_id FROM customer WHERE user_id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows === 0) {
            // Auto-create a customer record if it doesn't exist
            $insert_sql = "INSERT INTO customer (user_id, fname, lname, title, address, town, zipcode, phone) VALUES (?, 'User', 'Guest', '', '', '', '', '')";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, 'i', $_SESSION['user_id']);
            if (!mysqli_stmt_execute($insert_stmt)) {
                throw new Exception("Failed to create customer profile: " . mysqli_stmt_error($insert_stmt));
            }
            $customer_id = mysqli_insert_id($conn);
            mysqli_stmt_close($insert_stmt);
        } else {
            $customer = mysqli_fetch_assoc($result);
            $customer_id = $customer['customer_id'];
        }
        mysqli_stmt_close($stmt);

        // Calculate the total amount from the cart
        $total_amount = 0.00;
        foreach ($_SESSION['cart_products'] as $product_id => $item) {
            $total_amount += $item['quantity'] * $item['price'];
        }

        // Add shipping cost
        $shipping = 10.00;
        $total_with_shipping = $total_amount + $shipping;

        // Insert the order into 'orders' table with total INCLUDING shipping
        $order_query = 'INSERT INTO orders (customer_id, order_date, total, status, shipping) VALUES (?, NOW(), ?, "pending", ?)';
        $order_stmt = mysqli_prepare($conn, $order_query);
        mysqli_stmt_bind_param($order_stmt, 'idi', $customer_id, $total_with_shipping, $shipping);
        mysqli_stmt_execute($order_stmt);
        $order_id = mysqli_insert_id($conn);

        // Insert order info into 'orderinfo' table
        $orderinfo_query = 'INSERT INTO orderinfo (customer_id, date_placed, date_shipped, shipping) VALUES (?, NOW(), NOW(), ?)';
        $orderinfo_stmt = mysqli_prepare($conn, $orderinfo_query);
        mysqli_stmt_bind_param($orderinfo_stmt, 'id', $customer_id, $shipping);
        mysqli_stmt_execute($orderinfo_stmt);

        // Prepare statements for adding order details and updating stock
        $orderline_query = 'INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)';
        $orderline_stmt = mysqli_prepare($conn, $orderline_query);
        mysqli_stmt_bind_param($orderline_stmt, 'iiid', $order_id, $product_id, $quantity, $price);

        $stock_update_query = 'UPDATE products SET stock = stock - ? WHERE product_id = ?';
        $stock_stmt = mysqli_prepare($conn, $stock_update_query);
        mysqli_stmt_bind_param($stock_stmt, 'ii', $quantity, $product_id);

        // Loop through cart products
        foreach ($_SESSION['cart_products'] as $product_id => $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];

            if (empty($product_id) || empty($quantity)) {
                throw new Exception("Missing product ID or quantity in cart.");
            }

            mysqli_stmt_execute($orderline_stmt);

            if (!mysqli_stmt_execute($stock_stmt)) {
                throw new Exception("Failed to update stock for product ID: $product_id");
            }
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Send order confirmation email
        $userEmail = $_SESSION['email'];
        $emailSent = sendOrderConfirmationEmail($userEmail, $order_id, $total_with_shipping, $_SESSION['cart_products']);
        
        // Log email status (optional)
        if ($emailSent) {
            error_log("Order confirmation email sent successfully to: " . $userEmail);
        } else {
            error_log("Order confirmation email failed to send to: " . $userEmail);
        }

        // Clear cart
        unset($_SESSION['cart_products']);

        // Set success message
        $_SESSION['success'] = "Checkout successful! Your order has been placed. Thank you for shopping with Fumo Store!";
        header("Location: user/myorders.php");
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $_SESSION['error'] = "Checkout failed: " . $e->getMessage();
        header("Location: view_cart.php");
        exit;

    } finally {
        // Close statements
        if (isset($stmt)) mysqli_stmt_close($stmt);
        if (isset($order_stmt)) mysqli_stmt_close($order_stmt);
        if (isset($orderinfo_stmt)) mysqli_stmt_close($orderinfo_stmt);
        if (isset($orderline_stmt)) mysqli_stmt_close($orderline_stmt);
        if (isset($stock_stmt)) mysqli_stmt_close($stock_stmt);
    }
}

// Calculate order totals for display
$subtotal = 0;
foreach ($_SESSION['cart_products'] as $item) {
    $subtotal += $item['quantity'] * $item['price'];
}
$shipping = 10.00;
$total = $subtotal + $shipping;

?>

<div class="checkout-container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <h1>Checkout</h1>
    
    <div class="checkout-form" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>Order Summary</h2>
        <div class="order-summary" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span>Subtotal:</span>
                <span>₱<?= number_format($subtotal, 2); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span>Shipping:</span>
                <span>₱<?= number_format($shipping, 2); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px; font-size: 18px; color: #6f42c1;">
                <span><strong>Total Amount:</strong></span>
                <span><strong>₱<?= number_format($total, 2); ?></strong></span>
            </div>
        </div>

        <form method="POST" action="checkout.php">
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                <a href="view_cart.php" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none;">Back to Cart</a>
                <button type="submit" name="confirm_checkout" style="padding: 10px 20px; background-color: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Confirm Checkout</button>
            </div>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>