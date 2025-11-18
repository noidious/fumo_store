<?php
include 'config.php';

// Use statements for PHPMailer - must be at the top level
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Send order confirmation email using PHPMailer
 * @param string $recipientEmail - Customer email address
 * @param int $orderId - Order ID
 * @param float $totalAmount - Total order amount including shipping
 * @param array $cartItems - Array of cart items
 * @return bool - True if email sent successfully, false otherwise
 */
function sendOrderConfirmationEmail($recipientEmail, $orderId, $totalAmount, $cartItems) {
    try {
        // Include email configuration
        require_once __DIR__ . '/email_config.php';
        
        // Include PHPMailer classes
        require_once __DIR__ . '/../src/PHPMailer.php';
        require_once __DIR__ . '/../src/SMTP.php';
        require_once __DIR__ . '/../src/Exception.php';
        
        $mail = new PHPMailer(true);
        
        // Check if using built-in mail or SMTP
        if (defined('USE_MAIL') && USE_MAIL) {
            // Use PHP's built-in mail function
            $mail->isMail();
        } else {
            // Use SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            
            // Set encryption
            if (SMTP_ENCRYPTION === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port = SMTP_PORT;
        }
        
        // Sender settings
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addReplyTo(EMAIL_REPLY_TO, EMAIL_REPLY_TO_NAME);        // Recipient settings
        $mail->addAddress($recipientEmail);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - Fumo Store #' . $orderId;
        
        // Build cart items HTML
        $cartItemsHtml = '';
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $itemTotal = $item['quantity'] * $item['price'];
            $subtotal += $itemTotal;
            $cartItemsHtml .= '<tr>';
            $cartItemsHtml .= '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($item['name'] ?? 'Product') . '</td>';
            $cartItemsHtml .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $item['quantity'] . '</td>';
            $cartItemsHtml .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">₱' . number_format($item['price'], 2) . '</td>';
            $cartItemsHtml .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">₱' . number_format($itemTotal, 2) . '</td>';
            $cartItemsHtml .= '</tr>';
        }
        
        $shipping = 10.00;
        
        // Create HTML email body
        $emailBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; }
                .header { background: #6f42c1; color: white; padding: 20px; text-align: center; border-radius: 5px; }
                .content { background: white; padding: 20px; margin-top: 20px; border-radius: 5px; }
                .order-summary { margin: 20px 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #f0f0f0; padding: 12px; text-align: left; font-weight: bold; border-bottom: 2px solid #ddd; }
                .total-row { background: #f9f9f9; font-weight: bold; }
                .footer { background: #f0f0f0; padding: 20px; text-align: center; font-size: 12px; color: #666; margin-top: 20px; border-radius: 5px; }
                .button { display: inline-block; background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Order Confirmation</h1>
                    <p>Thank you for your purchase!</p>
                </div>
                
                <div class="content">
                    <h2>Order Details</h2>
                    <p><strong>Order ID:</strong> #' . $orderId . '</p>
                    <p><strong>Order Date:</strong> ' . date('F d, Y h:i A') . '</p>
                    
                    <h3>Order Items</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="text-align: center;">Quantity</th>
                                <th style="text-align: right;">Price</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $cartItemsHtml . '
                            <tr class="total-row">
                                <td colspan="3" style="padding: 10px; text-align: right;">Subtotal:</td>
                                <td style="padding: 10px; text-align: right;">₱' . number_format($subtotal, 2) . '</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" style="padding: 10px; text-align: right;">Shipping:</td>
                                <td style="padding: 10px; text-align: right;">₱' . number_format($shipping, 2) . '</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" style="padding: 10px; text-align: right; color: #6f42c1; font-size: 16px;">Total Amount:</td>
                                <td style="padding: 10px; text-align: right; color: #6f42c1; font-size: 16px;">₱' . number_format($totalAmount, 2) . '</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <h3>What\'s Next?</h3>
                    <p>Your order has been placed and is being processed. You will receive a shipping notification once your order is dispatched.</p>
                    <p>If you have any questions or concerns, please contact our support team at support@fumostore.com</p>
                    
                    <a href="https://fumostore.com/user/myorders.php" class="button">View Your Order</a>
                </div>
                
                <div class="footer">
                    <p>&copy; 2025 Fumo Store. All rights reserved.</p>
                    <p>This is an automated message, please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->Body = $emailBody;
        
        // Alternative plain text body
        $mail->AltBody = 'Order Confirmation - Order ID: #' . $orderId . ' | Total Amount: ₱' . number_format($totalAmount, 2);
        
        // Send the email
        if ($mail->send()) {
            if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
                error_log('Order confirmation email sent successfully to: ' . $recipientEmail);
            }
            return true;
        } else {
            $errorMsg = 'Order confirmation email send failed for Order #' . $orderId . ' to ' . $recipientEmail . '. Error: ' . $mail->ErrorInfo;
            error_log($errorMsg);
            if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
                error_log('Full error details: ' . print_r($mail, true));
            }
            return false;
        }
        
    } catch (Exception $e) {
        $errorMsg = 'Order confirmation email exception for Order #' . $orderId . ' to ' . $recipientEmail . '. Message: ' . $e->getMessage();
        error_log($errorMsg);
        if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
            error_log('Exception trace: ' . $e->getTraceAsString());
        }
        return false;
    }
}

/**
 * Send order status update email to customer
 * @param string $recipientEmail - Customer email address
 * @param int $orderId - Order ID
 * @param string $customerName - Customer name
 * @param string $newStatus - New order status
 * @param string $customerAddress - Shipping address
 * @param string $customerTown - Town/City
 * @param string $customerZipcode - Zipcode
 * @param string $customerPhone - Phone number
 * @param string $productDetails - Product details HTML
 * @param float $totalAmount - Total order amount
 * @param float $shipping - Shipping cost
 * @return bool - True if email sent successfully, false otherwise
 */
function sendOrderStatusUpdateEmail($recipientEmail, $orderId, $customerName, $newStatus, $customerAddress, $customerTown, $customerZipcode, $customerPhone, $productDetails, $totalAmount, $shipping) {
    try {
        // Include email configuration
        require_once __DIR__ . '/email_config.php';
        
        // Include PHPMailer classes
        require_once __DIR__ . '/../src/PHPMailer.php';
        require_once __DIR__ . '/../src/SMTP.php';
        require_once __DIR__ . '/../src/Exception.php';
        
        $mail = new PHPMailer(true);
        
        // Check if using built-in mail or SMTP
        if (defined('USE_MAIL') && USE_MAIL) {
            $mail->isMail();
        } else {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            
            if (SMTP_ENCRYPTION === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port = SMTP_PORT;
        }
        
        // Sender settings
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addReplyTo(EMAIL_REPLY_TO, EMAIL_REPLY_TO_NAME);
        
        // Recipient
        $mail->addAddress($recipientEmail, $customerName);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Order Status Update - Fumo Store #' . $orderId;
        
        // Status badge color based on status
        $statusColor = '#FFC107';
        switch (strtolower($newStatus)) {
            case 'delivered':
                $statusColor = '#28A745';
                break;
            case 'shipped':
                $statusColor = '#007BFF';
                break;
            case 'processing':
                $statusColor = '#FF9800';
                break;
            case 'cancelled':
                $statusColor = '#DC3545';
                break;
        }
        
        // Create HTML email body
        $emailBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; }
                .header { background: #6f42c1; color: white; padding: 20px; text-align: center; border-radius: 5px; }
                .content { background: white; padding: 20px; margin-top: 20px; border-radius: 5px; }
                .status-badge { display: inline-block; background: ' . $statusColor . '; color: white; padding: 10px 15px; border-radius: 5px; font-weight: bold; font-size: 16px; margin: 15px 0; }
                .order-details { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .order-details p { margin: 8px 0; }
                .footer { background: #f0f0f0; padding: 20px; text-align: center; font-size: 12px; color: #666; margin-top: 20px; border-radius: 5px; }
                .button { display: inline-block; background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Order Status Update</h1>
                </div>
                
                <div class="content">
                    <h2>Hello ' . htmlspecialchars($customerName) . ',</h2>
                    <p>Your order status has been updated!</p>
                    
                    <div class="status-badge">' . htmlspecialchars(ucfirst($newStatus)) . '</div>
                    
                    <div class="order-details">
                        <h3>Order Information</h3>
                        <p><strong>Order ID:</strong> #' . $orderId . '</p>
                        <p><strong>Order Status:</strong> ' . htmlspecialchars(ucfirst($newStatus)) . '</p>
                        <p><strong>Total Amount:</strong> ₱' . number_format($totalAmount, 2) . '</p>
                        <p><strong>Shipping Cost:</strong> ₱' . number_format($shipping, 2) . '</p>
                    </div>
                    
                    <div class="order-details">
                        <h3>Delivery Address</h3>
                        <p>' . htmlspecialchars($customerAddress) . '</p>
                        <p>' . htmlspecialchars($customerTown . ', ' . $customerZipcode) . '</p>
                        <p><strong>Phone:</strong> ' . htmlspecialchars($customerPhone) . '</p>
                    </div>
                    
                    <div class="order-details">
                        <h3>Products Ordered</h3>
                        <p>' . $productDetails . '</p>
                    </div>
                    
                    <p>If you have any questions, please contact our support team at ' . EMAIL_REPLY_TO . '</p>
                    
                    <a href="https://fumostore.com/user/myorders.php" class="button">View Your Orders</a>
                </div>
                
                <div class="footer">
                    <p>&copy; 2025 Fumo Store. All rights reserved.</p>
                    <p>This is an automated message, please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->Body = $emailBody;
        $mail->AltBody = 'Order Status Update - Order ID: #' . $orderId . ' | Status: ' . htmlspecialchars(ucfirst($newStatus));
        
        // Send the email
        if ($mail->send()) {
            if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
                error_log('Order status update email sent successfully to: ' . $recipientEmail . ' (Order #' . $orderId . ', Status: ' . $newStatus . ')');
            }
            return true;
        } else {
            $errorMsg = 'Order status email send failed for Order #' . $orderId . ' to ' . $recipientEmail . ' (Status: ' . $newStatus . '). Error: ' . $mail->ErrorInfo;
            error_log($errorMsg);
            if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
                error_log('Full error details: ' . print_r($mail, true));
            }
            return false;
        }
        
    } catch (Exception $e) {
        $errorMsg = 'Order status email exception for Order #' . $orderId . ' to ' . $recipientEmail . '. Message: ' . $e->getMessage();
        error_log($errorMsg);
        if (defined('EMAIL_DEBUG') && EMAIL_DEBUG) {
            error_log('Exception trace: ' . $e->getTraceAsString());
        }
        return false;
    }
}
?>
