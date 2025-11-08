<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$farmer_id = $_SESSION['farmer_id'];

$stmt = $conn->prepare(
    "SELECT crop_id, crop_name, category, investment, turnover, profit, description, season, 
            planting_date, harvest_date, quantity, quantity_unit, created_at, updated_at
     FROM crops 
     WHERE farmer_id = ? AND is_deleted = FALSE 
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

$crops = [];
while ($row = $result->fetch_assoc()) {
    $crops[] = $row;
}

echo json_encode(['success' => true, 'crops' => $crops]);

$stmt->close();
$conn->close();
?>

