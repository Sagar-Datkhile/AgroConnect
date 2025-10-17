<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$crop_name = isset($_GET['crop_name']) ? trim($_GET['crop_name']) : '';
$region = isset($_GET['region']) ? trim($_GET['region']) : '';
$min_area = isset($_GET['min_area']) ? floatval($_GET['min_area']) : 0;

// Build dynamic query
// Check if is_deleted column exists
$checkColumn = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
$hasDeletedColumn = ($checkColumn && $checkColumn->num_rows > 0);

$query = "SELECT c.crop_id, c.crop_name, c.investment, c.turnover, c.description, c.created_at,
          f.name as farmer_name, f.region, f.soil_type, f.area 
          FROM crops c 
          JOIN farmers f ON c.farmer_id = f.farmer_id 
          WHERE f.is_blocked = FALSE";

if ($hasDeletedColumn) {
    $query .= " AND (c.is_deleted = 0 OR c.is_deleted IS NULL)";
}

$params = [];
$types = "";

if (!empty($crop_name)) {
    $query .= " AND c.crop_name LIKE ?";
    $params[] = "%$crop_name%";
    $types .= "s";
}

if (!empty($region)) {
    $query .= " AND f.region LIKE ?";
    $params[] = "%$region%";
    $types .= "s";
}

if ($min_area > 0) {
    $query .= " AND f.area >= ?";
    $params[] = $min_area;
    $types .= "d";
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

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

