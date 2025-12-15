<?php
session_start();

// Redirect based on user role or to home page
if (isset($_SESSION['user_id'])) {
    $userRole = $_SESSION['role'];
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
            header('Location: ../index.php');
            break;
    }
} else {
    header('Location: ../index.php');
}
exit();
?>