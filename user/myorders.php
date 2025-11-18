<?php
// Include the header
include '../includes/header.php';
include '../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view your orders.";
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = (int)$_SESSION['user_id'];

// SQL query to fetch orders with product names and reviews
$query = "
    SELECT 
        o.order_id, 
        o.order_date, 
        o.total, 
        o.status, 
        od.product_id, 
        p.name AS product_name,
        r.review_id
    FROM 
        orders o
    JOIN 
        order_details od ON o.order_id = od.order_id
    JOIN 
        products p ON od.product_id = p.product_id
    LEFT JOIN 
        review r ON r.product_id = od.product_id AND r.customer_id = o.customer_id
    WHERE 
        o.customer_id = (SELECT customer_id FROM customer WHERE user_id = ?)
    ORDER BY 
        o.order_date DESC
";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
        /* General Styles */
        .orders-container {
            margin: 40px auto;
            padding: 20px;
            max-width: 1200px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .orders-container h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Table Styles */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .orders-table th, .orders-table td {
            padding: 12px 15px;
            text-align: left;
        }
        .orders-table th {
            background: linear-gradient(90deg, #6a0dad, #4b6cb7);
            color: white;
            border-bottom: 2px solid #4b6cb7;
        }
        .orders-table tr:nth-child(even) {
            background-color: #f9f7ff;
        }
        .orders-table tr:hover {
            background-color: #e6e1f7;
        }
        .orders-table td {
            border-bottom: 1px solid #ddd;
        }

        /* Links and Buttons */
        .orders-table a {
            color: #4b6cb7;
            text-decoration: none;
            font-weight: bold;
        }
        .orders-table a:hover {
            color: #6a0dad;
            text-decoration: underline;
        }
        .orders-table button {
            background: linear-gradient(90deg, #6a0dad, #4b6cb7);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        .orders-table button:hover {
            background: linear-gradient(90deg, #4b6cb7, #6a0dad);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #6a0dad;
            padding: 40px 0;
        }
        
        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }
        .status-completed {
            color: #4caf50;
            font-weight: bold;
        }
        .status-shipped {
            color: #2196f3;
            font-weight: bold;
        }
        .status-delivered {
            color: #4caf50;
            font-weight: bold;
        }
    </style>

<div class="orders-container">
    <h2>Your Orders</h2>
    
    <?php if (count($orders) > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td>₱<?= isset($order['total']) ? number_format($order['total'], 2) : '0.00' ?></td>
                        <td>
                            <span class="status-<?= strtolower($order['status']) ?>">
                                <?= htmlspecialchars(ucfirst($order['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($order['status'] === 'completed' || $order['status'] === 'Delivered' || strtolower($order['status']) === 'delivered'): ?>
                                <?php if (!empty($order['review_id'])): ?>
                                    <a href="review.php?order_id=<?= htmlspecialchars($order['order_id']) ?>&product_id=<?= htmlspecialchars($order['product_id']) ?>&review_id=<?= htmlspecialchars($order['review_id']) ?>">Update Review</a>
                                <?php else: ?>
                                    <a href="review.php?order_id=<?= htmlspecialchars($order['order_id']) ?>&product_id=<?= htmlspecialchars($order['product_id']) ?>">Leave a Review</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button onclick="alert('You can leave a review once the order is completed or delivered.')">Leave a Review</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-orders">You have no orders yet. <a href="../index.php">Start shopping!</a></p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
