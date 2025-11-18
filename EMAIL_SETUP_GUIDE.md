# Fumo Store Email Configuration Guide

## Overview
The email system has been configured to support both SMTP and PHP's built-in `mail()` function. For best results on XAMPP, we recommend using the PHP `mail()` function.

## Current Configuration
- **File**: `includes/email_config.php`
- **Current Mode**: PHP's `mail()` function (USE_MAIL = true)
- **Email Debug**: Enabled (EMAIL_DEBUG = true)

## Setup Options

### Option 1: Using PHP mail() Function (RECOMMENDED for XAMPP)

This is the simplest setup and works out of the box on XAMPP.

**No additional configuration needed!** The system is already set to use this mode.

#### What happens:
- Emails are sent using PHP's built-in `mail()` function
- On Windows XAMPP, ensure your `php.ini` is configured with a mail relay service

#### Check if it's working:
1. Place an order or update an order status
2. Check the PHP error log at: `apache/logs/error.log` or `php_error.log`
3. You should see log messages indicating email success/failure

---

### Option 2: Using Gmail SMTP

If you want to use Gmail's SMTP server:

#### Step 1: Enable 2-Factor Authentication in Gmail
1. Go to https://myaccount.google.com/
2. Navigate to Security → 2-Step Verification
3. Complete the setup

#### Step 2: Generate App Password
1. Go to https://myaccount.google.com/apppasswords
2. Select "Mail" and "Windows Computer"
3. Google will generate a 16-character password
4. **Copy this password** (you'll use it in the next step)

#### Step 3: Update `includes/email_config.php`
```php
define('USE_MAIL', false);  // Set to false to use SMTP

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-gmail@gmail.com');
define('SMTP_PASSWORD', 'xxxx xxxx xxxx xxxx');  // Paste the 16-char password here
define('SMTP_ENCRYPTION', 'tls');
```

#### Step 4: Test
- Update an order status in admin
- Check if the customer receives an email

---

### Option 3: Using Custom SMTP Server

Edit `includes/email_config.php` and update:
```php
define('USE_MAIL', false);

define('SMTP_HOST', 'your-smtp-server.com');
define('SMTP_PORT', 587);  // Usually 587 or 465
define('SMTP_USERNAME', 'your-username');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_ENCRYPTION', 'tls');  // or 'ssl'
```

---

## Email Settings

All email settings are defined in `includes/email_config.php`:

| Setting | Value | Notes |
|---------|-------|-------|
| EMAIL_FROM_ADDRESS | noreply@fumostore.com | Sender address (can be any domain) |
| EMAIL_FROM_NAME | Fumo Store | Display name |
| EMAIL_REPLY_TO | support@fumostore.com | Reply-to address |
| EMAIL_DEBUG | true | Set to false to disable detailed logging |

---

## Where Emails Are Sent

### Customer Receives Emails When:
1. **Order Confirmation** - After placing an order in `checkout.php`
2. **Order Status Update** - When admin updates order status in `admin/admin_orders.php`

### What's In the Emails:
- Order ID and date
- Product details with quantities and prices
- Shipping cost and total
- Delivery address
- Status badge (for status updates)
- Link to view orders

---

## Troubleshooting

### "Email notification could not be sent" Message

**Step 1: Check Error Logs**
- Open `apache/logs/error.log` in your XAMPP directory
- Look for entries with "email" or "mail"
- Common errors:
  - `Failed HELO command`
  - `SMTP connect() failed`
  - `Authentication failed`

**Step 2: Verify Configuration**
- Confirm SMTP credentials are correct in `email_config.php`
- Ensure `USE_MAIL` setting matches your chosen method

**Step 3: If Using Gmail**
- Verify you're using an app-specific password (not your Gmail password)
- Check that 2-Factor Authentication is enabled
- Ensure the app password is entered correctly (with or without spaces)

**Step 4: If Using mail() Function**
- Check that `USE_MAIL` is set to `true`
- Verify Windows Mail is configured on your system
- Or configure `sendmail` in php.ini

---

## Email Logs

When `EMAIL_DEBUG` is enabled, detailed logs are written to the PHP error log. This includes:
- Successful email sends with recipient addresses
- Failed sends with error reasons
- Full exception traces for debugging

**Location**: Check your XAMPP PHP error log file

---

## Email Functions Available

### For Developers

Two main email functions are available in `includes/functions.php`:

```php
// Send order confirmation email
sendOrderConfirmationEmail($email, $orderId, $totalAmount, $cartItems)

// Send order status update email
sendOrderStatusUpdateEmail($email, $orderId, $customerName, $newStatus, $address, $town, $zipcode, $phone, $products, $total, $shipping)
```

Both return `true` on success, `false` on failure.

---

## Testing Your Setup

### Manual Test
1. Open admin/admin_orders.php
2. Find any order
3. Change the status
4. Check if the customer email receives the email

### Debug Mode
When `EMAIL_DEBUG = true`:
1. Open the PHP error log
2. Look for detailed error messages
3. These logs help identify configuration issues

---

## Questions?

Check the following files:
- `includes/email_config.php` - Configuration file
- `includes/functions.php` - Email functions
- `admin/admin_orders.php` - Admin email sending
- `checkout.php` - Customer email sending
