<?php
require_once 'config.php';

try {
    $stmt = $pdo->prepare('SELECT title, city FROM properties WHERE city LIKE ?');
    $stmt->execute(['%Paris%']);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Properties in Paris:\n";
    foreach ($properties as $prop) {
        echo "- " . $prop['title'] . " in " . $prop['city'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>