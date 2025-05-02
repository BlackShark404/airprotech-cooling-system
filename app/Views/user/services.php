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
                    <li class="nav-item"><a class="nav-link" href="/user/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user/products">Products</a></li>
                    <!-- User Profile -->
                    <li class="nav-item dropdown ms-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/assets/images/Profile.jpg" alt="Online Image" class="rounded-circle me-2" width="36" height="36">
                            <div class="d-flex flex-column lh-sm">
                                <span class="fw-semibold small text-dark">Arlon Rondina</span>
                                <small class="text-success">● Online</small>
                            </div>
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

    <!-- Hero Section -->
<section class="hero-section text-white py-5" style="background: url('/assets/images/backgroundhero.jpg') no-repeat center center; background-size: cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <h1 class="fw-bold mb-3">Professional AC Services & Solutions</h1>
                <p class="mb-4">Expert installation, maintenance, and repair services for all your air conditioning needs</p>
                <a href="#" class="btn btn-danger rounded-3 px-4 py-2 fw-medium">Book Service Now</a>
            </div>
        </div>
    </div>
</section>

    <!-- Service Categories -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-tools fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Installation</h5>
                            <p class="text-muted mb-0">Professional AC Installation</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-wrench fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Repair</h5>
                            <p class="text-muted mb-0">Expert Check-up & Repair</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-broom fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Maintenance</h5>
                            <p class="text-muted mb-0">General Cleaning & PMS</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-calculator fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Survey & Quotation</h5>
                            <p class="text-muted mb-0">Free Site Estimation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Services Section -->
    <section id="our-services" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Our Services</h2>
            
            <div class="row g-4">
                <!-- AC Check-up & Repair -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-tools fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Aircon Check-up & Repair</h5>
                            <p class="text-muted mb-3">Professional diagnostics and repair for all AC brands</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Installation of Units -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-plug fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Installation of Units</h5>
                            <p class="text-muted mb-3">Expert installation of all types of air conditioning units</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ducting Works -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-wind fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Ducting Works</h5>
                            <p class="text-muted mb-3">Professional ducting installation and maintenance</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- General Cleaning & PMS -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-broom fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">General Cleaning & PMS</h5>
                            <p class="text-muted mb-3">Preventive maintenance service and thorough cleaning</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Supply of Brand New Aircon Units -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-box-open fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Supply of Brand New Units</h5>
                            <p class="text-muted mb-3">Wide range of brand new aircon units available</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Survey & Estimation -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-search fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Survey & Estimation</h5>
                            <p class="text-muted mb-3">On-site assessment and professional recommendations</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Project Quotations -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-file-invoice-dollar fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Project Quotations</h5>
                            <p class="text-muted mb-3">Detailed cost estimates for your aircon projects</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Project Biddings -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-handshake fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Project Biddings</h5>
                            <p class="text-muted mb-3">Competitive proposals for commercial and residential projects</p>
                            <div>
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Service Card (Optional) -->
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-phone-alt fa-2x text-primary"></i>
                            </div>
                            <h5 class="fw-bold">Need Other Services?</h5>
                            <p class="text-muted mb-3">Contact our team for custom solutions tailored to your needs</p>
                            <div>
                                <a href="#contact" class="btn btn-outline-primary">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Process -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Simple Booking Process</h2>
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-tools fa-2x text-white"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold">Choose Service</h5>
                    <div class="text-muted mt-2">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                            <i class="far fa-clock fa-2x text-white"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold">Select Schedule</h5>
                    <div class="text-muted mt-2">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-tools fa-2x text-white"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold">Provide Details</h5>
                    <div class="text-muted mt-2">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-check-circle fa-2x text-white"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold">Confirmation</h5>
                </div>
            </div>
        </div>
    </section>

     <!-- Contact Section -->
     <section id="contact" class="contact py-5" class="col-md-4" data-aos="fade-up" data-aos-delay="150">
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

        <!-- Book Service Modal -->
<div class="modal fade" id="bookServiceModal" tabindex="-1" aria-labelledby="bookServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="bookServiceModalLabel">Book Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="serviceBookingForm">
          <!-- Service Selection -->
          <div class="mb-3">
            <label for="serviceSelect" class="form-label">Select Service <span class="text-danger">*</span></label>
            <select class="form-select" id="serviceSelect" required>
                <option value="" selected disabled>Choose a service</option>
                <option value="checkup-repair">Aircon Check-up & Repair</option>
                <option value="installation">Installation of Units</option>
                <option value="ducting">Ducting Works</option>
                <option value="cleaning-pms">General Cleaning & PMS</option>
                <option value="supply-units">Supply of Brand New Aircon Units</option>
                <option value="survey-estimation">Survey & Estimation</option>
                <option value="quotations">Project Quotations</option>
                <option value="biddings">Project Biddings</option>
            </select>

          </div>
          
          <!-- Date and Time Selection -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="preferredDate" class="form-label">Preferred Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="preferredDate" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="preferredTime" class="form-label">Preferred Time <span class="text-danger">*</span></label>
              <input type="time" class="form-control" id="preferredTime" required>
            </div>
          </div>
          
          <!-- Service Description -->
          <div class="mb-3">
            <label for="serviceDescription" class="form-label">Service Description <span class="text-danger">*</span></label>
            <textarea class="form-control" id="serviceDescription" rows="3" placeholder="Please describe your service needs..." required></textarea>
          </div>
          
          <!-- Contact Information -->
          <div class="mb-3">
            <label for="fullName" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="fullName" placeholder="Enter your full name" required>
          </div>
          
          <div class="mb-3">
            <label for="emailAddress" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="emailAddress" placeholder="Enter your email address" required>
          </div>
          
          <div class="mb-3">
            <label for="phoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" id="phoneNumber" placeholder="Enter your phone number" required>
          </div>
          
          <div class="mb-3">
            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="address" placeholder="Enter your address" required>
          </div>
          
          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

   
    <!-- Footer -->
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
    <script src="/assets/js/home.js"></script>

    <script>
        AOS.init({
            duration: 1000, 
            easing: 'ease-in-out', 
            once: true, 
        });
    </script>

    <script>
        // Service Booking Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get all "Book Now" buttons
    const bookButtons = document.querySelectorAll('.btn-primary[href="#"]');
    
    // Add click event to all book buttons
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get service name from the parent card
            const serviceCard = this.closest('.card');
            if (serviceCard) {
                const serviceName = serviceCard.querySelector('h5.fw-bold').textContent;
                
                // Pre-select the service in the dropdown
                const serviceSelect = document.getElementById('serviceSelect');
                
                for (let i = 0; i < serviceSelect.options.length; i++) {
                    if (serviceSelect.options[i].text.includes(serviceName)) {
                        serviceSelect.selectedIndex = i;
                        break;
                    }
                }
            }
            
            // Open the modal
            const bookingModal = new bootstrap.Modal(document.getElementById('bookServiceModal'));
            bookingModal.show();
        });
    });
    
    // Form submission handling
    const bookingForm = document.getElementById('serviceBookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                service: document.getElementById('serviceSelect').value,
                date: document.getElementById('preferredDate').value,
                time: document.getElementById('preferredTime').value,
                description: document.getElementById('serviceDescription').value,
                name: document.getElementById('fullName').value,
                email: document.getElementById('emailAddress').value,
                phone: document.getElementById('phoneNumber').value,
                address: document.getElementById('address').value
            };
            
            // Here you would typically send this data to your server
            console.log('Booking request:', formData);
            
            // For demo purposes, show success message
            alert('Your service booking request has been submitted successfully! We will contact you shortly to confirm your appointment.');
            
            // Close the modal
            bootstrap.Modal.getInstance(document.getElementById('bookServiceModal')).hide();
            
            // Reset the form
            bookingForm.reset();
        });
    }
    
    // Add event listener to "Book Service Now" button in hero section
    const heroBookButton = document.querySelector('.hero-section .btn-danger');
    if (heroBookButton) {
        heroBookButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Open the modal
            const bookingModal = new bootstrap.Modal(document.getElementById('bookServiceModal'));
            bookingModal.show();
        });
    }
});
    </script>
</body>
</html>