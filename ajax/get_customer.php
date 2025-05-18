<?php
require_once '../config/app.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Debug için
error_log("Get customer called with: " . print_r($_GET, true));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$customer_id = intval($_GET['id'] ?? 0);

if (!$customer_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
    exit;
}

try {
    $db = new Database();
    
    $sql = "SELECT id, first_name, last_name, phone, email, address, notes
           FROM customers 
           WHERE id = ?";
    
    $customer_data = $db->fetchRow($sql, [$customer_id]);
    
    if ($customer_data) {
        echo json_encode([
            'success' => true,
            'customer' => $customer_data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Get customer error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>