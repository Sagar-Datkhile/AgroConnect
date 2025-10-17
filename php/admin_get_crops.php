<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Fetch all crops with farmer details
$query = "SELECT c.crop_id, c.crop_name, c.investment, c.turnover, c.description, c.created_at,
          f.name as farmer_name, f.email as farmer_email, f.region 
          FROM crops c 
          JOIN farmers f ON c.farmer_id = f.farmer_id 
          ORDER BY c.created_at DESC";
$result = $conn->query($query);

$crops = [];
while ($row = $result->fetch_assoc()) {
    $crops[] = $row;
}

echo json_encode(['success' => true, 'crops' => $crops]);

$conn->close();
?>

