<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'Eddy');
define('DB_PASS', 'Daddiesammy1$');
define('DB_NAME', 'immohome');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>