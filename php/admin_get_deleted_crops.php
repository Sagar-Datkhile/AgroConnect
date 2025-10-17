<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Check if soft delete columns exist
$checkColumn = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
$hasDeletedColumn = ($checkColumn && $checkColumn->num_rows > 0);

$crops = [];

if ($hasDeletedColumn) {
    // Get deleted crops from last 30 days
    $query = "SELECT c.*, f.name as farmer_name, f.email as farmer_email, f.region 
              FROM crops c 
              JOIN farmers f ON c.farmer_id = f.farmer_id 
              WHERE c.is_deleted = 1 
              AND c.deleted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
              ORDER BY c.deleted_at DESC";
    
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $crops[] = $row;
        }
    }
}

echo json_encode(['success' => true, 'crops' => $crops]);

$conn->close();
?>

