<?php
session_start();
require_once 'config.php';
require_once 'includes/language_handler.php';
require_once 'includes/navigation.php';

// Function to check if current page is active
function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $page;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        
        $message = t('account_updated');
    } catch(PDOException $e) {
        $error = t('error_updating_account') . ': ' . $e->getMessage();
    }
}

// Fetch current user information
try {
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, city, country, language_preference, theme_preference, email_notifications, search_alerts, newsletter FROM users WHERE id = ?");
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
} catch(PDOException $e) {
    $error = "Error fetching user information: " . $e->getMessage();
    $firstName = $lastName = $email = $phone = $city = $country = '';
    $languagePreference = 'fr';
    $themePreference = 'light';
    $emailNotifications = $searchAlerts = 1;
    $newsletter = 0;
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('account_settings'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <header>
        <?php renderNavigation('account_settings.php', $username, $userRole); ?>
    </header>

    <section class="settings-hero">
        <div class="container">
            <h1><?php echo t('account_settings'); ?></h1>
            <p><?php echo t('personalize_experience'); ?></p>
        </div>
    </section>

    <section class="settings-content">
        <div class="container">
            <?php if (!empty($message)): ?>
                <div class="alert success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="settings-grid">
                <div class="settings-sidebar">
                    <div class="settings-menu">
                        <a href="#" class="settings-menu-item active">
                            <i class="fas fa-user"></i>
                            <span><?php echo t('profile'); ?></span>
                        </a>
                        <a href="#" class="settings-menu-item">
                            <i class="fas fa-lock"></i>
                            <span><?php echo t('security'); ?></span>
                        </a>
                        <a href="#" class="settings-menu-item">
                            <i class="fas fa-bell"></i>
                            <span><?php echo t('notifications'); ?></span>
                        </a>
                        <a href="#" class="settings-menu-item">
                            <i class="fas fa-heart"></i>
                            <span><?php echo t('favorites'); ?></span>
                        </a>
                        <a href="#" class="settings-menu-item">
                            <i class="fas fa-file-invoice"></i>
                            <span><?php echo t('billing'); ?></span>
                        </a>
                    </div>
                </div>
                
                <div class="settings-main">
                    <div class="settings-card">
                        <div class="settings-header">
                            <h2><?php echo t('update_profile'); ?></h2>
                            <p><?php echo t('personalize_experience'); ?></p>
                        </div>
                        
                        <form class="settings-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName"><?php echo t('first_name'); ?></label>
                                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" placeholder="<?php echo t('first_name'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName"><?php echo t('last_name'); ?></label>
                                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" placeholder="<?php echo t('last_name'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email"><?php echo t('email'); ?></label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="<?php echo t('email'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone"><?php echo t('phone'); ?></label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="<?php echo t('phone'); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city"><?php echo t('city'); ?></label>
                                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="<?php echo t('city'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="country"><?php echo t('country'); ?></label>
                                    <select id="country" name="country">
                                        <option value=""><?php echo t('country'); ?></option>
                                        <option value="fr" <?php echo $country === 'fr' ? 'selected' : ''; ?>>France</option>
                                        <option value="be" <?php echo $country === 'be' ? 'selected' : ''; ?>>Belgique</option>
                                        <option value="ch" <?php echo $country === 'ch' ? 'selected' : ''; ?>>Suisse</option>
                                    </select>
                                </div>
                            </div>
                            
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

                            <div class="form-actions">
                                <button type="reset" class="btn-secondary"><?php echo t('cancel'); ?></button>
                                <button type="submit" class="btn-primary"><?php echo t('save_changes'); ?></button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="settings-card">
                        <div class="settings-header">
                            <h2><?php echo t('preferences'); ?></h2>
                            <p><?php echo t('personalize_experience'); ?></p>
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
</body>
</html>