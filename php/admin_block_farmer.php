<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = intval($_POST['farmer_id']);
    $admin_id = $_SESSION['admin_id'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'No reason provided';
    
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
    
    $stmt = $conn->prepare(
        "INSERT INTO farmer_blocks (farmer_id, admin_id, reason, is_active) 
         VALUES (?, ?, ?, TRUE)"
    );
    $stmt->bind_param("iis", $farmer_id, $admin_id, $reason);
    
    if ($stmt->execute()) {
        log_activity($conn, 'admin', $admin_id, 'block_farmer', 'farmer', $farmer_id, "Blocked farmer: $farmer_name. Reason: $reason");
        
        echo json_encode(['success' => true, 'message' => 'Farmer blocked successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to block farmer.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

