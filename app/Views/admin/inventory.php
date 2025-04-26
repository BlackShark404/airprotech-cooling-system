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
                        <tr>
                            <td>IVN1004</td>
                            <td>PRD004</td>
                            <td>Smart Inventer Ac</td>
                            <td>100</td>
                            <td>$199.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>IVN1005</td>
                            <td>PRD005</td>
                            <td>Split System Classic</td>
                            <td>300</td>
                            <td>$29.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>IVN1006</td>
                            <td>PRD006</td>
                            <td>Split System Classic</td>
                            <td>50</td>
                            <td>$899.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>IVN1007</td>
                            <td>PRD007</td>
                            <td>Portable Ac Unit</td>
                            <td>250</td>
                            <td>$79.99</td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Update</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>IVN1008</td>
                            <td>PRD008</td>
                            <td>Smart Inventer Ac</td>
                            <td>125</td>
                            <td>$399.99</td>
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
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="productId" class="form-label">Product ID</label>
                        <input type="text" class="form-control" id="productId" placeholder="Enter product ID">
                    </div>
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" placeholder="Enter product name">
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Product Category</label>
                        <select class="form-select" id="productCategory">
                            <option value="">Select Category</option>
                            <option value="1">Smart Inventer AC</option>
                            <option value="2">Split System Classic</option>
                            <option value="3">Portable AC Unit</option>
                            <option value="4">AC Parts</option>
                            <option value="5">Tools</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="productStock" placeholder="Enter quantity">
                        </div>
                        <div class="col">
                            <label for="productPrice" class="form-label">Price ($)</label>
                            <input type="number" class="form-control" id="productPrice" placeholder="Enter price" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" rows="3" placeholder="Enter product description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Add Product</button>
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