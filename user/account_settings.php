session_start();
require_once dirname(__DIR__) . '/config.php';

// Function to check if current page is active
function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $page;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Handle form submission
$message = '';
$error = '';

// Handle profile picture upload
if (isset($_POST['upload_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/profile_pictures/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        // Check if file extension is allowed
        if (in_array($file_extension, $allowed_extensions)) {
            // Generate unique filename
            $new_filename = uniqid() . '_' . $_SESSION['user_id'] . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                try {
                    // Update user profile picture in database
                    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                    $stmt->execute([$target_file, $_SESSION['user_id']]);
                    $message = 'Profile picture updated successfully!';
                } catch(PDOException $e) {
                    $error = 'Error updating profile picture: ' . $e->getMessage();
                }
            } else {
                $error = 'Error uploading file.';
            }
        } else {
            $error = 'Invalid file type. Please upload JPG, JPEG, PNG, or GIF files only.';
        }
    } else {
        $error = 'Please select a file to upload.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['upload_picture'])) {
    try {
        // Update user profile information
        $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
        $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $language = isset($_POST['language']) ? $_POST['language'] : 'fr';
        $theme = isset($_POST['theme']) ? $_POST['theme'] : 'light';
        $emailNotifications = isset($_POST['email-notifications']) ? 1 : 0;
        $searchAlerts = isset($_POST['search-alerts']) ? 1 : 0;
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;
        
        // Update user information in database
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, city = ?, country = ?, language_preference = ?, theme_preference = ?, email_notifications = ?, search_alerts = ?, newsletter = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $email, $phone, $city, $country, $language, $theme, $emailNotifications, $searchAlerts, $newsletter, $_SESSION['user_id']]);
        
        // Update session language/theme if changed
        if ($language !== $_SESSION['language']) {
            $_SESSION['language'] = $language;
        }
        if ($theme !== $_SESSION['theme']) {
            $_SESSION['theme'] = $theme;
        }
        
        $message = 'Account updated successfully!';
    } catch(PDOException $e) {
        $error = 'Error updating account: ' . $e->getMessage();
    }
}

// Fetch current user information
try {
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, city, country, language_preference, theme_preference, email_notifications, search_alerts, newsletter, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Set variables for form population
    $firstName = isset($user['first_name']) ? $user['first_name'] : '';
    $lastName = isset($user['last_name']) ? $user['last_name'] : '';
    $email = isset($user['email']) ? $user['email'] : '';
    $phone = isset($user['phone']) ? $user['phone'] : '';
    $city = isset($user['city']) ? $user['city'] : '';
    $country = isset($user['country']) ? $user['country'] : '';
    $languagePreference = isset($user['language_preference']) ? $user['language_preference'] : 'fr';
    $themePreference = isset($user['theme_preference']) ? $user['theme_preference'] : 'light';
    $emailNotifications = isset($user['email_notifications']) ? $user['email_notifications'] : 1;
    $searchAlerts = isset($user['search_alerts']) ? $user['search_alerts'] : 1;
    $newsletter = isset($user['newsletter']) ? $user['newsletter'] : 0;
    $profilePicture = isset($user['profile_picture']) ? $user['profile_picture'] : '';
} catch(PDOException $e) {
    $error = "Error fetching user information: " . $e->getMessage();
    $firstName = $lastName = $email = $phone = $city = $country = '';
    $languagePreference = 'fr';
    $themePreference = 'light';
    $emailNotifications = $searchAlerts = 1;
    $newsletter = 0;
    $profilePicture = '';
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Simple translation function
function t($key) {
    $translations = [
        'fr' => [
            'account_settings' => 'Paramètres du compte',
            'personalize_experience' => 'Personnalisez votre expérience',
            'profile' => 'Profil',
            'security' => 'Sécurité',
            'notifications' => 'Notifications',
            'favorites' => 'Favoris',
            'billing' => 'Facturation',
            'update_profile' => 'Mettre à jour le profil',
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
            'cancel' => 'Annuler',
            'save_changes' => 'Enregistrer les modifications',
            'preferences' => 'Préférences',
            'email_notifications' => 'Notifications par Email',
            'search_alerts' => 'Alertes de Recherche',
            'newsletter' => 'Newsletter',
            'receive_important_emails' => 'Recevoir des notifications importantes par email',
            'receive_property_alerts' => 'Recevoir des alertes pour les nouvelles propriétés correspondant à vos recherches',
            'receive_monthly_newsletter' => 'Recevoir notre newsletter mensuelle avec les dernières tendances immobilières',
            'profile_picture' => 'Photo de Profil',
            'change_your_profile_picture' => 'Changez votre photo de profil',
            'no_profile_picture' => 'Aucune photo de profil',
            'select_new_picture' => 'Sélectionner une nouvelle photo',
            'supported_formats' => 'Formats supportés: JPG, JPEG, PNG, GIF',
            'upload_picture' => 'Télécharger la photo',
            'language_and_theme' => 'Langue et Thème',
            'customize_interface' => 'Personnalisez l\'interface',
            'user_information' => 'Informations Utilisateur',
            'manage_personal_details' => 'Gérez vos informations personnelles',
            'manage_notification_preferences' => 'Gérez vos préférences de notification',
            'settings' => 'Paramètres',
            'user_information' => 'Informations Utilisateur',
            'logout' => 'Déconnexion'
        ],
        'en' => [
            'account_settings' => 'Account Settings',
            'personalize_experience' => 'Personalize your experience',
            'profile' => 'Profile',
            'security' => 'Security',
            'notifications' => 'Notifications',
            'favorites' => 'Favorites',
            'billing' => 'Billing',
            'update_profile' => 'Update Profile',
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
            'cancel' => 'Cancel',
            'save_changes' => 'Save Changes',
            'preferences' => 'Preferences',
            'email_notifications' => 'Email Notifications',
            'search_alerts' => 'Search Alerts',
            'newsletter' => 'Newsletter',
            'receive_important_emails' => 'Receive important notifications by email',
            'receive_property_alerts' => 'Receive alerts for new properties matching your searches',
            'receive_monthly_newsletter' => 'Receive our monthly newsletter with the latest real estate trends',
            'profile_picture' => 'Profile Picture',
            'change_your_profile_picture' => 'Change your profile picture',
            'no_profile_picture' => 'No profile picture',
            'select_new_picture' => 'Select new picture',
            'supported_formats' => 'Supported formats: JPG, JPEG, PNG, GIF',
            'upload_picture' => 'Upload Picture',
            'language_and_theme' => 'Language and Theme',
            'customize_interface' => 'Customize interface',
            'user_information' => 'User Information',
            'manage_personal_details' => 'Manage your personal details',
            'manage_notification_preferences' => 'Manage your notification preferences',
            'settings' => 'Settings',
            'user_information' => 'User Information',
            'logout' => 'Logout'
        ]
    ];
    
    $currentLang = isset($_SESSION['language']) ? $_SESSION['language'] : 'fr';
    if (isset($translations[$currentLang][$key])) {
        return $translations[$currentLang][$key];
    }
    return $key;
}
?>
<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['language']) ? $_SESSION['language'] : 'fr'; ?>" class="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('account_settings'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light'; ?>">
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='../index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <?php if ($userRole === 'buyer'): ?>
                <ul class="nav-links">
                    <li><a href="buyer_dashboard.php" class="<?php echo isActivePage('buyer_dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="search_properties.php" class="<?php echo isActivePage('search_properties.php') ? 'active' : ''; ?>"><?php echo t('search'); ?></a></li>
                    <li><a href="my_orders.php" class="<?php echo isActivePage('my_orders.php') ? 'active' : ''; ?>">Mes Commandes</a></li>
                    <li><a href="favorites.php" class="<?php echo isActivePage('favorites.php') ? 'active' : ''; ?>"><?php echo t('favorites'); ?></a></li>
                </ul>
                <?php elseif ($userRole === 'seller'): ?>
                <ul class="nav-links">
                    <li><a href="seller_dashboard.php" class="<?php echo isActivePage('seller_dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="my_properties.php" class="<?php echo isActivePage('my_properties.php') ? 'active' : ''; ?>">Mes Propriétés</a></li>
                    <li><a href="add_property.php" class="<?php echo isActivePage('add_property.php') ? 'active' : ''; ?>">Ajouter</a></li>
                    <li><a href="my_sales.php" class="<?php echo isActivePage('my_sales.php') ? 'active' : ''; ?>">Mes Ventes</a></li>
                    <li><a href="favorites.php" class="<?php echo isActivePage('favorites.php') ? 'active' : ''; ?>"><?php echo t('favorites'); ?></a></li>
                </ul>
                <?php elseif ($userRole === 'agent'): ?>
                <ul class="nav-links">
                    <li><a href="agent_dashboard.php" class="<?php echo isActivePage('agent_dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="my_listings.php" class="<?php echo isActivePage('my_listings.php') ? 'active' : ''; ?>">Mes Annonces</a></li>
                    <li><a href="client_leads.php" class="<?php echo isActivePage('client_leads.php') ? 'active' : ''; ?>">Prospects</a></li>
                    <li><a href="appointments.php" class="<?php echo isActivePage('appointments.php') ? 'active' : ''; ?>">Rendez-vous</a></li>
                    <li><a href="favorites.php" class="<?php echo isActivePage('favorites.php') ? 'active' : ''; ?>"><?php echo t('favorites'); ?></a></li>
                </ul>
                <?php endif; ?>
                <div class="nav-actions">
                    <div class="user-profile-dropdown">
                        <div class="user-avatar" onclick="toggleProfileDropdown()">
                            <?php if (!empty($profilePicture) && file_exists($profilePicture)): ?>
                                <img src="<?php echo $profilePicture; ?>" alt="Profile" class="profile-img">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-2x"></i>
                            <?php endif; ?>
                        </div>
                        <div class="profile-dropdown-content" id="profileDropdown">
                            <div class="profile-info">
                                <p><?php echo htmlspecialchars($username); ?></p>
                            </div>
                            <a href="account_settings.php"><i class="fas fa-cog"></i> <?php echo t('settings'); ?></a>
                            <a href="account_settings.php#language-theme"><i class="fas fa-language"></i> <?php echo t('language_and_theme'); ?></a>
                            <a href="account_settings.php#user-info"><i class="fas fa-user-edit"></i> <?php echo t('user_information'); ?></a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?php echo t('logout'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="settings-hero">
        <div class="container">
            <h1><?php echo t('account_settings'); ?></h1>
            <p><?php echo t('personalize_experience'); ?></p>
        </div>
    </section>

    <section class="settings-content">
        <div class="container">
            <div class="settings-grid">
                <div class="settings-sidebar">
                    <a href="#profile" class="settings-menu-item active"><i class="fas fa-user"></i><?php echo t('profile'); ?></a>
                    <a href="#security" class="settings-menu-item"><i class="fas fa-lock"></i><?php echo t('security'); ?></a>
                    <a href="#notifications" class="settings-menu-item"><i class="fas fa-bell"></i><?php echo t('notifications'); ?></a>
                    <a href="#favorites" class="settings-menu-item"><i class="fas fa-heart"></i><?php echo t('favorites'); ?></a>
                    <a href="#billing" class="settings-menu-item"><i class="fas fa-credit-card"></i><?php echo t('billing'); ?></a>
                </div>
                <div class="settings-main">
                    <div id="profile" class="settings-card">
                        <div class="settings-header">
                            <h2><?php echo t('profile'); ?></h2>
                            <p><?php echo t('update_profile'); ?></p>
                        </div>
                        <div class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName"><?php echo t('first_name'); ?></label>
                                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName"><?php echo t('last_name'); ?></label>
                                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email"><?php echo t('email'); ?></label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone"><?php echo t('phone'); ?></label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city"><?php echo t('city'); ?></label>
                                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="country"><?php echo t('country'); ?></label>
                                    <select id="country" name="country">
                                        <option value="Canada" <?php echo $country === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                        <option value="United States" <?php echo $country === 'United States' ? 'selected' : ''; ?>>United States</option>
                                        <option value="France" <?php echo $country === 'France' ? 'selected' : ''; ?>>France</option>
                                        <option value="Germany" <?php echo $country === 'Germany' ? 'selected' : ''; ?>>Germany</option>
                                        <option value="United Kingdom" <?php echo $country === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                                        <option value="Australia" <?php echo $country === 'Australia' ? 'selected' : ''; ?>>Australia</option>
                                        <option value="Japan" <?php echo $country === 'Japan' ? 'selected' : ''; ?>>Japan</option>
                                        <option value="China" <?php echo $country === 'China' ? 'selected' : ''; ?>>China</option>
                                        <option value="India" <?php echo $country === 'India' ? 'selected' : ''; ?>>India</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="reset" name="cancel"><?php echo t('cancel'); ?></button>
                                <button type="submit"><?php echo t('save_changes'); ?></button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="language-theme" class="settings-card">
                        <div class="settings-header">
                            <h2><?php echo t('language_and_theme'); ?></h2>
                            <p><?php echo t('customize_interface'); ?></p>
                        </div>
                        <div class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="language"><?php echo t('language'); ?></label>
                                    <select id="language" name="language">
                                        <option value="fr" <?php echo $languagePreference === 'fr' ? 'selected' : ''; ?>><?php echo t('french'); ?></option>
                                        <option value="en" <?php echo $languagePreference === 'en' ? 'selected' : ''; ?>><?php echo t('english'); ?></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="theme"><?php echo t('theme'); ?></label>
                                    <select id="theme" name="theme">
                                        <option value="light" <?php echo $themePreference === 'light' ? 'selected' : ''; ?>><?php echo t('light_theme'); ?></option>
                                        <option value="dark" <?php echo $themePreference === 'dark' ? 'selected' : ''; ?>><?php echo t('dark_theme'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="reset" name="cancel"><?php echo t('cancel'); ?></button>
                                <button type="submit"><?php echo t('save_changes'); ?></button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="user-info" class="settings-card">
                        <div class="settings-header">
                            <h2><?php echo t('user_information'); ?></h2>
                            <p><?php echo t('manage_personal_details'); ?></p>
                        </div>
                        <div class="settings-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName"><?php echo t('first_name'); ?></label>
                                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName"><?php echo t('last_name'); ?></label>
                                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email"><?php echo t('email'); ?></label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone"><?php echo t('phone'); ?></label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city"><?php echo t('city'); ?></label>
                                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="country"><?php echo t('country'); ?></label>
                                    <select id="country" name="country">
                                        <option value="Canada" <?php echo $country === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                        <option value="United States" <?php echo $country === 'United States' ? 'selected' : ''; ?>>United States</option>
                                        <option value="France" <?php echo $country === 'France' ? 'selected' : ''; ?>>France</option>
                                        <option value="Germany" <?php echo $country === 'Germany' ? 'selected' : ''; ?>>Germany</option>
                                        <option value="United Kingdom" <?php echo $country === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                                        <option value="Australia" <?php echo $country === 'Australia' ? 'selected' : ''; ?>>Australia</option>
                                        <option value="Japan" <?php echo $country === 'Japan' ? 'selected' : ''; ?>>Japan</option>
                                        <option value="China" <?php echo $country === 'China' ? 'selected' : ''; ?>>China</option>
                                        <option value="India" <?php echo $country === 'India' ? 'selected' : ''; ?>>India</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="reset" name="cancel"><?php echo t('cancel'); ?></button>
                                <button type="submit"><?php echo t('save_changes'); ?></button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="notifications" class="settings-card">
                        <div class="settings-header">
                            <h2><?php echo t('notifications'); ?></h2>
                            <p><?php echo t('manage_notification_preferences'); ?></p>
                        </div>
                        
                        <div class="preferences-list">
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3><?php echo t('email_notifications'); ?></h3>
                                    <p><?php echo t('receive_important_emails'); ?></p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="email-notifications" name="email-notifications" <?php echo $emailNotifications ? 'checked' : ''; ?>>
                                    <label for="email-notifications" class="switch-label"></label>
                                </div>
                            </div>
                            
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3><?php echo t('search_alerts'); ?></h3>
                                    <p><?php echo t('receive_property_alerts'); ?></p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="search-alerts" name="search-alerts" <?php echo $searchAlerts ? 'checked' : ''; ?>>
                                    <label for="search-alerts" class="switch-label"></label>
                                </div>
                            </div>
                            
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3><?php echo t('newsletter'); ?></h3>
                                    <p><?php echo t('receive_monthly_newsletter'); ?></p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="newsletter" name="newsletter" <?php echo $newsletter ? 'checked' : ''; ?>>
                                    <label for="newsletter" class="switch-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <style>
        .settings-hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .settings-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .settings-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .settings-content {
            padding: 80px 0;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }
        
        .settings-sidebar {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            height: fit-content;
        }
        
        .settings-menu-item {
            display: flex;
            align-items: center;
            padding: 15px;
            color: #6B6B6B;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .settings-menu-item:hover,
        .settings-menu-item.active {
            background: #006AFF;
            color: white;
        }
        
        .settings-menu-item i {
            margin-right: 15px;
            font-size: 18px;
        }
        
        .settings-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .settings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }
        
        .settings-header {
            margin-bottom: 30px;
        }
        
        .settings-header h2 {
            font-size: 28px;
            color: #1A1A1A;
            margin-bottom: 10px;
        }
        
        .settings-header p {
            color: #6B6B6B;
            margin: 0;
        }
        
        .settings-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #1A1A1A;
        }
        
        .form-group input,
        .form-group select {
            padding: 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #006AFF;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .preferences-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #E0E0E0;
        }
        
        .preference-item:last-child {
            border-bottom: none;
        }
        
        .preference-info h3 {
            font-size: 18px;
            color: #1A1A1A;
            margin-bottom: 5px;
        }
        
        .preference-info p {
            color: #6B6B6B;
            margin: 0;
        }
        
        .toggle-switch {
            position: relative;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
            width: 60px;
            height: 34px;
        }
        
        .switch-label:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .switch-label {
            background-color: #006AFF;
        }
        
        input:checked + .switch-label:before {
            transform: translateX(26px);
        }
        
        /* Profile picture styles */
        .profile-picture-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }
        
        .current-picture {
            text-align: center;
        }
        
        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #006AFF;
        }
        
        .profile-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            color: #666;
        }
        
        .profile-placeholder i {
            margin-bottom: 10px;
        }
        
        .picture-upload-form {
            width: 100%;
            max-width: 500px;
        }
        
        .help-text {
            font-size: 14px;
            color: #6B6B6B;
            margin-top: 5px;
        }
        
        /* User avatar styles */
        .user-avatar {
            cursor: pointer;
            position: relative;
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        @media (max-width: 768px) {
            .settings-hero h1 {
                font-size: 36px;
            }
            
            .settings-hero p {
                font-size: 18px;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .preference-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
    
    <script>
        function toggleProfileDropdown() {
            document.getElementById("profileDropdown").classList.toggle("show");
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-avatar') && !event.target.matches('.user-avatar *')) {
                var dropdowns = document.getElementsByClassName("profile-dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update active menu item
                    document.querySelectorAll('.settings-menu-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });
        
        // Set active menu item based on scroll position
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.settings-card');
            const menuItems = document.querySelectorAll('.settings-menu-item');
            
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            
            menuItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === '#' + current) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>