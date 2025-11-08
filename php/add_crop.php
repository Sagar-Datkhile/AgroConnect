<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = $_SESSION['farmer_id'];
    $crop_name = trim($_POST['crop_name']);
    $category = isset($_POST['category']) ? trim($_POST['category']) : null;
    $investment = floatval($_POST['investment']);
    $turnover = floatval($_POST['turnover']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $season = isset($_POST['season']) ? trim($_POST['season']) : null;
    $planting_date = isset($_POST['planting_date']) ? $_POST['planting_date'] : null;
    $harvest_date = isset($_POST['harvest_date']) ? $_POST['harvest_date'] : null;
    $quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : null;
    $quantity_unit = isset($_POST['quantity_unit']) ? trim($_POST['quantity_unit']) : null;
    
    if (empty($crop_name) || $investment < 0 || $turnover < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid crop details.']);
        exit;
    }
    
    $stmt = $conn->prepare(
        "INSERT INTO crops (farmer_id, crop_name, category, investment, turnover, description, season, planting_date, harvest_date, quantity, quantity_unit) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "issddssssds", 
        $farmer_id, $crop_name, $category, $investment, $turnover, $description, 
        $season, $planting_date, $harvest_date, $quantity, $quantity_unit
    );
    
    if ($stmt->execute()) {
        $crop_id = $conn->insert_id;
        
        log_activity($conn, 'farmer', $farmer_id, 'add_crop', 'crop', $crop_id, "Added crop: $crop_name");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Crop post added successfully!',
            'crop_id' => $crop_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add crop post.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

