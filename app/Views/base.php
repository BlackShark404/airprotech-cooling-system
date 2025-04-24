<?php
// Define page title and description - to be set by individual pages
$pageTitle = $pageTitle ?? 'Air-Protech Cooling Services';
$pageDescription = $pageDescription ?? 'Comprehensive Service Booking & Inventory Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <title><?php echo $pageTitle; ?> | Air-Protech Cooling</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #0069d9;
            --primary-light: #e8f1fd;
            --primary-dark: #004c9e;
            --secondary: #e53935;
            --secondary-light: #ffebee;
            --dark: #212529;
            --dark-light: #343a40;
            --light: #f8f9fa;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --card-shadow: 0 5px 15px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--dark);
            background-color: #f9fafb;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo-container img {
            max-height: 50px;
            transition: var(--transition);
        }
        
        .logo-text {
            margin-left: 12px;
        }
        
        .logo-text h1 {
            font-size: 1.5rem;
            margin-bottom: 0;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.5px;
        }
        
        .logo-text p {
            font-size: 0.8rem;
            margin-bottom: 0;
            color: var(--gray);
        }
        
        .navbar {
            padding: 0.75rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            background-color: white;
        }
        
        .navbar .nav-link {
            color: var(--dark);
            font-weight: 500;
            padding: 0.75rem 1.2rem;
            border-radius: 4px;
            transition: var(--transition);
            margin: 0 0.15rem;
            font-size: 0.9rem;
        }
        
        .navbar .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }
        
        .navbar .nav-link:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }
        
        .navbar .nav-link.active {
            color: var(--primary);
            background-color: var(--primary-light);
            font-weight: 600;
        }
        
        .top-bar {
            background-color: var(--dark);
            color: white;
            padding: 0.6rem 0;
            font-size: 0.85rem;
        }
        
        .top-bar a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .top-bar a:hover {
            color: var(--gray-light);
        }
        
        .btn {
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 5px;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }
        
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #c62828;
            border-color: #c62828;
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        header.page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        header.page-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('assets/images/pattern.png');
            opacity: 0.1;
            z-index: 1;
        }
        
        header.page-header .container {
            position: relative;
            z-index: 2;
        }
        
        header.page-header h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        main {
            flex: 1;
            padding: 2.5rem 0;
        }
        
        footer {
            background-color: var(--dark);
            color: var(--light);
            padding: 3rem 0 1.5rem;
            position: relative;
        }
        
        footer h5, footer h6 {
            color: white;
            margin-bottom: 1.5rem;
            position: relative;
            font-weight: 600;
        }
        
        footer h5::after, footer h6::after {
            content: '';
            position: absolute;
            bottom: -0.75rem;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }
        
        footer ul {
            padding-left: 0;
        }
        
        footer ul li {
            margin-bottom: 0.75rem;
        }
        
        footer a {
            color: #b0b5bb;
            text-decoration: none;
            transition: var(--transition);
        }
        
        footer a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        footer i {
            margin-right: 0.5rem;
        }
        
        footer .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            transition: var(--transition);
            margin-right: 0.75rem;
        }
        
        footer .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            font-weight: 600;
            padding: 1.2rem 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Custom styles for each user type */
        .customer-theme .accent-color {
            color: var(--primary);
        }
        
        .admin-theme .accent-color {
            color: var(--secondary);
        }
        
        .technician-theme .accent-color {
            color: var(--success);
        }
        
        /* Form styles */
        .form-control, .form-select {
            padding: 0.65rem 1rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 105, 217, 0.25);
            border-color: var(--primary);
        }
        
        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        
        .table thead th {
            background-color: var(--gray-light);
            border-bottom: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: var(--dark);
        }
        
        /* Badge styles */
        .badge {
            padding: 0.5rem 0.85rem;
            border-radius: 50rem;
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Custom responsive classes */
        @media (max-width: 767.98px) {
            .navbar .nav-link {
                padding: 0.5rem 0.75rem;
                margin: 0.25rem 0;
            }
            
            footer {
                padding-bottom: 1rem;
            }
            
            footer h5, footer h6 {
                margin-top: 1.5rem;
            }
        }
        
        /* Add any additional dynamic styles here */
        <?php echo $additionalStyles ?? ''; ?>
    </style>
    
    <!-- Additional page-specific styles -->
    <?php if (isset($pageStyles)): ?>
        <style><?php echo $pageStyles; ?></style>
    <?php endif; ?>
</head>
<body class="<?php echo $userType ?? 'customer'; ?>-theme">
    <?php if (isset($headerPath) && file_exists($headerPath)): ?>
        <?php include_once($headerPath); ?>
    <?php endif; ?>
    
    <?php if (isset($navbarPath) && file_exists($navbarPath)): ?>
        <?php include_once($navbarPath); ?>
    <?php endif; ?>
    
    <?php if (isset($pageHeader)): ?>
        <header class="page-header">
            <div class="container">
                <h1><?php echo $pageHeader; ?></h1>
                <?php if (isset($pageSubheader)): ?>
                    <p class="lead mb-0"><?php echo $pageSubheader; ?></p>
                <?php endif; ?>
            </div>
        </header>
    <?php endif; ?>
    
    <main>
        <div class="container">
            <!-- Main content will be included here -->
            <?php echo $content ?? ''; ?>
        </div>
    </main>
    
    <?php if (isset($footerPath) && file_exists($footerPath)): ?>
        <?php include_once($footerPath); ?>
    <?php endif; ?>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="/assets/js/utility/toast-notifications.js"></script>
    <script src="/assets/js/utility/form-handler.js"></script>

    <!-- Additional page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
        <script><?php echo $pageScripts; ?></script>
    <?php endif; ?>
    
    <!-- Custom JS -->
    <script>
        // Common JavaScript functions
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle active class in navbar based on current page
            const currentLocation = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath && currentLocation.includes(linkPath) && linkPath !== '#') {
                    link.classList.add('active');
                }
            });
            
            // Add smooth scrolling to all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && document.querySelector(href)) {
                        e.preventDefault();
                        document.querySelector(href).scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Add any additional common JS functionality here
        });
        
        // Add any additional dynamic scripts here
        <?php echo $additionalScripts ?? ''; ?>
    </script>
</body>
</html>