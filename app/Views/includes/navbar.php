<!-- navbar.php -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <!-- Logo and brand name on the left with colored text -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="/assets/images/logo/Air-TechLogo.jpg" alt="AirProtech Logo" class="me-2" width="35" height="35">
            <span class="d-none d-md-inline">
                <span style="color: #0d6efd; font-weight: 700; font-size: 1.3rem;">Air</span><span style="color: #dc3545; font-weight: 700; font-size: 1.3rem;">Protech</span>
            </span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <?php 
            // Define navigation based on user type
            $userType = $_SESSION['user_type'] ?? ''; // Default to user if not set
            
            if (isset($_SESSION['user_id'])) {
                // User is logged in, show appropriate navigation based on user type
                if ($userType === 'user'): ?>
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="services.php">
                                <i class="bi bi-tools"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="bi bi-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="requestsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-calendar-plus"></i> Requests
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="requestsDropdown">
                                <li><a class="dropdown-item" href="service-request.php">Service Request</a></li>
                                <li><a class="dropdown-item" href="product-order.php">Product Order</a></li>
                                <li><a class="dropdown-item" href="emergency-service.php">Emergency Service</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my-orders.php">
                                <i class="bi bi-list-check"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="support.php">
                                <i class="bi bi-headset"></i> Support
                            </a>
                        </li>
                    </ul>
                <?php 
                // Admin Navigation
                elseif ($userType === 'admin'): ?>
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="admin/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-clipboard-data"></i> Services
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                                <li><a class="dropdown-item" href="admin/service-requests.php">Service Requests</a></li>
                                <li><a class="dropdown-item" href="admin/service-schedule.php">Schedule</a></li>
                                <li><a class="dropdown-item" href="admin/service-history.php">History</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/technicians.php">
                                <i class="bi bi-person-badge"></i> Technicians
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-box-seam"></i> Inventory
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="inventoryDropdown">
                                <li><a class="dropdown-item" href="admin/inventory.php">Stock Management</a></li>
                                <li><a class="dropdown-item" href="admin/products.php">Products</a></li>
                                <li><a class="dropdown-item" href="admin/suppliers.php">Suppliers</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/customers.php">
                                <i class="bi bi-people"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/reports.php">
                                <i class="bi bi-graph-up"></i> Reports
                            </a>
                        </li>
                    </ul>
                <?php 
                // Technician Navigation
                elseif ($userType === 'technician'): ?>
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="technician/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="technician/assignments.php">
                                <i class="bi bi-calendar-check"></i> My Assignments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="technician/schedule.php">
                                <i class="bi bi-calendar-week"></i> Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="technician/inventory.php">
                                <i class="bi bi-box-seam"></i> Parts Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="technician/reports.php">
                                <i class="bi bi-file-earmark-text"></i> Reports
                            </a>
                        </li>
                    </ul>
                <?php endif;
            } else {
                // User is not logged in, show landing page navigation
                ?>
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="bi bi-info-circle"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">
                            <i class="bi bi-tools"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="bi bi-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="testimonials.php">
                            <i class="bi bi-chat-quote"></i> Testimonials
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="bi bi-envelope"></i> Contact
                        </a>
                    </li>
                </ul>
            <?php } ?>
            
            <!-- Right user profile -->
            <div class="d-flex align-items-center ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- User profile dropdown when logged in -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        <span class="d-none d-sm-inline">My Account</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
                <?php else: ?>
                <!-- Login and Register buttons when not logged in -->
                <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>