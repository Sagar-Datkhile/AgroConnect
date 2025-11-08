<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crop_id = intval($_POST['crop_id']);
    $admin_id = $_SESSION['admin_id'];
    
    $crop_query = $conn->prepare("SELECT crop_name, farmer_id FROM crops WHERE crop_id = ?");
    $crop_query->bind_param("i", $crop_id);
    $crop_query->execute();
    $crop_result = $crop_query->get_result();
    $crop_name = "";
    $farmer_id = null;
    if ($crop_result->num_rows > 0) {
        $crop_data = $crop_result->fetch_assoc();
        $crop_name = $crop_data['crop_name'];
        $farmer_id = $crop_data['farmer_id'];
    }
    $crop_query->close();
    
    $stmt = $conn->prepare("UPDATE crops SET is_deleted = TRUE, deleted_by = ? WHERE crop_id = ?");
    $stmt->bind_param("ii", $admin_id, $crop_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        log_activity($conn, 'admin', $admin_id, 'delete_crop', 'crop', $crop_id, "Admin deleted crop: $crop_name (Farmer ID: $farmer_id)");
        
        echo json_encode(['success' => true, 'message' => 'Crop deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete crop.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

