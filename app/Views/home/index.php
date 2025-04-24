<?php
// Define page variables
$pageTitle = 'Welcome to Air-Protech Cooling';
$pageDescription = 'Professional Air Conditioning Services - Installation, Maintenance & Repair';

// Add page-specific styles
$pageStyles = '
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url("/assets/images/landing/aircon_background.png");
        background-size: cover;
        background-position: center;
        padding: 9rem 0;
        color: white;
        position: relative;
    }
    
    .hero-content {
        max-width: 650px;
        padding-left: 2rem; /* Added padding to move content right */
    }
    
    /* For larger screens, add even more padding */
    @media (min-width: 992px) {
        .hero-content {
            padding-left: 3rem;
            margin-left: 2rem; /* Additional margin for larger screens */
        }
    }
    
    .hero-badge {
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 1.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .service-card {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .service-card:hover {
        transform: translateY(-8px);
    }
    
    .service-card .card-img-top {
        height: 220px;
        object-fit: cover;
    }
    
    .service-card .card-body {
        padding: 1.75rem;
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        background-color: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border-radius: 50%;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .feature-item:hover .feature-icon {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 105, 217, 0.15);
    }
    
    .testimonial-card {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    .testimonial-card .card-body {
        padding: 2.25rem;
    }
    
    .testimonial-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 1rem;
        background-color: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .cta-section {
        background: linear-gradient(135deg, var(--primary) 0%, #004080 100%);
        padding: 6rem 0;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url("assets/images/pattern.png");
        opacity: 0.08;
        z-index: 1;
    }
    
    .cta-content {
        position: relative;
        z-index: 2;
    }
    
    .stat-item {
        text-align: center;
        padding: 2.5rem 1.5rem;
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        height: 100%;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    
    .stat-number {
        font-size: 2.75rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.75rem;
        line-height: 1;
    }
    
    .stat-label {
        color: var(--gray);
        font-weight: 500;
        font-size: 1.05rem;
    }
    
    .brands-section {
        padding: 3.5rem 0;
        background-color: #f8f9fa;
    }
    
    .brand-logo {
        height: 60px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .brand-logo:hover {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
    }
    
    .section-heading {
        position: relative;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    
    .section-heading::after {
        content: "";
        position: absolute;
        bottom: -0.75rem;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--primary);
    }
    
    .section-intro {
        max-width: 700px;
        margin: 0 auto 3rem;
    }
    
    .service-icon {
        font-size: 1.5rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    
    @media (max-width: 767.98px) {
        .hero-section {
            padding: 6rem 0;
        }
        
        .hero-content {
            padding-left: 1rem; /* Less padding on mobile */
        }
        
        .hero-content h1 {
            font-size: 2.25rem;
        }
    }
';

// Content for the landing page
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 hero-content"> <!-- Increased column width for more room -->
                <div class="hero-badge">
                    <i class="bi bi-check-circle-fill me-2"></i>Trusted HVAC Professionals
                </div>
                <h1 class="display-4 fw-bold mb-4">Advanced Cooling Solutions For Ultimate Comfort</h1>
                <p class="lead mb-5">Energy-efficient, reliable air conditioning systems expertly installed and maintained for residential and commercial properties.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="service-request.php" class="btn btn-primary btn-lg px-4 py-2">Schedule Service</a>
                    <a href="services.php" class="btn btn-outline-light btn-lg px-4 py-2">View Our Services</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5 py-lg-6">
    <div class="container">
        <div class="text-center section-intro">
            <h6 class="text-primary fw-semibold text-uppercase mb-3">Our Services</h6>
            <h2 class="fw-bold mb-3">Comprehensive Air Conditioning Solutions</h2>
            <p class="lead text-muted">Delivering professional cooling services tailored to your specific needs</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card service-card h-100 border-0">
                    <img src="/api/placeholder/800/500" class="card-img-top" alt="Air Conditioning Installation">
                    <div class="card-body">
                        <div class="service-icon">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">AC Installation & Replacement</h3>
                        <p class="card-text text-muted">Expert installation of energy-efficient air conditioning systems with personalized recommendations for your space.</p>
                        <a href="services.php?type=installation" class="btn btn-outline-primary mt-3">
                            Learn More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card service-card h-100 border-0">
                    <img src="/api/placeholder/800/500" class="card-img-top" alt="AC Maintenance Service">
                    <div class="card-body">
                        <div class="service-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">Preventive Maintenance</h3>
                        <p class="card-text text-muted">Regular maintenance plans to optimize performance, improve efficiency, and extend the lifespan of your cooling system.</p>
                        <a href="services.php?type=maintenance" class="btn btn-outline-primary mt-3">
                            Learn More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card service-card h-100 border-0">
                    <img src="/api/placeholder/800/500" class="card-img-top" alt="AC Repair Services">
                    <div class="card-body">
                        <div class="service-icon">
                            <i class="bi bi-wrench"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">Repair & Troubleshooting</h3>
                        <p class="card-text text-muted">Fast, reliable diagnosis and repair services for all air conditioning systems with quality replacement parts.</p>
                        <a href="services.php?type=repair" class="btn btn-outline-primary mt-3">
                            Learn More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="services.php" class="btn btn-primary px-4 py-2">View All Services</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 py-lg-6 bg-light">
    <div class="container">
        <div class="text-center section-intro">
            <h6 class="text-primary fw-semibold text-uppercase mb-3">Why Choose Us</h6>
            <h2 class="fw-bold mb-3">The Air-Protech Advantage</h2>
            <p class="lead text-muted">Industry-leading expertise, exceptional service, and guaranteed satisfaction</p>
        </div>
        
        <div class="row g-4 g-lg-5">
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Certified Technicians</h3>
                    <p class="text-muted">NATE-certified professionals with extensive training and years of industry experience.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">24/7 Emergency Service</h3>
                    <p class="text-muted">Round-the-clock emergency support when you need immediate cooling solutions.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">100% Satisfaction Guarantee</h3>
                    <p class="text-muted">We stand behind our work with comprehensive service warranties and guarantees.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="d-flex flex-column align-items-center text-center feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-3">Transparent Pricing</h3>
                    <p class="text-muted">Upfront quotes with no hidden fees and flexible financing options available.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 py-lg-6">
    <div class="container">
        <div class="text-center section-intro">
            <h6 class="text-primary fw-semibold text-uppercase mb-3">Testimonials</h6>
            <h2 class="fw-bold mb-3">What Our Clients Say</h2>
            <p class="lead text-muted">Read about real experiences from our satisfied customers</p>
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
                        <p class="card-text mb-4">"Air-Protech's team was professional, knowledgeable, and efficient. They completed our new system installation faster than expected and were incredibly neat. Our home has never been more comfortable!"</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="testimonial-avatar">
                                MJ
                            </div>
                            <div>
                                <h4 class="h6 mb-1 fw-bold">Michael Johnson</h4>
                                <p class="small text-muted mb-0">Residential Client</p>
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
                        <p class="card-text mb-4">"As a business owner, I need reliable contractors. Air-Protech has maintained our office cooling systems for 3 years with exceptional service. They're responsive, thorough, and always professional."</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="testimonial-avatar">
                                SW
                            </div>
                            <div>
                                <h4 class="h6 mb-1 fw-bold">Sarah Williams</h4>
                                <p class="small text-muted mb-0">Commercial Client</p>
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
                        <p class="card-text mb-4">"When our AC broke down during a heatwave, Air-Protech's emergency service was a lifesaver. The technician arrived within hours, diagnosed the problem quickly, and had our system running again by evening."</p>
                        <div class="d-flex align-items-center mt-4">
                            <div class="testimonial-avatar">
                                DT
                            </div>
                            <div>
                                <h4 class="h6 mb-1 fw-bold">David Thompson</h4>
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
<section class="py-5 py-lg-6 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <div class="stat-number">3,500+</div>
                    <div class="stat-label">Systems Installed</div>
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
                    <div class="stat-label">Client Satisfaction</div>
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

<!-- Brands Section -->
<section class="brands-section">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-semibold text-uppercase mb-3">Our Partners</h6>
            <h2 class="fw-bold mb-0">We Work With Premium Brands</h2>
        </div>
        
        <div class="row align-items-center justify-content-center g-5">
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/160/80" class="brand-logo" alt="Carrier Logo">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/160/80" class="brand-logo" alt="Trane Logo">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/160/80" class="brand-logo" alt="Lennox Logo">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/160/80" class="brand-logo" alt="Daikin Logo">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/160/80" class="brand-logo" alt="Mitsubishi Logo">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/160/80" class="brand-logo" alt="York Logo">
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container cta-content">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-4">Ready for Perfect Indoor Climate Control?</h2>
                <p class="lead mb-5">Schedule a consultation today and discover how our expert technicians can deliver the perfect cooling solution for your needs.</p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="service-request.php" class="btn btn-light btn-lg px-4 py-2">Schedule Service</a>
                    <a href="register.php" class="btn btn-outline-light btn-lg px-4 py-2">Create Account</a>
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