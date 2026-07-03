<?php
/**
 * Database Connection File
 * Advanced GST Billing Management System
 */

// Database Configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'billing_system');

// GST Configuration
define('GST_RATE', 5); // 5% GST

// Application Settings
define('APP_NAME', 'Advanced Billing System');
define('CURRENCY', '₹');
define('ITEMS_PER_PAGE', 10);

// Create Database Connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Session Configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input data
 */
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Check if user is logged in
 */
function check_login() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Generate unique bill number
 */
function generate_bill_number($conn) {
    $query = "SELECT bill_number FROM bills ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_number = (int)str_replace('BILL-', '', $row['bill_number']);
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return 'BILL-' . str_pad($new_number, 4, '0', STR_PAD_LEFT);
}

/**
 * Format currency
 */
function format_currency($amount) {
    return CURRENCY . number_format($amount, 2);
}

/**
 * Calculate GST
 */
function calculate_gst($amount) {
    return ($amount * GST_RATE) / 100;
}

/**
 * Get admin name
 */
function get_admin_name() {
    return isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
}

/**
 * Alert message function
 */
function show_alert($message, $type = 'success') {
    return "<script>
        Swal.fire({
            icon: '$type',
            title: '$message',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    </script>";
}
?>