<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Fetch all farmers
$query = "SELECT farmer_id, name, email, region, soil_type, area, is_blocked, created_at FROM farmers ORDER BY created_at DESC";
$result = $conn->query($query);

$farmers = [];
while ($row = $result->fetch_assoc()) {
    $farmers[] = $row;
}

echo json_encode(['success' => true, 'farmers' => $farmers]);

$conn->close();
?>

