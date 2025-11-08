<?php
require_once 'php/db_connect.php';

header('Content-Type: application/json');

// Check if farmers table exists
$result = $conn->query("SHOW TABLES LIKE 'farmers'");
if ($result->num_rows == 0) {
    echo json_encode(['error' => 'Farmers table does not exist']);
    exit;
}

// Count farmers
$result = $conn->query("SELECT COUNT(*) as count FROM farmers");
$count = $result->fetch_assoc();

// Get all farmers
$result = $conn->query("SELECT farmer_id, name, email, region, soil_type, area, is_blocked, created_at FROM farmers ORDER BY created_at DESC");
$farmers = [];
while ($row = $result->fetch_assoc()) {
    $farmers[] = $row;
}

echo json_encode([
    'table_exists' => true,
    'total_count' => $count['count'],
    'farmers' => $farmers
], JSON_PRETTY_PRINT);

$conn->close();
?>
