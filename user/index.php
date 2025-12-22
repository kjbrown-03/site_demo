<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: ../auth/login.php');
    exit();
}

// Get user role
$userRole = $_SESSION['role'] ?? '';

// Redirect to appropriate dashboard based on user role
switch ($userRole) {
    case 'buyer':
        header('Location: ../dashboards/buyer_dashboard.php');
        break;
    case 'seller':
        header('Location: ../dashboards/seller_dashboard.php');
        break;
    case 'agent':
        header('Location: ../dashboards/agent_dashboard.php');
        break;
    case 'admin':
        header('Location: ../dashboards/admin_dashboard.php');
        break;
    default:
        // Default to main index page if role is not recognized
        header('Location: ../index.php');
        break;
}

exit();
?>