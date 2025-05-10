<?php use Core\Session;?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Conditioning Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/home.css" >
    
    <style>
        .product-card {
            border-radius: 12px;
            transition: transform 0.3s ease;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background-color: white;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }
        
        .product-img-container {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-color: #f8f9fa;
        }
        
        .product-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .product-desc {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .product-price {
            font-weight: 700;
            color: #dc3545;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .btn-book-now {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-book-now:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px);
        }
        
        .hero-section {
            background: linear-gradient(to right, rgba(26, 35, 126, 0.9), rgba(13, 22, 62, 0.9)), 
                        url('/assets/images/ac-unit-bg.jpg') no-repeat center center;
            background-size: cover;
            padding: 100px 0;
            color: white;
            margin-bottom: 3rem;
        }
        
        .featured-section {
            padding: 2rem 0 4rem;
            background-color: #f5f7fa;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            position: relative;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background-color: var(--secondary-color);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Filter Styles - Modified for horizontal layout */
        .filter-card {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
            background-color: white;
        }
        
        .filter-title {
            font-weight: 600;
            margin-bottom: 1.2rem;
            color: var(--primary-color);
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.8rem;
        }
        
        .filter-group {
            margin-bottom: 0;
        }
        
        .horizontal-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 1rem;
        }
        
        .horizontal-filters .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-buttons {
            display: flex;
            align-items: flex-end;
        }
        
        .btn-filter {
            background-color: var(--primary-color);
            border: none;
        }
        
        .btn-reset {
            background-color: transparent;
            border: 1px solid #dc3545;
            color: #dc3545;
            white-space: nowrap;
        }
        
        /* Search Box */
        .search-box {
            position: relative;
            margin-bottom: 2rem;
            flex-grow: 2;
            min-width: 300px;
        }
        
        .search-box input {
            border-radius: 50px;
            padding-left: 3rem;
            height: 48px;
            border: 1px solid #e5e5e5;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
        }
        
        .search-box .search-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .results-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 992px) {
            .horizontal-filters {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .horizontal-filters .filter-group {
                width: 100%;
            }
            
            .filter-buttons {
                width: 100%;
                margin-top: 1rem;
            }
            
            .btn-reset {
                width: 100%;
            }
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="/user/orders-services">My Orders & Service Requests</a></li>
                    <!-- User Profile -->
                    <li class="nav-item dropdown ms-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="position-relative">
                                <img src="<?=Session::get('profile_url')?>" alt="Profile Image" class="rounded-circle" width="30" height="30">
                                <span class="position-absolute bottom-0 end-0 translate-middle-y bg-success rounded-circle border border-white" style="width: 8px; height: 8px;"></span>
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

<!-- Optional CSS for hover effect -->
<style>
    #userDropdown:hover img {
        opacity: 0.8;
        transform: scale(1.1);
        transition: all 0.2s ease-in-out;
    }
</style>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Professional Air Conditioning Solutions</h1>
                    <p class="lead mb-4">Browse our high-quality products for all your AC needs</p>
                    <a href="#product-section" class="btn btn-danger btn-lg">View Products</a>
                </div>
                <div class="col-lg-6">
                    <!-- Hero image is in the background via CSS -->
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="product-section" class="featured-section">
        <div class="container">
            <h2 class="section-title">Our AC Products</h2>
            
            <!-- Filters at the top -->
            <div class="filter-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="filter-title mb-0">Filter Products</h3>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Sort By: Default
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item" href="#" data-sort="default">Default</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="price-low">Price: Low to High</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="price-high">Price: High to Low</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="name-asc">Name: A to Z</a></li>
                        </ul>
                    </div>
                </div>
                
                <form id="product-filters">
                    <div class="horizontal-filters">
                        <!-- Search box -->
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="product-search" class="form-control" placeholder="Search products...">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="filter-group">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">All Categories</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div class="filter-group">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min-price" class="form-control" placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max-price" class="form-control" placeholder="Max">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stock Status Filter -->
                        <div class="filter-group">
                            <label for="stock-status" class="form-label">Availability</label>
                            <select id="stock-status" name="stock-status" class="form-select">
                                <option value="">All</option>
                                <option value="in-stock">In Stock</option>
                                <option value="out-of-stock">Out of Stock</option>
                            </select>
                        </div>
                        
                        <!-- Filter Buttons -->
                        <div class="filter-buttons">
                            <button type="reset" class="btn btn-reset">Clear Filters</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Results Info -->
            <div class="results-info">
                <div id="results-count" class="text-muted">Showing all products</div>
            </div>
            
            <!-- Products Container - Full Width -->
            <div class="row g-4" id="products-container">
                <!-- Products will be dynamically inserted here by ProductManager.js -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading products...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Modal Template -->
    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="productDetailModalLabel">Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Product Image -->
                        <div class="col-md-5 mb-3">
                            <img id="modal-product-image" src="" alt="Product" class="img-fluid rounded">
                            
                            <div class="bg-light p-3 mt-3 rounded">
                                <p class="mb-1">Product Code</p>
                                <h5 id="modal-product-code" class="fw-bold"></h5>
                            </div>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="col-md-7">
                            <h2 id="modal-product-title" class="mb-2"></h2>
                            <h3 id="modal-product-price" class="text-danger mb-4"></h3>
                            
                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-success rounded-pill p-2 me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span id="modal-stock-status" class="text-success fw-bold"></span>
                                <span id="modal-stock-quantity" class="text-muted ms-2"></span>
                            </div>
                            
                            <div class="mb-4">
                                <label for="modal-quantity" class="form-label">Quantity</label>
                                <div class="input-group w-50">
                                    <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">−</button>
                                    <input type="text" class="form-control text-center" id="modal-quantity" value="1" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
                                </div>
                            </div>
                            
                            <div class="modal-section mb-4">
                                <h4>Order Details</h4>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Order ID</p>
                                        <p id="modal-order-id" class="fw-bold"></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Order Date</p>
                                        <p id="modal-order-date" class="fw-bold"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Status</p>
                                        <p id="modal-status" class="text-primary fw-bold"></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Total Amount</p>
                                        <p id="modal-total-amount" class="fw-bold"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-section">
                                <h4>Specifications</h4>
                                <ul id="modal-specifications" class="list-unstyled">
                                    <!-- Specifications will be added dynamically -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Confirm Order</button>
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- Smooth scrolling script -->
    <script src="/assets/js/home/home.js"></script>
    
    <!-- Product Manager Script -->
    <script src="/assets/js/utility/ProductManger.js"></script>
    
    <script>
        // Initialize AOS animation
        AOS.init({
            duration: 1000, 
            easing: 'ease-in-out', 
            once: true, 
        });
        
        // Initialize Product Manager when the document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Sample product data for testing without API
            const sampleProducts = [
                {
                    id: 4,
                    title: "Commercial HVAC System",
                    description: "Professional grade cooling system",
                    price: "2499",
                    image: "/assets/images/commercial-hvac.jpg",
                    inStock: false,
                    stock: 0,
                    category: "commercial",
                    code: "CHVAC-4004"
                },
                {
                    id: 5,
                    title: "LG Air Conditioning",
                    description: "Premium cooling with advanced features",
                    price: "1299",
                    image: "/assets/images/lg-air-conditioning.jpg",
                    inStock: true,
                    stock: 12,
                    category: "split-system",
                    code: "LG-5005"
                },
                {
                    id: 6,
                    title: "Vibration Damper",
                    description: "Reduce noise and vibration in AC units",
                    price: "129",
                    image: "/assets/images/vibration-damper.jpg",
                    inStock: true,
                    stock: 50,
                    category: "accessories",
                    code: "VD-6006"
                },
                {
                    id: 7,
                    title: "Smart Inverter AC Premium",
                    description: "Top-of-the-line energy efficient cooling",
                    price: "1499",
                    image: "/assets/images/smart-inverter-ac2.jpg",
                    inStock: true,
                    stock: 5,
                    category: "split-system",
                    code: "SIP-7007"
                },
                {
                    id: 8,
                    title: "Mitsubishi Electric WiFi",
                    description: "High-end cooling with smart controls",
                    price: "1599",
                    image: "/assets/images/mitsubishi-electric-wifi.jpg",
                    inStock: true,
                    stock: 7,
                    category: "split-system",
                    code: "MEW-8008"
                },
                {
                    id: 9,
                    title: "Assembly Kit AC 3/5 m",
                    description: "Complete installation kit for AC units",
                    price: "89",
                    image: "/assets/images/assembly-kit-ac.jpg",
                    inStock: true,
                    stock: 35,
                    category: "accessories",
                    code: "AK-9009"
                },
                {
                    id: 10,
                    title: "Eco Smart Inverter",
                    description: "Environmentally friendly cooling solution",
                    price: "1399",
                    image: "/assets/images/smart-inverter-ac3.jpg",
                    inStock: true,
                    stock: 9,
                    category: "split-system",
                    code: "ESI-1010"
                }
            ];
            
            // Create a ProductManager instance
            const productManager = new ProductManager({
                // Override the fetchAndRenderProducts method for demo purposes
                productsEndpoint: '/api/products'
            });
            
            // Mock the API call for demonstration
            productManager.fetchAndRenderProducts = function() {
                // Store all products for filtering
                this.allProducts = sampleProducts;
                
                // Populate category filter
                this.populateCategoryFilter(sampleProducts);
                
                // Render all products initially
                this.renderProducts(sampleProducts);
            };
            
            // Mock the openProductModal method for demonstration
            productManager.openProductModal = function(productId) {
                const product = this.allProducts.find(p => p.id == productId);
                
                if (product) {
                    this.currentProduct = product;
                    this.populateModal(product);
                    
                    // Show modal using Bootstrap's modal API
                    const modalElement = document.getElementById(this.config.modalId);
                    const bsModal = new bootstrap.Modal(modalElement);
                    bsModal.show();
                } else {
                    console.error('Product not found:', productId);
                    alert('Product details not available. Please try again.');
                }
            };
            
            // Initialize the product manager
            productManager.fetchAndRenderProducts();
            
            // Add event listener for sorting dropdown
            document.querySelectorAll('[data-sort]').forEach(element => {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sortType = this.getAttribute('data-sort');
                    const sortText = this.textContent;
                    
                    // Update dropdown button text
                    document.getElementById('sortDropdown').textContent = 'Sort By: ' + sortText;
                    
                    // Sort products
                    let sortedProducts = [...productManager.allProducts];
                    
                    switch (sortType) {
                        case 'price-low':
                            sortedProducts.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
                            break;
                        case 'price-high':
                            sortedProducts.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                            break;
                        case 'name-asc':
                            sortedProducts.sort((a, b) => a.title.localeCompare(b.title));
                            break;
                        default:
                            // Default sorting (by ID)
                            sortedProducts.sort((a, b) => a.id - b.id);
                    }
                    
                    // Render sorted products
                    productManager.renderProducts(sortedProducts);
                });
            });
        });
    </script>
    
</body>
</html>