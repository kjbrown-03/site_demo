<?php
session_start();
require_once 'includes/language_handler.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmoHome - <?php echo t('welcome'); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="buy.php" onclick="redirectTo('buy.php'); return false;"><?php echo t('buy'); ?></a></li>
                    <li><a href="rent.php" onclick="redirectTo('rent.php'); return false;"><?php echo t('rent'); ?></a></li>
                    <li><a href="sell.php" onclick="redirectTo('sell.php'); return false;"><?php echo t('sell'); ?></a></li>
                    <li><a href="agents.php" onclick="redirectTo('agents.php'); return false;"><?php echo t('agents'); ?></a></li>
                    <li><a href="financing.php" onclick="redirectTo('financing.php'); return false;"><?php echo t('financing'); ?></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropbtn"><i class="fas fa-ellipsis-v"></i></a>
                        <div class="dropdown-content">
                            <a href="#" onclick="showLanguageMenu(event)"><?php echo t('language'); ?></a>
                            <div id="languageSubmenu" class="submenu" style="display: none;">
                                <a href="?lang=fr"><?php echo t('french'); ?></a>
                                <a href="?lang=en"><?php echo t('english'); ?></a>
                            </div>
                            <a href="#" onclick="showThemeMenu(event)"><?php echo t('theme'); ?></a>
                            <div id="themeSubmenu" class="submenu" style="display: none;">
                                <a href="?theme=light"><?php echo t('light_theme'); ?></a>
                                <a href="?theme=dark"><?php echo t('dark_theme'); ?></a>
                            </div>
                            <?php if ($isLoggedIn): ?>
                                <a href="favorites.php"><?php echo t('favorites'); ?></a>
                            <?php else: ?>
                                <a href="login.php"><?php echo t('favorites'); ?></a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
                <div class="nav-actions">
                    <?php if ($isLoggedIn): ?>
                        <span class="user-welcome"><?php echo t('hello'); ?>, <?php echo htmlspecialchars($username); ?>!</span>
                        <a href="logout.php" class="btn-secondary"><?php echo t('logout'); ?></a>
                    <?php else: ?>
                        <button class="btn-primary" onclick="location.href='login.php'"><?php echo t('login'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo t('welcome'); ?></h1>
                <p class="hero-subtitle"><?php echo t('home'); ?> · <?php echo t('buy'); ?> · <?php echo t('rent'); ?> · <?php echo t('sell'); ?></p>
                
                <div class="search-box">
                    <div class="search-tabs">
                        <button class="tab-btn active" data-tab="buy"><?php echo t('buy'); ?></button>
                        <button class="tab-btn" data-tab="rent"><?php echo t('rent'); ?></button>
                        <button class="tab-btn" data-tab="sell"><?php echo t('sell'); ?></button>
                    </div>
                    <div class="search-form">
                        <div class="search-input-group">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="<?php echo t('home'); ?>, <?php echo t('buy'); ?>, <?php echo t('rent'); ?>..." class="search-input" id="mainSearchInput">
                        </div>
                        <button class="search-btn" onclick="performSearch()"><i class="fas fa-search"></i> <?php echo t('search'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-overlay"></div>
    </section>

    <section class="filters-section">
        <div class="container">
            <div class="filters">
                <div class="filter-item">
                    <label><?php echo t('property_type'); ?></label>
                    <select aria-label="<?php echo t('property_type'); ?>" id="propertyType">
                        <option value=""><?php echo t('all'); ?></option>
                        <option value="house"><?php echo t('house'); ?></option>
                        <option value="apartment"><?php echo t('apartment'); ?></option>
                        <option value="villa"><?php echo t('villa'); ?></option>
                        <option value="land"><?php echo t('land'); ?></option>
                    </select>
                </div>
                <div class="filter-item">
                    <label><?php echo t('max_price'); ?></label>
                    <select aria-label="<?php echo t('max_price'); ?>" id="maxPrice">
                        <option value=""><?php echo t('all'); ?></option>
                        <option value="100000">100 000€</option>
                        <option value="200000">200 000€</option>
                        <option value="300000">300 000€</option>
                        <option value="500000">500 000€</option>
                        <option value="1000000">1 000 000€+</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label><?php echo t('bedrooms'); ?></label>
                    <select aria-label="<?php echo t('bedrooms'); ?>" id="bedrooms">
                        <option value=""><?php echo t('all'); ?></option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label><?php echo t('bathrooms'); ?></label>
                    <select aria-label="<?php echo t('bathrooms'); ?>" id="bathrooms">
                        <option value=""><?php echo t('all'); ?></option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                    </select>
                </div>
                <button class="filter-more" aria-label="<?php echo t('more_filters'); ?>" onclick="applyFilters()">
                    <i class="fas fa-sliders-h"></i> <?php echo t('filter'); ?>
                </button>
            </div>
        </div>
    </section>

    <section class="trending-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('trending_properties'); ?></h2>
                <p><?php echo t('most_viewed_saved'); ?></p>
            </div>
            
            <div class="properties-carousel">
                <button class="carousel-btn prev" aria-label="<?php echo t('previous'); ?>"><i class="fas fa-chevron-left"></i></button>
                <div class="properties-grid" id="propertiesGrid">
                    <!-- Properties will be loaded dynamically from database -->
                </div>
                <button class="carousel-btn next" aria-label="<?php echo t('next'); ?>"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <section class="services-section">
        <div class="container">
            <div class="services-grid">
                <div class="service-card" onclick="redirectTo('buy.php')">
                    <div class="service-image" style="background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600');">
                        <div class="service-overlay"></div>
                    </div>
                    <div class="service-content">
                        <i class="fas fa-home service-icon"></i>
                        <h3><?php echo t('buy_home'); ?></h3>
                        <p><?php echo t('agent_help'); ?></p>
                        <span class="service-link"><?php echo t('find_local_agent'); ?> <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>

                <div class="service-card" onclick="redirectTo('financing.php')">
                    <div class="service-image" style="background-image: url('https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600');">
                        <div class="service-overlay"></div>
                    </div>
                    <div class="service-content">
                        <i class="fas fa-hand-holding-usd service-icon"></i>
                        <h3><?php echo t('finance_purchase'); ?></h3>
                        <p><?php echo t('get_pre_approved'); ?></p>
                        <span class="service-link"><?php echo t('start_now'); ?> <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>

                <div class="service-card" onclick="redirectTo('sell.php')">
                    <div class="service-image" style="background-image: url('https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=600');">
                        <div class="service-overlay"></div>
                    </div>
                    <div class="service-content">
                        <i class="fas fa-dollar-sign service-icon"></i>
                        <h3><?php echo t('sell_home'); ?></h3>
                        <p><?php echo t('sell_success'); ?></p>
                        <span class="service-link"><?php echo t('see_options'); ?> <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">1M+</div>
                    <div class="stat-label"><?php echo t('listed_properties'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label"><?php echo t('sales_completed'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">2K+</div>
                    <div class="stat-label"><?php echo t('partner_agents'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label"><?php echo t('satisfied_clients'); ?></div>
                </div>
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
                        <li><a href="buy.php"><?php echo t('houses'); ?></a></li>
                        <li><a href="buy.php"><?php echo t('apartments'); ?></a></li>
                        <li><a href="buy.php"><?php echo t('villas'); ?></a></li>
                        <li><a href="buy.php"><?php echo t('lands'); ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('services'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo t('free_estimation'); ?></a></li>
                        <li><a href="financing.php"><?php echo t('financing'); ?></a></li>
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
        // Function to redirect to pages with login check
        function redirectTo(page) {
            <?php if ($isLoggedIn): ?>
                window.location.href = page;
            <?php else: ?>
                if (confirm("<?php echo t('login_required'); ?>")) {
                    window.location.href = 'login.php';
                }
            <?php endif; ?>
        }
        
        // Search function
        function performSearch() {
            const searchTerm = document.getElementById('mainSearchInput').value;
            window.location.href = 'search_properties.php?search=' + encodeURIComponent(searchTerm);
        }

        // Apply filters function
        function applyFilters() {
            const type = document.getElementById('propertyType').value;
            const price = document.getElementById('maxPrice').value;
            const bedrooms = document.getElementById('bedrooms').value;
            const bathrooms = document.getElementById('bathrooms').value;
            
            let url = 'search_properties.php?';
            const params = [];
            
            if (type) params.push('type=' + type);
            if (price) params.push('max_price=' + price);
            if (bedrooms) params.push('bedrooms=' + bedrooms);
            if (bathrooms) params.push('bathrooms=' + bathrooms);
            
            if (params.length > 0) {
                url += params.join('&');
                window.location.href = url;
            }
        }

        // Handle Enter key in search input
        document.getElementById('mainSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const tab = this.dataset.tab;
                const searchInput = document.getElementById('mainSearchInput');
                
                if (tab === 'buy') {
                    searchInput.placeholder = '<?php echo t('search_buy'); ?>';
                } else if (tab === 'rent') {
                    searchInput.placeholder = '<?php echo t('search_rent'); ?>';
                } else {
                    searchInput.placeholder = '<?php echo t('search_sell'); ?>';
                }
            });
        });
        
        // Simple dropdown menu functions
        function showLanguageMenu(event) {
            event.preventDefault();
            hideAllSubmenus();
            const submenu = document.getElementById('languageSubmenu');
            if (submenu) {
                submenu.style.display = 'block';
            }
        }
        
        function showThemeMenu(event) {
            event.preventDefault();
            hideAllSubmenus();
            const submenu = document.getElementById('themeSubmenu');
            if (submenu) {
                submenu.style.display = 'block';
            }
        }
        
        function hideAllSubmenus() {
            const submenus = document.querySelectorAll('.submenu');
            submenus.forEach(menu => {
                menu.style.display = 'none';
            });
        }
        
        // Main dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            const dropbtn = document.querySelector('.dropbtn');
            const dropdownContent = document.querySelector('.dropdown-content');
            
            if (dropdownContent) {
                dropdownContent.style.display = 'none';
            }
            
            
            if (dropbtn && dropdownContent) {
                dropbtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isVisible = dropdownContent.style.display === 'block';
                    hideAllSubmenus();
                    dropdownContent.style.display = isVisible ? 'none' : 'block';
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    if (dropdownContent) {
                        dropdownContent.style.display = 'none';
                    }
                    hideAllSubmenus();
                }
            });
        });
    </script>

    <script>
        // Load trending properties from database
        document.addEventListener('DOMContentLoaded', function() {
            fetch('get_trending_properties.php')
                .then(response => response.json())
                .then(properties => {
                    const grid = document.getElementById('propertiesGrid');
                    grid.innerHTML = '';
                    
                    // Get favorites from localStorage
                    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
                    
                    properties.forEach(property => {
                        const isFavorite = favorites.includes(property.id);
                        const card = document.createElement('div');
                        card.className = 'property-card';
                        card.innerHTML = `
                            <div class="property-image" style="background-image: url('${property.image}');">
                                <span class="property-badge">${property.status}</span>
                                <div class="property-favorite ${isFavorite ? 'active' : ''}" data-property-id="${property.id}">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                            <div class="property-info">
                                <div class="property-price">${parseInt(property.price).toLocaleString('<?php echo $currentLang === 'fr' ? 'fr-FR' : 'en-US'; ?>')} €</div>
                                <div class="property-details">
                                    <div class="property-detail">
                                        <i class="fas fa-bed"></i>
                                        <span>${property.beds} <?php echo t('beds_abbr'); ?></span>
                                    </div>
                                    <div class="property-detail">
                                        <i class="fas fa-bath"></i>
                                        <span>${property.baths} <?php echo t('baths_abbr'); ?></span>
                                    </div>
                                    <div class="property-detail">
                                        <i class="fas fa-ruler-combined"></i>
                                        <span>${property.sqft} m²</span>
                                    </div>
                                </div>
                                <div class="property-address">${property.address}</div>
                                <div class="property-meta"><?php echo t('agent'); ?>: ${property.agent}</div>
                            </div>
                        `;
                        
                        // Add click event to property card
                        card.addEventListener('click', (e) => {
                            // Don't trigger when clicking on the favorite button
                            if (!e.target.closest('.property-favorite')) {
                                alert(`<?php echo t('property_details'); ?>:
<?php echo t('price'); ?>: ${parseInt(property.price).toLocaleString('<?php echo $currentLang === 'fr' ? 'fr-FR' : 'en-US'; ?>')} €
<?php echo t('address'); ?>: ${property.address}
<?php echo t('bedrooms'); ?>: ${property.beds}
<?php echo t('bathrooms'); ?>: ${property.baths}
<?php echo t('area'); ?>: ${property.sqft} m²
<?php echo t('agent'); ?>: ${property.agent}`);
                            }
                        });
                        
                        grid.appendChild(card);
                    });
                    
                    // Add event listeners to favorite buttons
                    document.querySelectorAll('.property-favorite').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation();
                            toggleFavorite(this);
                        });
                    });
                })
                .catch(error => {
                    console.error('<?php echo t('error_loading_properties'); ?>:', error);
                });
        });
        
        // Toggle favorite status
        function toggleFavorite(button) {
            const propertyId = parseInt(button.getAttribute('data-property-id'));
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            if (button.classList.contains('active')) {
                // Remove from favorites
                favorites = favorites.filter(id => id !== propertyId);
                button.classList.remove('active');
            } else {
                // Add to favorites
                favorites.push(propertyId);
                button.classList.add('active');
            }
            
            // Save to localStorage
            localStorage.setItem('favorites', JSON.stringify(favorites));
        }
    </script>
    
    <style>
        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1001;
            border-radius: 8px;
            top: 100%;
            right: 0;
            left: auto;
        }
        
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
            border-radius: 4px;
        }
        
        /* Dark theme adjustments */
        body.dark .dropdown-content {
            background-color: #2D2D2D;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.4);
        }
        
        body.dark .dropdown-content a {
            color: white;
        }
        
        body.dark .dropdown-content a:hover {
            background-color: #3D3D3D;
        }
        
        /* Remove this hover functionality since we're using click */
        /* .dropdown:hover .dropdown-content {
            display: block;
        } */
        
        .dropbtn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }
        
        .dropbtn i {
            font-size: 20px;
        }
        
        /* Submenu styles */
        .submenu {
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1002;
            left: 100%;
            top: 0;
            margin-left: 5px;
            display: none;
        }
        
        .submenu a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .submenu a:hover {
            background-color: #f1f1f1;
            border-radius: 4px;
        }
        
        /* Dark theme adjustments for submenu */
        body.dark .submenu {
            background-color: #2D2D2D;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.4);
        }
        
        body.dark .submenu a {
            color: white;
        }
        
        body.dark .submenu a:hover {
            background-color: #3D3D3D;
        }
        
        .service-link {
            cursor: pointer;
        }
    </style>
</body>
</html>