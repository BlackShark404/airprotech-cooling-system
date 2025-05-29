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
            // Redirect to product detail page
            window.location.href = '/admin/add-product?id=' + rowData.PROD_ID;
        },
        editRowCallback: function(rowData) {
            // Redirect to product edit page
            window.location.href = '/admin/add-product?id=' + rowData.PROD_ID + '&edit=true';
        },
        deleteRowCallback: function(rowData) {
            // Handle product deletion via API
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
                    window.location.href = '/admin/add-product';
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