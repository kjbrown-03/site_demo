<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Get favorite properties from database
$favoritesData = [];
try {
    // Get favorite IDs from localStorage (passed via AJAX or stored in session)
    // For now, we'll simulate this with sample IDs
    $sampleFavoriteIds = [1, 2, 3];
    
    if (!empty($sampleFavoriteIds)) {
        $placeholders = str_repeat('?,', count($sampleFavoriteIds) - 1) . '?';
        $stmt = $pdo->prepare("
            SELECT p.*, u.username as agent_name 
            FROM properties p 
            LEFT JOIN users u ON p.agent_id = u.id 
            WHERE p.id IN ($placeholders)
        ");
        $stmt->execute($sampleFavoriteIds);
        $favoritesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $favoritesData = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - ImmoHome</title>
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
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="buy.php">Acheter</a></li>
                    <?php if ($userRole != 'buyer'): ?>
                    <li><a href="rent.php">Louer</a></li>
                    <?php endif; ?>
                    <?php if ($userRole != 'buyer'): ?>
                    <li><a href="sell.php">Vendre</a></li>
                    <?php endif; ?>
                    <?php if ($userRole != 'buyer'): ?>
                    <li><a href="agents.php">Agents</a></li>
                    <?php endif; ?>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome">Bonjour, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="logout.php" class="btn-secondary">Déconnexion</a>
                </div>
            </div>
        </nav>
    </header>

    <section class="favorites-hero">
        <div class="container">
            <h1>Mes Propriétés Favorites</h1>
            <p>Gérez vos propriétés sauvegardées</p>
        </div>
    </section>

    <section class="favorites-content">
        <div class="container">
            <div class="section-header">
                <h2>Vos Favoris</h2>
                <p>Vous avez <span id="favoriteCount">0</span> propriétés favorites</p>
            </div>
            
            <div class="properties-grid" id="favoritesGrid">
                <!-- Favorite properties will be loaded here -->
            </div>
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
                        <li><a href="buy.php">Maisons</a></li>
                        <li><a href="buy.php">Appartements</a></li>
                        <li><a href="buy.php">Villas</a></li>
                        <li><a href="buy.php">Terrains</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Évaluation Gratuite</a></li>
                        <li><a href="financing.php">Financement</a></li>
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
        // Function to format price
        function formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 0
            }).format(price);
        }

        // Function to create property card
        function createPropertyCard(property) {
            const card = document.createElement('div');
            card.className = 'property-card';
            card.innerHTML = `
                <div class="property-image" style="background-image: url('${property.image_url || 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800'}');">
                    <span class="property-badge">${property.status === 'for_rent' ? 'À louer' : 'À vendre'}</span>
                    <div class="property-favorite active" data-property-id="${property.id}">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
                <div class="property-info">
                    <div class="property-price">${formatPrice(property.price)}</div>
                    <div class="property-details">
                        <div class="property-detail">
                            <i class="fas fa-bed"></i>
                            <span>${property.bedrooms || 0} ch</span>
                        </div>
                        <div class="property-detail">
                            <i class="fas fa-bath"></i>
                            <span>${property.bathrooms || 0} sdb</span>
                        </div>
                        <div class="property-detail">
                            <i class="fas fa-ruler-combined"></i>
                            <span>${property.area_sqm || 0} m²</span>
                        </div>
                    </div>
                    <div class="property-address">${property.address}</div>
                    <div class="property-meta">Agent: ${property.agent_name || 'Agent ImmoHome'}</div>
                </div>
            `;

            const favoriteBtn = card.querySelector('.property-favorite');
            favoriteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeFromFavorites(property.id, card);
            });

            return card;
        }

        // Function to remove property from favorites
        function removeFromFavorites(propertyId, cardElement) {
            if (confirm('Êtes-vous sûr de vouloir retirer cette propriété de vos favoris ?')) {
                // Remove from localStorage
                let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
                favorites = favorites.filter(id => id !== propertyId);
                localStorage.setItem('favorites', JSON.stringify(favorites));
                
                // Remove the card from the DOM
                cardElement.remove();
                
                // Update the favorite count
                updateFavoriteCount();
            }
        }

        // Function to update favorite count
        function updateFavoriteCount() {
            const favoritesGrid = document.getElementById('favoritesGrid');
            const favoriteCount = document.getElementById('favoriteCount');
            const cards = favoritesGrid.querySelectorAll('.property-card');
            favoriteCount.textContent = cards.length;
            
            // Show empty state if no favorites
            if (cards.length === 0) {
                favoritesGrid.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-heart fa-3x"></i>
                        <h3>Aucun favori pour le moment</h3>
                        <p>Parcourez nos propriétés et ajoutez celles qui vous intéressent à vos favoris</p>
                        <a href="buy.php" class="btn-primary">Explorer les propriétés</a>
                    </div>
                `;
            }
        }

        // Function to load favorites
        async function loadFavorites() {
            // Get favorite IDs from localStorage
            const favoriteIds = JSON.parse(localStorage.getItem('favorites')) || [];
            
            const favoritesGrid = document.getElementById('favoritesGrid');
            
            if (favoriteIds.length === 0) {
                updateFavoriteCount();
                return;
            }
            
            try {
                // Fetch property details from the server
                const response = await fetch('get_favorites.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: favoriteIds })
                });
                
                const favoriteProperties = await response.json();
                
                favoritesGrid.innerHTML = '';
                
                if (favoriteProperties.length > 0) {
                    favoriteProperties.forEach(property => {
                        favoritesGrid.appendChild(createPropertyCard(property));
                    });
                }
                
                updateFavoriteCount();
            } catch (error) {
                console.error('Error loading favorites:', error);
                // Fallback to showing empty state
                updateFavoriteCount();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadFavorites();
        });
    </script>

    <style>
        .favorites-hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .favorites-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .favorites-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .favorites-content {
            padding: 80px 0;
        }
        
        .user-welcome {
            margin-right: 20px;
            font-weight: 500;
        }
        
        /* Property favorite button styles */
        .property-favorite {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s, color 0.2s;
        }
        
        .property-favorite:hover {
            transform: scale(1.1);
            background: #006AFF;
            color: white;
        }
        
        .property-favorite.active {
            background: #006AFF;
            color: white;
        }
        
        @media (max-width: 768px) {
            .favorites-hero h1 {
                font-size: 36px;
            }
            
            .favorites-hero p {
                font-size: 18px;
            }
        }
    </style>
</body>
</html>