<?php
session_start();
require_once 'config.php';
require_once 'includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$agentId = $_SESSION['user_id'];

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertyType = $_POST['propertyType'] ?? '';
    $price = $_POST['price'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postalCode = $_POST['postalCode'] ?? '';
    $bedrooms = $_POST['bedrooms'] ?? null;
    $bathrooms = $_POST['bathrooms'] ?? null;
    $area = $_POST['area'] ?? null;
    $description = $_POST['description'] ?? '';
    
    // Validate required fields
    if (empty($propertyType) || empty($price) || empty($address) || empty($city) || empty($postalCode)) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        try {
            // Insert property into database
            $stmt = $pdo->prepare("INSERT INTO properties (agent_id, title, description, price, address, city, type, bedrooms, bathrooms, area_sqm) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $agentId,
                $address . ', ' . $city, // title
                $description,
                $price,
                $address,
                $city,
                $propertyType,
                $bedrooms ?: null,
                $bathrooms ?: null,
                $area ?: null
            ]);
            
            $message = 'Annonce publiée avec succès!';
            // Redirect to listings page after successful submission
            header('Location: my_listings.php');
            exit();
        } catch (PDOException $e) {
            error_log("Error saving property: " . $e->getMessage());
            $message = 'Erreur lors de la publication de l\'annonce. Veuillez réessayer.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Annonce - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php renderNavigation('add_listing.php', $username, $userRole); ?>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1>Ajouter une Annonce</h1>
            <p>Publier une nouvelle annonce immobilière</p>
        </div>
    </section>

    <section class="property-form-section">
        <div class="container">
            <div class="form-container">
                <h2>Informations sur l'Annonce</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <form id="listingForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="propertyType">Type de Propriété</label>
                            <select id="propertyType" name="propertyType" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="house">Maison</option>
                                <option value="apartment">Appartement</option>
                                <option value="condo">Condo</option>
                                <option value="townhouse">Townhouse</option>
                                <option value="land">Terrain</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Prix (€)</label>
                            <input type="number" id="price" name="price" placeholder="Entrez le prix" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresse</label>
                        <input type="text" id="address" name="address" placeholder="Adresse complète" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <input type="text" id="city" name="city" placeholder="Nom de la ville" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="postalCode">Code Postal</label>
                            <input type="text" id="postalCode" name="postalCode" placeholder="Code postal" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bedrooms">Chambres</label>
                            <input type="number" id="bedrooms" name="bedrooms" min="0" placeholder="Nombre de chambres">
                        </div>
                        
                        <div class="form-group">
                            <label for="bathrooms">Salles de bain</label>
                            <input type="number" id="bathrooms" name="bathrooms" min="0" placeholder="Nombre de salles de bain">
                        </div>
                        
                        <div class="form-group">
                            <label for="area">Surface (m²)</label>
                            <input type="number" id="area" name="area" min="0" placeholder="Surface totale">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5" placeholder="Décrivez la propriété en détail..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="images">Images de la Propriété</label>
                        <input type="file" id="images" name="images" multiple accept="image/*">
                        <p class="help-text">Vous pouvez sélectionner plusieurs images (max 10)</p>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="history.back()">Annuler</button>
                        <button type="submit" class="btn-primary">Publier l'Annonce</button>
                    </div>
                </form>
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
                    <p>Your trusted platform for real estate professionals.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Agent Tools</h4>
                    <ul>
                        <li><a href="#">Lead Management</a></li>
                        <li><a href="#">CRM Integration</a></li>
                        <li><a href="#">Marketing Materials</a></li>
                        <li><a href="#">Training Resources</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Listing Promotion</a></li>
                        <li><a href="#">Professional Photography</a></li>
                        <li><a href="#">Virtual Tours</a></li>
                        <li><a href="#">Legal Support</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <style>
        .dashboard-hero {
            margin-top: 70px;
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .dashboard-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .dashboard-hero p {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .property-form-section {
            padding: 80px 0;
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-container h2 {
            margin-top: 0;
            margin-bottom: 30px;
            color: #1A1A1A;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #006AFF;
            box-shadow: 0 0 0 3px rgba(0, 106, 255, 0.1);
        }
        
        .help-text {
            font-size: 14px;
            color: #6B6B6B;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        
        
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions button {
                width: 100%;
            }
        }
    </style>
    
    <script>
        // Form validation is handled server-side
        // This script can be used for client-side enhancements if needed
        
        
    </script>
</body>
</html>