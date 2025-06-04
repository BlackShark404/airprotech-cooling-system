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
                    <i class="fas fa-phone me-2"></i>09338525313
                </a>
                <a href="mailto:airprotechaircon123@gmail.com" class="text-white text-decoration-none">
                    <i class="fas fa-envelope me-2"></i>airprotechaircon123@gmail.com
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
                    <li class="nav-item"><a class="nav-link" href="/user/my-bookings">My Bookings & Service Requests</a></li>
                    <!-- User Profile -->
                    <li class="nav-item dropdown ms-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src=<?=Session::get('profile_url') ? Session::get('profile_url') : '/assets/images/default-profile.jpg'?> alt="Profile" class="rounded-circle me-2" width="36" height="36">
                            <div class="d-flex flex-column lh-sm">
                                <span class="fw-semibold small text-dark"><?=$_SESSION['full_name'] ?? 'User'?></span>
                                <small class="text-success">● Online</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/user/profile">Profile</a></li>
                            
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
                            <label for="availability-status" class="form-label">Availability</label>
                            <select id="availability-status" name="availability-status" class="form-select">
                                <option value="">All</option>
                                <option value="Available">Available</option>
                                <option value="Out of Stock">Out of Stock</option>
                                <option value="Discontinued">Discontinued</option>
                            </select>
                        </div>
                        
                        <!-- Filter Buttons -->
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-filter btn-primary me-2">Apply Filters</button>
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
            
            <!-- Pagination -->
            <div id="pagination-container" class="mt-4"></div>
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
                            <h2 id="modal-product-name" class="mb-2"></h2>
                            <h3 id="modal-product-price" class="text-danger mb-4"></h3>
                            
                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-success rounded-pill p-2 me-2">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span id="modal-availability-status" class="text-success fw-bold"></span>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modal-variant-select" class="form-label">Select Variant</label>
                                <select id="modal-variant-select" class="form-select">
                                    <!-- Variants will be added dynamically -->
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modal-quantity" class="form-label">Quantity</label>
                                <div class="input-group w-50">
                                    <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">−</button>
                                    <input type="text" class="form-control text-center" id="modal-quantity" value="1" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
                                </div>
                            </div>
                            
                            <div class="modal-section mb-4">
                                <h4>Booking Information</h4>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Booking ID</p>
                                        <p id="modal-order-id" class="fw-bold"></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Booking Date</p>
                                        <p id="modal-order-date" class="fw-bold"></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Status</p>
                                        <p id="modal-status" class="text-primary fw-bold"></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Total Amount</p>
                                        <p id="modal-total-amount" class="fw-bold"></p>
                                    </div>
                                </div>
                                
                                <!-- New fields for preferred date, time, and address -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="modal-preferred-date" class="form-label">Preferred Date*</label>
                                        <input type="date" id="modal-preferred-date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-preferred-time" class="form-label">Preferred Time*</label>
                                        <input type="time" id="modal-preferred-time" class="form-control" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="modal-address" class="form-label">Delivery/Installation Address*</label>
                                    <textarea id="modal-address" class="form-control" rows="2" required></textarea>
                                </div>
                            </div>
                            
                            <div class="modal-section">
                                <h4>Features</h4>
                                <ul id="modal-features" class="list-unstyled">
                                    <!-- Features will be added dynamically -->
                                </ul>
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
                    <button type="button" id="confirm-order" class="btn btn-primary">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require __DIR__. '/../includes/shared/footer.php' ?>
        
    <!-- JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- Smooth scrolling script -->
    <script src="/assets/js/home/home.js"></script>
    
    <!-- Product Manager Script -->
    <script src="/assets/js/utility/ProductManager.js"></script>
    
    <script>
        // Initialize AOS animation
        AOS.init({
            duration: 1000, 
            easing: 'ease-in-out', 
            once: true, 
        });
    </script>

    <script>
        // Initialize Product Manager when the document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Create a ProductManager instance with our API endpoints
            const productManager = new ProductManager({
                productsEndpoint: '/api/products',
                orderEndpoint: '/api/product-bookings'
            });
            
            // Initialize the product manager to fetch and display products
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
                            sortedProducts.sort((a, b) => {
                                const aPrice = a.variants && a.variants.length > 0 ? parseFloat(a.variants[0].VAR_SRP_PRICE) : 0;
                                const bPrice = b.variants && b.variants.length > 0 ? parseFloat(b.variants[0].VAR_SRP_PRICE) : 0;
                                return aPrice - bPrice;
                            });
                            break;
                        case 'price-high':
                            sortedProducts.sort((a, b) => {
                                const aPrice = a.variants && a.variants.length > 0 ? parseFloat(a.variants[0].VAR_SRP_PRICE) : 0;
                                const bPrice = b.variants && b.variants.length > 0 ? parseFloat(b.variants[0].VAR_SRP_PRICE) : 0;
                                return bPrice - aPrice;
                            });
                            break;
                        case 'name-asc':
                            sortedProducts.sort((a, b) => a.PROD_NAME.localeCompare(b.PROD_NAME));
                            break;
                        default:
                            // Default sorting (by ID)
                            sortedProducts.sort((a, b) => a.PROD_ID - b.PROD_ID);
                    }
                    
                    // Render sorted products
                    productManager.renderProducts(sortedProducts);
                });
            });
        });
    </script>
    
</body>
</html>