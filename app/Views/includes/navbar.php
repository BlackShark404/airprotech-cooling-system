<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-lg-none" href="index.php">
            <img src="assets/images/logo-small.png" alt="Air-Protech" onerror="this.src='https://via.placeholder.com/40x40?text=AP'">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <?php 
            // Define navigation based on user type
            $userType = $_SESSION['user_type'] ?? 'customer'; // Default to customer if not set
            
            if ($userType === 'customer'): ?>
                <ul class="navbar-nav me-auto">
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
                <ul class="navbar-nav me-auto">
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
                <ul class="navbar-nav me-auto">
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
            <?php endif; ?>
            
            <!-- Search form - visible for all user types -->
            <form class="d-flex ms-auto">
                <div class="input-group">
                    <input class="form-control border-end-0" type="search" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-outline-primary border-start-0" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>