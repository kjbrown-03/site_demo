<?php
// Navigation component for consistent navigation across all dashboards

function renderNavigation($currentPage = '', $username = '', $userRole = '') {
    $navLinks = [
        'buyer' => [
            ['url' => '../dashboards/buyer_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'buyer_dashboard.php'],
            ['url' => '../pages/search_properties.php', 'label' => 'Rechercher', 'active' => $currentPage === 'search_properties.php'],
            ['url' => '../user/my_orders.php', 'label' => 'Mes Commandes', 'active' => $currentPage === 'my_orders.php']
        ],
        'seller' => [
            ['url' => '../dashboards/seller_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'seller_dashboard.php'],
            ['url' => '../user/my_properties.php', 'label' => 'Mes Propriétés', 'active' => $currentPage === 'my_properties.php'],
            ['url' => '../pages/add_property.php', 'label' => 'Ajouter', 'active' => $currentPage === 'add_property.php'],
            ['url' => '../user/my_sales.php', 'label' => 'Mes Ventes', 'active' => $currentPage === 'my_sales.php']
        ],
        'agent' => [
            ['url' => '../dashboards/agent_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'agent_dashboard.php'],
            ['url' => '../agent/my_listings.php', 'label' => 'Mes Annonces', 'active' => $currentPage === 'my_listings.php'],
            ['url' => '../agent/client_leads.php', 'label' => 'Prospects', 'active' => $currentPage === 'client_leads.php'],
            ['url' => '../agent/appointments.php', 'label' => 'Rendez-vous', 'active' => $currentPage === 'appointments.php']
        ],
        'admin' => [
            ['url' => '../dashboards/admin_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'admin_dashboard.php'],
            ['url' => '../pages/buy.php', 'label' => 'Acheter', 'active' => $currentPage === 'buy.php'],
            ['url' => '../pages/rent.php', 'label' => 'Louer', 'active' => $currentPage === 'rent.php'],
            ['url' => '../pages/sell.php', 'label' => 'Vendre', 'active' => $currentPage === 'sell.php'],
            ['url' => '../pages/agents.php', 'label' => 'Agents', 'active' => $currentPage === 'agents.php'],
            ['url' => '../pages/financing.php', 'label' => 'Financement', 'active' => $currentPage === 'financing.php']
        ]
    ];

    $settingsLinks = [
        ['url' => '../user/account_settings.php', 'label' => 'Langue & Thème'],
        ['url' => '../user/account_settings.php', 'label' => 'Informations Utilisateur']
    ];

    $commonLinks = [
        ['url' => '../user/favorites.php', 'label' => 'Favoris', 'active' => $currentPage === 'favorites.php']
    ];

    // Determine which navigation links to show based on user role
    $linksToShow = isset($navLinks[$userRole]) ? $navLinks[$userRole] : [];
    
    // Add common links to all roles
    $linksToShow = array_merge($linksToShow, $commonLinks);

    echo '<nav class="navbar">';
    echo '    <div class="container">';
    echo '        <div class="logo" onclick="location.href=\'../index.php\'">';
    echo '            <i class="fas fa-home"></i>';
    echo '            <span>ImmoHome</span>';
    echo '        </div>';
    echo '        <ul class="nav-links">';

    // Render main navigation links
    foreach ($linksToShow as $link) {
        $activeClass = isset($link['active']) && $link['active'] ? ' class="active"' : '';
        echo '            <li><a href="' . $link['url'] . '"' . $activeClass . '>' . $link['label'] . '</a></li>';
    }

    // Add settings dropdown for all roles
    echo '            <li class="dropdown">';
    echo '                <a href="#" class="dropbtn">Paramètres <i class="fas fa-caret-down"></i></a>';
    echo '                <div class="dropdown-content">';
    foreach ($settingsLinks as $link) {
        echo '                    <a href="' . $link['url'] . '">' . $link['label'] . '</a>';
    }
    echo '                </div>';
    echo '            </li>';

    echo '        </ul>';
    echo '        <div class="nav-actions">';
    
    if ($username) {
        echo '            <span class="user-welcome">' . ($userRole === 'admin' ? 'Admin: ' : 'Bonjour, ') . htmlspecialchars($username) . '!</span>';
    }
    
    echo '            <a href="../auth/logout.php" class="btn-secondary">Déconnexion</a>';
    echo '        </div>';
    echo '    </div>';
    echo '</nav>';

    // Include navigation styles
    echo '<style>';
    echo '    .dropdown {';
    echo '        position: relative;';
    echo '        display: inline-block;';
    echo '    }';
    echo '    ';
    echo '    .dropdown-content {';
    echo '        display: none;';
    echo '        position: absolute;';
    echo '        background-color: #f9f9f9;';
    echo '        min-width: 200px;';
    echo '        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);';
    echo '        z-index: 1;';
    echo '        border-radius: 8px;';
    echo '        top: 100%;';
    echo '        left: 0;';
    echo '    }';
    echo '    ';
    echo '    .dropdown-content a {';
    echo '        color: black;';
    echo '        padding: 12px 16px;';
    echo '        text-decoration: none;';
    echo '        display: block;';
    echo '    }';
    echo '    ';
    echo '    .dropdown-content a:hover {';
    echo '        background-color: #f1f1f1;';
    echo '        border-radius: 4px;';
    echo '    }';
    echo '    ';
    echo '    .dropdown:hover .dropdown-content {';
    echo '        display: block;';
    echo '    }';
    echo '    ';
    echo '    .dropbtn {';
    echo '        display: flex;';
    echo '        align-items: center;';
    echo '        gap: 5px;';
    echo '    }';
    echo '</style>';
}
?>