<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

try {
    // Fetch all farmers
    $query = "SELECT farmer_id, name, email, region, soil_type, area, is_blocked, registration_date as created_at FROM farmers ORDER BY registration_date DESC";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $farmers = [];
    while ($row = $result->fetch_assoc()) {
        $farmers[] = $row;
    }
    
    echo json_encode(['success' => true, 'farmers' => $farmers, 'count' => count($farmers)]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching farmers: ' . $e->getMessage()]);
}

$conn->close();
?>

