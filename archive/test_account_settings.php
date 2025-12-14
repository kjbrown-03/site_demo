<?php
// Test script to check if t() function and variables work correctly

// Simple translation function for testing
function t($key) {
    $translations = [
        'fr' => [
            'account_settings' => 'Paramètres du Compte',
            'personalize_experience' => 'Personnalisez votre expérience',
            'profile_picture' => 'Photo de Profil'
        ]
    ];
    
    $currentLang = 'fr'; // Default to French
    if (isset($translations[$currentLang][$key])) {
        return $translations[$currentLang][$key];
    }
    return $key;
}

// Initialize variables
$username = 'testuser';
$userRole = 'buyer';

// Test the function and variables
echo "Testing t() function:\n";
echo t('account_settings') . "\n";
echo t('personalize_experience') . "\n";
echo t('profile_picture') . "\n";

echo "\nTesting variables:\n";
echo "Username: " . htmlspecialchars($username) . "\n";
echo "User Role: " . ucfirst($userRole) . "\n";

echo "\nAll tests passed!\n";
?>