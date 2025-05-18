<?php
require_once '../config/app.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Debug için
error_log("Search customers called with: " . print_r($_GET, true));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['results' => [], 'more' => false]);
    exit;
}

$search = $_GET['search'] ?? '';
$page = intval($_GET['page'] ?? 1);
$limit = 10;

try {
    $db = new Database();
    
    $sql = "SELECT id, CONCAT(first_name, ' ', last_name) as full_name, phone, email
           FROM customers 
           WHERE first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?
           ORDER BY first_name, last_name
           LIMIT ?";
    
    $search_term = '%' . $search . '%';
    $customers = $db->fetchAll($sql, [$search_term, $search_term, $search_term, $limit + 1]);
    
    $results = [];
    $more = count($customers) > $limit;
    
    if ($more) {
        array_pop($customers); // Son elementi çıkar
    }
    
    foreach ($customers as $cust) {
        $results[] = [
            'id' => $cust['id'],
            'text' => $cust['full_name'] . ' - ' . $cust['phone']
        ];
    }
    
    echo json_encode([
        'results' => $results,
        'more' => $more
    ]);
    
} catch (Exception $e) {
    error_log("Search customers error: " . $e->getMessage());
    echo json_encode(['results' => [], 'more' => false]);
}
?>