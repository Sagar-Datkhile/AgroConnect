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
    $name = trim($_POST['name']);
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $region = trim($_POST['region']);
    $soil_type = trim($_POST['soil_type']);
    $area = floatval($_POST['area']);
    
    if (empty($name) || empty($region) || empty($soil_type) || $area <= 0) {
        echo json_encode(['success' => false, 'message' => 'All fields are required and area must be positive.']);
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE farmers SET name = ?, phone = ?, region = ?, soil_type = ?, area = ? WHERE farmer_id = ?");
    $stmt->bind_param("ssssdi", $name, $phone, $region, $soil_type, $area, $farmer_id);
    
    if ($stmt->execute()) {
        $_SESSION['farmer_name'] = $name;
        $_SESSION['farmer_region'] = $region;
        $_SESSION['farmer_soil_type'] = $soil_type;
        $_SESSION['farmer_area'] = $area;
        
        log_activity($conn, 'farmer', $farmer_id, 'update_profile', 'farmer', $farmer_id, "Updated profile");
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

