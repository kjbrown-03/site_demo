<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Favorites - ImmoHome</title>
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
                    <li><a href="buy.php">Buy</a></li>
                    <li><a href="rent.php">Rent</a></li>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome">Hello, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="logout.php" class="btn-secondary">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Test Favorites Functionality</h1>
            <p>Click the hearts to add/remove properties from favorites</p>
        </div>
    </section>

    <section class="properties-section">
        <div class="container">
            <div class="properties-grid">
                <div class="property-card" data-property-id="1">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600');">
                        <span class="property-badge">For Sale</span>
                        <div class="property-favorite">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="property-info">
                        <div class="property-price">€485,000</div>
                        <div class="property-details">
                            <div class="property-detail">
                                <i class="fas fa-bed"></i>
                                <span>4 beds</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-bath"></i>
                                <span>2 baths</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-ruler-combined"></i>
                                <span>1883 m²</span>
                            </div>
                        </div>
                        <div class="property-address">123 Main Street, Paris</div>
                        <div class="property-meta">Agent: Sophie Martin</div>
                    </div>
                </div>

                <div class="property-card" data-property-id="2">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=600');">
                        <span class="property-badge">Price Reduced</span>
                        <div class="property-favorite">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="property-info">
                        <div class="property-price">€325,000</div>
                        <div class="property-details">
                            <div class="property-detail">
                                <i class="fas fa-bed"></i>
                                <span>3 beds</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-bath"></i>
                                <span>2 baths</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-ruler-combined"></i>
                                <span>1440 m²</span>
                            </div>
                        </div>
                        <div class="property-address">45 City Avenue, Lyon</div>
                        <div class="property-meta">Agent: Jean Dupont</div>
                    </div>
                </div>

                <div class="property-card" data-property-id="3">
                    <div class="property-image" style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=600');">
                        <span class="property-badge">New</span>
                        <div class="property-favorite">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="property-info">
                        <div class="property-price">€629,000</div>
                        <div class="property-details">
                            <div class="property-detail">
                                <i class="fas fa-bed"></i>
                                <span>5 beds</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-bath"></i>
                                <span>3 baths</span>
                            </div>
                            <div class="property-detail">
                                <i class="fas fa-ruler-combined"></i>
                                <span>2819 m²</span>
                            </div>
                        </div>
                        <div class="property-address">78 Hill Road, Nice</div>
                        <div class="property-meta">Agent: Marie Leclerc</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Function to initialize favorite buttons
        function initFavoriteButtons() {
            // Get favorites from localStorage
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            // Update favorite buttons based on saved favorites
            document.querySelectorAll('.property-favorite').forEach(button => {
                const propertyId = parseInt(button.closest('.property-card').dataset.propertyId);
                if (favorites.includes(propertyId)) {
                    button.classList.add('active');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                }
            });
            
            // Add click event listeners to favorite buttons
            document.querySelectorAll('.property-favorite').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleFavorite(this);
                });
            });
        }
        
        // Toggle favorite status
        function toggleFavorite(button) {
            const propertyCard = button.closest('.property-card');
            const propertyId = parseInt(propertyCard.dataset.propertyId);
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            if (button.classList.contains('active')) {
                // Remove from favorites
                favorites = favorites.filter(id => id !== propertyId);
                button.classList.remove('active');
                button.innerHTML = '<i class="far fa-heart"></i>';
            } else {
                // Add to favorites
                favorites.push(propertyId);
                button.classList.add('active');
                button.innerHTML = '<i class="fas fa-heart"></i>';
            }
            
            // Save to localStorage
            localStorage.setItem('favorites', JSON.stringify(favorites));
            
            // Show current favorites in console for testing
            console.log('Current favorites:', favorites);
        }
        
        // Initialize favorite buttons when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initFavoriteButtons();
        });
    </script>

    <style>
        .hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .properties-section {
            padding: 80px 0;
        }
    </style>
</body>
</html>