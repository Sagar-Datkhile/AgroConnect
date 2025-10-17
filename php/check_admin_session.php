<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    echo json_encode([
        'logged_in' => true,
        'admin_email' => $_SESSION['admin_email']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>

