<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $region = trim($_POST['region']);
    $soil_type = trim($_POST['soil_type']);
    $area = floatval($_POST['area']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($region) || empty($soil_type) || $area <= 0) {
        echo json_encode(['success' => false, 'message' => 'All fields are required and area must be positive.']);
        exit;
    }
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT farmer_id FROM farmers WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit;
    }
    
    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert farmer
    $stmt = $conn->prepare("INSERT INTO farmers (name, email, password, region, soil_type, area) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $name, $email, $hashed_password, $region, $soil_type, $area);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful! You can now login.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
    
    $stmt->close();
    $check_email->close();
}

$conn->close();
?>

