<?php
// Simple script to check if admin user exists
echo "Checking admin user...\n";

// Database configuration
$host = 'localhost';
$db_user = 'root';
$db_pass = ''; // No password as specified
$db_name = 'immohome';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute(['admin@example.com', 'admin_user']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "SUCCESS: Admin user found!\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Role: " . $user['role'] . "\n";
    } else {
        echo "ERROR: Admin user not found!\n";
    }
} catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>