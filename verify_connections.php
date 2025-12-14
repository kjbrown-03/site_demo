<?php
echo "Vérification des Connexions Base de Données\n";
echo "=========================================\n\n";

// Check config.php
echo "1. Vérification de config.php...\n";
if (file_exists('config.php')) {
    echo "   ✓ config.php existe\n";
    
    // Try to include and test connection
    try {
        include 'config.php';
        echo "   ✓ Connexion base de données réussie\n";
        echo "   ✓ Base de données: " . DB_NAME . "\n";
        echo "   ✓ Utilisateur: " . DB_USER . "\n";
    } catch (Exception $e) {
        echo "   ✗ Échec connexion base de données: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ config.php introuvable\n";
}

echo "\n2. Vérification des fichiers PHP nécessitant la connexion...\n";

$files = [
    'index.php' => 'Page d\'accueil',
    'buy.php' => 'Page d\'achat', 
    'rent.php' => 'Page de location',
    'sell.php' => 'Page de vente',
    'agents.php' => 'Page des agents',
    'financing.php' => 'Page financement',
    'search_properties.php' => 'Recherche propriétés',
    'admin_dashboard.php' => 'Tableau de bord admin'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, "require_once 'config.php'") !== false) {
            echo "   ✓ $file - $description (connecté à la base)\n";
        } else {
            echo "   ✗ $file - $description (NON connecté à la base)\n";
        }
    } else {
        echo "   ✗ $file introuvable\n";
    }
}

echo "\n3. Vérification de la structure de la base...\n";
try {
    include 'config.php';
    
    // Check if tables exist
    $tables = ['users', 'properties', 'orders'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            echo "   ✓ Table '$table' existe\n";
        } else {
            echo "   ✗ Table '$table' manquante\n";
        }
    }
    
    // Check if there's data in tables
    echo "\n4. Vérification des données...\n";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   ✓ Table '$table': " . $count['count'] . " enregistrements\n";
    }
} catch (Exception $e) {
    echo "   ✗ Échec vérification structure: " . $e->getMessage() . "\n";
}

echo "\nVérification terminée!\n";
?>