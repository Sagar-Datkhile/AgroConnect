<?php
// Database migration script to add soft delete columns
require_once 'db_connect.php';

echo "Starting database migration...\n";

// Check if columns exist and add them if they don't
$result = $conn->query("SHOW COLUMNS FROM crops LIKE 'is_deleted'");
if ($result->num_rows == 0) {
    echo "Adding is_deleted column...\n";
    $conn->query("ALTER TABLE crops ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE");
    echo "is_deleted column added successfully.\n";
} else {
    echo "is_deleted column already exists.\n";
}

$result = $conn->query("SHOW COLUMNS FROM crops LIKE 'deleted_at'");
if ($result->num_rows == 0) {
    echo "Adding deleted_at column...\n";
    $conn->query("ALTER TABLE crops ADD COLUMN deleted_at TIMESTAMP NULL");
    echo "deleted_at column added successfully.\n";
} else {
    echo "deleted_at column already exists.\n";
}

echo "\nMigration completed successfully!\n";
echo "You can now use the application normally.\n";

$conn->close();
?>

