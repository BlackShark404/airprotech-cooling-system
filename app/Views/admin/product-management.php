<?php
$title = 'Product Management - AirProtect';
$activeTab = 'product_management';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
    }
    
    .icon-container {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .bg-info-soft {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .feature-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    
    .feature-badge {
        background-color: #f1f5f9;
        color: #334155;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .spec-table {
        font-size: 13px;
    }
    
    .product-image {
        height: 120px;
        object-fit: contain;
    }
    
    .product-card {
        height: 100%;
    }
</style>
HTML;

// Add any additional scripts specific to this page
$additionalScripts = <<<HTML
<script src="/assets/js/utility/DataTablesManager.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for products
    const productTableManager = new DataTablesManager('productsTable', {
        ajaxUrl: '/api/products',
        columns: [
            { data: 'PROD_ID', title: 'ID', width: '5%' },
            { 
                data: 'PROD_IMAGE', 
                title: 'Image', 
                width: '10%',
                render: function(data) {
                    return '<img src="' + data + '" alt="Product" class="img-fluid" style="max-height: 50px;">';
                }
            },
            { data: 'PROD_NAME', title: 'Name', width: '20%' },
            { 
                data: 'PROD_AVAILABILITY_STATUS', 
                title: 'Status', 
                width: '10%',
                render: function(data) {
                    const badgeClasses = {
                        'Available': 'bg-success',
                        'Out of Stock': 'bg-danger',
                        'Discontinued': 'bg-secondary'
                    };
                    const badgeClass = badgeClasses[data] ? badgeClasses[data] : 'bg-primary';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            {
                data: 'PROD_CREATED_AT',
                title: 'Created',
                width: '10%',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            }
        ],
        viewRowCallback: function(rowData) {
            // Show product details in modal
            showProductDetailsModal(rowData);
        },
        editRowCallback: function(rowData) {
            // Show product edit modal
            showProductEditModal(rowData);
        },
        deleteRowCallback: function(rowData) {
            // Show deletion confirmation in modal (already using a modal from DataTablesManager)
            // The actual deletion will happen when confirmed
            fetch('/api/products/delete/' + rowData.PROD_ID, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    productTableManager.showSuccessToast('Success', 'Product deleted successfully');
                    productTableManager.refresh();
                } else {
                    productTableManager.showErrorToast('Error', data.message || 'Failed to delete product');
                }
            })
            .catch(error => {
                productTableManager.showErrorToast('Error', 'An error occurred while deleting the product');
                console.error('Error:', error);
            });
        },
        customButtons: {
            addButton: {
                text: '<i class="bi bi-plus-circle me-1"></i>Add Product',
                className: 'btn btn-primary',
                action: function() {
                    showAddProductModal();
                }
            },
            refreshButton: {
                text: '<i class="bi bi-arrow-clockwise me-1"></i>Refresh',
                className: 'btn btn-outline-secondary',
                action: function() {
                    productTableManager.refresh();
                }
            }
        }
    });
    
    // Function to show product details modal
    function showProductDetailsModal(product) {
        // Set modal title
        document.getElementById('productModalLabel').textContent = 'Product Details: ' + product.PROD_NAME;
        
        // Clear previous content
        const modalBody = document.getElementById('productModalBody');
        modalBody.innerHTML = '';
        
        // Create product details HTML
        let detailsHtml = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="\${product.PROD_IMAGE}" alt="\${product.PROD_NAME}" class="img-fluid mb-3" style="max-height: 150px;">
                    <div class="badge bg-\${product.PROD_AVAILABILITY_STATUS === 'Available' ? 'success' : (product.PROD_AVAILABILITY_STATUS === 'Out of Stock' ? 'danger' : 'secondary')} mb-3">
                        \${product.PROD_AVAILABILITY_STATUS}
                    </div>
                </div>
                <div class="col-md-8">
                    <h5>Product Information</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID:</strong> \${product.PROD_ID}</li>
                        <li class="list-group-item"><strong>Name:</strong> \${product.PROD_NAME}</li>
                        <li class="list-group-item"><strong>Description:</strong> \${product.PROD_DESCRIPTION || 'No description available'}</li>
                        <li class="list-group-item"><strong>Created:</strong> \${new Date(product.PROD_CREATED_AT).toLocaleString()}</li>
                    </ul>
                </div>
            </div>
        `;
        
        // Fetch additional details if needed (variants, specs, features)
        fetch('/api/products/' + product.PROD_ID, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const productDetails = data.data;
                
                // Add variants if available
                if (productDetails.variants && productDetails.variants.length > 0) {
                    detailsHtml += `
                        <div class="mt-4">
                            <h5>Product Variants</h5>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Capacity</th>
                                        <th>SRP Price</th>
                                        <th>With Installation</th>
                                        <th>Power Consumption</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    productDetails.variants.forEach(variant => {
                        detailsHtml += `
                            <tr>
                                <td>\${variant.VAR_CAPACITY}</td>
                                <td>$\${variant.VAR_SRP_PRICE}</td>
                                <td>\${variant.VAR_PRICE_WITH_INSTALL ? '$' + variant.VAR_PRICE_WITH_INSTALL : 'N/A'}</td>
                                <td>\${variant.VAR_POWER_CONSUMPTION || 'N/A'}</td>
                            </tr>
                        `;
                    });
                    
                    detailsHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                // Add features if available
                if (productDetails.features && productDetails.features.length > 0) {
                    detailsHtml += `
                        <div class="mt-3">
                            <h5>Features</h5>
                            <ul class="list-group">
                    `;
                    
                    productDetails.features.forEach(feature => {
                        detailsHtml += `<li class="list-group-item">\${feature.FEATURE_NAME}</li>`;
                    });
                    
                    detailsHtml += `
                            </ul>
                        </div>
                    `;
                }
                
                // Add specifications if available
                if (productDetails.specs && productDetails.specs.length > 0) {
                    detailsHtml += `
                        <div class="mt-3">
                            <h5>Specifications</h5>
                            <table class="table table-sm">
                                <tbody>
                    `;
                    
                    productDetails.specs.forEach(spec => {
                        detailsHtml += `
                            <tr>
                                <td><strong>\${spec.SPEC_NAME}</strong></td>
                                <td>\${spec.SPEC_VALUE}</td>
                            </tr>
                        `;
                    });
                    
                    detailsHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                // Update modal content
                modalBody.innerHTML = detailsHtml;
                
                // Set footer buttons
                const modalFooter = document.getElementById('productModalFooter');
                modalFooter.innerHTML = `
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="/admin/add-product?id=\${product.PROD_ID}&edit=true" class="btn btn-warning">Edit Product</a>
                `;
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to load product details</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading product details</div>';
        });
        
        // Show modal
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
    }
    
    // Function to show product edit modal
    function showProductEditModal(product) {
        // For complex forms, it's often better to redirect to a dedicated edit page
        // But we'll set up a simple modal edit for basic fields
        
        // Set modal title
        document.getElementById('productModalLabel').textContent = 'Edit Product: ' + product.PROD_NAME;
        
        // Create edit form
        const modalBody = document.getElementById('productModalBody');
        modalBody.innerHTML = `
            <form id="editProductForm">
                <input type="hidden" id="editProductId" value="\${product.PROD_ID}">
                
                <div class="mb-3">
                    <label for="editProductName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="editProductName" value="\${product.PROD_NAME}" required>
                </div>
                
                <div class="mb-3">
                    <label for="editProductDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="editProductDescription" rows="3">\${product.PROD_DESCRIPTION || ''}</textarea>
                </div>
                
                <div class="mb-3">
                    <label for="editProductStatus" class="form-label">Status</label>
                    <select class="form-select" id="editProductStatus" required>
                        <option value="Available" \${product.PROD_AVAILABILITY_STATUS === 'Available' ? 'selected' : ''}>Available</option>
                        <option value="Out of Stock" \${product.PROD_AVAILABILITY_STATUS === 'Out of Stock' ? 'selected' : ''}>Out of Stock</option>
                        <option value="Discontinued" \${product.PROD_AVAILABILITY_STATUS === 'Discontinued' ? 'selected' : ''}>Discontinued</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <p>For advanced editing (variants, features, specs), please use the full editor</p>
                </div>
            </form>
        `;
        
        // Set footer buttons
        const modalFooter = document.getElementById('productModalFooter');
        modalFooter.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveEditButton">Save Changes</button>
            <a href="/admin/add-product?id=\${product.PROD_ID}&edit=true" class="btn btn-warning">Advanced Edit</a>
        `;
        
        // Add event listener for save button
        document.getElementById('saveEditButton').addEventListener('click', function() {
            const productId = document.getElementById('editProductId').value;
            const productData = {
                product: {
                    PROD_NAME: document.getElementById('editProductName').value,
                    PROD_DESCRIPTION: document.getElementById('editProductDescription').value,
                    PROD_AVAILABILITY_STATUS: document.getElementById('editProductStatus').value
                }
            };
            
            // Submit update via API
            fetch('/api/products/' + productId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(productData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    productTableManager.showSuccessToast('Success', 'Product updated successfully');
                    
                    // Hide modal
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                    
                    // Refresh table
                    productTableManager.refresh();
                } else {
                    productTableManager.showErrorToast('Error', data.message || 'Failed to update product');
                }
            })
            .catch(error => {
                console.error('Error updating product:', error);
                productTableManager.showErrorToast('Error', 'An error occurred while updating the product');
            });
        });
        
        // Show modal
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
    }
    
    // Function to show add product modal
    function showAddProductModal() {
        // Set modal title
        document.getElementById('productModalLabel').textContent = 'Add New Product';
        
        // Create add form
        const modalBody = document.getElementById('productModalBody');
        modalBody.innerHTML = `
            <form id="addProductForm">
                <div class="mb-3">
                    <label for="addProductName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="addProductName" required>
                </div>
                
                <div class="mb-3">
                    <label for="addProductDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="addProductDescription" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="addProductImage" class="form-label">Image URL</label>
                    <input type="text" class="form-control" id="addProductImage" value="/assets/images/products/default.jpg">
                </div>
                
                <div class="mb-3">
                    <label for="addProductStatus" class="form-label">Status</label>
                    <select class="form-select" id="addProductStatus" required>
                        <option value="Available" selected>Available</option>
                        <option value="Out of Stock">Out of Stock</option>
                        <option value="Discontinued">Discontinued</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <p>For advanced product creation with variants, features, and specs, please use the full editor</p>
                </div>
            </form>
        `;
        
        // Set footer buttons
        const modalFooter = document.getElementById('productModalFooter');
        modalFooter.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveAddButton">Create Product</button>
            <a href="/admin/add-product" class="btn btn-warning">Advanced Create</a>
        `;
        
        // Add event listener for save button
        document.getElementById('saveAddButton').addEventListener('click', function() {
            const productData = {
                product: {
                    PROD_NAME: document.getElementById('addProductName').value,
                    PROD_DESCRIPTION: document.getElementById('addProductDescription').value,
                    PROD_IMAGE: document.getElementById('addProductImage').value,
                    PROD_AVAILABILITY_STATUS: document.getElementById('addProductStatus').value
                }
            };
            
            // Submit product via API
            fetch('/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(productData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    productTableManager.showSuccessToast('Success', 'Product created successfully');
                    
                    // Hide modal
                    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                    
                    // Refresh table
                    productTableManager.refresh();
                } else {
                    productTableManager.showErrorToast('Error', data.message || 'Failed to create product');
                }
            })
            .catch(error => {
                console.error('Error creating product:', error);
                productTableManager.showErrorToast('Error', 'An error occurred while creating the product');
            });
        });
        
        // Show modal
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
    }
    
    // Load summary data
    loadProductSummary();
});
</script>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Product Management</h1>
            <p class="mb-0 text-muted">Manage your air conditioning products, variants, and features</p>
        </div>
    </div>

    <!-- Content Row - Summary Cards -->
    <div class="row mb-4">
        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalProducts">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-primary-soft">
                                <i class="bi bi-box text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="availableProducts">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-success-soft">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Out of Stock Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="outOfStockProducts">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-warning-soft">
                                <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Variants Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Product Variants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="productVariants">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-info-soft">
                                <i class="bi bi-diagram-3 text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Products</h6>
        </div>
        <div class="card-body">
            <table id="productsTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created</th>
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

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productModalBody">
                <!-- Content will be dynamically loaded -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="productModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fetch product summary data and update the cards
function loadProductSummary() {
    fetch('/api/products/summary', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalProducts').textContent = data.data.total_products || 0;
            document.getElementById('availableProducts').textContent = data.data.available_products || 0;
            document.getElementById('outOfStockProducts').textContent = data.data.out_of_stock || 0;
            document.getElementById('productVariants').textContent = data.data.total_variants || 0;
        } else {
            console.error('Failed to load summary data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadProductSummary();
});
</script>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?> 