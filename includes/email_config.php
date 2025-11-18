<?php
/**
 * Email Configuration for Fumo Store
 * Configure your email settings here
 */

// SMTP Configuration
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');              // Your SMTP server
define('SMTP_PORT', 587);                           // SMTP port (usually 587 for TLS, 465 for SSL)
define('SMTP_USERNAME', '785caa5f30198b');     // Your email address
define('SMTP_PASSWORD', '95412ea42c39f1');       // Your email password or app-specific password
define('SMTP_ENCRYPTION', 'tls');                   // Encryption type: tls, ssl, or none

// Email From Settings
define('EMAIL_FROM_ADDRESS', 'noreply@fumostore.com');
define('EMAIL_FROM_NAME', 'Fumo Store');

// Reply-To Settings
define('EMAIL_REPLY_TO', 'support@fumostore.com');
define('EMAIL_REPLY_TO_NAME', 'Fumo Store Support');

// Alternative: Use PHP's built-in mail() function (set USE_MAIL to true)
// Note: This requires your server to have mail functionality configured
define('USE_MAIL', false);

?>
