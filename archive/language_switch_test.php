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
    <title>Language Switch Test</title>
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
    </style>
</head>
<body class="<?php echo $currentTheme; ?>">
    <div class="test-section">
        <h1>Language Switch Test</h1>
        
        <h2>Current State:</h2>
        <p><strong>Current Language:</strong> <?php echo $currentLang; ?></p>
        <p><strong>HTML Lang Attribute:</strong> <?php echo $htmlLang; ?></p>
        
        <h2>Translations:</h2>
        <p><strong>Welcome:</strong> <?php echo t('welcome'); ?></p>
        <p><strong>Home:</strong> <?php echo t('home'); ?></p>
        <p><strong>Buy:</strong> <?php echo t('buy'); ?></p>
        <p><strong>Rent:</strong> <?php echo t('rent'); ?></p>
        <p><strong>Sell:</strong> <?php echo t('sell'); ?></p>
        <p><strong>Theme:</strong> <?php echo t('theme'); ?></p>
        <p><strong>Language:</strong> <?php echo t('language'); ?></p>
        
        <h2>Language Switching Tests:</h2>
        <div class="test-links">
            <a href="<?php echo getLanguageSwitcherUrl('fr'); ?>">Set Language to French</a>
            <a href="<?php echo getLanguageSwitcherUrl('en'); ?>">Set Language to English</a>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Session Data:</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
</body>
</html>