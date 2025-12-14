<?php
session_start();
require_once 'config.php';
require_once 'includes/language_handler.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user info
$username = $_SESSION['username'];
$userRole = $_SESSION['role'];
$isLoggedIn = true;

// Check if user is a buyer - if so, redirect to buy page
if ($userRole == 'buyer') {
    header('Location: buy.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financing - ImmoHome</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo" onclick="location.href='index.html'">
                    <i class="fas fa-home"></i>
                    <span>ImmoHome</span>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php"><?php echo t('home'); ?></a></li>
                    <li><a href="buy.php"><?php echo t('buy'); ?></a></li>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="rent.php"><?php echo t('rent'); ?></a></li>
                    <?php endif; ?>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="sell.php"><?php echo t('sell'); ?></a></li>
                    <?php endif; ?>
                    <?php if (!$isLoggedIn || ($isLoggedIn && $userRole != 'buyer')): ?>
                    <li><a href="agents.php"><?php echo t('agents'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="financing.php" class="active"><?php echo t('financing'); ?></a></li>
                </ul>
                <div class="nav-actions">
                    <span class="user-welcome">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                    <a href="logout.php" class="btn-secondary">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <section class="finance-hero">
        <div class="container">
            <h1>Property Financing Solutions</h1>
            <p>Find the best mortgage rates and financing options for your dream property</p>
        </div>
    </section>

    <section class="finance-options">
        <div class="container">
            <div class="section-header">
                <h2>Financing Options</h2>
                <p>Explore our range of financing solutions tailored to your needs</p>
            </div>
            
            <div class="options-grid">
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-home fa-2x"></i>
                    </div>
                    <h3>Mortgage Loans</h3>
                    <p>Competitive fixed and variable rate mortgages with flexible terms</p>
                    <ul>
                        <li>Fixed rates from 2.5%</li>
                        <li>Loan terms up to 30 years</li>
                        <li>Quick approval process</li>
                    </ul>
                    <button class="btn-primary" onclick="applyFinance('mortgage')">Apply Now</button>
                </div>
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-key fa-2x"></i>
                    </div>
                    <h3>First-Time Buyer</h3>
                    <p>Special programs and incentives for first-time home buyers</p>
                    <ul>
                        <li>Lower down payment options</li>
                        <li>Government grants available</li>
                        <li>Dedicated support team</li>
                    </ul>
                    <button class="btn-primary" onclick="applyFinance('first_time')">Apply Now</button>
                </div>
                
                <div class="option-card">
                    <div class="option-icon">
                        <i class="fas fa-sync-alt fa-2x"></i>
                    </div>
                    <h3>Refinancing</h3>
                    <p>Reduce your monthly payments or access home equity</p>
                    <ul>
                        <li>Cash-out refinancing</li>
                        <li>Rate-and-term refinancing</li>
                        <li>Streamlined process</li>
                    </ul>
                    <button class="btn-primary" onclick="applyFinance('refinance')">Apply Now</button>
                </div>
            </div>
        </div>
    </section>

    <section class="calculator-section">
        <div class="container">
            <div class="calculator-container">
                <h2>Mortgage Calculator</h2>
                <p>Estimate your monthly payments</p>
                
                <div class="calculator-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="homePrice">Home Price (€)</label>
                            <input type="number" id="homePrice" value="500000" min="1">
                        </div>
                        
                        <div class="form-group">
                            <label for="downPayment">Down Payment (€)</label>
                            <input type="number" id="downPayment" value="100000" min="0">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="interestRate">Interest Rate (%)</label>
                            <input type="number" id="interestRate" value="3.5" step="0.01" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="loanTerm">Loan Term (years)</label>
                            <select id="loanTerm">
                                <option value="10">10 years</option>
                                <option value="15">15 years</option>
                                <option value="20">20 years</option>
                                <option value="25">25 years</option>
                                <option value="30" selected>30 years</option>
                            </select>
                        </div>
                    </div>
                    
                    <button class="btn-primary" onclick="calculatePayment()">Calculate Payment</button>
                    
                    <div class="results" id="results" style="display: none;">
                        <h3>Estimated Monthly Payment</h3>
                        <div class="payment-amount">€<span id="monthlyPayment">0</span></div>
                        <div class="payment-breakdown">
                            <div class="breakdown-item">
                                <span>Principal & Interest:</span>
                                <span>€<span id="principalInterest">0</span></span>
                            </div>
                            <div class="breakdown-item">
                                <span>Property Tax:</span>
                                <span>€<span id="propertyTax">0</span></span>
                            </div>
                            <div class="breakdown-item">
                                <span>Home Insurance:</span>
                                <span>€<span id="homeInsurance">0</span></span>
                            </div>
                            <div class="breakdown-item total">
                                <span>Total Payment:</span>
                                <span>€<span id="totalPayment">0</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="steps-section">
        <div class="container">
            <div class="section-header">
                <h2>Financing Process</h2>
                <p>Simple steps to secure your property financing</p>
            </div>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Pre-Approval</h3>
                    <p>Get pre-approved to know exactly what you can afford</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Choose Loan</h3>
                    <p>Select the best loan option for your financial situation</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Application</h3>
                    <p>Complete your application with our streamlined process</p>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Funding</h3>
                    <p>Receive funds and close on your new property</p>
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
                    <p>Your trusted partner for finding the perfect home.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Financing</h4>
                    <ul>
                        <li><a href="#">Mortgage Rates</a></li>
                        <li><a href="#">Loan Calculator</a></li>
                        <li><a href="#">Pre-Approval</a></li>
                        <li><a href="#">Refinancing</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Buying Guide</a></li>
                        <li><a href="#">Selling Guide</a></li>
                        <li><a href="#">Market Reports</a></li>
                        <li><a href="#">Investment Tips</a></li>
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

    <script>
        function applyFinance(type) {
            alert('Applying for ' + type + ' financing. In a real implementation, this would open an application form.');
        }
        
        function calculatePayment() {
            const homePrice = parseFloat(document.getElementById('homePrice').value) || 0;
            const downPayment = parseFloat(document.getElementById('downPayment').value) || 0;
            const interestRate = parseFloat(document.getElementById('interestRate').value) || 0;
            const loanTerm = parseInt(document.getElementById('loanTerm').value) || 30;
            
            const loanAmount = homePrice - downPayment;
            const monthlyRate = interestRate / 100 / 12;
            const numberOfPayments = loanTerm * 12;
            
            // Calculate monthly payment
            const monthlyPayment = loanAmount * 
                (monthlyRate * Math.pow(1 + monthlyRate, numberOfPayments)) / 
                (Math.pow(1 + monthlyRate, numberOfPayments) - 1);
            
            // Estimate taxes and insurance (simplified)
            const propertyTax = (homePrice * 0.012) / 12; // 1.2% annual property tax
            const homeInsurance = 100; // Fixed estimate
            
            const principalInterest = monthlyPayment;
            const totalPayment = monthlyPayment + propertyTax + homeInsurance;
            
            // Update UI
            document.getElementById('monthlyPayment').textContent = monthlyPayment.toFixed(2);
            document.getElementById('principalInterest').textContent = principalInterest.toFixed(2);
            document.getElementById('propertyTax').textContent = propertyTax.toFixed(2);
            document.getElementById('homeInsurance').textContent = homeInsurance.toFixed(2);
            document.getElementById('totalPayment').textContent = totalPayment.toFixed(2);
            
            document.getElementById('results').style.display = 'block';
        }
        
        // Calculate initial payment on page load
        window.addEventListener('load', calculatePayment);
    </script>

    <style>
        .finance-hero {
            margin-top: 70px;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .finance-hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .finance-hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .finance-options {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .option-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
        }
        
        .option-icon {
            width: 80px;
            height: 80px;
            background: #006AFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
        }
        
        .option-card h3 {
            margin-bottom: 15px;
            color: #1A1A1A;
        }
        
        .option-card p {
            color: #6B6B6B;
            margin-bottom: 20px;
        }
        
        .option-card ul {
            text-align: left;
            margin-bottom: 25px;
            padding-left: 20px;
        }
        
        .option-card ul li {
            margin-bottom: 10px;
            color: #6B6B6B;
        }
        
        .calculator-section {
            padding: 80px 0;
            background: white;
        }
        
        .calculator-container {
            max-width: 600px;
            margin: 0 auto;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .calculator-container h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .calculator-container p {
            text-align: center;
            color: #6B6B6B;
            margin-bottom: 30px;
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
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
        }
        
        .results {
            margin-top: 30px;
            padding: 25px;
            background: white;
            border-radius: 8px;
            text-align: center;
        }
        
        .payment-amount {
            font-size: 36px;
            font-weight: 700;
            color: #006AFF;
            margin: 20px 0;
        }
        
        .payment-breakdown {
            text-align: left;
            margin-top: 20px;
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .breakdown-item.total {
            font-weight: 700;
            border-top: 2px solid #ddd;
            border-bottom: none;
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .steps-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #006AFF, #00C896);
            color: white;
        }
        
        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .step {
            text-align: center;
            padding: 30px;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        
        .step h3 {
            margin-bottom: 15px;
        }
        
        .step:last-child {
            margin-bottom: 0;
        }
    </style>
    
    <style>
        /* Fix navigation spacing */
        .nav-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .user-welcome {
            margin-right: 10px;
        }
    </style>
</body>
</html>