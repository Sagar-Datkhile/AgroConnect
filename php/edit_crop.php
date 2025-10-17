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
    $crop_id = intval($_POST['crop_id']);
    $crop_name = trim($_POST['crop_name']);
    $investment = floatval($_POST['investment']);
    $turnover = floatval($_POST['turnover']);
    $description = trim($_POST['description']);
    
    // Validation
    if (empty($crop_name) || $investment <= 0 || $turnover < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid crop details.']);
        exit;
    }
    
    // Update crop (ensure it belongs to the logged-in farmer)
    $stmt = $conn->prepare("UPDATE crops SET crop_name = ?, investment = ?, turnover = ?, description = ? WHERE crop_id = ? AND farmer_id = ?");
    $stmt->bind_param("sddsii", $crop_name, $investment, $turnover, $description, $crop_id, $farmer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Crop updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update crop or no changes made.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

