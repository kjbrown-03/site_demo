<?php
session_start();
require_once 'config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// Get search parameters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$propertyType = isset($_GET['type']) ? $_GET['type'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '';

// Build SQL query
$sql = "SELECT p.*, u.username as agent_name FROM properties p LEFT JOIN users u ON p.agent_id = u.id WHERE 1=1";
$params = [];

if (!empty($searchTerm)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.address LIKE ? OR p.city LIKE ?)";
    $params = array_merge($params, ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]);
}

if (!empty($propertyType) && $propertyType != 'all') {
    $sql .= " AND p.type = ?";
    $params[] = $propertyType;
}

if (!empty($minPrice)) {
    $sql .= " AND p.price >= ?";
    $params[] = $minPrice;
}

if (!empty($maxPrice)) {
    $sql .= " AND p.price <= ?";
    $params[] = $maxPrice;
}

if (!empty($bedrooms)) {
    $sql .= " AND p.bedrooms >= ?";
    $params[] = $bedrooms;
}

$sql .= " ORDER BY p.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $properties = [];
    $error = "An error occurred while fetching properties.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Properties - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="buy.php">Acheter</a></li>
                    <li><a href="rent.php">Louer</a></li>
                    <li><a href="sell.php">Vendre</a></li>
                    <li><a href="agents.php">Agents</a></li>
                    <li><a href="financing.php">Financement</a></li>
                </ul>
                <div class="nav-actions">
                    <?php if ($isLoggedIn): ?>
                        <span class="user-welcome">Bonjour, <?php echo htmlspecialchars($username); ?>!</span>
                        <a href="logout.php" class="btn-secondary">Déconnexion</a>
                    <?php else: ?>
                        <button class="btn-secondary"><i class="fas fa-heart"></i> Favoris</button>
                        <button class="btn-primary" onclick="redirectToLogin()">Connexion</button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <section class="search-hero">
        <div class="container">
            <h1>Trouvez Votre Propriété Parfaite</h1>
            <p>Recherchez parmi des milliers d'annonces</p>
            
            <div class="search-box">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Ville, quartier, code postal..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i> Rechercher</button>
                </form>
            </div>
        </div>
    </section>

    <section class="search-filters-section">
        <div class="container">
            <div class="search-filters">
                <form method="GET" class="filters-form">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    
                    <div class="filter-item">
                        <label>Type de Propriété</label>
                        <select name="type">
                            <option value="all" <?php echo ($propertyType == '' || $propertyType == 'all') ? 'selected' : ''; ?>>Tous Types</option>
                            <option value="house" <?php echo ($propertyType == 'house') ? 'selected' : ''; ?>>Maison</option>
                            <option value="apartment" <?php echo ($propertyType == 'apartment') ? 'selected' : ''; ?>>Appartement</option>
                            <option value="villa" <?php echo ($propertyType == 'villa') ? 'selected' : ''; ?>>Villa</option>
                            <option value="land" <?php echo ($propertyType == 'land') ? 'selected' : ''; ?>>Terrain</option>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label>Prix Min (€)</label>
                        <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($minPrice); ?>">
                    </div>
                    
                    <div class="filter-item">
                        <label>Prix Max (€)</label>
                        <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice); ?>">
                    </div>
                    
                    <div class="filter-item">
                        <label>Chambres</label>
                        <select name="bedrooms">
                            <option value="">Toutes</option>
                            <option value="1" <?php echo ($bedrooms == '1') ? 'selected' : ''; ?>>1+</option>
                            <option value="2" <?php echo ($bedrooms == '2') ? 'selected' : ''; ?>>2+</option>
                            <option value="3" <?php echo ($bedrooms == '3') ? 'selected' : ''; ?>>3+</option>
                            <option value="4" <?php echo ($bedrooms == '4') ? 'selected' : ''; ?>>4+</option>
                            <option value="5" <?php echo ($bedrooms == '5') ? 'selected' : ''; ?>>5+</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary filter-btn">Filtrer</button>
                    <a href="search_properties.php" class="btn-secondary clear-btn">Effacer</a>
                </form>
            </div>
        </div>
    </section>

    <section class="properties-results">
        <div class="container">
            <div class="section-header">
                <h2>Résultats de Recherche</h2>
                <p><?php echo count($properties); ?> propriétés trouvées</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (count($properties) > 0): ?>
                <div class="properties-grid">
                    <?php foreach ($properties as $property): ?>
                        <div class="property-card">
                            <div class="property-image" style="background-image: url('<?php echo !empty($property['image_url']) ? $property['image_url'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800'; ?>');">
                                <span class="property-badge"><?php echo ucfirst(str_replace('_', ' ', $property['status'])); ?></span>
                                <div class="property-favorite">
                                    <i class="far fa-heart"></i>
                                </div>
                            </div>
                            <div class="property-info">
                                <div class="property-price"><?php echo number_format($property['price'], 0, ',', ' '); ?> €</div>
                                <div class="property-details">
                                    <?php if ($property['bedrooms'] > 0): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bed"></i>
                                            <span><?php echo $property['bedrooms']; ?> ch</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($property['bathrooms'] > 0): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-bath"></i>
                                            <span><?php echo $property['bathrooms']; ?> sdb</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($property['area_sqm'] > 0): ?>
                                        <div class="property-detail">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span><?php echo $property['area_sqm']; ?> m²</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="property-address"><?php echo htmlspecialchars($property['address']); ?></div>
                                <div class="property-meta">Agent: <?php echo htmlspecialchars(isset($property['agent_name']) ? $property['agent_name'] : 'N/A'); ?></div>
                                <div class="property-actions">
                                    <button class="btn-secondary" onclick="viewProperty(<?php echo $property['id']; ?>)">Voir Détails</button>
                                    <?php if ($isLoggedIn): ?>
                                        <button class="btn-primary" onclick="placeOrder(<?php echo $property['id']; ?>)">Passer Commande</button>
                                    <?php else: ?>
                                        <button class="btn-primary" onclick="redirectToLogin()">Se Connecter pour Commander</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-home fa-3x"></i>
                    <h3>Aucune propriété trouvée</h3>
                    <p>Essayez d'ajuster vos critères de recherche</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-home"></i>
                        <span>ImmoHome</span>
                    </div>
                    <p>Votre partenaire de confiance pour trouver la maison parfaite.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Achat</h4>
                    <ul>
                        <li><a href="#">Maisons</a></li>
                        <li><a href="#">Appartements</a></li>
                        <li><a href="#">Villas</a></li>
                        <li><a href="#">Terrains</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Évaluation Gratuite</a></li>
                        <li><a href="#">Financement</a></li>
                        <li><a href="#">Assurance</a></li>
                        <li><a href="#">Déménagement</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Entreprise</h4>
                    <ul>
                        <li><a href="#">À Propos</a></li>
                        <li><a href="#">Carrières</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        function viewProperty(propertyId) {
            alert('Affichage des détails de la propriété ID: ' + propertyId);
            // In a real implementation, this would redirect to a property detail page
        }
        
        function placeOrder(propertyId) {
            if (confirm('Êtes-vous sûr de vouloir passer commande pour cette propriété ?')) {
                // In a real implementation, this would submit an order
                alert('Commande passée avec succès pour la propriété ID: ' + propertyId);
            }
        }
        
        function redirectToLogin() {
            if (confirm("Vous devez vous connecter pour passer une commande. Souhaitez-vous vous connecter maintenant ?")) {
                window.location.href = 'login.php';
            }
        }
    </script>

    <style>
        .search-hero {
            margin-top: 70px;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .search-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .search-hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .search-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .search-form {
            display: flex;
            gap: 15px;
        }
        
        .search-filters-section {
            padding: 30px 0;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .search-filters {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .filters-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: end;
        }
        
        .filter-item {
            flex: 1;
            min-width: 150px;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1A1A1A;
            font-size: 14px;
        }
        
        .filter-item select,
        .filter-item input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
        }
        
        .filter-item select:focus,
        .filter-item input:focus {
            outline: none;
            border-color: #006AFF;
        }
        
        .filter-btn,
        .clear-btn {
            height: 46px;
            align-self: end;
        }
        
        .properties-results {
            padding: 80px 0;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .property-actions .btn-secondary,
        .property-actions .btn-primary {
            flex: 1;
            padding: 10px;
            font-size: 14px;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6B6B6B;
        }
        
        .no-results i {
            margin-bottom: 20px;
            color: #006AFF;
        }
        
        .no-results h3 {
            margin-bottom: 10px;
            color: #1A1A1A;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .alert.error {
            background: #FFEAEA;
            color: #FF4757;
            border: 1px solid #FFD1D1;
        }
        
        @media (max-width: 768px) {
            .search-hero h1 {
                font-size: 36px;
            }
            
            .search-hero p {
                font-size: 18px;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .filters-form {
                flex-direction: column;
            }
            
            .filter-item {
                min-width: 100%;
            }
            
            .property-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>