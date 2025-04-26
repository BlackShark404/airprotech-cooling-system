<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Conditioning Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/home.css" >
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
        <nav class="navbar navbar-expand-lg bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="/assets/images/Air-TechLogo.jpg" alt="Logo" class="rounded-circle me-2" width="40" height="40">
                    <span class="brand-text">AIR<span class="text-danger">PROTECH</span></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#hero">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#our-services">Our Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#featured-products">Featured Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#why-choose-us">Why Choose Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-danger ms-2" href="/auth/login">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="hero" >
          <section class="hero-section text-white py-5" data-aos="fade-up">
              <div class="container">
                  <div class="row align-items-center">
                      <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right" data-aos-delay="150">
                          <h1 class="display-4 fw-bold mb-4">Professional Air Conditioning Solutions</h1>
                          <p class="lead mb-4">Expert installation and maintenance services for your comfort</p>
                          <a class="btn btn-danger btn-lg" href="/auth">Get Started Now</a>
                      </div>
                      <div class="col-md-6"  data-aos="fade-left" data-aos-delay="200">
                          <img src="/assets/images/ac.png" 
                              alt="Air Conditioning" 
                              class="img-fluid rounded shadow">
                      </div>
                  </div>
              </div>
          </section>
        </section>

        <!-- Services Section -->
        <section id="our-services" >
        <section class="services-section py-5 bg-light">
            <div class="container">
              <h2 class="text-center mb-5 fw-bold">Our Services</h2>
              <div class="row g-4">
                <!-- Installation -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
                  <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body">
                      <div class="service-icon mb-3 text-primary fs-2">
                        <i class="fas fa-tools"></i>
                      </div>
                      <h5 class="card-title fw-bold">Installation</h5>
                      <p class="card-text text-muted">Professional AC installation with expert guidance</p>
                      <a href="/auth" class="btn btn-primary mt-2">Book Now</a>
                    </div>
                  </div>
                </div>
            
                <!-- Maintenance -->
                <div class="col-md-4"  data-aos="fade-up" data-aos-delay="150">
                  <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body">
                      <div class="service-icon mb-3 text-primary fs-2">
                        <i class="fas fa-globe"></i>
                      </div>
                      <h5 class="card-title fw-bold">Maintenance</h5>
                      <p class="card-text text-muted">Regular maintenance to ensure optimal performance</p>
                      <a href="/auth" class="btn btn-primary mt-2">Book Now</a>
                    </div>
                  </div>
                </div>
          
                <!-- Repair -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                  <div class="card text-center h-100 shadow-sm border-0">
                    <div class="card-body">
                      <div class="service-icon mb-3 text-primary fs-2">
                        <i class="fas fa-wrench"></i>
                      </div>
                      <h5 class="card-title fw-bold">Repair</h5>
                      <p class="card-text text-muted">Quick and reliable repair services</p>
                      <a href="/auth" class="btn btn-primary mt-2">Book Now</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          </section>

        <!-- Products Section -->
        <section id="featured-products" class="products-section py-5 bg-light">
            <div class="container">
              <h2 class="text-center fw-bold mb-5">Featured Products</h2>
              <div class="row g-4">
                <!-- Product 1 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200" >
                  <div class="card product-card shadow-sm border-0 h-100">
                    <img src="path/to/product1.jpg" class="card-img-top" alt="Smart Inverter AC">
                    <div class="card-body">
                      <h5 class="fw-bold">Smart Inverter AC</h5>
                      <p class="text-muted mb-2">Energy-efficient cooling with smart controls</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="price text-danger fw-bold">$1,299</span>
                        <a href="/auth" class="btn btn-danger btn-sm">Book Now</a>
                      </div>
                    </div>
                  </div>
                </div>
          
                <!-- Product 2 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300" >
                  <div class="card product-card shadow-sm border-0 h-100">
                    <img src="path/to/product2.jpg" class="card-img-top" alt="Split System Classic">
                    <div class="card-body">
                      <h5 class="fw-bold">Split System Classic</h5>
                      <p class="text-muted mb-2">Reliable cooling for any room size</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="price text-danger fw-bold">$899</span>
                        <a href="/auth" class="btn btn-danger btn-sm">Book Now</a>
                      </div>
                    </div>
                  </div>
                </div>
          
                <!-- Product 3 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                  <div class="card product-card shadow-sm border-0 h-100">
                    <img src="path/to/product3.jpg" class="card-img-top" alt="Portable AC Unit">
                    <div class="card-body">
                      <h5 class="fw-bold">Portable AC Unit</h5>
                      <p class="text-muted mb-2">Flexible cooling solution for any space</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="price text-danger fw-bold">$499</span>
                        <a href="/auth" class="btn btn-danger btn-sm">Book Now</a>
                      </div>
                    </div>
                  </div>
                </div>
          
                <!-- Product 4 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                  <div class="card product-card shadow-sm border-0 h-100">
                    <img src="path/to/product4.jpg" class="card-img-top" alt="Commercial HVAC System">
                    <div class="card-body">
                      <h5 class="fw-bold">Commercial HVAC System</h5>
                      <p class="text-muted mb-2">Professional grade cooling system</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="price text-danger fw-bold">$2,499</span>
                        <a href="/auth" class="btn btn-danger btn-sm">Book Now</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          

    <!-- Why Choose Us Section -->
    <section id="why-choose-us" class="py-5 bg-light text-center">
        <div class="container">
          <h2 class="mb-5 fw-bold">Why Choose Us</h2>
          <div class="row">
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300" >
              <div>
                <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">24/7 Service</h5>
                <p class="text-muted">Round-the-clock emergency support</p>
              </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
              <div>
                <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">Certified Technicians</h5>
                <p class="text-muted">Experienced and qualified professionals</p>
              </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
              <div>
                <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">Guaranteed Satisfaction</h5>
                <p class="text-muted">100% satisfaction guaranteed</p>
              </div>
            </div>
            <div class="col-md-3 mb-4"  data-aos="fade-up" data-aos-delay="300">
              <div>
                <i class="fas fa-dollar-sign fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">Competitive Pricing</h5>
                <p class="text-muted">Best value for your money</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      
        <!-- Contact Section -->
        <section id="contact" class="contact py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="section-title mb-4">Contact Us</h2>
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" data-aos="fade-up" data-aos-delay="300">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control"  data-aos="fade-up" data-aos-delay="300">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="4" data-aos="fade-up" data-aos-delay="300 "></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" data-aos="fade-up" data-aos-delay="300">Send Message</button>
                        </form>
                    </div>
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30596073366!2d-74.25986652089843!3d40.69714941932609!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1647043435011!5m2!1sen!2sus" 
                            class="w-100 h-100 rounded" 
                            style="min-height: 400px; border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Updated Footer -->
    <footer class="footer text-white py-5">
        <div class="container">
          <div class="row">
            <!-- Brand & Description -->
            <div class="col-md-3 mb-4">
              <h3 class="h5 mb-3"><span style="color: white;">AIR</span><span class="text-danger">PROTECH</span></h3>
              <p class="text-white-50">Your trusted partner for all air conditioning needs. Professional service guaranteed.</p>
            </div>
      
            <!-- Quick Links -->
            <div class="col-md-3 mb-4">
              <h4 class="h6 mb-3">Quick Links</h4>
              <ul class="list-unstyled">
                <li><a href="#hero" class="text-white-50 text-decoration-none">Home</a></li>
                <li><a href="#our-services" class="text-white-50 text-decoration-none">Services</a></li>
                <li><a href="#featured-products" class="text-white-50 text-decoration-none">Products</a></li>
                <li><a href="#why-choose-us" class="text-white-50 text-decoration-none">Why Choose Us</a></li>
                <li><a href="#contact" class="text-white-50 text-decoration-none">Contact</a></li>
              </ul>
            </div>
      
            <!-- Contact Info -->
            <div class="col-md-3 mb-4">
              <h4 class="h6 mb-3">Contact Info</h4>
              <ul class="list-unstyled text-white-50">
                <li><i class="fas fa-phone text-primary me-2"></i> 1-800-AIR-COOL</li>
                <li><i class="fas fa-envelope text-primary me-2"></i> info@airprotech.com</li>
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> 123 Cooling Street, AC City</li>
              </ul>
            </div>
      
            <!-- Newsletter -->
            <div class="col-md-3 mb-4">
              <h4 class="h6 mb-3">Newsletter</h4>
              <p class="text-white-50">Subscribe for updates and special offers</p>
              <div class="input-group">
                <input type="email" class="form-control bg-dark text-white border-0" placeholder="Your email">
                <button class="btn btn-primary">Subscribe</button>
              </div>
            </div>
          </div>
          <div class="border-top border-white-50 mt-4 pt-4 text-center text-white-50">
            <p class="mb-0">&copy; 2025 Air-Protech. All rights reserved.</p>
          </div>
        </div>
      </footer>
      
    <!-- JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- Smooth scrolling script -->
    <script src="/assets/js/home/SmoothScrolling.js"></script>

    <script>
          AOS.init({
            duration: 1000, 
            easing: 'ease-in-out', 
            once: true, 
          });
    </script>
  </body>
</html>