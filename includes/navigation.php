<?php
// Navigation component for consistent navigation across all dashboards

function renderNavigation($currentPage = '', $username = '', $userRole = '') {
    // Get profile picture if user is logged in
    $profilePicture = null;
    if (isset($_SESSION['user_id'])) {
        // We need to get the PDO connection
        require_once 'common_header.php';
        global $pdo;
        $profilePicture = getUserProfilePicture($pdo, $_SESSION['user_id']);
    }
    
    $navLinks = [
        'buyer' => [
            ['url' => 'buyer_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'buyer_dashboard.php'],
            ['url' => 'search_properties.php', 'label' => 'Rechercher', 'active' => $currentPage === 'search_properties.php'],
            ['url' => 'my_orders.php', 'label' => 'Mes Commandes', 'active' => $currentPage === 'my_orders.php']
        ],
        'seller' => [
            ['url' => 'seller_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'seller_dashboard.php'],
            ['url' => 'my_properties.php', 'label' => 'Mes Propriétés', 'active' => $currentPage === 'my_properties.php'],
            ['url' => 'add_property.php', 'label' => 'Ajouter', 'active' => $currentPage === 'add_property.php'],
            ['url' => 'my_sales.php', 'label' => 'Mes Ventes', 'active' => $currentPage === 'my_sales.php']
        ],
        'agent' => [
            ['url' => 'agent_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'agent_dashboard.php'],
            ['url' => 'my_listings.php', 'label' => 'Mes Annonces', 'active' => $currentPage === 'my_listings.php'],
            ['url' => 'client_leads.php', 'label' => 'Prospects', 'active' => $currentPage === 'client_leads.php'],
            ['url' => 'appointments.php', 'label' => 'Rendez-vous', 'active' => $currentPage === 'appointments.php']
        ],
        'admin' => [
            ['url' => 'admin_dashboard.php', 'label' => 'Dashboard', 'active' => $currentPage === 'admin_dashboard.php'],
            ['url' => 'buy.php', 'label' => 'Acheter', 'active' => $currentPage === 'buy.php'],
            ['url' => 'rent.php', 'label' => 'Louer', 'active' => $currentPage === 'rent.php'],
            ['url' => 'sell.php', 'label' => 'Vendre', 'active' => $currentPage === 'sell.php'],
            ['url' => 'agents.php', 'label' => 'Agents', 'active' => $currentPage === 'agents.php'],
            ['url' => 'financing.php', 'label' => 'Financement', 'active' => $currentPage === 'financing.php']
        ]
    ];

    $settingsLinks = [
        ['url' => 'account_settings.php', 'label' => 'Langue & Thème'],
        ['url' => 'account_settings.php', 'label' => 'Informations Utilisateur']
    ];

    $commonLinks = [
        ['url' => 'favorites.php', 'label' => 'Favoris', 'active' => $currentPage === 'favorites.php']
    ];

    // Determine which navigation links to show based on user role
    $linksToShow = isset($navLinks[$userRole]) ? $navLinks[$userRole] : [];
    
    // Add common links to all roles
    $linksToShow = array_merge($linksToShow, $commonLinks);

    echo '<nav class="navbar">';
    echo '    <div class="container">';
    echo '        <div class="logo" onclick="location.href=\'index.php\'">';
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
        echo '            <div class="user-profile-dropdown">';
        echo '                <div class="user-avatar" onclick="toggleProfileDropdown()">';
        if ($profilePicture) {
            echo '                    <img src="' . $profilePicture . '" alt="Profile" class="profile-img" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">';
        } else {
            echo '                    <i class="fas fa-user-circle fa-2x"></i>';
        }
        echo '                </div>';
        echo '                <div class="profile-dropdown-content" id="profileDropdown">';
        echo '                    <div class="profile-info">';
        echo '                        <p>' . htmlspecialchars($username) . '</p>';
        echo '                    </div>';
        echo '                    <a href="account_settings.php"><i class="fas fa-cog"></i> Paramètres</a>';
        echo '                    <a href="account_settings.php#language-theme"><i class="fas fa-language"></i> Langue & Thème</a>';
        echo '                    <a href="account_settings.php#user-info"><i class="fas fa-user-edit"></i> Informations Utilisateur</a>';
        echo '                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>';
        echo '                </div>';
        echo '            </div>';
    }
    
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
    echo '    ';
    echo '    /* User profile dropdown */';
    echo '    .user-profile-dropdown {';
    echo '        position: relative;';
    echo '        display: inline-block;';
    echo '    }';
    echo '    ';
    echo '    .user-avatar {';
    echo '        cursor: pointer;';
    echo '        color: #006AFF;';
    echo '    }';
    echo '    ';
    echo '    .profile-dropdown-content {';
    echo '        display: none;';
    echo '        position: absolute;';
    echo '        right: 0;';
    echo '        background-color: #f9f9f9;';
    echo '        min-width: 200px;';
    echo '        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);';
    echo '        z-index: 1;';
    echo '        border-radius: 8px;';
    echo '        top: 100%;';
    echo '    }';
    echo '    ';
    echo '    .profile-dropdown-content.show {';
    echo '        display: block;';
    echo '    }';
    echo '    ';
    echo '    .profile-info {';
    echo '        padding: 15px;';
    echo '        border-bottom: 1px solid #eee;';
    echo '        font-weight: 500;';
    echo '    }';
    echo '    ';
    echo '    .profile-dropdown-content a {';
    echo '        color: black;';
    echo '        padding: 12px 16px;';
    echo '        text-decoration: none;';
    echo '        display: flex;';
    echo '        align-items: center;';
    echo '        gap: 10px;';
    echo '    }';
    echo '    ';
    echo '    .profile-dropdown-content a:hover {';
    echo '        background-color: #f1f1f1;';
    echo '        border-radius: 4px;';
    echo '        margin: 0 5px;';
    echo '    }';
    echo '</style>';
    
    // Include JavaScript for profile dropdown
    echo '<script>';
    echo '    function toggleProfileDropdown() {';
    echo '        document.getElementById("profileDropdown").classList.toggle("show");';
    echo '    }';
    echo '    ';
    echo '    // Close dropdown when clicking outside';
    echo '    window.onclick = function(event) {';
    echo '        if (!event.target.matches(\'.user-avatar\') && !event.target.matches(\'.user-avatar *\')) {';
    echo '            var dropdowns = document.getElementsByClassName("profile-dropdown-content");';
    echo '            for (var i = 0; i < dropdowns.length; i++) {';
    echo '                var openDropdown = dropdowns[i];';
    echo '                if (openDropdown.classList.contains(\'show\')) {';
    echo '                    openDropdown.classList.remove(\'show\');';
    echo '                }';
    echo '            }';
    echo '        }';
    echo '    }';
    echo '</script>';
}

?>