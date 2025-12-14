<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header('Location: ../auth/login.php');
    exit();
}

$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('add_property'); ?> - ImmoHome</title>
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
                    <li><a href="../dashboards/seller_dashboard.php"><?php echo t('dashboard'); ?></a></li>
                    <li><a href="../user/my_properties.php"><?php echo t('my_properties_title'); ?></a></li>
                    <li><a href="add_property.php" class="active"><?php echo t('add'); ?></a></li>
                    <li><a href="../user/my_sales.php"><?php echo t('my_sales_title'); ?></a></li>
                    <li><a href="../user/favorites.php"><?php echo t('favorites'); ?></a></li>
                </ul>
                <div class="nav-actions">
                    <div class="user-profile-dropdown">
                        <div class="user-avatar" onclick="toggleProfileDropdown()">
                            <?php
                            // Fetch user profile picture
                            try {
                                $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                $profilePicture = isset($user['profile_picture']) ? $user['profile_picture'] : '';
                                
                                if (!empty($profilePicture) && file_exists($profilePicture)) {
                                    echo '<img src="' . $profilePicture . '" alt="Profile" class="profile-img">';
                                } else {
                                    echo '<i class="fas fa-user-circle fa-2x"></i>';
                                }
                            } catch(PDOException $e) {
                                echo '<i class="fas fa-user-circle fa-2x"></i>';
                            }
                            ?>
                        </div>
                        <div class="profile-dropdown-content" id="profileDropdown">
                            <div class="profile-info">
                                <p><?php echo htmlspecialchars($username); ?></p>
                            </div>
                            <a href="account_settings.php"><i class="fas fa-cog"></i> Paramètres</a>
                            <a href="account_settings.php#language-theme"><i class="fas fa-language"></i> Langue & Thème</a>
                            <a href="account_settings.php#user-info"><i class="fas fa-user-edit"></i> Informations Utilisateur</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="dashboard-hero">
        <div class="container">
            <h1><?php echo t('add_property'); ?></h1>
            <p><?php echo t('publish_new_property'); ?></p>
        </div>
    </section>

    <section class="property-form-section">
        <div class="container">
            <div class="form-container">
                <h2><?php echo t('property_information'); ?></h2>
                <form id="propertyForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title"><?php echo t('title'); ?></label>
                        <input type="text" id="title" name="title" placeholder="<?php echo t('enter_title'); ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="propertyType"><?php echo t('property_type'); ?></label>
                            <select id="propertyType" name="type" required>
                                <option value=""><?php echo t('select_type'); ?></option>
                                <option value="house"><?php echo t('house'); ?></option>
                                <option value="apartment"><?php echo t('apartment'); ?></option>
                                <option value="villa"><?php echo t('villa'); ?></option>
                                <option value="land"><?php echo t('land'); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="price"><?php echo t('price'); ?> (€)</label>
                            <input type="number" id="price" name="price" placeholder="<?php echo t('enter_price'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address"><?php echo t('address'); ?></label>
                        <input type="text" id="address" name="address" placeholder="<?php echo t('enter_address'); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city"><?php echo t('city'); ?></label>
                            <input type="text" id="city" name="city" placeholder="<?php echo t('enter_city'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="postalCode">Code Postal</label>
                            <input type="text" id="postalCode" name="postalCode" placeholder="Code postal" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bedrooms"><?php echo t('bedrooms'); ?></label>
                            <input type="number" id="bedrooms" name="bedrooms" min="0" placeholder="<?php echo t('select_bedrooms'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="bathrooms"><?php echo t('bathrooms'); ?></label>
                            <input type="number" id="bathrooms" name="bathrooms" min="0" placeholder="<?php echo t('select_bathrooms'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="area"><?php echo t('area'); ?> (m²)</label>
                            <input type="number" id="area" name="area_sqm" min="0" placeholder="<?php echo t('enter_area'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><?php echo t('description'); ?></label>
                        <textarea id="description" name="description" rows="5" placeholder="<?php echo t('describe_property'); ?>"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image"><?php echo t('property_image'); ?></label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <p class="help-text"><?php echo t('image_help_text'); ?></p>
                        <div id="imagePreview" style="margin-top: 10px;"></div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="history.back()"><?php echo t('cancel'); ?></button>
                        <button type="submit" class="btn-primary"><?php echo t('submit_listing'); ?></button>
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
                    <p>Your trusted partner for selling properties.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Sell</h4>
                    <ul>
                        <li><a href="#">List Property</a></li>
                        <li><a href="#">Pricing Guide</a></li>
                        <li><a href="#">Marketing Options</a></li>
                        <li><a href="#">Success Stories</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Professional Photography</a></li>
                        <li><a href="#">Virtual Tours</a></li>
                        <li><a href="#">Legal Support</a></li>
                        <li><a href="#">Moving Assistance</a></li>
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
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '200px';
                    img.style.borderRadius = '8px';
                    img.style.marginTop = '10px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Form submission
        document.getElementById('propertyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = '<?php echo t('submitting'); ?>...';
            
            try {
                const response = await fetch('../api/submit_property.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('<?php echo t('property_listing_submitted'); ?>');
                    window.location.href = '../user/my_properties.php';
                } else {
                    alert('<?php echo t('error_submitting_listing'); ?>: ' + result.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('<?php echo t('error_submitting_listing'); ?>');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    </script>
</body>
</html>