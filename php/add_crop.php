<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if farmer is logged in
if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = $_SESSION['farmer_id'];
    $crop_name = trim($_POST['crop_name']);
    $investment = floatval($_POST['investment']);
    $turnover = floatval($_POST['turnover']);
    $description = trim($_POST['description']);
    
    // Validation
    if (empty($crop_name) || $investment <= 0 || $turnover < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid crop details.']);
        exit;
    }
    
    // Insert crop
    $stmt = $conn->prepare("INSERT INTO crops (farmer_id, crop_name, investment, turnover, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdds", $farmer_id, $crop_name, $investment, $turnover, $description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Crop post added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add crop post.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

