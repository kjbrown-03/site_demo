<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_user':
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $role = $_POST['role'];
                    
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $password, $role]);
                    $message = "Utilisateur ajouté avec succès!";
                    break;
                    
                case 'delete_user':
                    $userId = $_POST['user_id'];
                    // Prevent deleting the current admin user
                    if ($userId != $_SESSION['user_id']) {
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$userId]);
                        $message = "Utilisateur supprimé avec succès!";
                    }
                    break;
                    
                case 'add_property':
                    $title = $_POST['title'];
                    $description = $_POST['description'];
                    $price = $_POST['price'];
                    $address = $_POST['address'];
                    $city = $_POST['city'];
                    $type = $_POST['type'];
                    $bedrooms = $_POST['bedrooms'];
                    $bathrooms = $_POST['bathrooms'];
                    $area_sqm = $_POST['area_sqm'];
                    $status = $_POST['status'];
                    $agent_id = $_POST['agent_id'];
                    
                    $stmt = $pdo->prepare("INSERT INTO properties (title, description, price, address, city, type, bedrooms, bathrooms, area_sqm, status, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $price, $address, $city, $type, $bedrooms, $bathrooms, $area_sqm, $status, $agent_id]);
                    $message = "Propriété ajoutée avec succès!";
                    break;
                    
                case 'delete_property':
                    $propertyId = $_POST['property_id'];
                    $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
                    $stmt->execute([$propertyId]);
                    $message = "Propriété supprimée avec succès!";
                    break;
            }
        } catch(PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    }
}

// Fetch data for display
try {
    // Get all users
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all properties
    $stmt = $pdo->query("SELECT p.*, u.username as agent_name FROM properties p LEFT JOIN users u ON p.agent_id = u.id ORDER BY p.created_at DESC");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all orders
    $stmt = $pdo->query("SELECT o.*, u.username as user_name, p.title as property_title FROM orders o LEFT JOIN users u ON o.user_id = u.id LEFT JOIN properties p ON o.property_id = p.id ORDER BY o.created_at DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get agents for dropdown
    $stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'agent' ORDER BY username");
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Erreur lors du chargement des données: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_dashboard'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('admin_dashboard.php', $username, $userRole); ?>
    </header>

    <section class="admin-hero">
        <div class="container">
            <h1><?php echo t('admin_dashboard'); ?></h1>
            <p><?php echo t('manage_users_properties'); ?></p>
        </div>
    </section>

    <section class="admin-content">
        <div class="container">
            <?php if (isset($message)): ?>
                <div class="alert success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="admin-tabs">
                <button class="tab-btn active" data-tab="users"><?php echo t('users'); ?></button>
                <button class="tab-btn" data-tab="properties"><?php echo t('properties'); ?></button>
                <button class="tab-btn" data-tab="orders"><?php echo t('orders'); ?></button>
                <button class="tab-btn" data-tab="history"><?php echo t('history'); ?></button>
            </div>
            
            <!-- Users Tab -->
            <div class="tab-content active" id="users-tab">
                <div class="section-header">
                    <h2><?php echo t('user_management'); ?></h2>
                    <button class="btn-primary" onclick="showAddUserForm()"><?php echo t('add_user'); ?></button>
                </div>
                
                <div class="add-form" id="add-user-form" style="display: none;">
                    <h3><?php echo t('new_user'); ?></h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_user">
                        <div class="form-group">
                            <label><?php echo t('username_label'); ?></label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('email_label'); ?></label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('password_label'); ?></label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('role_label'); ?></label>
                            <select name="role" required>
                                <option value="buyer"><?php echo t('buyer_role'); ?></option>
                                <option value="seller"><?php echo t('seller_role'); ?></option>
                                <option value="agent"><?php echo t('agent_role'); ?></option>
                                <option value="admin"><?php echo t('admin_role'); ?></option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary"><?php echo t('add'); ?></button>
                        <button type="button" class="btn-secondary" onclick="hideAddUserForm()"><?php echo t('cancel'); ?></button>
                    </form>
                </div>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo t('username_label'); ?></th>
                                <th><?php echo t('email_label'); ?></th>
                                <th><?php echo t('role_label'); ?></th>
                                <th><?php echo t('registration_date'); ?></th>
                                <th><?php echo t('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn-danger"><?php echo t('delete'); ?></button>
                                            </form>
                                        <?php else: ?>
                                            <span><?php echo t('current'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Properties Tab -->
            <div class="tab-content" id="properties-tab">
                <div class="section-header">
                    <h2><?php echo t('property_management'); ?></h2>
                    <button class="btn-primary" onclick="showAddPropertyForm()"><?php echo t('add_property'); ?></button>
                </div>
                
                <div class="add-form" id="add-property-form" style="display: none;">
                    <h3><?php echo t('new_property'); ?></h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_property">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Titre</label>
                                <input type="text" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" required>
                                    <option value="house">Maison</option>
                                    <option value="apartment">Appartement</option>
                                    <option value="villa">Villa</option>
                                    <option value="land">Terrain</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Prix (€)</label>
                                <input type="number" name="price" required>
                            </div>
                            <div class="form-group">
                                <label>Statut</label>
                                <select name="status" required>
                                    <option value="for_sale">À vendre</option>
                                    <option value="for_rent">À louer</option>
                                    <option value="sold">Vendu</option>
                                    <option value="rented">Loué</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Chambres</label>
                                <input type="number" name="bedrooms" min="0">
                            </div>
                            <div class="form-group">
                                <label>Salles de bain</label>
                                <input type="number" name="bathrooms" min="0">
                            </div>
                            <div class="form-group">
                                <label>Surface (m²)</label>
                                <input type="number" name="area_sqm" min="1">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" name="address" required>
                            </div>
                            <div class="form-group">
                                <label>Ville</label>
                                <input type="text" name="city" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Agent</label>
                            <select name="agent_id" required>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?php echo $agent['id']; ?>"><?php echo htmlspecialchars($agent['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Ajouter</button>
                        <button type="button" class="btn-secondary" onclick="hideAddPropertyForm()">Annuler</button>
                    </form>
                </div>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Prix</th>
                                <th>Ville</th>
                                <th>Statut</th>
                                <th>Agent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td><?php echo $property['id']; ?></td>
                                    <td><?php echo htmlspecialchars($property['title']); ?></td>
                                    <td><?php echo ucfirst($property['type']); ?></td>
                                    <td><?php echo number_format($property['price'], 0, ',', ' '); ?> €</td>
                                    <td><?php echo htmlspecialchars($property['city']); ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $property['status'])); ?></td>
                                    <td><?php echo htmlspecialchars(isset($property['agent_name']) ? $property['agent_name'] : 'N/A'); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette propriété ?')">
                                            <input type="hidden" name="action" value="delete_property">
                                            <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                                            <button type="submit" class="btn-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Orders Tab -->
            <div class="tab-content" id="orders-tab">
                <div class="section-header">
                    <h2><?php echo t('order_management'); ?></h2>
                </div>
                
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo t('user_name'); ?></th>
                                <th><?php echo t('property_title_label'); ?></th>
                                <th><?php echo t('order_type'); ?></th>
                                <th><?php echo t('order_status'); ?></th>
                                <th><?php echo t('order_date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars(isset($order['user_name']) ? $order['user_name'] : 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(isset($order['property_title']) ? $order['property_title'] : 'N/A'); ?></td>
                                    <td><?php echo ucfirst($order['order_type']); ?></td>
                                    <td><?php echo ucfirst($order['status']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- History Tab -->
            <div class="tab-content" id="history-tab">
                <div class="section-header">
                    <h2><?php echo t('system_history'); ?></h2>
                </div>
                
                <div class="history-sections">
                    <div class="history-card">
                        <div class="history-header">
                            <h3><i class="fas fa-users"></i> <?php echo t('user_history'); ?></h3>
                        </div>
                        <div class="table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom d'utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Date d'inscription</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo ucfirst($user['role']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                    <span class="status-badge active">Actif</span>
                                                <?php else: ?>
                                                    <span class="status-badge inactive">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="history-card">
                        <div class="history-header">
                            <h3><i class="fas fa-home"></i> <?php echo t('property_history'); ?></h3>
                        </div>
                        <div class="table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Prix</th>
                                        <th>Ville</th>
                                        <th>Statut</th>
                                        <th>Agent</th>
                                        <th><?php echo t('order_date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($properties as $property): ?>
                                        <tr>
                                            <td><?php echo $property['id']; ?></td>
                                            <td><?php echo htmlspecialchars($property['title']); ?></td>
                                            <td><?php echo ucfirst($property['type']); ?></td>
                                            <td><?php echo number_format($property['price'], 0, ',', ' '); ?> €</td>
                                            <td><?php echo htmlspecialchars($property['city']); ?></td>
                                            <td>
                                                <?php 
                                                $statusText = ucfirst(str_replace('_', ' ', $property['status']));
                                                $statusClass = '';
                                                switch($property['status']) {
                                                    case 'for_sale':
                                                    case 'for_rent':
                                                        $statusClass = 'active';
                                                        break;
                                                    case 'sold':
                                                    case 'rented':
                                                        $statusClass = 'completed';
                                                        break;
                                                    default:
                                                        $statusClass = 'inactive';
                                                }
                                                ?>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars(isset($property['agent_name']) ? $property['agent_name'] : 'N/A'); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($property['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="history-card">
                        <div class="history-header">
                            <h3><i class="fas fa-file-invoice"></i> <?php echo t('order_history'); ?></h3>
                        </div>
                        <div class="table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Utilisateur</th>
                                        <th>Propriété</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th><?php echo t('total'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars(isset($order['user_name']) ? $order['user_name'] : 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars(isset($order['property_title']) ? $order['property_title'] : 'N/A'); ?></td>
                                            <td><?php echo ucfirst($order['order_type']); ?></td>
                                            <td>
                                                <?php 
                                                $statusClass = '';
                                                switch($order['status']) {
                                                    case 'pending':
                                                        $statusClass = 'pending';
                                                        break;
                                                    case 'confirmed':
                                                        $statusClass = 'active';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'completed';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'cancelled';
                                                        break;
                                                    default:
                                                        $statusClass = 'inactive';
                                                }
                                                ?>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <?php if (isset($order['total_amount'])): ?>
                                                    <?php echo number_format($order['total_amount'], 0, ',', ' '); ?> €
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="../pages/buy.php">Acheter</a></li>
                        <li><a href="../pages/rent.php">Louer</a></li>
                        <li><a href="../pages/sell.php">Vendre</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="../pages/agents.php">Agents</a></li>
                        <li><a href="../pages/financing.php">Financement</a></li>
                        <li><a href="#">Évaluation</a></li>
                        <li><a href="#">Assurance</a></li>
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

    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all tabs and buttons
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.dataset.tab + '-tab';
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Form toggling functions
        function showAddUserForm() {
            document.getElementById('add-user-form').style.display = 'block';
        }
        
        function hideAddUserForm() {
            document.getElementById('add-user-form').style.display = 'none';
        }
        
        function showAddPropertyForm() {
            document.getElementById('add-property-form').style.display = 'block';
        }
        
        function hideAddPropertyForm() {
            document.getElementById('add-property-form').style.display = 'none';
        }
    </script>

    <style>
        .admin-hero {
            margin-top: 70px;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .admin-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .admin-hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .admin-content {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .admin-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab-btn {
            padding: 15px 25px;
            background: transparent;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            color: #6B6B6B;
        }
        
        .tab-btn.active {
            color: #006AFF;
            border-bottom: 3px solid #006AFF;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .section-header h2 {
            margin: 0;
            color: #1A1A1A;
        }
        
        .add-form {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        
        .add-form h3 {
            margin-top: 0;
            color: #1A1A1A;
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1A1A1A;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #006AFF;
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .admin-table th {
            background: #f1f4f8;
            font-weight: 600;
            color: #1A1A1A;
        }
        
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        .admin-table tr:hover {
            background: #f8f9fa;
        }
        
        .btn-danger {
            background: #FF4757;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn-danger:hover {
            background: #ff2e43;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .alert.success {
            background: #E8F5E9;
            color: #4CAF50;
            border: 1px solid #C8E6C9;
        }
        
        .alert.error {
            background: #FFEAEA;
            color: #FF4757;
            border: 1px solid #FFD1D1;
        }
        
        /* History Section Styles */
        .history-sections {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .history-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .history-header {
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        .history-header h3 {
            margin: 0;
            color: #1A1A1A;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .history-header h3 i {
            color: #006AFF;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.active {
            background: #E8F5E9;
            color: #4CAF50;
        }
        
        .status-badge.inactive {
            background: #FFEAEA;
            color: #FF4757;
        }
        
        .status-badge.completed {
            background: #E3F2FD;
            color: #2196F3;
        }
        
        .status-badge.pending {
            background: #FFF8E1;
            color: #FFC107;
        }
        
        .status-badge.cancelled {
            background: #F5F5F5;
            color: #9E9E9E;
        }
        
        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
            top: 100%;
            left: 0;
        }
        
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
            border-radius: 4px;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        .dropbtn {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        @media (max-width: 768px) {
            .admin-hero h1 {
                font-size: 36px;
            }
            
            .admin-hero p {
                font-size: 18px;
            }
            
            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .admin-table {
                font-size: 14px;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 10px 8px;
            }
            
            .admin-tabs {
                flex-wrap: wrap;
            }
        }
    </style>
</body>
</html>