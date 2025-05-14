<?php
$title = 'Inventory Management - AirProtect';
$activeTab = 'inventory';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<style>
   /* Inventory Management Styles */

    /* Stats Cards */
    .stats-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 24px;
    }

    .stats-icon.products {
        background-color: rgba(92, 182, 242, 0.1);
        color: #5cb6f2;
    }

    .stats-icon.variants {
        background-color: rgba(75, 192, 192, 0.1);
        color: #4bc0c0;
    }

    .stats-icon.warehouses {
        background-color: rgba(153, 102, 255, 0.1);
        color: #9966ff;
    }

    .stats-icon.low-stock {
        background-color: rgba(255, 99, 132, 0.1);
        color: #ff6384;
    }

    .stats-info h3 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .stats-info p {
        margin: 0;
        color: #6c757d;
        font-size: 14px;
    }

    /* View Selector */
    .view-selector .btn-group .btn {
        padding: 8px 16px;
    }

    /* Main Content Card */
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 16px 20px;
    }

    /* Tables */
    .table th {
        font-weight: 500;
        color: #495057;
        border-top: none;
        background-color: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
    }

    /* Action Buttons */
    .btn-red {
        background-color: #ff6b6b;
        border-color: #ff6b6b;
        color: white;
    }

    .btn-red:hover {
        background-color: #ff5252;
        border-color: #ff5252;
        color: white;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .action-buttons .btn i {
        font-size: 14px;
    }

    /* Product Detail Modal */
    .product-detail-image {
        width: 150px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .product-detail-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .feature-list {
        padding-left: 20px;
        margin-bottom: 0;
    }

    .spec-table {
        margin-bottom: 0;
    }

    .spec-table td:first-child {
        font-weight: 500;
        width: 40%;
    }

    /* Inventory Detail Cards */
    .inventory-detail-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .inventory-detail-card h6 {
        margin-bottom: 10px;
        color: #495057;
    }

    .inventory-detail-card .badge {
        font-size: 11px;
        padding: 5px 8px;
    }

    /* Forms in Modals */
    .modal-body .form-label {
        font-weight: 500;
        color: #495057;
    }

    .tab-content {
        background: #fff;
    }

    /* Variant Form */
    .variant-form {
        background-color: rgba(92, 182, 242, 0.05);
    }

    /* Warehouse List */
    #warehouseListTable th {
        font-size: 14px;
    }

    #warehouseListTable td {
        font-size: 14px;
    }

    /* Stock History Table */
    #stock-history .table th {
        font-size: 14px;
    }

    #stock-history .table td {
        font-size: 14px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stats-card {
            padding: 15px;
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .stats-info h3 {
            font-size: 20px;
        }
        
        .product-detail-image {
            width: 100px;
            height: 100px;
        }
    }

    /* Mobile optimizations */
    @media (max-width: 576px) {
        .view-selector, .filter-selector {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .col-md-4.d-flex.justify-content-end {
            justify-content: flex-start !important;
            margin-top: 10px;
        }
    }

    /* Table Action Buttons */
    .table-action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        margin-right: 5px;
    }

    .table-action-btn i {
        font-size: 14px;
    }

    .view-btn {
        background-color: #5cb6f2;
        border-color: #5cb6f2;
        color: white;
    }

    .view-btn:hover {
        background-color: #4aa0db;
        border-color: #4aa0db;
        color: white;
    }

    .edit-btn {
        background-color: #ffc107;
        border-color: #ffc107;
        color: white;
    }

    .edit-btn:hover {
        background-color: #e0a800;
        border-color: #e0a800;
        color: white;
    }

    .delete-btn {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .delete-btn:hover {
        background-color: #c82333;
        border-color: #c82333;
        color: white;
    }

    /* Stock Status Badges */
    .badge.bg-success {
        background-color: #28a745 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    /* Inventory Type Badges */
    .badge.inventory-regular {
        background-color: #5cb6f2;
    }

    .badge.inventory-display {
        background-color: #6f42c1;
    }

    .badge.inventory-reserve {
        background-color: #20c997;
    }

    .badge.inventory-damaged {
        background-color: #dc3545;
    }

    .badge.inventory-returned {
        background-color: #fd7e14;
    }

    .badge.inventory-quarantine {
        background-color: #6c757d;
    }

    /* Custom Scrollbar for Tables */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
HTML;

ob_start();
?>
<div class="container-fluid py-4">
    <!-- Page Heading and Add Product Button -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Inventory Management</h1>
            <p class="text-muted">Manage your product inventory and product information</p>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon products">
                    <i class="bi bi-box"></i>
                </div>
                <div class="stats-info">
                    <h3 id="totalProducts">0</h3>
                    <p>Total Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon variants">
                    <i class="bi bi-layers"></i>
                </div>
                <div class="stats-info">
                    <h3 id="totalVariants">0</h3>
                    <p>Product Variants</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon warehouses">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stats-info">
                    <h3 id="totalWarehouses">0</h3>
                    <p>Warehouses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon low-stock">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stats-info">
                    <h3 id="lowStockItems">0</h3>
                    <p>Low Stock Items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- View Selector and Add Product Button -->
    <div class="row mb-4">
        <div class="col-md-8 d-flex align-items-center">
            <div class="view-selector">
                <div class="btn-group">
                    <button class="btn btn-outline-primary active" data-view="inventory">Inventory View</button>
                    <button class="btn btn-outline-primary" data-view="products">Products View</button>
                    <button class="btn btn-outline-primary" data-view="warehouses">Warehouses View</button>
                </div>
            </div>
            <div class="filter-selector ms-3">
                <select class="form-select" id="stockTypeFilter">
                    <option value="all">All Stock Types</option>
                    <option value="Regular">Regular</option>
                    <option value="Display">Display</option>
                    <option value="Reserve">Reserve</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Returned">Returned</option>
                    <option value="Quarantine">Quarantine</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <div class="btn-group">
                <button class="btn btn-outline-primary d-flex align-items-center" id="warehouseBtn" data-bs-toggle="modal" data-bs-target="#warehouseModal">
                    <i class="bi bi-building me-2"></i>
                    Warehouses
                </button>
            </div>
            <button class="btn btn-red d-flex align-items-center ms-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus me-2"></i>
                Add Product
            </button>
        </div>
    </div>

    <!-- Main Content Cards -->
    <div class="card view-card" id="inventoryView">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventory Dashboard</h5>
            <div class="input-group w-25">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="inventorySearch" placeholder="Search inventory">
            </div>
        </div>
        <div class="card-body">
            <table id="inventoryTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Warehouse</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="card view-card d-none" id="productsView">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products Dashboard</h5>
            <div class="input-group w-25">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="productsSearch" placeholder="Search products">
            </div>
        </div>
        <div class="card-body">
            <table id="productsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Variants</th>
                        <th>Total Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="card view-card d-none" id="warehousesView">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Warehouses Dashboard</h5>
            <div class="input-group w-25">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="warehousesSearch" placeholder="Search warehouses">
            </div>
        </div>
        <div class="card-body">
            <table id="warehousesTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Storage Capacity</th>
                        <th>Current Usage</th>
                        <th>Items Below Threshold</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="product-info-tab" data-bs-toggle="tab" data-bs-target="#product-info" type="button" role="tab">Product Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="product-variants-tab" data-bs-toggle="tab" data-bs-target="#product-variants" type="button" role="tab">Variants</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="product-features-tab" data-bs-toggle="tab" data-bs-target="#product-features" type="button" role="tab">Features & Specs</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="product-inventory-tab" data-bs-toggle="tab" data-bs-target="#product-inventory" type="button" role="tab">Inventory</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productTabsContent">
                        <!-- Product Information Tab -->
                        <div class="tab-pane fade show active" id="product-info" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" name="prod_name" placeholder="Enter product name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="productAvailability" class="form-label">Availability Status</label>
                                    <select class="form-select" id="productAvailability" name="prod_availability_status">
                                        <option value="Available">Available</option>
                                        <option value="Out of Stock">Out of Stock</option>
                                        <option value="Discontinued">Discontinued</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="productDescription" name="prod_description" rows="3" placeholder="Enter product description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Product Image</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="productImage" name="prod_image">
                                    <label class="input-group-text" for="productImage">Upload</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Variants Tab -->
                        <div class="tab-pane fade" id="product-variants" role="tabpanel">
                            <div class="variants-container">
                                <div class="variant-form mb-3 p-3 border rounded">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="varCapacity" class="form-label">Capacity</label>
                                            <input type="text" class="form-control" id="varCapacity" name="variants[0][var_capacity]" placeholder="e.g., 0.8HP (20)">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="varPowerConsumption" class="form-label">Power Consumption</label>
                                            <input type="text" class="form-control" id="varPowerConsumption" name="variants[0][var_power_consumption]" placeholder="e.g., CSPF (4.60)">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="varSrpPrice" class="form-label">SRP Price</label>
                                            <input type="number" class="form-control" id="varSrpPrice" name="variants[0][var_srp_price]" placeholder="Standard price" step="0.01" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="varFreeInstall" class="form-label">Price (Free Install)</label>
                                            <input type="number" class="form-control" id="varFreeInstall" name="variants[0][var_price_free_install]" placeholder="Optional" step="0.01">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="varWithInstall" class="form-label">Price (With Install)</label>
                                            <input type="number" class="form-control" id="varWithInstall" name="variants[0][var_price_with_install]" placeholder="Optional" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary add-variant-btn">
                                    <i class="bi bi-plus"></i> Add Another Variant
                                </button>
                            </div>
                        </div>
                        
                        <!-- Features & Specs Tab -->
                        <div class="tab-pane fade" id="product-features" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Product Features</h6>
                                    <div class="features-container mb-3">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="features[0]" placeholder="Enter feature">
                                            <button class="btn btn-outline-danger remove-feature" type="button">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary add-feature-btn">
                                        <i class="bi bi-plus"></i> Add Feature
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Product Specifications</h6>
                                    <div class="specs-container mb-3">
                                        <div class="row mb-2">
                                            <div class="col-5">
                                                <input type="text" class="form-control" name="specs[0][spec_name]" placeholder="Spec name">
                                            </div>
                                            <div class="col-5">
                                                <input type="text" class="form-control" name="specs[0][spec_value]" placeholder="Spec value">
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-outline-danger w-100 remove-spec" type="button">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary add-spec-btn">
                                        <i class="bi bi-plus"></i> Add Specification
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Inventory Tab -->
                        <div class="tab-pane fade" id="product-inventory" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                You can set initial inventory levels here, or add them later after creating the product.
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="warehouseSelect" class="form-label">Warehouse Location</label>
                                    <select class="form-select" id="warehouseSelect" name="warehouse_id">
                                        <option value="">Select warehouse</option>
                                        <!-- Will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inventoryType" class="form-label">Inventory Type</label>
                                    <select class="form-select" id="inventoryType" name="inventory_type">
                                        <option value="Regular">Regular</option>
                                        <option value="Display">Display</option>
                                        <option value="Reserve">Reserve</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Returned">Returned</option>
                                        <option value="Quarantine">Quarantine</option>
                                    </select>
                                </div>
                            </div>
                            <div class="variants-inventory-container">
                                <!-- Will be populated dynamically based on variants -->
                                <div class="alert alert-secondary">
                                    Please add variants in the Variants tab first.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveProductBtn">Save Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="product-detail-header d-flex mb-4">
                    <div class="product-detail-image me-3">
                        <img src="/api/placeholder/150/150" alt="Product Image" id="productDetailImage" class="img-fluid rounded">
                    </div>
                    <div class="product-detail-info">
                        <h4 class="product-detail-name" id="productDetailName">Product Name</h4>
                        <p class="product-detail-description" id="productDetailDescription">Description will be loaded here.</p>
                        <div class="product-detail-id text-muted" id="productDetailId">Product ID: --</div>
                        <div class="product-detail-status" id="productDetailStatus"><span class="badge bg-success">Available</span></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Features</h6>
                            </div>
                            <div class="card-body">
                                <ul class="feature-list" id="productDetailFeatures">
                                    <!-- Features will be loaded dynamically -->
                                    <li class="text-muted">No features specified</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Specifications</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm spec-table">
                                    <tbody id="productDetailSpecs">
                                        <!-- Specs will be loaded dynamically -->
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">No specifications available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h5 class="mb-3 mt-2">Product Variants</h5>
                <div class="table-responsive">
                    <table class="table table-bordered variant-table">
                        <thead class="table-light">
                            <tr>
                                <th>Capacity</th>
                                <th>CSPF</th>
                                <th>SRP Price</th>
                                <th>Free Install Price</th>
                                <th>With Install Price</th>
                            </tr>
                        </thead>
                        <tbody id="productDetailVariants">
                            <!-- Variants will be loaded dynamically -->
                            <tr>
                                <td colspan="5" class="text-center text-muted">No variants available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <h5 class="mb-3 mt-4">Inventory Summary</h5>
                <div class="inventory-detail-cards">
                    <div class="row" id="productDetailInventory">
                        <!-- Inventory cards will be loaded dynamically -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editProductBtn">Edit Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Inventory Modal -->
<div class="modal fade" id="manageInventoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="current-stock-tab" data-bs-toggle="tab" data-bs-target="#current-stock" type="button" role="tab">Current Stock</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-stock-tab" data-bs-toggle="tab" data-bs-target="#add-stock" type="button" role="tab">Add Stock</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="move-stock-tab" data-bs-toggle="tab" data-bs-target="#move-stock" type="button" role="tab">Move Stock</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stock-history-tab" data-bs-toggle="tab" data-bs-target="#stock-history" type="button" role="tab">Stock History</button>
                    </li>
                </ul>
                <div class="tab-content" id="inventoryTabsContent">
                    <!-- Current Stock Tab -->
                    <div class="tab-pane fade show active" id="current-stock" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Variant</th>
                                        <th>Warehouse</th>
                                        <th>Inventory Type</th>
                                        <th>Quantity</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody id="currentStockTable">
                                    <!-- Current stock will be loaded dynamically -->
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Add Stock Tab -->
                    <div class="tab-pane fade" id="add-stock" role="tabpanel">
                        <form id="addStockForm">
                            <input type="hidden" id="addStockProductId" name="prod_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addVariantSelect" class="form-label">Select Variant</label>
                                    <select class="form-select" id="addVariantSelect" name="var_id" required>
                                        <!-- Variants will be loaded dynamically -->
                                        <option value="">Loading variants...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="addWarehouseSelect" class="form-label">Select Warehouse</label>
                                    <select class="form-select" id="addWarehouseSelect" name="whouse_id" required>
                                        <!-- Warehouses will be loaded dynamically -->
                                        <option value="">Loading warehouses...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="addInventoryType" class="form-label">Inventory Type</label>
                                    <select class="form-select" id="addInventoryType" name="inve_type">
                                        <option value="Regular">Regular</option>
                                        <option value="Display">Display</option>
                                        <option value="Reserve">Reserve</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Returned">Returned</option>
                                        <option value="Quarantine">Quarantine</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="addQuantity" class="form-label">Quantity to Add</label>
                                    <input type="number" class="form-control" id="addQuantity" name="quantity" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="addReason" class="form-label">Reason</label>
                                <select class="form-select" id="addReason" name="reason">
                                    <option value="Initial">Initial Stock</option>
                                    <option value="Restock">Restock</option>
                                    <option value="Return">Customer Return</option>
                                    <option value="Adjustment">Inventory Adjustment</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="addNotes" class="form-label">Notes</label>
                                <textarea class="form-control" id="addNotes" name="notes" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Stock</button>
                        </form>
                    </div>
                    
                    <!-- Move Stock Tab -->
                    <div class="tab-pane fade" id="move-stock" role="tabpanel">
                        <form id="moveStockForm">
                            <input type="hidden" id="moveStockProductId" name="prod_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="moveVariantSelect" class="form-label">Select Variant</label>
                                    <select class="form-select" id="moveVariantSelect" name="var_id" required>
                                        <!-- Variants will be loaded dynamically -->
                                        <option value="">Loading variants...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="moveInventoryType" class="form-label">Inventory Type</label>
                                    <select class="form-select" id="moveInventoryType" name="inve_type">
                                        <option value="Regular">Regular</option>
                                        <option value="Display">Display</option>
                                        <option value="Reserve">Reserve</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Returned">Returned</option>
                                        <option value="Quarantine">Quarantine</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sourceWarehouse" class="form-label">Source Warehouse</label>
                                    <select class="form-select" id="sourceWarehouse" name="source_whouse_id" required>
                                        <!-- Warehouses will be loaded dynamically -->
                                        <option value="">Loading warehouses...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="destinationWarehouse" class="form-label">Destination Warehouse</label>
                                    <select class="form-select" id="destinationWarehouse" name="dest_whouse_id" required>
                                        <!-- Warehouses will be loaded dynamically -->
                                        <option value="">Loading warehouses...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="moveQuantity" class="form-label">Quantity to Move</label>
                                <input type="number" class="form-control" id="moveQuantity" name="quantity" min="1" value="1" required>
                                <small class="form-text text-muted available-quantity">Available in source: -- units</small>
                            </div>
                            <div class="mb-3">
                                <label for="moveNotes" class="form-label">Notes</label>
                                <textarea class="form-control" id="moveNotes" name="notes" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Move Stock</button>
                        </form>
                    </div>
                    
                    <!-- Stock History Tab -->
                    <div class="tab-pane fade" id="stock-history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Variant</th>
                                        <th>Warehouse</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                        <th>Quantity</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody id="stockHistoryTable">
                                    <!-- Stock history will be loaded dynamically -->
                                    <tr>
                                        <td colspan="7" class="text-center py-3">
                                            <div class="alert alert-info mb-0">
                                                <i class="bi bi-info-circle me-2"></i>
                                                Stock history feature coming soon.
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Warehouse Modal -->
<div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Warehouses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="warehouseForm" class="mb-4">
                    <input type="hidden" id="warehouseId" name="whouse_id" value="">
                    <div class="mb-3">
                        <label for="warehouseName" class="form-label">Warehouse Name</label>
                        <input type="text" class="form-control" id="warehouseName" name="whouse_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="warehouseLocation" name="whouse_location" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="warehouseCapacity" class="form-label">Storage Capacity</label>
                            <input type="number" class="form-control" id="warehouseCapacity" name="whouse_storage_capacity" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="warehouseThreshold" class="form-label">Restock Threshold</label>
                            <input type="number" class="form-control" id="warehouseThreshold" name="whouse_restock_threshold" min="1">
                        </div>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary" id="saveWarehouseBtn">Save Warehouse</button>
                        <button type="button" class="btn btn-outline-secondary ms-2" id="resetWarehouseBtn">Reset</button>
                    </div>
                </form>
                
                <h6 class="mb-3">Existing Warehouses</h6>
                <div class="table-responsive">
                    <table class="table table-sm" id="warehouseListTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Warehouse list will be loaded dynamically -->
                            <tr>
                                <td colspan="3" class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/inventory.js"></script>
<script src="/assets/js/utility/InventoryViewDataTables.js"></script>
<script src="/assets/js/utility/ProductViewDataTables.js"></script>
<script src="/assets/js/utility/WarehouseViewDataTables.js"></script>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>