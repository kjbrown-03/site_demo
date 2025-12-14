<?php
require_once 'config.php';

try {
    // Check if admin user exists and verify credentials
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE email = 'admin@example.com' AND role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "SUCCESS: Admin user found in database!\n";
        echo "==================================\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Email: " . $admin['email'] . "\n";
        echo "Role: " . $admin['role'] . "\n\n";
        echo "Login credentials:\n";
        echo "- Email: admin@example.com\n";
        echo "- Password: admin123\n";
        echo "==================================\n";
        echo "You can now log in to the admin dashboard.\n";
    } else {
        echo "ERROR: Admin user not found in database!\n";
    }
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>