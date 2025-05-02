<?php
$title = 'Inventory Management - AirProtect';
$activeTab = 'inventory';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .filter-card {
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .filter-dropdown {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 1rem;
        width: 100%;
    }
    .action-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        background-color: #f8f9fa;
        margin-right: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-icon:hover {
        background-color: #e9ecef;
    }
    .pagination-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 1rem;
    }
    .pagination-button {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        cursor: pointer;
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    .pagination-button.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .pagination-button:hover:not(.active) {
        background-color: #f8f9fa;
    }
    .inventory-tab {
        padding: 10px 20px;
        border-radius: 8px;
        margin-right: 10px;
        cursor: pointer;
        font-weight: 500;
    }
    .inventory-tab.active {
        background-color: #007bff;
        color: white;
    }
    .inventory-tab:not(.active) {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Page Heading and Add Product Button -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Inventory Management</h1>
            <p class="text-muted">Manage your product inventory</p>
        </div>
        
    </div>

    <!-- Inventory Tabs -->
    <div class="row mb-4">
        <div class="col d-flex">
            <div class="inventory-tab active">Inventory 1</div>
            <div class="inventory-tab">Inventory 2</div>
        </div>

        <div class="col d-flex justify-content-end">
            <button class="btn btn-red d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus me-2"></i>
                Add Product
            </button>
        </div>
    </div>

    <!-- Inventory Table Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Product List</h5>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <select class="form-select form-select-sm">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Search products...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Inventory ID</th>
                            <th>Product ID</th>
                            <th>Product name</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>IVN1001</td>
                            <td>PRD001</td>
                            <td>Smart Inventer Ac</td>
                            <td>150</td>
                            <td>$299.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>IVN1002</td>
                            <td>PRD002</td>
                            <td>Split System Classic</td>
                            <td>75</td>
                            <td>$599.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>IVN1003</td>
                            <td>PRD003</td>
                            <td>Portable Ac Unit</td>
                            <td>200</td>
                            <td>$49.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    Showing 1 to 8 of 24 entries
                </div>
                <div class="pagination-container">
                    <div class="pagination-button"><i class="bi bi-chevron-left"></i></div>
                    <div class="pagination-button active">1</div>
                    <div class="pagination-button">2</div>
                    <div class="pagination-button">3</div>
                    <div class="pagination-button"><i class="bi bi-chevron-right"></i></div>
                </div>
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
            <form id="addProductForm">
            <!-- Basic Information Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Basic Information</h6>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="productName" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="productName" name="productName" required>
                </div>
                <div class="col-md-6">
                    <label for="skuCode" class="form-label">SKU/Product Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="skuCode" name="skuCode" required>
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                    <option value="" selected disabled>Select category</option>
                    <option value="air-conditioners">Air Conditioners</option>
                    <option value="parts">Parts & Accessories</option>
                    <option value="tools">Tools & Equipment</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
                    <select class="form-select" id="brand" name="brand" required>
                    <option value="" selected disabled>Select brand</option>
                    <option value="daikin">Daikin</option>
                    <option value="carrier">Carrier</option>
                    <option value="trane">Trane</option>
                    <option value="lg">LG</option>
                    </select>
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
                </div>
            </div>

            <!-- Pricing & Inventory Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Pricing & Inventory</h6>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="purchasePrice" class="form-label">Purchase Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="purchasePrice" name="purchasePrice" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="sellingPrice" class="form-label">Selling Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="sellingPrice" name="sellingPrice" step="0.01" min="0" required>
                    </div>
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="currentStock" class="form-label">Current Stock <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="currentStock" name="currentStock" min="0" required>
                </div>
                <div class="col-md-6">
                    <label for="lowStockThreshold" class="form-label">Low Stock Alert Threshold</label>
                    <input type="number" class="form-control" id="lowStockThreshold" name="lowStockThreshold" min="0">
                </div>
                </div>
            </div>

            <!-- Technical Specifications Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Technical Specifications</h6>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="coolingCapacity" class="form-label">Cooling Capacity (BTU)</label>
                    <input type="number" class="form-control" id="coolingCapacity" name="coolingCapacity" min="0">
                </div>
                <div class="col-md-6">
                    <label for="energyRating" class="form-label">Energy Efficiency Rating</label>
                    <input type="text" class="form-control" id="energyRating" name="energyRating">
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="inventoryType" class="form-label">Inventory Type</label>
                    <select class="form-select" id="inventoryType" name="inventoryType">
                    <option value="" selected disabled>Select type</option>
                    <option value="finished-good">Finished Good</option>
                    <option value="raw-material">Raw Material</option>
                    <option value="component">Component/Part</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="warrantyPeriod" class="form-label">Warranty Period</label>
                    <input type="text" class="form-control" id="warrantyPeriod" name="warrantyPeriod" placeholder="e.g., 1 year">
                </div>
                </div>
            </div>

            <!-- Product Images Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Product Images</h6>
                <div class="row">
                <div class="col-12">
                    <div class="border rounded p-3 text-center position-relative" style="min-height: 180px;">
                    <div class="image-upload-area d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cloud-arrow-up fs-2 mb-2"></i>
                        <p class="mb-1">Drag and drop images here or click to upload</p>
                        <p class="text-muted small">Supported formats: JPG, PNG. Max file size: 5MB</p>
                        <input type="file" id="productImages" name="productImages[]" class="position-absolute inset-0 opacity-0 w-100 h-100 cursor-pointer" multiple accept=".jpg,.jpeg,.png">
                    </div>
                    <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mt-3" style="display: none !important;"></div>
                    </div>
                </div>
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Save Product</button>
        </div>
        </div>
    </div>
    </div>

    <!-- View Product Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="viewProductModalLabel">Product Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
            <!-- Product Image Column -->
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="border rounded p-2 mb-3">
                <img src="/api/placeholder/400/400" alt="Product image" class="img-fluid">
                </div>
                <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-primary">In Stock: 150</span>
                <span class="badge bg-success">Active</span>
                </div>
            </div>
            
            <!-- Product Details Column -->
            <div class="col-md-8">
                <h4 class="mb-1" id="viewProductName">Smart Inverter AC</h4>
                <p class="text-muted mb-3" id="viewProductSKU">SKU: PRD001</p>
                
                <div class="mb-4">
                <h6 class="fw-bold mb-2">Product Information</h6>
                <table class="table table-sm">
                    <tbody>
                    <tr>
                        <td width="30%" class="fw-medium">Category:</td>
                        <td id="viewProductCategory">Air Conditioners</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Brand:</td>
                        <td id="viewProductBrand">Carrier</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Price:</td>
                        <td id="viewProductPrice">$299.99</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Inventory Type:</td>
                        <td id="viewProductInventoryType">Finished Good</td>
                    </tr>
                    </tbody>
                </table>
                </div>
                
                <div class="mb-4">
                <h6 class="fw-bold mb-2">Technical Specifications</h6>
                <table class="table table-sm">
                    <tbody>
                    <tr>
                        <td width="30%" class="fw-medium">Cooling Capacity:</td>
                        <td id="viewProductCoolingCapacity">12,000 BTU</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Energy Rating:</td>
                        <td id="viewProductEnergyRating">4.5 Star</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Warranty:</td>
                        <td id="viewProductWarranty">2 Years</td>
                    </tr>
                    </tbody>
                </table>
                </div>
                
                <div>
                <h6 class="fw-bold mb-2">Description</h6>
                <p id="viewProductDescription">This energy-efficient Smart Inverter AC provides exceptional cooling performance with low noise operation. Features include a sleep mode, timer, and smartphone control capability.</p>
                </div>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateProductModal">
            <i class="bi bi-pencil me-1"></i> Edit
            </button>
        </div>
        </div>
    </div>
    </div>

    <!-- Update Product Modal -->
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="updateProductModalLabel">Update Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="updateProductForm">
            <input type="hidden" id="updateProductId" name="productId" value="">
            
            <!-- Basic Information Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Basic Information</h6>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="updateProductName" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="updateProductName" name="productName" value="Smart Inverter AC" required>
                </div>
                <div class="col-md-6">
                    <label for="updateSkuCode" class="form-label">SKU/Product Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="updateSkuCode" name="skuCode" value="PRD001" required>
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="updateCategory" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="updateCategory" name="category" required>
                    <option value="" disabled>Select category</option>
                    <option value="air-conditioners" selected>Air Conditioners</option>
                    <option value="parts">Parts & Accessories</option>
                    <option value="tools">Tools & Equipment</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="updateBrand" class="form-label">Brand <span class="text-danger">*</span></label>
                    <select class="form-select" id="updateBrand" name="brand" required>
                    <option value="" disabled>Select brand</option>
                    <option value="daikin">Daikin</option>
                    <option value="carrier" selected>Carrier</option>
                    <option value="trane">Trane</option>
                    <option value="lg">LG</option>
                    </select>
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-12">
                    <label for="updateDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="updateDescription" name="description" rows="4">This energy-efficient Smart Inverter AC provides exceptional cooling performance with low noise operation. Features include a sleep mode, timer, and smartphone control capability.</textarea>
                </div>
                </div>
            </div>

            <!-- Pricing & Inventory Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Pricing & Inventory</h6>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="updatePurchasePrice" class="form-label">Purchase Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="updatePurchasePrice" name="purchasePrice" step="0.01" min="0" value="199.99" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="updateSellingPrice" class="form-label">Selling Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="updateSellingPrice" name="sellingPrice" step="0.01" min="0" value="299.99" required>
                    </div>
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="updateCurrentStock" class="form-label">Current Stock <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="updateCurrentStock" name="currentStock" min="0" value="150" required>
                </div>
                <div class="col-md-6">
                    <label for="updateLowStockThreshold" class="form-label">Low Stock Alert Threshold</label>
                    <input type="number" class="form-control" id="updateLowStockThreshold" name="lowStockThreshold" min="0" value="20">
                </div>
                </div>
            </div>

            <!-- Technical Specifications Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Technical Specifications</h6>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="updateCoolingCapacity" class="form-label">Cooling Capacity (BTU)</label>
                    <input type="number" class="form-control" id="updateCoolingCapacity" name="coolingCapacity" min="0" value="12000">
                </div>
                <div class="col-md-6">
                    <label for="updateEnergyRating" class="form-label">Energy Efficiency Rating</label>
                    <input type="text" class="form-control" id="updateEnergyRating" name="energyRating" value="4.5 Star">
                </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="updateInventoryType" class="form-label">Inventory Type</label>
                    <select class="form-select" id="updateInventoryType" name="inventoryType">
                    <option value="" disabled>Select type</option>
                    <option value="finished-good" selected>Finished Good</option>
                    <option value="raw-material">Raw Material</option>
                    <option value="component">Component/Part</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="updateWarrantyPeriod" class="form-label">Warranty Period</label>
                    <input type="text" class="form-control" id="updateWarrantyPeriod" name="warrantyPeriod" value="2 Years">
                </div>
                </div>
            </div>

            <!-- Product Images Section -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">Product Images</h6>
                <div class="row">
                <div class="col-12">
                    <div class="border rounded p-3 text-center">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                        <img src="/api/placeholder/180/180" alt="Product image" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div class="col-md-8 text-md-start">
                        <p class="mb-2">Current image: product-image.jpg</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Remove
                            </button>
                            <div class="position-relative">
                            <button type="button" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-arrow-repeat me-1"></i> Replace
                            </button>
                            <input type="file" class="position-absolute top-0 start-0 opacity-0 w-100 h-100" style="cursor: pointer;">
                            </div>
                        </div>
                        </div>
                    </div>
                    <hr>
                    <div class="image-upload-area d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="bi bi-cloud-arrow-up fs-2 mb-2"></i>
                        <p class="mb-1">Add more images</p>
                        <p class="text-muted small">Supported formats: JPG, PNG. Max file size: 5MB</p>
                        <input type="file" id="updateProductImages" name="productImages[]" class="position-absolute inset-0 opacity-0 w-100 h-100 cursor-pointer" multiple accept=".jpg,.jpeg,.png">
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Update Product</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
            <div class="mb-4">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
            </div>
            <h5 class="mb-3">Are you sure you want to delete this product?</h5>
            <p class="text-muted mb-0">This action cannot be undone. All data associated with this product will be permanently removed from the system.</p>
            <input type="hidden" id="deleteProductId" value="">
        </div>
        <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteButton">
            <i class="bi bi-trash me-1"></i> Delete Product
            </button>
        </div>
        </div>
    </div>
    </div>

</div>


<?php
$content = ob_get_clean();

// Additional scripts specific to this page
$additionalScripts = <<<HTML
<script>
    // Initialize JavaScript functionality for the inventory page
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation dialog for delete actions
        const deleteButtons = document.querySelectorAll('.btn-danger');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this product?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Switch between inventory tabs
        const inventoryTabs = document.querySelectorAll('.inventory-tab');
        inventoryTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                inventoryTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                if (this.textContent.trim() === 'Inventory 2') {
                    // Redirect or load inventory 2 content
                    alert('Would navigate to Inventory 2 page');
                }
            });
        });
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>