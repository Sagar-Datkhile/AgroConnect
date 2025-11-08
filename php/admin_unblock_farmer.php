<?php
/**
 * Admin Unblock Farmer
 * Updated for new database schema with farmer_blocks tracking
 */
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
    $admin_id = $_SESSION['admin_id'];
    
    // Get farmer name for logging
    $farmer_query = $conn->prepare("SELECT name FROM farmers WHERE farmer_id = ?");
    $farmer_query->bind_param("i", $farmer_id);
    $farmer_query->execute();
    $farmer_result = $farmer_query->get_result();
    $farmer_name = "";
    if ($farmer_result->num_rows > 0) {
        $farmer_data = $farmer_result->fetch_assoc();
        $farmer_name = $farmer_data['name'];
    }
    $farmer_query->close();
    
    // Update active block record (trigger will update farmers.is_blocked)
    $stmt = $conn->prepare(
        "UPDATE farmer_blocks 
         SET is_active = FALSE, unblocked_at = NOW(), unblocked_by = ? 
         WHERE farmer_id = ? AND is_active = TRUE"
    );
    $stmt->bind_param("ii", $admin_id, $farmer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Log activity
        log_activity($conn, 'admin', $admin_id, 'unblock_farmer', 'farmer', $farmer_id, "Unblocked farmer: $farmer_name");
        
        echo json_encode(['success' => true, 'message' => 'Farmer unblocked successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unblock farmer or farmer not blocked.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

