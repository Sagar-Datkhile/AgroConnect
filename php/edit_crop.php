<?php
/**
 * Edit Crop Handler
 * Updated for new database schema
 */
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
    $category = isset($_POST['category']) ? trim($_POST['category']) : null;
    $investment = floatval($_POST['investment']);
    $turnover = floatval($_POST['turnover']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $season = isset($_POST['season']) ? trim($_POST['season']) : null;
    
    // Validation
    if (empty($crop_name) || $investment < 0 || $turnover < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid crop details.']);
        exit;
    }
    
    // Update crop (ensure it belongs to the logged-in farmer)
    $stmt = $conn->prepare(
        "UPDATE crops SET crop_name = ?, category = ?, investment = ?, turnover = ?, description = ?, season = ? 
         WHERE crop_id = ? AND farmer_id = ?"
    );
    $stmt->bind_param("ssddssis", $crop_name, $category, $investment, $turnover, $description, $season, $crop_id, $farmer_id);
    
    if ($stmt->execute()) {
        // Log activity
        log_activity($conn, 'farmer', $farmer_id, 'edit_crop', 'crop', $crop_id, "Updated crop: $crop_name");
        
        echo json_encode(['success' => true, 'message' => 'Crop updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update crop.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

