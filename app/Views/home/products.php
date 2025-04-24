<?php

// Set page specific variables
$pageTitle = 'Our Products';
$pageDescription = 'Browse our complete range of air conditioning units, parts and accessories';
$pageHeader = 'Product Catalog';
$pageSubheader = 'Quality cooling products for all your needs';


// Additional page-specific styles
$pageStyles = '
    .product-card {
        transition: all 0.3s ease;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .product-img {
        height: 200px;
        object-fit: contain;
        padding: 1rem;
    }
    .category-btn.active {
        background-color: var(--primary);
        color: white;
    }
    .price {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary);
    }
    .badge-special {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
    .product-rating {
        color: #ffc107;
    }
    .stock-status {
        font-size: 0.85rem;
    }
    .filter-sidebar {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }
';

// Start output buffering to capture content
ob_start();
?>

<div class="row">
    <!-- Filter Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card filter-sidebar">
            <div class="card-header bg-white">
                <h5 class="mb-0">Filter Products</h5>
            </div>
            <div class="card-body">
                <!-- Categories Filter -->
                <h6 class="mb-3">Categories</h6>
                <div class="d-grid gap-2 mb-4">
                    <button class="btn btn-sm category-btn active" data-category="all">All Products</button>
                    <button class="btn btn-sm category-btn" data-category="ac-units">AC Units</button>
                    <button class="btn btn-sm category-btn" data-category="parts">Replacement Parts</button>
                    <button class="btn btn-sm category-btn" data-category="filters">Air Filters</button>
                    <button class="btn btn-sm category-btn" data-category="accessories">Accessories</button>
                    <button class="btn btn-sm category-btn" data-category="tools">Tools & Equipment</button>
                </div>
                
                <!-- Price Range Filter -->
                <h6 class="mb-3">Price Range</h6>
                <div class="mb-4">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" id="price-min" placeholder="Min">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" id="price-max" placeholder="Max">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-sm btn-primary" id="apply-price">Apply</button>
                    </div>
                </div>
                
                <!-- Brands Filter -->
                <h6 class="mb-3">Brands</h6>
                <div class="mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="carrier" id="brand-carrier">
                        <label class="form-check-label" for="brand-carrier">Carrier</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="daikin" id="brand-daikin">
                        <label class="form-check-label" for="brand-daikin">Daikin</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="trane" id="brand-trane">
                        <label class="form-check-label" for="brand-trane">Trane</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="lg" id="brand-lg">
                        <label class="form-check-label" for="brand-lg">LG</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="mitsubishi" id="brand-mitsubishi">
                        <label class="form-check-label" for="brand-mitsubishi">Mitsubishi</label>
                    </div>
                </div>
                
                <!-- Availability Filter -->
                <h6 class="mb-3">Availability</h6>
                <div class="mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="in-stock" id="in-stock" checked>
                        <label class="form-check-label" for="in-stock">In Stock</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="out-of-stock" id="out-of-stock">
                        <label class="form-check-label" for="out-of-stock">Out of Stock</label>
                    </div>
                </div>
                
                <!-- Energy Efficiency Filter -->
                <h6 class="mb-3">Energy Efficiency</h6>
                <div class="mb-4">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="energy-star" id="energy-star">
                        <label class="form-check-label" for="energy-star">Energy Star Certified</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="eco-friendly" id="eco-friendly">
                        <label class="form-check-label" for="eco-friendly">Eco-Friendly</label>
                    </div>
                </div>
                
                <!-- Reset Filters Button -->
                <div class="d-grid mt-4">
                    <button class="btn btn-outline-secondary" id="reset-filters">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Container -->
    <div class="col-lg-9">
        <!-- Search and Sort Controls -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search products..." id="product-search">
                            <button class="btn btn-primary" type="button" id="search-btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end">
                        <div class="d-flex align-items-center">
                            <label for="sort-by" class="me-2 form-label mb-0">Sort by:</label>
                            <select class="form-select" id="sort-by">
                                <option value="popular">Most Popular</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="name-asc">Name: A to Z</option>
                                <option value="name-desc">Name: Z to A</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Featured Products -->
        <div class="mb-4">
            <h4 class="mb-3">Featured Products</h4>
            <div class="row g-3">
                <!-- Featured Product 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <span class="badge bg-danger badge-special">Hot Deal</span>
                        <img src="assets/images/products/ac-unit-premium.jpg" class="card-img-top product-img" alt="Premium AC Unit" onerror="this.src='https://via.placeholder.com/300x200?text=Premium+AC'">
                        <div class="card-body">
                            <h5 class="card-title">Premium Inverter Split AC</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </span>
                                <small class="text-muted ms-1">(45 reviews)</small>
                            </div>
                            <p class="card-text">Energy-efficient split AC with smart controls and advanced air purification.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$1,199.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="1001">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=1001" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Featured Product 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <span class="badge bg-success badge-special">Energy Star</span>
                        <img src="assets/images/products/smart-thermostat.jpg" class="card-img-top product-img" alt="Smart Thermostat" onerror="this.src='https://via.placeholder.com/300x200?text=Smart+Thermostat'">
                        <div class="card-body">
                            <h5 class="card-title">Smart Wi-Fi Thermostat</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                                <small class="text-muted ms-1">(78 reviews)</small>
                            </div>
                            <p class="card-text">Control your home temperature from anywhere with this smart Wi-Fi enabled thermostat.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$249.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="1002">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=1002" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Featured Product 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <span class="badge bg-primary badge-special">Best Seller</span>
                        <img src="assets/images/products/air-purifier.jpg" class="card-img-top product-img" alt="Air Purifier" onerror="this.src='https://via.placeholder.com/300x200?text=Air+Purifier'">
                        <div class="card-body">
                            <h5 class="card-title">HEPA Air Purifier</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </span>
                                <small class="text-muted ms-1">(36 reviews)</small>
                            </div>
                            <p class="card-text">True HEPA filtration system removes 99.97% of particles as small as 0.3 microns.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$189.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="1003">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=1003" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- All Products -->
        <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">All Products</h4>
                <span class="text-muted" id="product-count">Showing 12 products</span>
            </div>
            
            <div class="row g-3" id="products-container">
                <!-- Product 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <img src="assets/images/products/window-ac.jpg" class="card-img-top product-img" alt="Window AC" onerror="this.src='https://via.placeholder.com/300x200?text=Window+AC'">
                        <div class="card-body">
                            <h5 class="card-title">Window Air Conditioner</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </span>
                                <small class="text-muted ms-1">(21 reviews)</small>
                            </div>
                            <p class="card-text">Compact window AC unit perfect for small rooms and apartments.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$349.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="2001">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=2001" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <img src="assets/images/products/air-filter-replacement.jpg" class="card-img-top product-img" alt="Air Filter" onerror="this.src='https://via.placeholder.com/300x200?text=Air+Filter'">
                        <div class="card-body">
                            <h5 class="card-title">HEPA Air Filter Replacement</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </span>
                                <small class="text-muted ms-1">(54 reviews)</small>
                            </div>
                            <p class="card-text">High-quality replacement HEPA filter compatible with most AC units.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$29.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="2002">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=2002" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <img src="assets/images/products/portable-ac.jpg" class="card-img-top product-img" alt="Portable AC" onerror="this.src='https://via.placeholder.com/300x200?text=Portable+AC'">
                        <div class="card-body">
                            <h5 class="card-title">Portable Air Conditioner</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </span>
                                <small class="text-muted ms-1">(32 reviews)</small>
                            </div>
                            <p class="card-text">Move cooling where you need it with this efficient portable AC unit.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$499.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="2003">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=2003" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <img src="assets/images/products/refrigerant.jpg" class="card-img-top product-img" alt="Refrigerant" onerror="this.src='https://via.placeholder.com/300x200?text=Refrigerant'">
                        <div class="card-body">
                            <h5 class="card-title">R-410A Refrigerant</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </span>
                                <small class="text-muted ms-1">(17 reviews)</small>
                            </div>
                            <p class="card-text">Professional-grade refrigerant for modern AC systems.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$89.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="2004">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=2004" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product 5 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <img src="assets/images/products/condenser-fan.jpg" class="card-img-top product-img" alt="Condenser Fan" onerror="this.src='https://via.placeholder.com/300x200?text=Condenser+Fan'">
                        <div class="card-body">
                            <h5 class="card-title">Condenser Fan Motor</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </span>
                                <small class="text-muted ms-1">(28 reviews)</small>
                            </div>
                            <p class="card-text">Replacement condenser fan motor compatible with most major brands.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$119.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="2005">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=2005" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product 6 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card">
                        <img src="assets/images/products/coil-cleaner.jpg" class="card-img-top product-img" alt="Coil Cleaner" onerror="this.src='https://via.placeholder.com/300x200?text=Coil+Cleaner'">
                        <div class="card-body">
                            <h5 class="card-title">Professional Coil Cleaner</h5>
                            <div class="mb-2">
                                <span class="product-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </span>
                                <small class="text-muted ms-1">(41 reviews)</small>
                            </div>
                            <p class="card-text">Industrial strength cleaner for evaporator and condenser coils.</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price">$24.99</span>
                                <span class="stock-status text-success"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary add-to-cart" data-id="2006">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="product-details.php?id=2006" class="btn btn-outline-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- More products can be added here -->
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Product pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Shopping Cart Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="cartOffcanvasLabel">Your Cart <span id="cart-count" class="badge bg-primary">0</span></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="cart-empty" class="text-center py-5">
            <i class="bi bi-cart text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 mb-4">Your cart is empty</p>
            <button class="btn btn-primary" data-bs-dismiss="offcanvas">Continue Shopping</button>
        </div>
        <div id="cart-items" class="d-none">
            <!-- Cart items will be added here dynamically -->
            <div class="list-group mb-3" id="cart-items-container">
                <!-- Example cart item -->
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="Product" class="me-2" width="50">
                            <div>
                                <h6 class="mb-0">Product Name</h6>
                                <small class="text-muted">$99.99 x 1</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="input-group input-group-sm" style="width: 100px;">
                                <button class="btn btn-outline-secondary qty-btn" type="button">-</button>
                                <input type="text" class="form-control text-center" value="1">
                                <button class="btn btn-outline-secondary qty-btn" type="button">+</button>
                            </div>
                            <button class="btn btn-sm ms-2 btn-outline-danger remove-item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">$99.99</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span id="cart-shipping">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span id="cart-total">$99.99</span>
                    </div

<?php include $basePath ?>