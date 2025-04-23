<?php
// Define page variables
$pageTitle = 'Premium HVAC Solutions | Air-Protech Cooling';
$pageDescription = 'Industry-leading cooling solutions for residential and commercial spaces with expert installation, maintenance, and repair services.';
include $headerPath;
include $navbarPath;

// Add page-specific styles
$pageStyles = '
    /* Hero Section */
    .hero-section {
        background: linear-gradient(rgba(13, 110, 253, 0.85), rgba(0, 76, 158, 0.9)), url("assets/images/hero-bg.jpg");
        background-size: cover;
        background-position: center;
        padding: 10rem 0 8rem;
        position: relative;
        overflow: hidden;
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
    }
    
    .hero-content h1 {
        font-size: 3.2rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        letter-spacing: -0.5px;
    }
    
    .hero-content .lead {
        font-size: 1.25rem;
        font-weight: 400;
        margin-bottom: 2rem;
        opacity: 0.95;
    }
    
    .hero-badge {
        display: inline-block;
        background-color: rgba(255, 255, 255, 0.15);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(5px);
    }
    
    .hero-section::before {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        background: url("assets/images/pattern.png");
        opacity: 0.1;
        z-index: 1;
    }
    
    /* Service Cards */
    .service-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .service-card .card-body {
        padding: 1.75rem;
    }
    
    .service-card .card-img-top {
        height: 220px;
        object-fit: cover;
    }
    
    .service-card .card-title {
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--dark);
        font-size: 1.35rem;
    }
    
    .service-card .card-text {
        color: var(--gray);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }
    
    .service-card .btn {
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        border-radius: 6px;
    }
    
    /* Features */
    .feature-section {
        padding: 6rem 0;
        background: linear-gradient(to bottom, #f8f9fa, #ffffff);
    }
    
    .feature-block {
        padding: 2rem;
        border-radius: 12px;
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .feature-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
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
        border-radius: 12px;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .feature-block:hover .feature-icon {
        background-color: var(--primary);
        color: white;
    }
    
    .feature-title {
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.4rem;
    }
    
    .feature-text {
        color: var(--gray);
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    /* Section Headers */
    .section-header {
        margin-bottom: 3.5rem;
    }
    
    .section-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        position: relative;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }
    
    .section-header p {
        font-size: 1.125rem;
        color: var(--gray);
        max-width: 600px;
        margin: 0 auto;
    }
    
    .section-header h2:after {
        content: "";
        display: block;
        width: 70px;
        height: 4px;
        background: var(--primary);
        margin-top: 1rem;
        border-radius: 2px;
    }
    
    .text-center .section-header h2:after {
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Testimonials */
    .testimonial-section {
        background-color: #f9fafb;
        padding: 6rem 0;
    }
    
    .testimonial-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        background-color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .testimonial-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    }
    
    .testimonial-card .card-body {
        padding: 2rem;
    }
    
    .testimonial-text {
        position: relative;
        padding-top: 1.5rem;
        font-style: italic;
        color: #4a5568;
        line-height: 1.7;
    }
    
    .testimonial-text:before {
        content: """;
        font-size: 5rem;
        font-family: Georgia, serif;
        color: var(--primary-light);
        position: absolute;
        top: -2rem;
        left: -0.5rem;
        opacity: 0.3;
        line-height: 1;
    }
    
    .testimonial-rating {
        margin-bottom: 1rem;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .testimonial-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 1rem;
    }
    
    .testimonial-name {
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-size: 1.05rem;
    }
    
    .testimonial-role {
        color: var(--gray);
        font-size: 0.85rem;
    }
    
    /* Stats Section */
    .stats-section {
        padding: 5rem 0;
        background: linear-gradient(to bottom, #ffffff, #f8f9fa);
    }
    
    .stat-block {
        background-color: white;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        text-align: center;
        height: 100%;
        transition: all 0.3s ease;
        border-bottom: 4px solid var(--primary);
    }
    
    .stat-block:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary);
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
    }
    
    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
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
        opacity: 0.1;
        z-index: 1;
    }
    
    .cta-content {
        position: relative;
        z-index: 10;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .cta-title {
        font-size: 2.6rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }
    
    .cta-text {
        font-size: 1.2rem;
        margin-bottom: 2.5rem;
        opacity: 0.9;
    }
    
    .cta-buttons .btn {
        padding: 0.8rem 2rem;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 6px;
        margin: 0 0.5rem;
    }
    
    .btn-light {
        background-color: white;
        color: var(--primary);
    }
    
    .btn-light:hover {
        background-color: var(--light);
        color: var(--primary-dark);
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    /* Brand Section */
    .brands-section {
        padding: 4rem 0;
        background-color: #fff;
    }
    
    .brand-logo {
        height: 50px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.6;
        transition: all 0.3s ease;
    }
    
    .brand-logo:hover {
        filter: grayscale(0%);
        opacity: 1;
    }
    
    /* Why Choose Us Cards */
    .why-choose-card {
        padding: 2rem;
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        border-left: 4px solid var(--primary);
    }
    
    .why-choose-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .hero-content h1 {
            font-size: 2.5rem;
        }
        
        .section-header h2 {
            font-size: 2rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
        
        .cta-title {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .hero-section {
            padding: 6rem 0 5rem;
        }
        
        .hero-content h1 {
            font-size: 2rem;
        }
        
        .stat-block {
            margin-bottom: 1.5rem;
        }
        
        .testimonial-card, .service-card {
            margin-bottom: 1.5rem;
        }
        
        .cta-buttons .btn {
            display: block;
            width: 100%;
            margin: 0.5rem 0;
        }
    }
';

// Content for the enhanced landing page
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-xl-7 hero-content text-white">
                <span class="hero-badge">Trusted by 5,000+ customers</span>
                <h1>Expert Cooling Solutions for Ultimate Comfort</h1>
                <p class="lead">We deliver premium HVAC services with certified technicians, quality equipment, and guaranteed satisfaction for residential and commercial properties.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="service-request.php" class="btn btn-light btn-lg px-4">Schedule Service</a>
                    <a href="services.php" class="btn btn-outline-light btn-lg px-4">Explore Services</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-6 position-relative" style="padding-top: 5rem; padding-bottom: 5rem;">
    <div class="container">
        <div class="section-header text-center">
            <h2>Our Premium Services</h2>
            <p>Comprehensive cooling solutions tailored to your specific needs with attention to detail and superior craftsmanship</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <img src="/api/placeholder/600/400" class="card-img-top" alt="Premium AC Installation">
                    <div class="card-body">
                        <h3 class="card-title">AC Installation</h3>
                        <p class="card-text">Expert installation of energy-efficient cooling systems with precise sizing calculations and professional setup for optimal performance.</p>
                        <a href="services.php?type=installation" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <img src="/api/placeholder/600/400" class="card-img-top" alt="Preventive Maintenance Service">
                    <div class="card-body">
                        <h3 class="card-title">Preventive Maintenance</h3>
                        <p class="card-text">Comprehensive maintenance programs to extend system lifespan, prevent breakdowns, and maintain peak efficiency all year round.</p>
                        <a href="services.php?type=maintenance" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <img src="/api/placeholder/600/400" class="card-img-top" alt="Expert Repair Services">
                    <div class="card-body">
                        <h3 class="card-title">Repair Services</h3>
                        <p class="card-text">Fast, reliable repairs performed by certified technicians using genuine parts to restore your system to optimal performance quickly.</p>
                        <a href="services.php?type=repair" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="services.php" class="btn btn-primary btn-lg">View All Services</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="feature-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>Why Choose Air-Protech</h2>
            <p>We're committed to excellence in every aspect of our service</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-block text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-award"></i>
                    </div>
                    <h3 class="feature-title">Certified Experts</h3>
                    <p class="feature-text">Our technicians are certified professionals with extensive training and years of hands-on experience.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-block text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="feature-title">24/7 Emergency Service</h3>
                    <p class="feature-text">Round-the-clock emergency services available when you need immediate assistance.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-block text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="feature-title">Satisfaction Guarantee</h3>
                    <p class="feature-text">We stand behind our work with comprehensive warranties and satisfaction guarantees.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-block text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3 class="feature-title">Energy Efficiency</h3>
                    <p class="feature-text">Solutions designed to maximize performance while minimizing energy consumption and costs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonial-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>Customer Testimonials</h2>
            <p>What our satisfied clients have to say about our services</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="testimonial-card">
                    <div class="card-body">
                        <div class="testimonial-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="testimonial-text">The technicians were professional, punctual, and incredibly knowledgeable. Installation was completed ahead of schedule and our new system works perfectly. Highly recommend Air-Protech!</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">MJ</div>
                            <div>
                                <h4 class="testimonial-name">Michael Johnson</h4>
                                <p class="testimonial-role">Residential Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="testimonial-card">
                    <div class="card-body">
                        <div class="testimonial-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="testimonial-text">We've been relying on Air-Protech for maintenance of our office building for over three years. Their service is consistently excellent, responsive, and their team is always professional and courteous.</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">SW</div>
                            <div>
                                <h4 class="testimonial-name">Sarah Williams</h4>
                                <p class="testimonial-role">Business Owner</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="testimonial-card">
                    <div class="card-body">
                        <div class="testimonial-rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-half text-warning"></i>
                        </div>
                        <p class="testimonial-text">When our AC broke down during a heatwave, Air-Protech's emergency service saved us. They arrived within hours, diagnosed the issue quickly, and had our system up and running the same day.</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">DT</div>
                            <div>
                                <h4 class="testimonial-name">David Thompson</h4>
                                <p class="testimonial-role">Homeowner</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-block">
                    <div class="stat-number">3,500+</div>
                    <div class="stat-label">Installations</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="stat-block">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="stat-block">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Customer Satisfaction</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="stat-block">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Emergency Service</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Brands Section -->
<section class="brands-section border-top border-bottom border-light">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="h5 text-uppercase fw-bold text-muted">Trusted Brands We Work With</h3>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="col-4 col-md-2 text-center mb-4 mb-md-0">
                <img src="/api/placeholder/120/60" class="brand-logo mx-auto" alt="Brand Logo">
            </div>
            <div class="col-4 col-md-2 text-center mb-4 mb-md-0">
                <img src="/api/placeholder/120/60" class="brand-logo mx-auto" alt="Brand Logo">
            </div>
            <div class="col-4 col-md-2 text-center mb-4 mb-md-0">
                <img src="/api/placeholder/120/60" class="brand-logo mx-auto" alt="Brand Logo">
            </div>
            <div class="col-4 col-md-2 text-center mb-4 mb-md-0">
                <img src="/api/placeholder/120/60" class="brand-logo mx-auto" alt="Brand Logo">
            </div>
            <div class="col-4 col-md-2 text-center mb-4 mb-md-0">
                <img src="/api/placeholder/120/60" class="brand-logo mx-auto" alt="Brand Logo">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="/api/placeholder/120/60" class="brand-logo mx-auto" alt="Brand Logo">
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content text-center">
            <h2 class="cta-title">Ready For Superior Cooling Service?</h2>
            <p class="cta-text">Join thousands of satisfied customers who trust Air-Protech for their cooling needs. Sign up today and experience the difference professional service makes.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-light">Get Started</a>
                <a href="contact.php" class="btn btn-outline-light">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Include the base template
include $basePath;
?>