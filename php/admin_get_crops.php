<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$query = "SELECT c.crop_id, c.crop_name, c.category, c.investment, c.turnover, c.profit,
                 c.description, c.season, c.created_at,
                 f.farmer_id, f.name as farmer_name, f.email as farmer_email, f.region 
          FROM crops c 
          INNER JOIN farmers f ON c.farmer_id = f.farmer_id
          WHERE c.is_deleted = FALSE
          ORDER BY c.created_at DESC";

$result = $conn->query($query);

$crops = [];
while ($row = $result->fetch_assoc()) {
    $crops[] = $row;
}

echo json_encode(['success' => true, 'crops' => $crops, 'count' => count($crops)]);

$conn->close();
?>

