<div class="top-bar py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <a href="tel:+1234567890">123-456-7890</a>
                    </div>
                    <div>
                        <i class="bi bi-envelope-fill me-2"></i>
                        <a href="mailto:info@air-protech.com">info@air-protech.com</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-lg-end mt-2 mt-lg-0">
                <div class="d-flex justify-content-lg-end align-items-center">
                    <div class="me-4">
                        <i class="bi bi-clock-fill me-2"></i>
                        <span>Mon-Fri: 8:00 AM - 7:00 PM</span>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="text-white text-decoration-none me-3">
                            <i class="bi bi-person-circle me-1"></i> My Profile
                        </a>
                        <a href="logout.php" class="text-white text-decoration-none">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-white text-decoration-none me-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                        <a href="register.php" class="text-white text-decoration-none">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<header class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="logo-container">
                    <img src="assets/images/logo.png" alt="Air-Protech Logo" onerror="this.src='https://via.placeholder.com/50x50?text=AP'">
                    <div class="logo-text">
                        <h1>Air-Protech</h1>
                        <p>Cooling Excellence Since 2005</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center bg-primary-light rounded-circle" style="width: 45px; height: 45px;">
                                    <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-semibold">Our Location</h6>
                                <p class="mb-0 small text-muted">123 Cooling Ave, Cold City</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center bg-primary-light rounded-circle" style="width: 45px; height: 45px;">
                                    <i class="bi bi-headset text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-semibold">24/7 Support</h6>
                                <p class="mb-0 small text-muted">Always here to help you</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="quote.php" class="btn btn-primary">
                            <i class="bi bi-clipboard-check me-2"></i> Get a Free Quote
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>