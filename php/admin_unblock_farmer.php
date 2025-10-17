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
    $farmer_id = intval($_POST['farmer_id']);
    
    $stmt = $conn->prepare("UPDATE farmers SET is_blocked = FALSE WHERE farmer_id = ?");
    $stmt->bind_param("i", $farmer_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Farmer unblocked successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unblock farmer.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

