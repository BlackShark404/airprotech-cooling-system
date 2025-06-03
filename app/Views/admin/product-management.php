<?php
$title = 'Product Management - AirProtech';
$activeTab = 'product_management';

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
    .action-icon-view {
        color: #007bff;
    }
    .action-icon-edit {
        color: #28a745;
    }
    .action-icon-delete {
        color: #dc3545;
    }
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.375rem;
    }
    .badge-available {
        background-color: #198754;
        color: #fff;
    }
    .badge-out-of-stock {
        background-color: #dc3545;
        color: #fff;
    }
    .badge-discontinued {
        background-color: #6c757d;
        color: #fff;
    }
    .modal-header {
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: calc(0.3rem - 1px);
        border-top-right-radius: calc(0.3rem - 1px);
        padding: 1rem 1rem;
    }
    .modal-body {
        padding: 1rem;
    }
    .modal-footer {
        border-top: 1px solid #dee2e6;
        border-bottom-right-radius: calc(0.3rem - 1px);
        border-bottom-left-radius: calc(0.3rem - 1px);
        padding: 0.75rem;
    }
    .tab-content {
        padding: 20px 0;
    }
    .feature-item, .spec-item, .variant-item {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    .feature-remove, .spec-remove, .variant-remove {
        cursor: pointer;
        color: #dc3545;
    }
    .preview-image {
        max-width: 100%;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 8px;
    }
    
    /* Responsive table styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Table styling */
    #productsTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #productsTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    #productsTable tbody td {
        padding: 15px 8px;
        vertical-align: middle;
    }
    
    #productsTable tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }
    
    /* Collapsible row styling */
    .details-control {
        cursor: pointer;
        font-size: 1.2rem;
        color: #007bff;
    }
    
    tr.details-row {
        background-color: #f8f9fa;
    }
    
    .detail-content {
        padding: 15px;
    }
    
    .nested-table {
        width: 100%;
        margin-top: 10px;
    }
    
    .nested-table th {
        background-color: #e9ecef;
        padding: 8px;
        font-weight: 600;
    }
    
    .nested-table td {
        padding: 8px;
        border-bottom: 1px solid #dee2e6;
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Product Management</h1>
        <p class="text-muted">Manage products, features, specifications, and variants</p>
    </div>

    <!-- Filters Card -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Filters</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="statusFilter" class="form-label">Availability Status</label>
                    <select id="statusFilter" class="form-select filter-dropdown">
                        <option value="">All Statuses</option>
                        <option value="Available">Available</option>
                        <option value="Out of Stock">Out of Stock</option>
                        <option value="Discontinued">Discontinued</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="dateFilter" class="form-label">Date Range</label>
                    <input type="date" id="dateFilter" class="form-control date-input">
                </div>
                <div class="col-md-6 mb-3 text-end align-self-end">
                    <button id="addProductBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="productsTable" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table content will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId" name="productId">
                    
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">Product Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab" aria-controls="features" aria-selected="false">Features</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="false">Specifications</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants" type="button" role="tab" aria-controls="variants" aria-selected="false">Variants</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="productTabsContent">
                        <!-- Product Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="productName" name="productName" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="productDescription" name="productDescription" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="productStatus" class="form-label">Availability Status *</label>
                                <select class="form-select" id="productStatus" name="productStatus" required>
                                    <option value="Available">Available</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                    <option value="Discontinued">Discontinued</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="productImage" class="form-label">Product Image *</label>
                                <input type="file" class="form-control" id="productImage" name="productImage" accept="image/*">
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <!-- Features Tab -->
                        <div class="tab-pane fade" id="features" role="tabpanel" aria-labelledby="features-tab">
                            <div id="featuresContainer">
                                <!-- Features will be added here dynamically -->
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" id="addFeatureBtn" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Add Feature
                                </button>
                            </div>
                        </div>
                        
                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                            <div id="specsContainer">
                                <!-- Specs will be added here dynamically -->
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" id="addSpecBtn" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Add Specification
                                </button>
                            </div>
                        </div>
                        
                        <!-- Variants Tab -->
                        <div class="tab-pane fade" id="variants" role="tabpanel" aria-labelledby="variants-tab">
                            <div id="variantsContainer">
                                <!-- Variants will be added here dynamically -->
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" id="addVariantBtn" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Add Variant
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveProductBtn" class="btn btn-primary">Save Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/DataTablesManager.js"></script>


<!-- JavaScript for Product Management -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    let productsTable = new DataTablesManager('#productsTable', {
        ajax: {
            url: '/api/products',
            dataSrc: ''
        },
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '<i class="bi bi-chevron-down"></i>',
                width: '30px'
            },
            { 
                data: 'PROD_IMAGE',
                render: function(data) {
                    return '<img src="/assets/uploads/products/' + data + '" class="product-image" alt="Product Image">';
                }
            },
            { data: 'PROD_NAME' },
            { 
                data: 'PROD_DESCRIPTION',
                render: function(data) {
                    return data ? (data.length > 100 ? data.substring(0, 100) + '...' : data) : 'N/A';
                }
            },
            { 
                data: 'PROD_AVAILABILITY_STATUS',
                render: function(data) {
                    let badgeClass = 'badge-available';
                    if (data === 'Out of Stock') {
                        badgeClass = 'badge-out-of-stock';
                    } else if (data === 'Discontinued') {
                        badgeClass = 'badge-discontinued';
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { 
                data: 'PROD_CREATED_AT',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'PROD_UPDATED_AT',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <div class="d-flex">
                            <button class="action-icon action-icon-view view-product" data-id="${data.PROD_ID}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="action-icon action-icon-edit edit-product" data-id="${data.PROD_ID}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="action-icon action-icon-delete delete-product" data-id="${data.PROD_ID}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[5, 'desc']] // Sort by Created At column by default
    });
    
    // Handle row expand/collapse for details
    $('#productsTable tbody').on('click', 'td.details-control', function() {
        let tr = $(this).closest('tr');
        let row = productsTable.getApiInstance().row(tr);
        let icon = $(this).find('i');
        
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            // Open this row
            let productId = row.data().PROD_ID;
            fetchProductDetails(productId, function(details) {
                row.child(formatProductDetails(details)).show();
                tr.addClass('shown');
                icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            });
        }
    });
    
    // Format the product details row
    function formatProductDetails(details) {
        let featuresHtml = '';
        let specsHtml = '';
        let variantsHtml = '';
        
        // Generate features HTML
        if (details.features && details.features.length > 0) {
            featuresHtml = '<table class="nested-table"><thead><tr><th>Feature</th></tr></thead><tbody>';
            details.features.forEach(function(feature) {
                featuresHtml += `<tr><td>${feature.FEATURE_NAME}</td></tr>`;
            });
            featuresHtml += '</tbody></table>';
        } else {
            featuresHtml = '<p>No features available</p>';
        }
        
        // Generate specs HTML
        if (details.specs && details.specs.length > 0) {
            specsHtml = '<table class="nested-table"><thead><tr><th>Specification</th><th>Value</th></tr></thead><tbody>';
            details.specs.forEach(function(spec) {
                specsHtml += `<tr><td>${spec.SPEC_NAME}</td><td>${spec.SPEC_VALUE}</td></tr>`;
            });
            specsHtml += '</tbody></table>';
        } else {
            specsHtml = '<p>No specifications available</p>';
        }
        
        // Generate variants HTML
        if (details.variants && details.variants.length > 0) {
            variantsHtml = '<table class="nested-table"><thead><tr><th>Capacity</th><th>SRP Price</th><th>Free Install Price</th><th>With Install Price</th><th>Power Consumption</th></tr></thead><tbody>';
            details.variants.forEach(function(variant) {
                variantsHtml += `
                    <tr>
                        <td>${variant.VAR_CAPACITY}</td>
                        <td>₱${parseFloat(variant.VAR_SRP_PRICE).toLocaleString()}</td>
                        <td>${variant.VAR_PRICE_FREE_INSTALL ? '₱' + parseFloat(variant.VAR_PRICE_FREE_INSTALL).toLocaleString() : 'N/A'}</td>
                        <td>${variant.VAR_PRICE_WITH_INSTALL ? '₱' + parseFloat(variant.VAR_PRICE_WITH_INSTALL).toLocaleString() : 'N/A'}</td>
                        <td>${variant.VAR_POWER_CONSUMPTION || 'N/A'}</td>
                    </tr>
                `;
            });
            variantsHtml += '</tbody></table>';
        } else {
            variantsHtml = '<p>No variants available</p>';
        }
        
        return `
            <div class="detail-content">
                <div class="row">
                    <div class="col-md-4">
                        <h6>Features</h6>
                        ${featuresHtml}
                    </div>
                    <div class="col-md-4">
                        <h6>Specifications</h6>
                        ${specsHtml}
                    </div>
                    <div class="col-md-4">
                        <h6>Variants</h6>
                        ${variantsHtml}
                    </div>
                </div>
            </div>
        `;
    }
    
    // Fetch product details
    function fetchProductDetails(productId, callback) {
        fetch(`/api/products/${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    callback(data.data);
                } else {
                    console.error('Error fetching product details:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Filter products by status
    $('#statusFilter').on('change', function() {
        productsTable.getApiInstance().column(4).search($(this).val()).draw();
    });
    
    // Add New Product Button
    $('#addProductBtn').on('click', function() {
        resetProductForm();
        $('#productModalLabel').text('Add New Product');
        $('#productModal').modal('show');
    });
    
    // Edit Product Button
    $(document).on('click', '.edit-product', function() {
        const productId = $(this).data('id');
        resetProductForm();
        $('#productModalLabel').text('Edit Product');
        $('#productId').val(productId);
        
        fetchProductDetails(productId, function(product) {
            // Fill in product details
            $('#productName').val(product.PROD_NAME);
            $('#productDescription').val(product.PROD_DESCRIPTION);
            $('#productStatus').val(product.PROD_AVAILABILITY_STATUS);
            
            if (product.PROD_IMAGE) {
                $('#imagePreview').html(`<img src="/assets/uploads/products/${product.PROD_IMAGE}" class="preview-image" alt="Product Image">`);
            }
            
            // Add features
            if (product.features && product.features.length > 0) {
                product.features.forEach(function(feature) {
                    addFeatureRow(feature.FEATURE_NAME, feature.FEATURE_ID);
                });
            }
            
            // Add specs
            if (product.specs && product.specs.length > 0) {
                product.specs.forEach(function(spec) {
                    addSpecRow(spec.SPEC_NAME, spec.SPEC_VALUE, spec.SPEC_ID);
                });
            }
            
            // Add variants
            if (product.variants && product.variants.length > 0) {
                product.variants.forEach(function(variant) {
                    addVariantRow(
                        variant.VAR_ID,
                        variant.VAR_CAPACITY,
                        variant.VAR_SRP_PRICE,
                        variant.VAR_PRICE_FREE_INSTALL,
                        variant.VAR_PRICE_WITH_INSTALL,
                        variant.VAR_POWER_CONSUMPTION
                    );
                });
            }
            
            $('#productModal').modal('show');
        });
    });
    
    // Delete Product Button
    $(document).on('click', '.delete-product', function() {
        const productId = $(this).data('id');
        $('#confirmDeleteBtn').data('id', productId);
        $('#deleteConfirmModal').modal('show');
    });
    
    // Confirm Delete Button
    $('#confirmDeleteBtn').on('click', function() {
        const productId = $(this).data('id');
        
        fetch(`/api/products/delete/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#deleteConfirmModal').modal('hide');
                    productsTable.getApiInstance().ajax.reload();
                    showAlert('Product deleted successfully', 'success');
                } else {
                    showAlert('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while deleting the product', 'danger');
            });
    });
    
    // Save Product Button
    $('#saveProductBtn').on('click', function() {
        // Validate form
        if (!validateProductForm()) {
            return;
        }
        
        const productId = $('#productId').val();
        const isEditMode = productId !== '';
        
        // Create FormData object for file upload
        const formData = new FormData();
        
        // Add product details
        const productData = {
            PROD_NAME: $('#productName').val(),
            PROD_DESCRIPTION: $('#productDescription').val(),
            PROD_AVAILABILITY_STATUS: $('#productStatus').val()
        };
        
        formData.append('product', JSON.stringify(productData));
        
        // Add product image if selected
        const productImageInput = document.getElementById('productImage');
        if (productImageInput.files.length > 0) {
            formData.append('product_image', productImageInput.files[0]);
        }
        
        // Add features
        const features = [];
        $('.feature-row').each(function() {
            const featureId = $(this).data('id');
            const featureName = $(this).find('.feature-name').val();
            
            if (featureName) {
                features.push({
                    FEATURE_ID: featureId || null,
                    FEATURE_NAME: featureName
                });
            }
        });
        formData.append('features', JSON.stringify(features));
        
        // Add specifications
        const specs = [];
        $('.spec-row').each(function() {
            const specId = $(this).data('id');
            const specName = $(this).find('.spec-name').val();
            const specValue = $(this).find('.spec-value').val();
            
            if (specName && specValue) {
                specs.push({
                    SPEC_ID: specId || null,
                    SPEC_NAME: specName,
                    SPEC_VALUE: specValue
                });
            }
        });
        formData.append('specs', JSON.stringify(specs));
        
        // Add variants
        const variants = [];
        $('.variant-row').each(function() {
            const variantId = $(this).data('id');
            const capacity = $(this).find('.variant-capacity').val();
            const srpPrice = $(this).find('.variant-srp-price').val();
            const freeInstallPrice = $(this).find('.variant-free-install-price').val();
            const withInstallPrice = $(this).find('.variant-with-install-price').val();
            const powerConsumption = $(this).find('.variant-power-consumption').val();
            
            if (capacity && srpPrice) {
                variants.push({
                    VAR_ID: variantId || null,
                    VAR_CAPACITY: capacity,
                    VAR_SRP_PRICE: srpPrice,
                    VAR_PRICE_FREE_INSTALL: freeInstallPrice || null,
                    VAR_PRICE_WITH_INSTALL: withInstallPrice || null,
                    VAR_POWER_CONSUMPTION: powerConsumption || null
                });
            }
        });
        formData.append('variants', JSON.stringify(variants));
        
        // Determine the API endpoint based on whether we're adding or editing
        const url = isEditMode ? `/api/products/${productId}` : '/api/products';
        
        // Send AJAX request
        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#productModal').modal('hide');
                    productsTable.getApiInstance().ajax.reload();
                    showAlert(isEditMode ? 'Product updated successfully' : 'Product created successfully', 'success');
                } else {
                    showAlert('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while saving the product', 'danger');
            });
    });
    
    // Reset product form
    function resetProductForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#imagePreview').empty();
        $('#featuresContainer').empty();
        $('#specsContainer').empty();
        $('#variantsContainer').empty();
        $('#details-tab').tab('show');
    }
    
    // Validate product form
    function validateProductForm() {
        if (!$('#productName').val()) {
            showAlert('Please enter a product name', 'danger');
            $('#details-tab').tab('show');
            return false;
        }
        
        if (!$('#productStatus').val()) {
            showAlert('Please select an availability status', 'danger');
            $('#details-tab').tab('show');
            return false;
        }
        
        const productId = $('#productId').val();
        const isEditMode = productId !== '';
        
        // For new products, require an image
        if (!isEditMode && !$('#productImage').val() && !$('#imagePreview img').length) {
            showAlert('Please select a product image', 'danger');
            $('#details-tab').tab('show');
            return false;
        }
        
        // Require at least one variant
        if ($('.variant-row').length === 0) {
            showAlert('Please add at least one product variant', 'danger');
            $('#variants-tab').tab('show');
            return false;
        }
        
        return true;
    }
    
    // Show alert message
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    }
    
    // Add Feature Row
    $('#addFeatureBtn').on('click', function() {
        addFeatureRow();
    });
    
    function addFeatureRow(featureName = '', featureId = null) {
        const featureRow = `
            <div class="feature-row row mb-2" ${featureId ? `data-id="${featureId}"` : ''}>
                <div class="col-10">
                    <input type="text" class="form-control feature-name" placeholder="Feature" value="${featureName}">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-danger feature-remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#featuresContainer').append(featureRow);
    }
    
    // Remove Feature Row
    $(document).on('click', '.feature-remove', function() {
        $(this).closest('.feature-row').remove();
    });
    
    // Add Spec Row
    $('#addSpecBtn').on('click', function() {
        addSpecRow();
    });
    
    function addSpecRow(specName = '', specValue = '', specId = null) {
        const specRow = `
            <div class="spec-row row mb-2" ${specId ? `data-id="${specId}"` : ''}>
                <div class="col-5">
                    <input type="text" class="form-control spec-name" placeholder="Specification Name" value="${specName}">
                </div>
                <div class="col-5">
                    <input type="text" class="form-control spec-value" placeholder="Specification Value" value="${specValue}">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-danger spec-remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#specsContainer').append(specRow);
    }
    
    // Remove Spec Row
    $(document).on('click', '.spec-remove', function() {
        $(this).closest('.spec-row').remove();
    });
    
    // Add Variant Row
    $('#addVariantBtn').on('click', function() {
        addVariantRow();
    });
    
    function addVariantRow(variantId = null, capacity = '', srpPrice = '', freeInstallPrice = '', withInstallPrice = '', powerConsumption = '') {
        const variantRow = `
            <div class="variant-row card mb-3" ${variantId ? `data-id="${variantId}"` : ''}>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-10">
                            <h6>Product Variant</h6>
                        </div>
                        <div class="col-2 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm variant-remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Capacity (HP/BTU) *</label>
                            <input type="text" class="form-control variant-capacity" placeholder="e.g., 1.0 HP or 9000 BTU" value="${capacity}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SRP Price (₱) *</label>
                            <input type="number" class="form-control variant-srp-price" min="0" step="0.01" placeholder="e.g., 25000" value="${srpPrice}">
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Price with Free Installation (₱)</label>
                            <input type="number" class="form-control variant-free-install-price" min="0" step="0.01" placeholder="e.g., 27000" value="${freeInstallPrice}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price with Installation (₱)</label>
                            <input type="number" class="form-control variant-with-install-price" min="0" step="0.01" placeholder="e.g., 30000" value="${withInstallPrice}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Power Consumption</label>
                            <input type="text" class="form-control variant-power-consumption" placeholder="e.g., 800W" value="${powerConsumption}">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#variantsContainer').append(variantRow);
    }
    
    // Remove Variant Row
    $(document).on('click', '.variant-remove', function() {
        $(this).closest('.variant-row').remove();
    });
    
    // Preview product image
    $('#productImage').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`<img src="${e.target.result}" class="preview-image" alt="Product Image Preview">`);
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').empty();
        }
    });
});
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the admin template and pass in variables
include_once __DIR__ . '/../includes/admin/base.php';
?> 