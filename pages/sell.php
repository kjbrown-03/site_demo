<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Get user info
$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$isLoggedIn = true;

// Check if user is a buyer - if so, redirect to buy page
if ($userRole == 'buyer') {
    header('Location: buy.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $area_sqm = intval($_POST['area_sqm']);
    $bedrooms = intval($_POST['bedrooms']);
    $bathrooms = intval($_POST['bathrooms']);
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($price) || empty($address) || empty($city) || empty($area_sqm) || empty($bedrooms) || empty($bathrooms)) {
        $error = "All fields are required";
    } else {
        try {
            // Insert property listing
            $stmt = $pdo->prepare("INSERT INTO properties (title, description, price, address, city, area_sqm, bedrooms, bathrooms, agent_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$title, $description, $price, $address, $city, $area_sqm, $bedrooms, $bathrooms, $userId]);
            
            $success = "Property listing submitted successfully! It will be reviewed by our team.";
        } catch(PDOException $e) {
            $error = "Error submitting listing: " . $e->getMessage();
        }
    }
?>
<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('sell'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='../index.php'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php"><?php echo t('home'); ?></a></li>
                    <li><a href="buy.php"><?php echo t('buy'); ?></a></li>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="rent.php"><?php echo t('rent'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="sell.php" class="active"><?php echo t('sell'); ?></a></li>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="agents.php"><?php echo t('agents'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="financing.php"><?php echo t('financing'); ?></a></li>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome"><?php echo t('hello'); ?>, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="logout.php" class="btn-secondary"><?php echo t('logout'); ?></a>
                </div>
            </div>
        </nav>
    </header>

    <section class="sell-hero">
        <div class="container">
            <h1><?php echo t('sell_your_property'); ?></h1>
            <p><?php echo t('trusted_experts'); ?></p>
        </div>
    </section>

    <section class="sell-content">
        <div class="container">
            <?php if (!empty($success)): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="sell-grid">
                <div class="sell-info">
                    <h2><?php echo t('why_sell_with_us'); ?></h2>
                    <ul class="benefits-list">
                        <li>
                            <i class="fas fa-chart-line"></i>
                            <div>
                                <h3><?php echo t('maximum_exposure'); ?></h3>
                                <p><?php echo t('exposure_description'); ?></p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-search"></i>
                            <div>
                                <h3><?php echo t('targeted_marketing'); ?></h3>
                                <p><?php echo t('marketing_description'); ?></p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-handshake"></i>
                            <div>
                                <h3><?php echo t('expert_negotiation'); ?></h3>
                                <p><?php echo t('negotiation_description'); ?></p>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="sell-form">
                    <div class="form-card">
                        <h2><?php echo t('list_your_property'); ?></h2>
                        <p><?php echo t('provide_details'); ?></p>
                        
                        <form method="POST" class="property-form">
                            <div class="form-group">
                                <label for="title"><?php echo t('property_title'); ?> *</label>
                                <input type="text" id="title" name="title" required placeholder="<?php echo t('enter_title'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="description"><?php echo t('description'); ?> *</label>
                                <textarea id="description" name="description" rows="4" required placeholder="<?php echo t('describe_property'); ?>"></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="price"><?php echo t('price'); ?> (€) *</label>
                                    <input type="number" id="price" name="price" required placeholder="<?php echo t('enter_price'); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="type"><?php echo t('property_type'); ?> *</label>
                                    <select id="type" name="type" required>
                                        <option value=""><?php echo t('select_type'); ?></option>
                                        <option value="house"><?php echo t('house'); ?></option>
                                        <option value="apartment"><?php echo t('apartment'); ?></option>
                                        <option value="villa"><?php echo t('villa'); ?></option>
                                        <option value="land"><?php echo t('land'); ?></option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address"><?php echo t('address'); ?> *</label>
                                <input type="text" id="address" name="address" required placeholder="<?php echo t('enter_address'); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city"><?php echo t('city'); ?> *</label>
                                    <input type="text" id="city" name="city" required placeholder="<?php echo t('enter_city'); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="area_sqm"><?php echo t('area'); ?> (m²) *</label>
                                    <input type="number" id="area_sqm" name="area_sqm" required placeholder="<?php echo t('enter_area'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bedrooms"><?php echo t('bedrooms'); ?> *</label>
                                    <select id="bedrooms" name="bedrooms" required>
                                        <option value=""><?php echo t('select_bedrooms'); ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5+</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bathrooms"><?php echo t('bathrooms'); ?> *</label>
                                    <select id="bathrooms" name="bathrooms" required>
                                        <option value=""><?php echo t('select_bathrooms'); ?></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4+</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-primary"><?php echo t('submit_listing'); ?></button>
                        </form>
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
                    <p><?php echo t('trusted_partner'); ?></p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('sell'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo t('why_sell_with_us'); ?></a></li>
                        <li><a href="#"><?php echo t('selling_process'); ?></a></li>
                        <li><a href="#"><?php echo t('market_analysis'); ?></a></li>
                        <li><a href="#"><?php echo t('property_valuation'); ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('services'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo t('property_marketing'); ?></a></li>
                        <li><a href="#"><?php echo t('professional_photography'); ?></a></li>
                        <li><a href="#"><?php echo t('legal_support'); ?></a></li>
                        <li><a href="#"><?php echo t('staging_services'); ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php echo t('company'); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo t('about_us'); ?></a></li>
                        <li><a href="#"><?php echo t('careers'); ?></a></li>
                        <li><a href="#"><?php echo t('contact'); ?></a></li>
                        <li><a href="#"><?php echo t('blog'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <style>
        .sell-hero {
            margin-top: 70px;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .sell-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .sell-hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .sell-form-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-container h2 {
            margin-bottom: 30px;
            color: #1A1A1A;
            text-align: center;
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
        
        .sell-form button {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .sell-info {
            padding: 80px 0;
            background: white;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .info-card {
            text-align: center;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .info-card i {
            color: #006AFF;
            margin-bottom: 20px;
        }
        
        .info-card h3 {
            margin-bottom: 15px;
            color: #1A1A1A;
        }
        
        .info-card p {
            color: #6B6B6B;
            line-height: 1.6;
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
        
        @media (max-width: 768px) {
            .sell-hero h1 {
                font-size: 36px;
            }
            
            .sell-hero p {
                font-size: 18px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <style>
        /* Fix navigation spacing */
        .nav-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
    </style>
    
    <style>
        .user-welcome {
            margin-right: 10px;
        }
    </style>
</body>
</html>