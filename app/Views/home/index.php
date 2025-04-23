<?php
// Define page variables
$pageTitle = 'Welcome to Air-Protech Cooling';
$pageDescription = 'Professional HVAC Services - Installation, Maintenance & Repair';
include $headerPath;
include $navbarPath;

// Add page-specific styles
$pageStyles = '
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("assets/images/hero-bg.jpg");
        background-size: cover;
        background-position: center;
        padding: 8rem 0;
        color: white;
    }
    
    .hero-content {
        max-width: 700px;
    }
    
    .service-card {
        height: 100%;
    }
    
    .service-card .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    
    .feature-icon {
        width: 70px;
        height: 70px;
        background-color: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border-radius: 50%;
        margin-bottom: 1.5rem;
    }
    
    .testimonial-card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .testimonial-card .card-body {
        padding: 2rem;
    }
    
    .cta-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 5rem 0;
        color: white;
        position: relative;
    }
    
    .cta-section::after {
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
    
    .cta-content {
        position: relative;
        z-index: 2;
    }
    
    .stat-item {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 8px;
        background-color: white;
        box-shadow: var(--card-shadow);
        height: 100%;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: var(--dark);
        font-weight: 500;
    }
    
    @media (max-width: 767.98px) {
        .hero-section {
            padding: 5rem 0;
        }
    }
    
    .brand-section {
        padding: 3rem 0;
        background-color: var(--gray-light);
    }
    
    .brand-logo {
        height: 50px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: var(--transition);
    }
    
    .brand-logo:hover {
        filter: grayscale(0%);
        opacity: 1;
    }
';

// Content for the landing page
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 hero-content">
                <h1 class="display-4 fw-bold mb-4">Professional Cooling Solutions For Your Comfort</h1>
                <p class="lead mb-4">Expert HVAC services delivering reliable, energy-efficient cooling solutions for residential and commercial properties.</p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="service-request.php" class="btn btn-primary btn-lg">Schedule Service</a>
                    <a href="services.php" class="btn btn-outline-light btn-lg">Explore Services</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
            <p class="lead">Comprehensive cooling solutions for all your needs</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card service-card h-100">
                    <img src="/api/placeholder/600/400" class="card-img-top" alt="AC Installation Service">
                    <div class="card-body">
                        <h3 class="card-title h5">AC Installation</h3>
                        <p class="card-text">Professional installation of air conditioning systems with expert guidance on selecting the right unit for your space.</p>
                        <a href="services.php?type=installation" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card service-card h-100">
                    <img src="/api/placeholder/600/400" class="card-img-top" alt="Preventive Maintenance Service">
                    <div class="card-body">
                        <h3 class="card-title h5">Preventive Maintenance</h3>
                        <p class="card-text">Regular maintenance services to ensure your cooling system operates efficiently and extends its lifespan.</p>
                        <a href="services.php?type=maintenance" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card service-card h-100">
                    <img src="/api/placeholder/600/400" class="card-img-top" alt="Repair Services">
                    <div class="card-body">
                        <h3 class="card-title h5">Repair Services</h3>
                        <p class="card-text">Fast and reliable repair services for all types of cooling systems with quality replacement parts.</p>
                        <a href="services.php?type=repair" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="services.php" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Air-Protech</h2>
            <p class="lead">We deliver quality, reliability, and excellence in every service</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="feature-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h3 class="h5 mb-3">Certified Experts</h3>
                    <p>Our technicians are certified professionals with years of experience in the HVAC industry.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="feature-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="h5 mb-3">24/7 Service</h3>
                    <p>Emergency services available round the clock for your urgent cooling needs.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="h5 mb-3">Quality Guarantee</h3>
                    <p>We stand behind our work with satisfaction guarantees and warranties on services.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="feature-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h3 class="h5 mb-3">Competitive Pricing</h3>
                    <p>Transparent pricing with no hidden fees and flexible payment options.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Clients Say</h2>
            <p class="lead">Don't just take our word for it</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card testimonial-card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="card-text">"The technicians from Air-Protech were professional, knowledgeable, and completed the installation faster than expected. Our new AC works perfectly!"</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="flex-grow-1">
                                <h4 class="h6 mb-1">Michael Johnson</h4>
                                <p class="small text-muted mb-0">Residential Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card testimonial-card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="card-text">"We've been using Air-Protech for our office building maintenance for over 3 years now. They are reliable, responsive, and always deliver quality service."</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="flex-grow-1">
                                <h4 class="h6 mb-1">Sarah Williams</h4>
                                <p class="small text-muted mb-0">Business Owner</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card testimonial-card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-half text-warning"></i>
                        </div>
                        <p class="card-text">"When our AC broke down in the middle of summer, Air-Protech's emergency service was a lifesaver. They arrived within hours and fixed the issue efficiently."</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="flex-grow-1">
                                <h4 class="h6 mb-1">David Thompson</h4>
                                <p class="small text-muted mb-0">Homeowner</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">3,500+</div>
                    <div class="stat-label">Installations</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Customer Satisfaction</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Emergency Service</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container cta-content">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-6 fw-bold mb-4">Ready to Experience Superior Cooling Services?</h2>
                <p class="lead mb-4">Schedule a service appointment today and enjoy a comfortable environment with our expert solutions.</p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="register.php" class="btn btn-light btn-lg">Sign Up Now</a>
                    <a href="login.php" class="btn btn-outline-light btn-lg">Login</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Include the base template
include $basePath;
?>