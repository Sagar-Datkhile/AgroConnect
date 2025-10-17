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
    $name = trim($_POST['name']);
    $region = trim($_POST['region']);
    $soil_type = trim($_POST['soil_type']);
    $area = floatval($_POST['area']);
    
    // Validation
    if (empty($name) || empty($region) || empty($soil_type) || $area <= 0) {
        echo json_encode(['success' => false, 'message' => 'All fields are required and area must be positive.']);
        exit;
    }
    
    // Update profile
    $stmt = $conn->prepare("UPDATE farmers SET name = ?, region = ?, soil_type = ?, area = ? WHERE farmer_id = ?");
    $stmt->bind_param("sssdi", $name, $region, $soil_type, $area, $farmer_id);
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['farmer_name'] = $name;
        $_SESSION['farmer_region'] = $region;
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
    
    $stmt->close();
}

$conn->close();
?>

