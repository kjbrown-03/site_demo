<?php
require_once 'config.php';

try {
    // Test database connection
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Connected to database: " . $result['db_name'] . "\n";
    
    // Check if tables exist
    $tables = ['users', 'properties', 'orders'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            echo "Table '$table' exists\n";
            
            // Count records in table
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "  Records: " . $count['count'] . "\n";
        } else {
            echo "Table '$table' does not exist\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>