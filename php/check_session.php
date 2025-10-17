<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['farmer_id'])) {
    echo json_encode([
        'logged_in' => true,
        'farmer_name' => $_SESSION['farmer_name'],
        'farmer_region' => $_SESSION['farmer_region']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>

