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
    
    // Check if soft delete columns exist
    $checkColumn = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
    $hasDeletedColumn = ($checkColumn && $checkColumn->num_rows > 0);
    
    if ($hasDeletedColumn) {
        // Soft delete - mark as deleted instead of removing
        $stmt = $conn->prepare("UPDATE crops SET is_deleted = 1, deleted_at = NOW() WHERE crop_id = ? AND farmer_id = ?");
    } else {
        // Hard delete if columns don't exist
        $stmt = $conn->prepare("DELETE FROM crops WHERE crop_id = ? AND farmer_id = ?");
    }
    
    $stmt->bind_param("ii", $crop_id, $farmer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Crop deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete crop.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

