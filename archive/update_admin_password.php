<?php
require_once 'config.php';

try {
    // Update admin user password
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@example.com'");
    $stmt->execute([$hashedPassword]);
    
    echo "Admin password updated successfully!\n";
    echo "New credentials:\n";
    echo "- Email: admin@example.com\n";
    echo "- Password: admin123\n";
    
} catch(PDOException $e) {
    echo "Error updating admin password: " . $e->getMessage() . "\n";
}
?>