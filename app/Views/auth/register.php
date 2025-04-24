<?php
// Define page variables
$pageTitle = 'Create an Account';
$pageDescription = 'Register for an Air-Protech account to manage your cooling service needs';

// Add page-specific styles
$pageStyles = '
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
        background-color: #f9fafb;
        background-image: linear-gradient(135deg, #f9fafb 0%, #e9ecef 100%);
    }
    
    .auth-card {
        max-width: 550px;
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .auth-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    
    .auth-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 2.75rem 2rem;
        text-align: center;
        color: white;
        position: relative;
    }
    
    .auth-header::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url("assets/images/pattern.png");
        opacity: 0.1;
        z-index: 1;
    }
    
    .auth-header .logo-container {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: center;
        margin-bottom: 1.75rem;
    }
    
    .auth-header .logo-text h1 {
        color: white;
        font-size: 1.85rem;
        margin-bottom: 0.2rem;
        letter-spacing: -0.5px;
    }
    
    .auth-header .logo-text p {
        color: rgba(255, 255, 255, 0.85);
        font-weight: 500;
    }
    
    .auth-body {
        padding: 2.75rem 2.25rem;
        background-color: white;
    }
    
    .auth-footer {
        text-align: center;
        padding: 1.5rem 2rem;
        background-color: #f8f9fa;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .form-floating {
        margin-bottom: 1.5rem;
    }
    
    .form-floating > .form-control {
        padding: 1.1rem 0.95rem;
        border-radius: 8px;
        border: 1.5px solid #e0e5e9;
        height: calc(3.5rem + 2px);
        transition: all 0.25s ease;
    }
    
    .form-floating > .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(0, 105, 217, 0.25);
    }
    
    .form-floating > label {
        padding: 1.1rem 0.95rem;
    }
    
    .btn-auth {
        width: 100%;
        padding: 0.9rem 1rem;
        font-weight: 600;
        margin-top: 1.25rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 105, 217, 0.12);
    }
    
    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(0, 105, 217, 0.2);
    }
    
    .btn-auth:active {
        transform: translateY(0);
    }
    
    .auth-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.25s ease;
        position: relative;
    }
    
    .auth-link:hover {
        color: var(--primary-dark);
        text-decoration: none;
    }
    
    .auth-link::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--primary-dark);
        transition: width 0.25s ease;
    }
    
    .auth-link:hover::after {
        width: 100%;
    }
    
    .password-requirements {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        border-left: 4px solid var(--primary-light);
    }
    
    .password-requirements h6 {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--primary);
    }
    
    .password-requirements ul {
        margin-bottom: 0;
        padding-left: 1.25rem;
    }
    
    .password-requirements li {
        margin-bottom: 0.4rem;
    }
    
    .password-requirements li:last-child {
        margin-bottom: 0;
    }
    
    .form-check {
        padding-left: 1.75rem;
    }
    
    .form-check-input {
        width: 1.1em;
        height: 1.1em;
        margin-top: 0.25em;
        cursor: pointer;
        border: 1.5px solid #b0b5bb;
    }
    
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }
    
    .alert-danger {
        border-left-color: var(--danger);
        background-color: rgba(220, 53, 69, 0.1);
    }
';

// Page content
ob_start();
?>

<div class="auth-container">
    <div class="container">
        <div class="auth-card mx-auto">
            <div class="auth-header">
                <div class="logo-container">
                    <img src="public/assets/images/logo/Air-TechLogo.png" alt="Air-Protech Logo">
                    <div class="logo-text">
                        <h1>Air-Protech</h1>
                        
                    </div>
                </div>
                <h2 class="h4 mb-0">Join Our Community</h2>
            </div>
            
            <div class="auth-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?php 
                            $error = $_GET['error'];
                            if ($error === 'email_exists') {
                                echo 'This email address is already registered. Please use a different email.';
                            } elseif ($error === 'password_weak') {
                                echo 'Password does not meet the requirements. Please try again.';
                            } else {
                                echo 'An error occurred during registration. Please try again.';
                            }
                        ?>
                    </div>
                <?php endif; ?>
                
                <form id="registerForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="John" required autofocus>
                                <label for="firstName"><i class="bi bi-person me-2"></i>First Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Doe" required>
                                <label for="lastName"><i class="bi bi-person me-2"></i>Last Name</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email address</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required minlength="8">
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" 
                               placeholder="Confirm Password" required minlength="8">
                        <label for="confirmPassword"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                    </div>
                    
                    <div class="password-requirements">
                        <h6><i class="bi bi-shield-lock me-2"></i>Password Requirements</h6>
                        <ul>
                            <li>At least 8 characters long</li>
                            <li>Include at least one uppercase letter</li>
                            <li>Include at least one number</li>
                            <li>Include at least one special character</li>
                        </ul>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="terms.php" class="auth-link">Terms of Service</a> and <a href="privacy.php" class="auth-link">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-0">Already have an account? <a href="/auth/login" class="auth-link">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$pageScripts = "
    handleFormSubmission('loginForm', '/auth/register');
";

// Include the base template but override header and footer
$headerPath = null;
$navbarPath = null;
$footerPath = null;

include $basePath;
?>