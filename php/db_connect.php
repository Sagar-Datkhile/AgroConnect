<?php
// Database connection configuration for XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agroconnect";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>

