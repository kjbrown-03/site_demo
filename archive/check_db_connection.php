<?php
require_once 'config.php';

try {
    // Test connection
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    
    echo "Database connection successful!\n";
    echo "Found $count users in the database.\n\n";
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Admin user found:\n";
        echo "- Username: " . $admin['username'] . "\n";
        echo "- Email: " . $admin['email'] . "\n";
        echo "- Role: " . $admin['role'] . "\n\n";
        echo "You can log in as admin using:\n";
        echo "- Email: " . $admin['email'] . "\n";
        echo "- Password: admin\n";
    } else {
        echo "No admin user found in the database.\n";
    }
    
} catch(PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>