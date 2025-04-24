<?php
// Define page variables
$pageTitle = 'Login to Your Account';
$pageDescription = 'Access your Air-Protech account to manage service requests and more';

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
        max-width: 480px;
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
    
    .auth-divider {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
    }
    
    .auth-divider::before,
    .auth-divider::after {
        content: "";
        flex: 1;
        border-bottom: 1px solid var(--gray-light);
    }
    
    .auth-divider span {
        padding: 0 1rem;
        color: var(--gray);
        font-size: 0.9rem;
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
    
    .form-check-label {
        font-size: 0.95rem;
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
    
    .alert-success {
        border-left-color: var(--success);
        background-color: rgba(40, 167, 69, 0.1);
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
                    <img src="/assets/images/logo/Air-TechLogo.png" alt="Air-Protech Logo">
                    <div class="logo-text">
                        <h1>Air-Protech</h1>
                    </div>
                </div>
                <h2 class="h4 mb-0">Welcome Back</h2>
            </div>
            
            <div class="auth-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?php 
                            $error = $_GET['error'];
                            if ($error === 'invalid') {
                                echo 'Invalid email or password. Please try again.';
                            } elseif ($error === 'inactive') {
                                echo 'Your account is inactive. Please contact support.';
                            } else {
                                echo 'An error occurred. Please try again.';
                            }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success']) && $_GET['success'] === 'reset'): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        Password reset instructions have been sent to your email.
                    </div>
                <?php endif; ?>
                
                <form action="process-login.php" method="post">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required autofocus>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email address</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="/auth/reset-password" class="auth-link">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-0">Don't have an account? <a href="/auth/register" class="auth-link">Create one now</a></p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the base template but override header and footer
$headerPath = null;
$navbarPath = null;
$footerPath = null;
include $basePath;
?>