<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_id = intval($_POST['crop_id']);
    
    // Check if soft delete columns exist
    $checkColumn = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
    $hasDeletedColumn = ($checkColumn && $checkColumn->num_rows > 0);
    
    if ($hasDeletedColumn) {
        // Soft delete - mark as deleted instead of removing
        $stmt = $conn->prepare("UPDATE crops SET is_deleted = 1, deleted_at = NOW() WHERE crop_id = ?");
    } else {
        // Hard delete if columns don't exist
        $stmt = $conn->prepare("DELETE FROM crops WHERE crop_id = ?");
    }
    
    $stmt->bind_param("i", $crop_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Crop deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete crop.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

