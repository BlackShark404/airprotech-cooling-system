<?php
$title = 'Inventory Management - AirProtech';
$activeTab = 'inventory_management';

// Add styles specific to this page
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
    .badge-low {
        background-color: #dc3545;
        color: #fff;
    }
    .badge-medium {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-high {
        background-color: #198754;
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
    .card-counter {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        padding: 20px 10px;
        background-color: #fff;
        height: 100%;
        border-radius: 12px;
        transition: .3s linear all;
    }
    .card-counter:hover {
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        transition: .3s linear all;
    }
    .card-counter i {
        font-size: 5em;
        opacity: 0.2;
    }
    .card-counter .count-numbers {
        position: absolute;
        right: 35px;
        top: 20px;
        font-size: 32px;
        display: block;
    }
    .card-counter .count-name {
        position: absolute;
        right: 35px;
        top: 65px;
        font-style: italic;
        text-transform: capitalize;
        opacity: 0.5;
        display: block;
        font-size: 18px;
    }
    .card-counter.primary {
        background-color: #007bff;
        color: #FFF;
    }
    .card-counter.danger {
        background-color: #ef5350;
        color: #FFF;
    }
    .card-counter.success {
        background-color: #66bb6a;
        color: #FFF;
    }
    .card-counter.info {
        background-color: #26c6da;
        color: #FFF;
    }
    /* Responsive table styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    /* Table styling */
    #inventoryTable, #warehouseTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    #inventoryTable thead th, #warehouseTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 12px 8px;
        vertical-align: middle;
    }
    #inventoryTable tbody td, #warehouseTable tbody td {
        padding: 15px 8px;
        vertical-align: middle;
    }
    #inventoryTable tbody tr:hover, #warehouseTable tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }
    .inventory-type-badge {
        border-radius: 50px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .inventory-type-Regular {
        background-color: #cff4fc;
        color: #055160;
    }
    .inventory-type-Display {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .inventory-type-Reserve {
        background-color: #f8d7da;
        color: #842029;
    }
    .inventory-type-Damaged {
        background-color: #fff3cd;
        color: #664d03;
    }
    .inventory-type-Returned {
        background-color: #e2e3e5;
        color: #41464b;
    }
    .inventory-type-Quarantine {
        background-color: #cff4fc;
        color: #084298;
    }
    .tab-content {
        padding: 20px 0;
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Inventory Management</h1>
        <p class="text-muted">Manage warehouse locations and product inventory</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card-counter primary">
                <i class="bi bi-box-seam"></i>
                <span class="count-numbers" id="totalProducts">0</span>
                <span class="count-name">Total Products</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-counter success">
                <i class="bi bi-building"></i>
                <span class="count-numbers" id="totalWarehouses">0</span>
                <span class="count-name">Warehouses</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-counter info">
                <i class="bi bi-boxes"></i>
                <span class="count-numbers" id="totalInventory">0</span>
                <span class="count-name">Total Inventory</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-counter danger">
                <i class="bi bi-exclamation-triangle"></i>
                <span class="count-numbers" id="lowStockItems">0</span>
                <span class="count-name">Low Stock Items</span>
            </div>
        </div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="true">Inventory</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="warehouses-tab" data-bs-toggle="tab" data-bs-target="#warehouses" type="button" role="tab" aria-controls="warehouses" aria-selected="false">Warehouses</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="low-stock-tab" data-bs-toggle="tab" data-bs-target="#low-stock" type="button" role="tab" aria-controls="low-stock" aria-selected="false">Low Stock</button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content" id="inventoryTabsContent">
        <!-- Inventory Tab -->
        <div class="tab-pane fade show active" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label for="warehouseFilter" class="form-label">Warehouse</label>
                            <select id="warehouseFilter" class="form-select filter-dropdown">
                                <option value="">All Warehouses</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="inventoryTypeFilter" class="form-label">Inventory Type</label>
                            <select id="inventoryTypeFilter" class="form-select filter-dropdown">
                                <option value="">All Types</option>
                                <option value="Regular">Regular</option>
                                <option value="Display">Display</option>
                                <option value="Reserve">Reserve</option>
                                <option value="Damaged">Damaged</option>
                                <option value="Returned">Returned</option>
                                <option value="Quarantine">Quarantine</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="searchFilter" class="form-label">Search</label>
                            <input type="text" id="searchFilter" class="form-control" placeholder="Search products...">
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button id="addInventoryBtn" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                                <i class="bi bi-plus-circle me-1"></i> Add Inventory
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inventoryTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table data will be populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warehouses Tab -->
        <div class="tab-pane fade" id="warehouses" role="tabpanel" aria-labelledby="warehouses-tab">
            <div class="d-flex justify-content-end mb-4">
                <button id="addWarehouseBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Warehouse
                </button>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="warehouseTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Storage Capacity</th>
                                    <th>Restock Threshold</th>
                                    <th>Current Usage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table data will be populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Tab -->
        <div class="tab-pane fade" id="low-stock" role="tabpanel" aria-labelledby="low-stock-tab">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lowStockTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Current Quantity</th>
                                    <th>Threshold</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table data will be populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Inventory Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInventoryModalLabel">Add Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addInventoryForm">
                    <div class="mb-3">
                        <label for="productSelect" class="form-label">Product</label>
                        <select id="productSelect" class="form-select" required>
                            <option value="">Select Product</option>
                            <!-- Products will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseSelect" class="form-label">Warehouse</label>
                        <select id="warehouseSelect" class="form-select" required>
                            <option value="">Select Warehouse</option>
                            <!-- Warehouses will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inventoryType" class="form-label">Inventory Type</label>
                        <select id="inventoryType" class="form-select" required>
                            <option value="Regular">Regular</option>
                            <option value="Display">Display</option>
                            <option value="Reserve">Reserve</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Returned">Returned</option>
                            <option value="Quarantine">Quarantine</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantityInput" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantityInput" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveInventoryBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Inventory Modal -->
<div class="modal fade" id="editInventoryModal" tabindex="-1" aria-labelledby="editInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInventoryModalLabel">Edit Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editInventoryForm">
                    <input type="hidden" id="editInventoryId">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Product</label>
                        <input type="text" class="form-control" id="editProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editWarehouseName" class="form-label">Warehouse</label>
                        <input type="text" class="form-control" id="editWarehouseName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editInventoryType" class="form-label">Inventory Type</label>
                        <select id="editInventoryType" class="form-select" required>
                            <option value="Regular">Regular</option>
                            <option value="Display">Display</option>
                            <option value="Reserve">Reserve</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Returned">Returned</option>
                            <option value="Quarantine">Quarantine</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editQuantityInput" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="editQuantityInput" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateInventoryBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Warehouse Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1" aria-labelledby="addWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWarehouseModalLabel">Add Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addWarehouseForm">
                    <div class="mb-3">
                        <label for="warehouseName" class="form-label">Warehouse Name</label>
                        <input type="text" class="form-control" id="warehouseName" required>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseLocation" class="form-label">Location</label>
                        <textarea class="form-control" id="warehouseLocation" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="storageCapacity" class="form-label">Storage Capacity</label>
                        <input type="number" class="form-control" id="storageCapacity" min="1">
                        <small class="text-muted">Maximum number of items this warehouse can store</small>
                    </div>
                    <div class="mb-3">
                        <label for="restockThreshold" class="form-label">Restock Threshold</label>
                        <input type="number" class="form-control" id="restockThreshold" min="0">
                        <small class="text-muted">Minimum inventory level before restock is needed</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveWarehouseBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Warehouse Modal -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWarehouseModalLabel">Edit Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editWarehouseForm">
                    <input type="hidden" id="editWarehouseId">
                    <div class="mb-3">
                        <label for="editWarehouseName" class="form-label">Warehouse Name</label>
                        <input type="text" class="form-control" id="editWarehouseName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editWarehouseLocation" class="form-label">Location</label>
                        <textarea class="form-control" id="editWarehouseLocation" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editStorageCapacity" class="form-label">Storage Capacity</label>
                        <input type="number" class="form-control" id="editStorageCapacity" min="1">
                        <small class="text-muted">Maximum number of items this warehouse can store</small>
                    </div>
                    <div class="mb-3">
                        <label for="editRestockThreshold" class="form-label">Restock Threshold</label>
                        <input type="number" class="form-control" id="editRestockThreshold" min="0">
                        <small class="text-muted">Minimum inventory level before restock is needed</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateWarehouseBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Move Stock Modal -->
<div class="modal fade" id="moveStockModal" tabindex="-1" aria-labelledby="moveStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveStockModalLabel">Move Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="moveStockForm">
                    <input type="hidden" id="moveInventoryId">
                    <div class="mb-3">
                        <label for="moveProductName" class="form-label">Product</label>
                        <input type="text" class="form-control" id="moveProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="moveSourceWarehouse" class="form-label">Source Warehouse</label>
                        <input type="text" class="form-control" id="moveSourceWarehouse" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="moveQuantityAvailable" class="form-label">Available Quantity</label>
                        <input type="text" class="form-control" id="moveQuantityAvailable" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="moveTargetWarehouse" class="form-label">Target Warehouse</label>
                        <select id="moveTargetWarehouse" class="form-select" required>
                            <option value="">Select Warehouse</option>
                            <!-- Warehouses will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="moveQuantity" class="form-label">Quantity to Move</label>
                        <input type="number" class="form-control" id="moveQuantity" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="moveStockBtn">Move Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmMessage">Are you sure you want to delete this item?</p>
                <input type="hidden" id="deleteItemId">
                <input type="hidden" id="deleteItemType">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php
// End output buffering and get the content
$content = ob_get_clean();

// Add the styles to the content
$content .= $additionalStyles;

// Add additional scripts specific to this page
$additionalScripts = <<<'HTML'
<!-- DataTables CDN -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        const inventoryTable = $('#inventoryTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ordering: true,
            ajax: {
                url: '/api/inventory',
                dataSrc: ''
            },
            columns: [
                { 
                    data: null,
                    render: function(data) {
                        return `
                            <div class="d-flex align-items-center">
                                <img src="${data.PROD_IMAGE || '/assets/images/no-image.png'}" class="me-3" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                <div>
                                    <div class="fw-bold">${data.PROD_NAME}</div>
                                    <small class="text-muted">ID: ${data.PROD_ID}</small>
                                </div>
                            </div>
                        `;
                    }
                },
                { data: 'WHOUSE_NAME' },
                { 
                    data: 'INVE_TYPE',
                    render: function(data) {
                        return `<span class="inventory-type-badge inventory-type-${data}">${data}</span>`;
                    }
                },
                { data: 'QUANTITY' },
                { 
                    data: 'INVE_UPDATED_AT',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="d-flex">
                                <button class="action-icon action-icon-edit" data-id="${data.INVE_ID}" data-action="edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-icon action-icon-view" data-id="${data.INVE_ID}" data-action="move">
                                    <i class="bi bi-box-arrow-right"></i>
                                </button>
                                <button class="action-icon action-icon-delete" data-id="${data.INVE_ID}" data-action="delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                search: "Search inventory:",
                emptyTable: "No inventory items found"
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            lengthMenu: [10, 25, 50, 100]
        });
        
        const warehouseTable = $('#warehouseTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ordering: true,
            ajax: {
                url: '/api/warehouses',
                dataSrc: ''
            },
            columns: [
                { data: 'WHOUSE_NAME' },
                { data: 'WHOUSE_LOCATION' },
                { 
                    data: 'WHOUSE_STORAGE_CAPACITY',
                    render: function(data) {
                        return data ? data : 'N/A';
                    }
                },
                { 
                    data: 'WHOUSE_RESTOCK_THRESHOLD',
                    render: function(data) {
                        return data ? data : 'N/A';
                    }
                },
                { 
                    data: null,
                    render: function(data) {
                        if (!data.WHOUSE_STORAGE_CAPACITY) return 'N/A';
                        
                        const usage = data.TOTAL_INVENTORY / data.WHOUSE_STORAGE_CAPACITY * 100;
                        const usageClass = usage > 80 ? 'danger' : usage > 50 ? 'warning' : 'success';
                        
                        return `
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-${usageClass}" role="progressbar" 
                                    style="width: ${Math.min(100, usage)}%" 
                                    aria-valuenow="${usage}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small>${Math.round(usage)}% (${data.TOTAL_INVENTORY}/${data.WHOUSE_STORAGE_CAPACITY})</small>
                        `;
                    }
                },
                { 
                    data: null,
                    render: function(data) {
                        let badgeClass = 'success';
                        let status = 'Normal';
                        
                        if (data.WHOUSE_STORAGE_CAPACITY && data.TOTAL_INVENTORY) {
                            const usage = data.TOTAL_INVENTORY / data.WHOUSE_STORAGE_CAPACITY * 100;
                            if (usage > 90) {
                                badgeClass = 'danger';
                                status = 'Critical';
                            } else if (usage > 75) {
                                badgeClass = 'warning';
                                status = 'Warning';
                            }
                        }
                        
                        return `<span class="badge badge-${badgeClass}">${status}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="d-flex">
                                <button class="action-icon action-icon-edit" data-id="${data.WHOUSE_ID}" data-action="edit-warehouse">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-icon action-icon-delete" data-id="${data.WHOUSE_ID}" data-action="delete-warehouse">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                search: "Search warehouses:",
                emptyTable: "No warehouses found"
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            lengthMenu: [10, 25, 50, 100]
        });
        
        const lowStockTable = $('#lowStockTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ordering: true,
            ajax: {
                url: '/api/inventory/low-stock',
                dataSrc: ''
            },
            columns: [
                { 
                    data: null,
                    render: function(data) {
                        return `
                            <div class="d-flex align-items-center">
                                <img src="${data.PROD_IMAGE || '/assets/images/no-image.png'}" class="me-3" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                <div>
                                    <div class="fw-bold">${data.PROD_NAME}</div>
                                    <small class="text-muted">ID: ${data.PROD_ID}</small>
                                </div>
                            </div>
                        `;
                    }
                },
                { data: 'WHOUSE_NAME' },
                { 
                    data: 'QUANTITY',
                    render: function(data, type, row) {
                        const threshold = row.WHOUSE_RESTOCK_THRESHOLD;
                        const ratio = data / threshold;
                        let badgeClass = ratio <= 0.3 ? 'danger' : ratio <= 0.7 ? 'warning' : 'primary';
                        
                        return `<span class="badge bg-${badgeClass}">${data}</span>`;
                    }
                },
                { data: 'WHOUSE_RESTOCK_THRESHOLD' },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <button class="btn btn-sm btn-primary" data-id="${data.INVE_ID}" data-action="restock">
                                <i class="bi bi-plus-circle me-1"></i> Restock
                            </button>
                        `;
                    }
                }
            ],
            language: {
                search: "Search low stock:",
                emptyTable: "No low stock items found"
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            lengthMenu: [10, 25, 50, 100]
        });

        // Load dashboard data
        function loadDashboardData() {
            $.ajax({
                url: '/api/inventory/summary',
                method: 'GET',
                success: function(data) {
                    $('#totalProducts').text(data.TOTAL_PRODUCTS || 0);
                    $('#totalWarehouses').text(data.TOTAL_WAREHOUSES || 0);
                    $('#totalInventory').text(data.TOTAL_INVENTORY || 0);
                    $('#lowStockItems').text(data.LOW_STOCK_ITEMS || 0);
                },
                error: function(xhr) {
                    console.error('Error loading dashboard data:', xhr);
                }
            });
        }
        
        // Load warehouses for dropdowns
        function loadWarehouses() {
            $.ajax({
                url: '/api/warehouses',
                method: 'GET',
                success: function(response) {
                    // Clear and populate warehouse filter dropdown
                    $('#warehouseFilter').empty().append('<option value="">All Warehouses</option>');
                    $('#warehouseSelect').empty().append('<option value="">Select Warehouse</option>');
                    
                    // Populate dropdowns with warehouse data
                    if (response && response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(warehouse) {
                            $('#warehouseFilter').append(`<option value="${warehouse.WHOUSE_ID}">${warehouse.WHOUSE_NAME}</option>`);
                            $('#warehouseSelect').append(`<option value="${warehouse.WHOUSE_ID}">${warehouse.WHOUSE_NAME}</option>`);
                        });
                    } else {
                        console.error('Invalid data format for warehouses:', response);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading warehouses:', xhr);
                }
            });
        }
        
        // Load products for dropdowns
        function loadProducts() {
            $.ajax({
                url: '/api/products',
                method: 'GET',
                success: function(response) {
                    // Populate product select dropdown
                    $('#productSelect').empty().append('<option value="">Select Product</option>');
                    
                    if (response && response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(product) {
                            $('#productSelect').append(`<option value="${product.PROD_ID}">${product.PROD_NAME}</option>`);
                        });
                    } else {
                        console.error('Invalid data format for products:', response);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading products:', xhr);
                }
            });
        }
        
        // Initial data loading
        loadDashboardData();
        loadWarehouses();
        loadProducts();
        
        // Filter handling
        $('#warehouseFilter, #inventoryTypeFilter').on('change', function() {
            inventoryTable.draw();
        });
        
        $('#searchFilter').on('keyup', function() {
            inventoryTable.search($(this).val()).draw();
        });
        
        // Add custom filtering to DataTables
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'inventoryTable') return true;
            
            const warehouse = $('#warehouseFilter').val();
            const inventoryType = $('#inventoryTypeFilter').val();
            const rowData = inventoryTable.row(dataIndex).data();
            
            if (warehouse && rowData.WHOUSE_ID != warehouse) return false;
            if (inventoryType && rowData.INVE_TYPE != inventoryType) return false;
            
            return true;
        });
        
        // Action button event handlers
        $('#inventoryTable').on('click', '[data-action]', function() {
            const action = $(this).data('action');
            const id = $(this).data('id');
            
            if (action === 'edit') {
                openEditInventoryModal(id);
            } else if (action === 'delete') {
                openDeleteConfirmModal(id, 'inventory');
            } else if (action === 'move') {
                openMoveStockModal(id);
            }
        });
        
        $('#warehouseTable').on('click', '[data-action]', function() {
            const action = $(this).data('action');
            const id = $(this).data('id');
            
            if (action === 'edit-warehouse') {
                openEditWarehouseModal(id);
            } else if (action === 'delete-warehouse') {
                openDeleteConfirmModal(id, 'warehouse');
            }
        });
        
        $('#lowStockTable').on('click', '[data-action="restock"]', function() {
            const id = $(this).data('id');
            openEditInventoryModal(id);
        });
        
        // Save button event handlers
        $('#saveInventoryBtn').on('click', function() {
            if (!validateForm('addInventoryForm')) return;
            
            const data = {
                product_id: $('#productSelect').val(),
                warehouse_id: $('#warehouseSelect').val(),
                inventory_type: $('#inventoryType').val(),
                quantity: $('#quantityInput').val()
            };
            
            $.ajax({
                url: '/api/inventory/add-stock',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function() {
                    $('#addInventoryModal').modal('hide');
                    showToast('Inventory added successfully', 'success');
                    inventoryTable.ajax.reload();
                    lowStockTable.ajax.reload();
                    loadDashboardData();
                    $('#addInventoryForm')[0].reset();
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to add inventory'}`, 'error');
                }
            });
        });
        
        $('#saveWarehouseBtn').on('click', function() {
            if (!validateForm('addWarehouseForm')) return;
            
            const data = {
                WHOUSE_NAME: $('#warehouseName').val(),
                WHOUSE_LOCATION: $('#warehouseLocation').val(),
                WHOUSE_STORAGE_CAPACITY: $('#storageCapacity').val() || null,
                WHOUSE_RESTOCK_THRESHOLD: $('#restockThreshold').val() || null
            };
            
            $.ajax({
                url: '/api/warehouses',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function() {
                    $('#addWarehouseModal').modal('hide');
                    showToast('Warehouse added successfully', 'success');
                    warehouseTable.ajax.reload();
                    loadWarehouses();
                    loadDashboardData();
                    $('#addWarehouseForm')[0].reset();
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to add warehouse'}`, 'error');
                }
            });
        });
        
        $('#updateInventoryBtn').on('click', function() {
            if (!validateForm('editInventoryForm')) return;
            
            const data = {
                inventory_id: $('#editInventoryId').val(),
                inventory_type: $('#editInventoryType').val(),
                quantity: $('#editQuantityInput').val()
            };
            
            $.ajax({
                url: '/api/inventory/update',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function() {
                    $('#editInventoryModal').modal('hide');
                    showToast('Inventory updated successfully', 'success');
                    inventoryTable.ajax.reload();
                    lowStockTable.ajax.reload();
                    loadDashboardData();
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to update inventory'}`, 'error');
                }
            });
        });
        
        $('#updateWarehouseBtn').on('click', function() {
            if (!validateForm('editWarehouseForm')) return;
            
            const data = {
                WHOUSE_ID: $('#editWarehouseId').val(),
                WHOUSE_NAME: $('#editWarehouseName').val(),
                WHOUSE_LOCATION: $('#editWarehouseLocation').val(),
                WHOUSE_STORAGE_CAPACITY: $('#editStorageCapacity').val() || null,
                WHOUSE_RESTOCK_THRESHOLD: $('#editRestockThreshold').val() || null
            };
            
            $.ajax({
                url: '/api/warehouses/' + data.WHOUSE_ID,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function() {
                    $('#editWarehouseModal').modal('hide');
                    showToast('Warehouse updated successfully', 'success');
                    warehouseTable.ajax.reload();
                    loadWarehouses();
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to update warehouse'}`, 'error');
                }
            });
        });
        
        $('#moveStockBtn').on('click', function() {
            if (!validateForm('moveStockForm')) return;
            
            const data = {
                source_inventory_id: $('#moveInventoryId').val(),
                target_warehouse_id: $('#moveTargetWarehouse').val(),
                quantity: $('#moveQuantity').val()
            };
            
            $.ajax({
                url: '/api/inventory/move-stock',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function() {
                    $('#moveStockModal').modal('hide');
                    showToast('Stock moved successfully', 'success');
                    inventoryTable.ajax.reload();
                    warehouseTable.ajax.reload();
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to move stock'}`, 'error');
                }
            });
        });
        
        $('#confirmDeleteBtn').on('click', function() {
            const id = $('#deleteItemId').val();
            const type = $('#deleteItemType').val();
            
            let url = type === 'warehouse' ? `/api/warehouses/delete/${id}` : `/api/inventory/delete/${id}`;
            
            $.ajax({
                url: url,
                method: 'POST',
                success: function() {
                    $('#deleteConfirmModal').modal('hide');
                    showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully`, 'success');
                    
                    if (type === 'warehouse') {
                        warehouseTable.ajax.reload();
                        loadWarehouses();
                    } else {
                        inventoryTable.ajax.reload();
                        lowStockTable.ajax.reload();
                    }
                    
                    loadDashboardData();
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Deletion failed'}`, 'error');
                }
            });
        });
        
        // Modal helper functions
        function openEditInventoryModal(id) {
            $.ajax({
                url: `/api/inventory/${id}`,
                method: 'GET',
                success: function(data) {
                    $('#editInventoryId').val(data.INVE_ID);
                    $('#editProductName').val(data.PROD_NAME);
                    $('#editWarehouseName').val(data.WHOUSE_NAME);
                    $('#editInventoryType').val(data.INVE_TYPE);
                    $('#editQuantityInput').val(data.QUANTITY);
                    
                    $('#editInventoryModal').modal('show');
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to load inventory data'}`, 'error');
                }
            });
        }
        
        function openEditWarehouseModal(id) {
            $.ajax({
                url: `/api/warehouses/${id}`,
                method: 'GET',
                success: function(data) {
                    $('#editWarehouseId').val(data.WHOUSE_ID);
                    $('#editWarehouseName').val(data.WHOUSE_NAME);
                    $('#editWarehouseLocation').val(data.WHOUSE_LOCATION);
                    $('#editStorageCapacity').val(data.WHOUSE_STORAGE_CAPACITY);
                    $('#editRestockThreshold').val(data.WHOUSE_RESTOCK_THRESHOLD);
                    
                    $('#editWarehouseModal').modal('show');
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to load warehouse data'}`, 'error');
                }
            });
        }
        
        function openMoveStockModal(id) {
            $.ajax({
                url: `/api/inventory/${id}`,
                method: 'GET',
                success: function(data) {
                    $('#moveInventoryId').val(data.INVE_ID);
                    $('#moveProductName').val(data.PROD_NAME);
                    $('#moveSourceWarehouse').val(data.WHOUSE_NAME);
                    $('#moveQuantityAvailable').val(data.QUANTITY);
                    
                    // Load target warehouses (excluding source)
                    $('#moveTargetWarehouse').empty().append('<option value="">Select Warehouse</option>');
                    
                    $.ajax({
                        url: '/api/warehouses',
                        method: 'GET',
                        success: function(warehouses) {
                            warehouses.forEach(function(warehouse) {
                                if (warehouse.WHOUSE_ID != data.WHOUSE_ID) {
                                    $('#moveTargetWarehouse').append(`<option value="${warehouse.WHOUSE_ID}">${warehouse.WHOUSE_NAME}</option>`);
                                }
                            });
                            
                            $('#moveStockModal').modal('show');
                        }
                    });
                },
                error: function(xhr) {
                    showToast(`Error: ${xhr.responseJSON && xhr.responseJSON.message || 'Failed to load inventory data'}`, 'error');
                }
            });
        }
        
        function openDeleteConfirmModal(id, type) {
            $('#deleteItemId').val(id);
            $('#deleteItemType').val(type);
            $('#deleteConfirmMessage').text(`Are you sure you want to delete this ${type}?`);
            $('#deleteConfirmModal').modal('show');
        }
        
        // Helper functions
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }
            return true;
        }
        
        function showToast(message, type = 'info') {
            if (typeof Toastify === 'function') {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === 'success' ? "#198754" : 
                                     type === 'error' ? "#dc3545" : 
                                     type === 'warning' ? "#ffc107" : "#0d6efd"
                }).showToast();
            } else {
                alert(message);
            }
        }
    });
</script>
HTML;

// Render the full page
require_once __DIR__ . '/../includes/admin/base.php';
?> 