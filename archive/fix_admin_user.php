<?php
require_once 'config.php';

try {
    // Update admin user email and password
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET email = 'admin@example.com', password = ? WHERE username = 'admin_user'");
    $stmt->execute([$hashedPassword]);
    
    echo "Admin user updated successfully!\n";
    echo "Updated credentials:\n";
    echo "- Email: admin@example.com\n";
    echo "- Password: admin123\n";
    
    // Verify the update
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE username = 'admin_user' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "\nVerification:\n";
        echo "ID: " . $admin['id'] . "\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Email: " . $admin['email'] . "\n";
        echo "Role: " . $admin['role'] . "\n";
    }
    
} catch(PDOException $e) {
    echo "Error updating admin user: " . $e->getMessage() . "\n";
}
?>