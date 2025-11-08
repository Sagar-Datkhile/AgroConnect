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
    $crop_id = intval($_POST['crop_id']);
    
    $crop_query = $conn->prepare("SELECT crop_name FROM crops WHERE crop_id = ? AND farmer_id = ?");
    $crop_query->bind_param("ii", $crop_id, $farmer_id);
    $crop_query->execute();
    $crop_result = $crop_query->get_result();
    $crop_name = "";
    if ($crop_result->num_rows > 0) {
        $crop_data = $crop_result->fetch_assoc();
        $crop_name = $crop_data['crop_name'];
    }
    $crop_query->close();
    
    $stmt = $conn->prepare("UPDATE crops SET is_deleted = TRUE WHERE crop_id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $crop_id, $farmer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        log_activity($conn, 'farmer', $farmer_id, 'delete_crop', 'crop', $crop_id, "Deleted crop: $crop_name");
        
        echo json_encode(['success' => true, 'message' => 'Crop deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete crop.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

