<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/language_handler.php';
require_once dirname(__DIR__) . '/includes/navigation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Check if property ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$propertyId = (int)$_GET['id'];

// Fetch property details and verify ownership
try {
    $stmt = $pdo->prepare("
        SELECT * FROM properties 
        WHERE id = ? AND (seller_id = ? OR agent_id = ?)
    ");
    $stmt->execute([$propertyId, $userId, $userId]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$property) {
        header('Location: ../index.php');
        exit();
    }
} catch(PDOException $e) {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="<?php echo $htmlLang; ?>" class="<?php echo $currentTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('edit_property'); ?> - ImmoHome</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $currentTheme; ?>">
    <?php renderNavigation('edit_property', $username, $userRole); ?>

    <section class="dashboard-hero">
        <div class="container">
            <h1><?php echo t('edit_property'); ?></h1>
            <p><?php echo t('update_property_info'); ?></p>
        </div>
    </section>

    <section class="property-form-section">
        <div class="container">
            <div class="form-container">
                <h2><?php echo t('property_information'); ?></h2>
                <form id="propertyForm" enctype="multipart/form-data">
                    <input type="hidden" id="propertyId" name="property_id" value="<?php echo $propertyId; ?>">
                    
                    <div class="form-group">
                        <label for="title"><?php echo t('title'); ?></label>
                        <input type="text" id="title" name="title" placeholder="<?php echo t('enter_title'); ?>" value="<?php echo htmlspecialchars($property['title']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="propertyType"><?php echo t('property_type'); ?></label>
                            <select id="propertyType" name="type" required>
                                <option value=""><?php echo t('select_type'); ?></option>
                                <option value="house" <?php echo $property['type'] == 'house' ? 'selected' : ''; ?>><?php echo t('house'); ?></option>
                                <option value="apartment" <?php echo $property['type'] == 'apartment' ? 'selected' : ''; ?>><?php echo t('apartment'); ?></option>
                                <option value="villa" <?php echo $property['type'] == 'villa' ? 'selected' : ''; ?>><?php echo t('villa'); ?></option>
                                <option value="land" <?php echo $property['type'] == 'land' ? 'selected' : ''; ?>><?php echo t('land'); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="price"><?php echo t('price'); ?> (€)</label>
                            <input type="number" id="price" name="price" placeholder="<?php echo t('enter_price'); ?>" value="<?php echo htmlspecialchars($property['price']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address"><?php echo t('address'); ?></label>
                        <input type="text" id="address" name="address" placeholder="<?php echo t('enter_address'); ?>" value="<?php echo htmlspecialchars($property['address']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city"><?php echo t('city'); ?></label>
                            <input type="text" id="city" name="city" placeholder="<?php echo t('enter_city'); ?>" value="<?php echo htmlspecialchars($property['city']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="status"><?php echo t('status'); ?></label>
                            <select id="status" name="status" required>
                                <option value="for_sale" <?php echo $property['status'] == 'for_sale' ? 'selected' : ''; ?>><?php echo t('for_sale'); ?></option>
                                <option value="for_rent" <?php echo $property['status'] == 'for_rent' ? 'selected' : ''; ?>><?php echo t('for_rent'); ?></option>
                                <option value="sold" <?php echo $property['status'] == 'sold' ? 'selected' : ''; ?>><?php echo t('sold'); ?></option>
                                <option value="rented" <?php echo $property['status'] == 'rented' ? 'selected' : ''; ?>><?php echo t('rented'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bedrooms"><?php echo t('bedrooms'); ?></label>
                            <input type="number" id="bedrooms" name="bedrooms" min="0" placeholder="<?php echo t('select_bedrooms'); ?>" value="<?php echo htmlspecialchars($property['bedrooms'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="bathrooms"><?php echo t('bathrooms'); ?></label>
                            <input type="number" id="bathrooms" name="bathrooms" min="0" placeholder="<?php echo t('select_bathrooms'); ?>" value="<?php echo htmlspecialchars($property['bathrooms'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="area"><?php echo t('area'); ?> (m²)</label>
                            <input type="number" id="area" name="area_sqm" min="0" placeholder="<?php echo t('enter_area'); ?>" value="<?php echo htmlspecialchars($property['area_sqm'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><?php echo t('description'); ?></label>
                        <textarea id="description" name="description" rows="5" placeholder="<?php echo t('describe_property'); ?>"><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image"><?php echo t('property_image'); ?></label>
                        <?php if (!empty($property['image_url'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../<?php echo htmlspecialchars($property['image_url']); ?>" alt="Current image" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                <p class="help-text"><?php echo t('current_image'); ?></p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <p class="help-text"><?php echo t('image_help_text'); ?> <?php echo t('leave_empty_to_keep_current'); ?></p>
                        <div id="imagePreview" style="margin-top: 10px;"></div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="history.back()"><?php echo t('cancel'); ?></button>
                        <button type="submit" class="btn-primary"><?php echo t('update_property'); ?></button>
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
                    <p><?php echo t('footer_tagline'); ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 ImmoHome. <?php echo t('all_rights_reserved'); ?></p>
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

            .dashboard-hero h1 {
                font-size: 36px;
            }

            .dashboard-hero p {
                font-size: 18px;
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
            submitBtn.textContent = '<?php echo t('updating'); ?>...';
            
            try {
                const response = await fetch('../api/update_property.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('<?php echo t('property_updated_success'); ?>');
                    window.location.href = '<?php echo $userRole == 'agent' ? '../agent/my_listings.php' : '../user/my_properties.php'; ?>';
                } else {
                    alert('<?php echo t('error_updating_property'); ?>: ' + result.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('<?php echo t('error_updating_property'); ?>');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    </script>
</body>
</html>

