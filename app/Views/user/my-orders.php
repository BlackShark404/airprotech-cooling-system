<?php use Core\Session;?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders & Service Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="contact-info">
                <a href="tel:+1234567890" class="me-3 text-white text-decoration-none">
                    <i class="fas fa-phone me-2"></i>+1 234 567 890
                </a>
                <a href="mailto:contact@apcs.com" class="text-white text-decoration-none">
                    <i class="fas fa-envelope me-2"></i>contact@apcs.com
                </a>
            </div>
            <div class="social-links">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="/assets/images/logo/Air-TechLogo.png" alt="Logo" class="rounded-circle me-2" width="40" height="40">
                <span class="brand-text">AIR<span class="text-danger">PROTECH</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="/user/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/products">Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/user/orders-services">My Orders & Service Requests</a></li>
                    <!-- User Profile -->
                    <li class="nav-item dropdown ms-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="position-relative">
                                <img src="<?=Session::get('profile_url')?>" alt="Profile Image" class="rounded-circle" width="30" height="30">
                                <span class="position-absolute bottom-0 end-0 translate-middle-y bg-success rounded-circle border border-white" style="width: 8px; height: 8px;"></span>
                            </div>
                            <span class="ms-2 text-dark"><?=Session::get('user_name')?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/user/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="/user/settings">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="dashboard-area py-5">
        <div class="container">
            <h2 class="fw-bold mb-2">My Orders & Service Requests</h2>
            <p class="text-muted mb-4">View and track your orders and service history</p>

            <!-- Tabs and Filters -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <ul class="nav nav-tabs" id="ordersTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="true">Orders</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">Service Requests</button>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <select class="form-select me-2" style="width: 150px;">
                        <option>Last 30 days</option>
                        <option>Last 60 days</option>
                        <option>Last 90 days</option>
                        <option>All time</option>
                    </select>
                    <select class="form-select me-2" style="width: 150px;">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Completed</option>
                        <option>Cancelled</option>
                    </select>
                    <input type="text" class="form-control" placeholder="Search orders..." style="width: 200px;">
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="ordersTabContent">
                <!-- Orders Tab -->
                <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                    <!-- Order Items -->
                    <div class="booking-item card shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <img src="/assets/images/smart-inverter-ac.jpg" alt="Smart Inverter AC" class="me-4" width="100" height="100">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">ORD-2025-001 <span class="text-muted">Jan 15, 2024</span></p>
                                        <h5 class="fw-bold mb-1">Smart Inverter AC</h5>
                                        <p class="text-muted mb-0">Model: AS-234XC</p>
                                        <p class="fw-bold text-dark mb-0">$299</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Ordered on: May 10, 2025</p>
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        <div class="mt-2">
                                            <button class="btn btn-danger">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-item card shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <img src="/assets/images/portable-ac-unit.jpg" alt="Portable AC Unit" class="me-4" width="100" height="100">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">ORD-2025-002 <span class="text-muted">Jan 20, 2024</span></p>
                                        <h5 class="fw-bold mb-1">Portable AC Unit</h5>
                                        <p class="text-muted mb-0">Model: PA-789UV</p>
                                        <p class="fw-bold text-dark mb-0">$699</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Ordered on: May 20, 2025</p>
                                        <span class="badge bg-success-subtle text-success">Completed</span>
                                        <div class="mt-2">
                                            <button class="btn btn-danger">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-item card shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <img src="/assets/images/ac-maintenance-service.jpg" alt="AC Maintenance Service" class="me-4" width="100" height="100">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">ORD-2025-003 <span class="text-muted">Jan 10, 2024</span></p>
                                        <h5 class="fw-bold mb-1">AC Maintenance Service</h5>
                                        <p class="text-muted mb-0">Model: SVC-001</p>
                                        <p class="fw-bold text-dark mb-0">$149</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Ordered on: May 30, 2025</p>
                                        <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                        <div class="mt-2">
                                            <button class="btn btn-danger">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <label>Show 
                                <select class="form-select d-inline-block" style="width: 80px;">
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                </select> entries
                            </label>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Service Requests Tab -->
                <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                    <!-- Service Request Items -->
                    <div class="booking-item card shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="service-icon me-4">
                                <i class="fas fa-tools fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">SRV-2025-001 <span class="text-muted">Feb 1, 2024</span></p>
                                        <h5 class="fw-bold mb-1">Aircon Check-up & Repair</h5>
                                        <p class="text-muted mb-0">Service: Professional diagnostics and repair</p>
                                        <p class="fw-bold text-dark mb-0">$120</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Requested on: May 5, 2025</p>
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        <div class="mt-2">
                                            <button class="btn btn-danger">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-item card shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="service-icon me-4">
                                <i class="fas fa-plug fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">SRV-2025-002 <span class="text-muted">Mar 10, 2024</span></p>
                                        <h5 class="fw-bold mb-1">Installation of Units</h5>
                                        <p class="text-muted mb-0">Service: Expert installation of AC units</p>
                                        <p class="fw-bold text-dark mb-0">$250</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Requested on: May 15, 2025</p>
                                        <span class="badge bg-success-subtle text-success">Completed</span>
                                        <div class="mt-2">
                                            <button class="btn btn-danger">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-item card shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="service-icon me-4">
                                <i class="fas fa-broom fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">SRV-2025-003 <span class="text-muted">Apr 5, 2024</span></p>
                                        <h5 class="fw-bold mb-1">General Cleaning & PMS</h5>
                                        <p class="text-muted mb-0">Service: Preventive maintenance and cleaning</p>
                                        <p class="fw-bold text-dark mb-0">$80</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Requested on: May 25, 2025</p>
                                        <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                        <div class="mt-2">
                                            <button class="btn btn-danger">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <label>Show 
                                <select class="form-select d-inline-block" style="width: 80px;">
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                </select> entries
                            </label>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class "page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h3 class="h5 mb-3"><span style="color: white;">AIR</span><span class="text-danger">PROTECH</span></h3>
                    <p class="text-white-50">Your trusted partner for all air conditioning needs. Professional service guaranteed.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="h6 mb-3">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="/user/services" class="text-white-50 text-decoration-none">Services</a></li>
                        <li><a href="/user/products" class="text-white-50 text-decoration-none">Products</a></li>
                        <li><a href="/user/orders-services" class="text-white-50 text-decoration-none">My Orders</a></li>
                        <li><a href="#contact" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="h6 mb-3">Contact Info</h4>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="fas fa-phone text-primary me-2"></i> 1-800-AIR-COOL</li>
                        <li><i class="fas fa-envelope text-primary me-2"></i> info@airprotech.com</li>
                        <li><i class="fas fa-map-marker-alt text-primary me-2"></i> 123 Cooling Street, AC City</li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h4 class="h6 mb-3">Newsletter</h4>
                    <p class="text-white-50">Subscribe for updates and special offers</p>
                    <form>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email">
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="border-top border-white-50 mt-4 pt-4 text-center text-white-50">
                <p class="mb-0">© 2024 Air-ProTech. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="/assets/js/utility/toast-notifications.js"></script>
    <script src="/assets/js/utility/form-handler.js"></script>

    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
        });
    </script>
</body>
</html>