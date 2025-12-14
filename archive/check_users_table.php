<?php
// Script to check users table structure
require_once 'config.php';

try {
    // Get table structure
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users table structure:\n";
    echo "=====================\n";
    foreach ($columns as $column) {
        echo $column['Field'] . " " . $column['Type'];
        if ($column['Null'] === 'NO') {
            echo " NOT NULL";
        }
        if ($column['Default'] !== null) {
            echo " DEFAULT '" . $column['Default'] . "'";
        }
        echo "\n";
    }
    
} catch(PDOException $e) {
    echo "Error checking table structure: " . $e->getMessage() . "\n";
}
?>