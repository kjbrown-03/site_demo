<?php
// Language handler - manages user language preferences

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to get the current language
function getCurrentLanguage() {
    // Check if language is set in URL parameter (highest priority)
    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        if (in_array($lang, ['fr', 'en'])) {
            $_SESSION['language'] = $lang;
            // If user is logged in, save preference to database
            if (isset($_SESSION['user_id'])) {
                // Use global pdo variable
                global $pdo;
                // Include config only when needed to avoid conflicts
                if (!isset($pdo) || is_null($pdo)) {
                    require_once 'config.php';
                    global $pdo;
                }
                try {
                    $stmt = $pdo->prepare("UPDATE users SET language_preference = ? WHERE id = ?");
                    $stmt->execute([$lang, $_SESSION['user_id']]);
                } catch (PDOException $e) {
                    // Continue silently if update fails
                }
            }
            return $lang;
        }
    }
    
    // Check if language is set in session
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    
    // Check if user is logged in and has language preference
    if (isset($_SESSION['user_id'])) {
        // Use global pdo variable
        global $pdo;
        // Include config only when needed to avoid conflicts
        if (!isset($pdo) || is_null($pdo)) {
            require_once 'config.php';
            global $pdo;
        }
        try {
            $stmt = $pdo->prepare("SELECT language_preference FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && !empty($user['language_preference'])) {
                $_SESSION['language'] = $user['language_preference'];
                return $user['language_preference'];
            }
        } catch (PDOException $e) {
            // If there's an error, continue with default
        }
    }
    
    // Default to French
    return 'fr';
}

// Function to get the current theme
function getCurrentTheme() {
    // Check if theme is set in URL parameter (highest priority)
    if (isset($_GET['theme'])) {
        $theme = $_GET['theme'];
        if (in_array($theme, ['light', 'dark'])) {
            $_SESSION['theme'] = $theme;
            // If user is logged in, save preference to database
            if (isset($_SESSION['user_id'])) {
                // Use global pdo variable
                global $pdo;
                // Include config only when needed to avoid conflicts
                if (!isset($pdo) || is_null($pdo)) {
                    require_once 'config.php';
                    global $pdo;
                }
                try {
                    $stmt = $pdo->prepare("UPDATE users SET theme_preference = ? WHERE id = ?");
                    $stmt->execute([$theme, $_SESSION['user_id']]);
                } catch (PDOException $e) {
                    // Continue silently if update fails
                }
            }
            return $theme;
        }
    }
    
    // Check if theme is set in session
    if (isset($_SESSION['theme'])) {
        return $_SESSION['theme'];
    }
    
    // Check if user is logged in and has theme preference
    if (isset($_SESSION['user_id'])) {
        // Use global pdo variable
        global $pdo;
        // Include config only when needed to avoid conflicts
        if (!isset($pdo) || is_null($pdo)) {
            require_once 'config.php';
            global $pdo;
        }
        try {
            $stmt = $pdo->prepare("SELECT theme_preference FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && !empty($user['theme_preference'])) {
                $_SESSION['theme'] = $user['theme_preference'];
                return $user['theme_preference'];
            }
        } catch (PDOException $e) {
            // If there's an error, continue with default
        }
    }
    
    // Default to light theme
    return 'light';
}

// Function to set language
function setLanguage($lang) {
    if (in_array($lang, ['fr', 'en'])) {
        $_SESSION['language'] = $lang;
        // If user is logged in, save preference to database
        if (isset($_SESSION['user_id'])) {
            // Use global pdo variable
            global $pdo;
            // Include config only when needed to avoid conflicts
            if (!isset($pdo) || is_null($pdo)) {
                require_once 'config.php';
                global $pdo;
            }
            try {
                $stmt = $pdo->prepare("UPDATE users SET language_preference = ? WHERE id = ?");
                $stmt->execute([$lang, $_SESSION['user_id']]);
            } catch (PDOException $e) {
                // Continue silently if update fails
            }
        }
    }
}

// Function to set theme
function setTheme($theme) {
    if (in_array($theme, ['light', 'dark'])) {
        $_SESSION['theme'] = $theme;
        // If user is logged in, save preference to database
        if (isset($_SESSION['user_id'])) {
            // Use global pdo variable
            global $pdo;
            // Include config only when needed to avoid conflicts
            if (!isset($pdo) || is_null($pdo)) {
                require_once 'config.php';
                global $pdo;
            }
            try {
                $stmt = $pdo->prepare("UPDATE users SET theme_preference = ? WHERE id = ?");
                $stmt->execute([$theme, $_SESSION['user_id']]);
            } catch (PDOException $e) {
                // Continue silently if update fails
            }
        }
    }
}

// Get current language and theme
$currentLang = getCurrentLanguage();
$currentTheme = getCurrentTheme();

// Set HTML language attribute
$htmlLang = $currentLang;

// Make variables globally available
$GLOBALS['currentLang'] = $currentLang;
$GLOBALS['currentTheme'] = $currentTheme;
$GLOBALS['htmlLang'] = $htmlLang;

// Language translations array
$translations = [
    'fr' => [
        'welcome' => 'Bienvenue',
        'home' => 'Accueil',
        'buy' => 'Acheter',
        'rent' => 'Louer',
        'sell' => 'Vendre',
        'agents' => 'Agents',
        'financing' => 'Financement',
        'login' => 'Connexion',
        'logout' => 'Déconnexion',
        'register' => 'Inscription',
        'dashboard' => 'Tableau de bord',
        'account_settings' => 'Paramètres du compte',
        'hello' => 'Bonjour',
        'profile' => 'Profil',
        'security' => 'Sécurité',
        'notifications' => 'Notifications',
        'favorites' => 'Favoris',
        'billing' => 'Facturation',
        'save_changes' => 'Enregistrer les modifications',
        'cancel' => 'Annuler',
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'email' => 'Adresse Email',
        'phone' => 'Numéro de Téléphone',
        'city' => 'Ville',
        'country' => 'Pays',
        'language' => 'Langue',
        'theme' => 'Thème',
        'light_theme' => 'Clair',
        'dark_theme' => 'Sombre',
        'english' => 'Anglais',
        'french' => 'Français',
        'email_notifications' => 'Notifications par Email',
        'search_alerts' => 'Alertes de Recherche',
        'newsletter' => 'Newsletter',
        'receive_important_emails' => 'Recevoir des notifications importantes par email',
        'receive_property_alerts' => 'Recevoir des alertes pour les nouvelles propriétés correspondant à vos recherches',
        'receive_monthly_newsletter' => 'Recevoir notre newsletter mensuelle avec les dernières tendances immobilières',
        'update_profile' => 'Mettre à jour le profil',
        'preferences' => 'Préférences',
        'personalize_experience' => 'Personnalisez votre expérience',
        'account_updated' => 'Compte mis à jour avec succès!',
        'error_updating_account' => 'Erreur lors de la mise à jour du compte',
        'search' => 'Rechercher',
        'property_type' => 'Type de bien',
        'all' => 'Tous',
        'house' => 'Maison',
        'apartment' => 'Appartement',
        'villa' => 'Villa',
        'land' => 'Terrain',
        'max_price' => 'Prix max',
        'bedrooms' => 'Chambres',
        'bathrooms' => 'Salles de bain',
        'more_filters' => 'Plus de filtres',
        'filter' => 'Filtrer',
        'trending_properties' => 'Propriétés tendances',
        'most_viewed_saved' => 'Les plus vues et sauvegardées ces dernières 24 heures',
        'buy_home' => 'Acheter une maison',
        'agent_help' => 'Un agent immobilier peut vous fournir une ventilation claire des coûts pour éviter les dépenses surprises.',
        'find_local_agent' => 'Trouver un agent local',
        'finance_purchase' => 'Financer votre achat',
        'get_pre_approved' => 'Obtenez une pré-approbation pour être prêt à faire une offre rapidement quand vous trouvez la bonne maison.',
        'start_now' => 'Commencer maintenant',
        'sell_home' => 'Vendre votre maison',
        'sell_success' => 'Quel que soit le chemin que vous prenez pour vendre votre maison, nous pouvons vous aider à réussir.',
        'see_options' => 'Voir vos options',
        'listed_properties' => 'Propriétés listées',
        'sales_completed' => 'Ventes réalisées',
        'partner_agents' => 'Agents partenaires',
        'satisfied_clients' => 'Clients satisfaits',
        'trusted_partner' => 'Votre partenaire de confiance pour trouver la maison parfaite.',
        'services' => 'Services',
        'free_estimation' => 'Estimation gratuite',
        'insurance' => 'Assurance',
        'moving' => 'Déménagement',
        'company' => 'Société',
        'about' => 'À propos',
        'careers' => 'Carrières',
        'contact' => 'Contact',
        'blog' => 'Blog',
        'all_rights_reserved' => 'Tous droits réservés.',
        'login_required' => 'Vous devez vous connecter pour accéder à cette fonctionnalité. Souhaitez-vous vous connecter maintenant ?',
        'houses' => 'Maisons',
        'apartments' => 'Appartements',
        'villas' => 'Villas',
        'lands' => 'Terrains',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'beds_abbr' => 'ch',
        'baths_abbr' => 'sdb',
        'agent' => 'Agent',
        'property_details' => 'Détails de la propriété',
        'price' => 'Prix',
        'address' => 'Adresse',
        'area' => 'Surface',
        'error_loading_properties' => 'Erreur lors du chargement des propriétés',
        'buy_your_dream_home' => 'Acheter Votre Maison de Rêve',
        'find_your_perfect_home' => 'Trouvez la propriété parfaite pour l\'appeler vôtre',
        'search_properties' => 'Rechercher des propriétés',
        'min_price' => 'Prix min',
        'any' => 'Tout',
        'search_results' => 'Résultats de recherche',
        'found_properties' => 'propriétés trouvées',
        'view_details' => 'Voir les détails',
        'contact_agent' => 'Contacter l\'agent',
        'no_properties_found' => 'Aucune propriété trouvée',
        'try_different_search' => 'Essayez une recherche différente',
        'find_rental' => 'Trouvez votre location idéale',
        'thousands_properties' => 'Des milliers de propriétés disponibles pour vous',
        'search_placeholder' => 'Ville, quartier, code postal...',
        'properties_for_rent' => 'Propriétés à louer',
        'properties_available' => 'propriétés disponibles',
        'for_rent' => 'À louer',
        'rent_now' => 'Louer Maintenant',
        'no_rental_found' => 'Aucune propriété de location trouvée',
        'try_later' => 'Revenez plus tard pour de nouvelles annonces',
        'rental_history' => 'Votre Historique de Locations',
        'past_rentals' => 'locations passées',
        'date' => 'Date',
        'status' => 'Statut',
        'footer_tagline' => 'Votre partenaire de confiance pour trouver la maison parfaite.',
        'rental' => 'Location',
        'confirm_rent' => 'Êtes-vous sûr de vouloir louer cette propriété ?',
        'rental_request_submitted' => 'Demande de location soumise avec succès pour la propriété',
        'property_listing_submitted' => 'Annonce de propriété soumise avec succès !',
        'error_submitting_listing' => 'Erreur lors de la soumission de l\'annonce',
        'sell_your_property' => 'Vendez Votre Propriété',
        'trusted_experts' => 'Faites confiance à nos experts pour maximiser votre vente',
        'why_sell_with_us' => 'Pourquoi vendre avec nous ?',
        'maximum_exposure' => 'Exposition Maximale',
        'exposure_description' => 'Votre propriété sera présentée sur tous nos canaux de diffusion pour atteindre le plus grand nombre d\'acheteurs potentiels.',
        'targeted_marketing' => 'Marketing Ciblé',
        'marketing_description' => 'Nous utilisons des techniques de marketing avancées pour attirer les acheteurs qualifiés intéressés par votre type de propriété.',
        'expert_negotiation' => 'Négociation Expert',
        'expert_negotiation_description' => 'Nos agents expérimentés vous représentent pour obtenir le meilleur prix possible pour votre propriété.',
        'list_your_property' => 'Mettez Votre Propriété en Vente',
        'provide_details' => 'Fournissez les détails de votre propriété ci-dessous',
        'property_title' => 'Titre de la Propriété',
        'enter_title' => 'Entrez le titre de votre propriété',
        'description' => 'Description',
        'describe_property' => 'Décrivez en détail votre propriété',
        'enter_price' => 'Entrez le prix',
        'select_type' => 'Sélectionnez le type',
        'enter_address' => 'Entrez l\'adresse',
        'enter_city' => 'Entrez la ville',
        'enter_area' => 'Entrez la surface',
        'select_bedrooms' => 'Sélectionnez les chambres',
        'select_bathrooms' => 'Sélectionnez les salles de bain',
        'submit_listing' => 'Soumettre l\'Annonce'
    ],
    'en' => [
        'welcome' => 'Welcome',
        'home' => 'Home',
        'buy' => 'Buy',
        'rent' => 'Rent',
        'sell' => 'Sell',
        'agents' => 'Agents',
        'financing' => 'Financing',
        'login' => 'Login',
        'logout' => 'Logout',
        'register' => 'Register',
        'dashboard' => 'Dashboard',
        'account_settings' => 'Account Settings',
        'hello' => 'Hello',
        'profile' => 'Profile',
        'security' => 'Security',
        'notifications' => 'Notifications',
        'favorites' => 'Favorites',
        'billing' => 'Billing',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'city' => 'City',
        'country' => 'Country',
        'language' => 'Language',
        'theme' => 'Theme',
        'light_theme' => 'Light',
        'dark_theme' => 'Dark',
        'english' => 'English',
        'french' => 'French',
        'email_notifications' => 'Email Notifications',
        'search_alerts' => 'Search Alerts',
        'newsletter' => 'Newsletter',
        'receive_important_emails' => 'Receive important notifications by email',
        'receive_property_alerts' => 'Receive alerts for new properties matching your searches',
        'receive_monthly_newsletter' => 'Receive our monthly newsletter with the latest real estate trends',
        'update_profile' => 'Update Profile',
        'preferences' => 'Preferences',
        'personalize_experience' => 'Personalize your experience',
        'account_updated' => 'Account updated successfully!',
        'error_updating_account' => 'Error updating account',
        'search' => 'Search',
        'property_type' => 'Property Type',
        'all' => 'All',
        'house' => 'House',
        'apartment' => 'Apartment',
        'villa' => 'Villa',
        'land' => 'Land',
        'max_price' => 'Max Price',
        'bedrooms' => 'Bedrooms',
        'bathrooms' => 'Bathrooms',
        'more_filters' => 'More Filters',
        'filter' => 'Filter',
        'trending_properties' => 'Trending Properties',
        'most_viewed_saved' => 'Most viewed and saved in the last 24 hours',
        'buy_home' => 'Buy a Home',
        'agent_help' => 'A real estate agent can provide you with a clear breakdown of costs to avoid surprise expenses.',
        'find_local_agent' => 'Find a Local Agent',
        'finance_purchase' => 'Finance Your Purchase',
        'get_pre_approved' => 'Get pre-approved to be ready to make an offer quickly when you find the right home.',
        'start_now' => 'Start Now',
        'sell_home' => 'Sell Your Home',
        'sell_success' => 'Whatever path you take to sell your home, we can help you succeed.',
        'see_options' => 'See Your Options',
        'listed_properties' => 'Listed Properties',
        'sales_completed' => 'Sales Completed',
        'partner_agents' => 'Partner Agents',
        'satisfied_clients' => 'Satisfied Clients',
        'trusted_partner' => 'Your trusted partner to find the perfect home.',
        'services' => 'Services',
        'free_estimation' => 'Free Estimation',
        'insurance' => 'Insurance',
        'moving' => 'Moving',
        'company' => 'Company',
        'about' => 'About',
        'careers' => 'Careers',
        'contact' => 'Contact',
        'blog' => 'Blog',
        'all_rights_reserved' => 'All rights reserved.',
        'login_required' => 'You need to log in to access this feature. Would you like to log in now?',
        'houses' => 'Houses',
        'apartments' => 'Apartments',
        'villas' => 'Villas',
        'lands' => 'Lands',
        'previous' => 'Previous',
        'next' => 'Next',
        'beds_abbr' => 'bd',
        'baths_abbr' => 'ba',
        'agent' => 'Agent',
        'property_details' => 'Property Details',
        'price' => 'Price',
        'address' => 'Address',
        'area' => 'Area',
        'error_loading_properties' => 'Error loading properties',
        'buy_your_dream_home' => 'Buy Your Dream Home',
        'find_your_perfect_home' => 'Find the perfect property to call your own',
        'search_properties' => 'Search Properties',
        'min_price' => 'Min Price',
        'any' => 'Any',
        'search_results' => 'Search Results',
        'found_properties' => 'properties found',
        'view_details' => 'View Details',
        'contact_agent' => 'Contact Agent',
        'no_properties_found' => 'No properties found',
        'try_different_search' => 'Try a different search',
        'find_rental' => 'Find your ideal rental',
        'thousands_properties' => 'Thousands of properties available for you',
        'search_placeholder' => 'City, neighborhood, zip code...',
        'properties_for_rent' => 'Properties for rent',
        'properties_available' => 'properties available',
        'for_rent' => 'For rent',
        'rent_now' => 'Rent Now',
        'no_rental_found' => 'No rental properties found',
        'try_later' => 'Come back later for new listings',
        'rental_history' => 'Your Rental History',
        'past_rentals' => 'past rentals',
        'date' => 'Date',
        'status' => 'Status',
        'footer_tagline' => 'Your trusted partner to find the perfect home.',
        'rental' => 'Rental',
        'confirm_rent' => 'Are you sure you want to rent this property?',
        'rental_request_submitted' => 'Rental request submitted successfully for property',
        'property_listing_submitted' => 'Property listing submitted successfully!',
        'error_submitting_listing' => 'Error submitting listing',
        'sell_your_property' => 'Sell Your Property',
        'trusted_experts' => 'Trust our experts to maximize your sale',
        'why_sell_with_us' => 'Why sell with us?',
        'maximum_exposure' => 'Maximum Exposure',
        'exposure_description' => 'Your property will be showcased across all our distribution channels to reach the largest number of potential buyers.',
        'targeted_marketing' => 'Targeted Marketing',
        'marketing_description' => 'We use advanced marketing techniques to attract qualified buyers interested in your type of property.',
        'expert_negotiation' => 'Expert Negotiation',
        'expert_negotiation_description' => 'Our experienced agents represent you to obtain the best possible price for your property.',
        'list_your_property' => 'List Your Property for Sale',
        'provide_details' => 'Provide your property details below',
        'property_title' => 'Property Title',
        'enter_title' => 'Enter your property title',
        'description' => 'Description',
        'describe_property' => 'Describe your property in detail',
        'enter_price' => 'Enter price',
        'select_type' => 'Select type',
        'enter_address' => 'Enter address',
        'enter_city' => 'Enter city',
        'enter_area' => 'Enter area',
        'select_bedrooms' => 'Select bedrooms',
        'select_bathrooms' => 'Select bathrooms',
        'submit_listing' => 'Submit Listing'
    ]
];

// Function to translate a key
function t($key) {
    global $translations;
    // Get current language from GLOBALS
    $currentLang = isset($GLOBALS['currentLang']) ? $GLOBALS['currentLang'] : 'fr';
    if (isset($translations[$currentLang][$key])) {
        return $translations[$currentLang][$key];
    }
    // Return the key if translation not found
    return $key;
}

// Function to get language switcher URL
function getLanguageSwitcherUrl($lang) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $urlParts = parse_url($currentUrl);
    $queryParams = [];
    
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }
    
    $queryParams['lang'] = $lang;
    
    $newQuery = http_build_query($queryParams);
    $baseUrl = $urlParts['path'];
    
    return $baseUrl . '?' . $newQuery;
}

// Function to get theme switcher URL
function getThemeSwitcherUrl($theme) {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $urlParts = parse_url($currentUrl);
    $queryParams = [];
    
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }
    
    // Preserve language parameter if it exists, otherwise use current language
    if (!isset($queryParams['lang'])) {
        $queryParams['lang'] = isset($GLOBALS['currentLang']) ? $GLOBALS['currentLang'] : 'fr';
    }
    
    $queryParams['theme'] = $theme;
    
    $newQuery = http_build_query($queryParams);
    $baseUrl = $urlParts['path'];
    
    return $baseUrl . '?' . $newQuery;
}
?>