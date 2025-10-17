<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Check if is_deleted column exists
$checkColumn = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
$hasDeletedColumn = ($checkColumn && $checkColumn->num_rows > 0);

// Fetch all crops with farmer details
$query = "SELECT c.crop_id, c.crop_name, c.investment, c.turnover, c.description, c.created_at,
          f.name as farmer_name, f.email as farmer_email, f.region 
          FROM crops c 
          JOIN farmers f ON c.farmer_id = f.farmer_id";

if ($hasDeletedColumn) {
    $query .= " WHERE c.is_deleted = 0 OR c.is_deleted IS NULL";
}

$query .= " ORDER BY c.created_at DESC";
$result = $conn->query($query);

$crops = [];
while ($row = $result->fetch_assoc()) {
    $crops[] = $row;
}

echo json_encode(['success' => true, 'crops' => $crops]);

$conn->close();
?>

