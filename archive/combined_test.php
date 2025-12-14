<?php
session_start();
require_once 'config.php';
require_once 'includes/language_handler.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Test - Language and Theme</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            transition: all 0.3s ease;
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        body.light .test-section {
            background: #fff;
            border: 1px solid #eee;
            color: #333;
        }
        body.dark .test-section {
            background: #2d2d2d;
            border: 1px solid #444;
            color: #fff;
        }
        .test-links a {
            display: inline-block;
            margin: 5px;
            padding: 8px 15px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .test-links a:hover {
            opacity: 0.8;
        }
        .settings-card {
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        body.light .settings-card {
            background: #fff;
            border: 1px solid #eee;
        }
        body.dark .settings-card {
            background: #2d2d2d;
            border: 1px solid #444;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        body.dark .form-group select {
            background: #3d3d3d;
            border: 1px solid #555;
            color: #fff;
        }
    </style>
</head>
<body class="<?php echo $currentTheme; ?>">
    <div class="test-section">
        <h1>Combined Test - Language and Theme Switching</h1>
        
        <h2>Current State:</h2>
        <p><strong>Current Language:</strong> <?php echo $currentLang; ?></p>
        <p><strong>Current Theme:</strong> <?php echo $currentTheme; ?></p>
        <p><strong>HTML Lang Attribute:</strong> <?php echo $htmlLang; ?></p>
        
        <h2>Translations:</h2>
        <p><strong>Welcome:</strong> <?php echo t('welcome'); ?></p>
        <p><strong>Theme:</strong> <?php echo t('theme'); ?></p>
        <p><strong>Language:</strong> <?php echo t('language'); ?></p>
    </div>
    
    <div class="settings-card">
        <h2>User Preferences</h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="language"><?php echo t('language'); ?>:</label>
                <select id="language" name="language">
                    <option value="fr" <?php echo $currentLang === 'fr' ? 'selected' : ''; ?>><?php echo t('french'); ?></option>
                    <option value="en" <?php echo $currentLang === 'en' ? 'selected' : ''; ?>><?php echo t('english'); ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="theme"><?php echo t('theme'); ?>:</label>
                <select id="theme" name="theme">
                    <option value="light" <?php echo $currentTheme === 'light' ? 'selected' : ''; ?>><?php echo t('light_theme'); ?></option>
                    <option value="dark" <?php echo $currentTheme === 'dark' ? 'selected' : ''; ?>><?php echo t('dark_theme'); ?></option>
                </select>
            </div>
            
            <div class="test-links">
                <button type="submit" style="border: none; cursor: pointer;"><?php echo t('save_changes'); ?></button>
            </div>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Quick Switching Tests:</h2>
        <div class="test-links">
            <a href="<?php echo getLanguageSwitcherUrl('fr'); ?>">Set Language to French</a>
            <a href="<?php echo getLanguageSwitcherUrl('en'); ?>">Set Language to English</a>
            <a href="<?php echo getThemeSwitcherUrl('light'); ?>">Set Theme to Light</a>
            <a href="<?php echo getThemeSwitcherUrl('dark'); ?>">Set Theme to Dark</a>
            <a href="?lang=fr&theme=light">French + Light Theme</a>
            <a href="?lang=en&theme=dark">English + Dark Theme</a>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Session Data:</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    
    <?php
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['language'])) {
            $_SESSION['language'] = $_POST['language'];
        }
        if (isset($_POST['theme'])) {
            $_SESSION['theme'] = $_POST['theme'];
        }
        // Refresh the page to apply changes
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>
</body>
</html>