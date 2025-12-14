<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];

// Get favorite properties from database
$favoritesData = [];
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username as agent_name 
        FROM favorites f
        INNER JOIN properties p ON f.property_id = p.id
        LEFT JOIN users u ON p.agent_id = u.id 
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$userId]);
    $favoritesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $favoritesData = [];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('favorites'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='../index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="../index.php"><?php echo t('home'); ?></a></li>
                    <li><a href="../pages/buy.php"><?php echo t('buy'); ?></a></li>
                    <?php if ($userRole != 'buyer'): ?>
                    <li><a href="../pages/rent.php"><?php echo t('rent'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($userRole != 'buyer'): ?>
                    <li><a href="../pages/sell.php"><?php echo t('sell'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($userRole != 'buyer'): ?>
                    <li><a href="../pages/agents.php"><?php echo t('agents'); ?></a></li>
                    <?php endif; ?>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome"><?php echo t('hello'); ?>, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="../auth/logout.php" class="btn-secondary"><?php echo t('logout'); ?></a>
                </div>
            </div>
        </nav>
    </header>

    <section class="favorites-hero">
        <div class="container">
            <h1><?php echo t('favorites'); ?></h1>
            <p><?php echo t('view_saved_properties'); ?></p>
        </div>
    </section>

    <section class="favorites-content">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('favorites'); ?></h2>
                <p><?php echo t('you_have'); ?> <span id="favoriteCount"><?php echo count($favoritesData); ?></span> <?php echo t('favorite_properties'); ?></p>
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
                    <p><?php echo t('trusted_partner'); ?></p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('buy'); ?></h4>
                    <ul>
                        <li><a href="../pages/buy.php"><?php echo t('houses'); ?></a></li>
                        <li><a href="../pages/buy.php"><?php echo t('apartments'); ?></a></li>
                        <li><a href="../pages/buy.php"><?php echo t('villas'); ?></a></li>
                        <li><a href="../pages/buy.php"><?php echo t('lands'); ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('services'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo t('free_estimation'); ?></a></li>
                        <li><a href="../pages/financing.php"><?php echo t('financing'); ?></a></li>
                        <li><a href="#"><?php echo t('insurance'); ?></a></li>
                        <li><a href="#"><?php echo t('moving'); ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('company'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo t('about'); ?></a></li>
                        <li><a href="#"><?php echo t('careers'); ?></a></li>
                        <li><a href="#"><?php echo t('contact'); ?></a></li>
                        <li><a href="#"><?php echo t('blog'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. <?php echo t('all_rights_reserved'); ?></p>
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
                    <span class="property-badge">${property.status === 'for_rent' ? '<?php echo t('for_rent'); ?>' : '<?php echo t('for_sale'); ?>'}</span>
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
        async function removeFromFavorites(propertyId, cardElement) {
            if (confirm('<?php echo t('confirm_remove_favorite'); ?>')) {
                try {
                    // Remove from database
                    const response = await fetch('../api/remove_favorite.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ property_id: propertyId })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Remove from localStorage (for sync)
                        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
                        favorites = favorites.filter(id => id !== propertyId);
                        localStorage.setItem('favorites', JSON.stringify(favorites));
                        
                        // Remove the card from the DOM
                        cardElement.remove();
                        
                        // Update the favorite count
                        updateFavoriteCount();
                    } else {
                        alert('<?php echo t('error_removing_favorite'); ?>');
                    }
                } catch (error) {
                    console.error('Error removing favorite:', error);
                    alert('<?php echo t('error_removing_favorite'); ?>');
                }
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
                        <h3><?php echo t('no_favorites'); ?></h3>
                        <p><?php echo t('browse_add_favorites'); ?></p>
                        <a href="../pages/buy.php" class="btn-primary"><?php echo t('explore_properties'); ?></a>
                    </div>
                `;
            }
        }

        // Function to load favorites
        async function loadFavorites() {
            const favoritesGrid = document.getElementById('favoritesGrid');
            
            try {
                // Fetch favorites from database
                const response = await fetch('../api/get_favorites.php');
                const favoriteProperties = await response.json();
                
                favoritesGrid.innerHTML = '';
                
                if (favoriteProperties.length > 0) {
                    favoriteProperties.forEach(property => {
                        favoritesGrid.appendChild(createPropertyCard(property));
                    });
                    
                    // Sync with localStorage for offline support
                    const favoriteIds = favoriteProperties.map(p => p.id);
                    localStorage.setItem('favorites', JSON.stringify(favoriteIds));
                } else {
                    // Show empty state
                    updateFavoriteCount();
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