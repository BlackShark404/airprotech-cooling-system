<?php
$title = 'Inventory Management - AirProtech';
$activeTab = 'inventory_management';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
    .inventory-card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .inventory-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .stats-card {
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        background-color: white;
    }
    .stats-card .icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    .stats-card .number {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stats-card .label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    /* Table styling */
    #inventoryTable, #warehouseTable, #lowStockTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #inventoryTable thead th, #warehouseTable thead th, #lowStockTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    #inventoryTable tbody td, #warehouseTable tbody td, #lowStockTable tbody td {
        padding: 15px 8px;
        vertical-align: middle;
    }
    
    #inventoryTable tbody tr:hover, #warehouseTable tbody tr:hover, #lowStockTable tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Inventory Management</h1>
            <p class="text-muted">Manage inventory across all warehouses</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                <i class="bi bi-plus-circle"></i> Add Stock
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                <i class="bi bi-building-add"></i> Add Warehouse
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-light">
                <div class="icon text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="number" id="totalProductsCount">--</div>
                <div class="label">Total Products</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-light">
                <div class="icon text-success">
                    <i class="bi bi-buildings"></i>
                </div>
                <div class="number" id="totalWarehousesCount">--</div>
                <div class="label">Warehouses</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-light">
                <div class="icon text-info">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="number" id="totalInventoryCount">--</div>
                <div class="label">Total Items in Stock</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-light">
                <div class="icon text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="number" id="lowStockCount">--</div>
                <div class="label">Low Stock Items</div>
            </div>
        </div>
    </div>

    <!-- Tabs for Inventory and Warehouses -->
    <ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory-pane" type="button" role="tab" aria-controls="inventory-pane" aria-selected="true">Inventory</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="warehouses-tab" data-bs-toggle="tab" data-bs-target="#warehouses-pane" type="button" role="tab" aria-controls="warehouses-pane" aria-selected="false">Warehouses</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="low-stock-tab" data-bs-toggle="tab" data-bs-target="#low-stock-pane" type="button" role="tab" aria-controls="low-stock-pane" aria-selected="false">Low Stock</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="inventoryTabContent">
        <!-- Inventory Tab -->
        <div class="tab-pane fade show active" id="inventory-pane" role="tabpanel" aria-labelledby="inventory-tab">
            <!-- Filters Card -->
            <div class="card filter-card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Filters</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="warehouseFilter" class="form-label">Warehouse</label>
                            <select id="warehouseFilter" class="form-select filter-dropdown">
                                <option value="">All Warehouses</option>
                                <!-- To be populated by AJAX -->
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="inventoryTypeFilter" class="form-label">Inventory Type</label>
                            <select id="inventoryTypeFilter" class="form-select filter-dropdown">
                                <option value="">All Types</option>
                                <option value="Regular">Regular</option>
                                <option value="Reserved">Reserved</option>
                                <option value="Damaged">Damaged</option>
                                <option value="Returns">Returns</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inventoryTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Quantity</th>
                                    <th>Type</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warehouses Tab -->
        <div class="tab-pane fade" id="warehouses-pane" role="tabpanel" aria-labelledby="warehouses-tab">
            <!-- Warehouses Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="warehouseTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Storage Capacity</th>
                                    <th>Current Inventory</th>
                                    <th>Utilization</th>
                                    <th>Restock Threshold</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Tab -->
        <div class="tab-pane fade" id="low-stock-pane" role="tabpanel" aria-labelledby="low-stock-tab">
            <!-- Low Stock Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lowStockTable" class="table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Current Quantity</th>
                                    <th>Threshold</th>
                                    <th>Restock Needed</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStockModalLabel">Add Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStockForm">
                    <div class="mb-3">
                        <label for="productId" class="form-label">Product</label>
                        <select id="productId" name="product_id" class="form-select" required>
                            <option value="">Select Product</option>
                            <!-- Options will be loaded by AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseId" class="form-label">Warehouse</label>
                        <select id="warehouseId" name="warehouse_id" class="form-select" required>
                            <option value="">Select Warehouse</option>
                            <!-- Options will be loaded by AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="inventoryType" class="form-label">Inventory Type</label>
                        <select id="inventoryType" name="inventory_type" class="form-select">
                            <option value="Regular">Regular</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Returns">Returns</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStockBtn">Add Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Warehouse Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1" aria-labelledby="addWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWarehouseModalLabel">Add Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addWarehouseForm">
                    <div class="mb-3">
                        <label for="warehouseName" class="form-label">Warehouse Name</label>
                        <input type="text" class="form-control" id="warehouseName" name="warehouse_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="warehouseLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="warehouseLocation" name="warehouse_location" required>
                    </div>
                    <div class="mb-3">
                        <label for="storageCapacity" class="form-label">Storage Capacity</label>
                        <input type="number" class="form-control" id="storageCapacity" name="storage_capacity" min="1">
                        <small class="form-text text-muted">Maximum number of items that can be stored</small>
                    </div>
                    <div class="mb-3">
                        <label for="restockThreshold" class="form-label">Restock Threshold</label>
                        <input type="number" class="form-control" id="restockThreshold" name="restock_threshold" min="0">
                        <small class="form-text text-muted">Minimum inventory level before restocking is required</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveWarehouseBtn">Add Warehouse</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Warehouse Modal -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWarehouseModalLabel">Edit Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editWarehouseForm">
                    <input type="hidden" id="editWarehouseId" name="warehouse_id">
                    <div class="mb-3">
                        <label for="editWarehouseName" class="form-label">Warehouse Name</label>
                        <input type="text" class="form-control" id="editWarehouseName" name="warehouse_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editWarehouseLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="editWarehouseLocation" name="warehouse_location" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStorageCapacity" class="form-label">Storage Capacity</label>
                        <input type="number" class="form-control" id="editStorageCapacity" name="storage_capacity" min="1">
                        <small class="form-text text-muted">Maximum number of items that can be stored</small>
                    </div>
                    <div class="mb-3">
                        <label for="editRestockThreshold" class="form-label">Restock Threshold</label>
                        <input type="number" class="form-control" id="editRestockThreshold" name="restock_threshold" min="0">
                        <small class="form-text text-muted">Minimum inventory level before restocking is required</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateWarehouseBtn">Update Warehouse</button>
            </div>
        </div>
    </div>
</div>

<!-- Move Stock Modal -->
<div class="modal fade" id="moveStockModal" tabindex="-1" aria-labelledby="moveStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveStockModalLabel">Move Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="moveStockForm">
                    <input type="hidden" id="sourceInventoryId" name="source_inventory_id">
                    <div class="mb-3">
                        <label for="productDetails" class="form-label">Product</label>
                        <input type="text" class="form-control" id="productDetails" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="sourceWarehouse" class="form-label">Source Warehouse</label>
                        <input type="text" class="form-control" id="sourceWarehouse" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="availableQuantity" class="form-label">Available Quantity</label>
                        <input type="number" class="form-control" id="availableQuantity" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="targetWarehouseId" class="form-label">Target Warehouse</label>
                        <select id="targetWarehouseId" name="target_warehouse_id" class="form-select" required>
                            <option value="">Select Target Warehouse</option>
                            <!-- Options will be loaded by AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="moveQuantity" class="form-label">Quantity to Move</label>
                        <input type="number" class="form-control" id="moveQuantity" name="quantity" min="1" required>
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

<!-- View Inventory Details Modal -->
<div class="modal fade" id="viewInventoryModal" tabindex="-1" aria-labelledby="viewInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewInventoryModalLabel">Inventory Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img id="productImage" src="" alt="Product Image" class="img-fluid rounded">
                    </div>
                    <div class="col-md-8">
                        <h4 id="productName"></h4>
                        <p id="productDescription" class="text-muted"></p>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6>Inventory Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Warehouse:</th>
                                        <td id="detailWarehouse"></td>
                                    </tr>
                                    <tr>
                                        <th>Quantity:</th>
                                        <td id="detailQuantity"></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td id="detailType"></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td id="detailStatus"></td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td id="detailUpdated"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Warehouse Details Modal -->
<div class="modal fade" id="viewWarehouseModal" tabindex="-1" aria-labelledby="viewWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewWarehouseModalLabel">Warehouse Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Warehouse ID:</th>
                            <td id="detailWhId"></td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td id="detailWhName"></td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td id="detailWhLocation"></td>
                        </tr>
                        <tr>
                            <th>Storage Capacity:</th>
                            <td id="detailWhCapacity"></td>
                        </tr>
                        <tr>
                            <th>Current Inventory (Items):</th>
                            <td id="detailWhCurrentStock"></td>
                        </tr>
                        <tr>
                            <th>Utilization:</th>
                            <td id="detailWhUtilization"></td>
                        </tr>
                        <tr>
                            <th>Restock Threshold:</th>
                            <td id="detailWhThreshold"></td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td id="detailWhCreatedAt"></td>
                        </tr>
                        <tr>
                            <th>Last Updated At:</th>
                            <td id="detailWhUpdatedAt"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Warehouse Confirmation Modal -->
<div class="modal fade" id="deleteWarehouseModal" tabindex="-1" aria-labelledby="deleteWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteWarehouseModalLabel">Confirm Warehouse Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the following warehouse? This action cannot be undone.</p>
                <p><strong>ID:</strong> <span id="deleteWarehouseIdSpan"></span></p>
                <p><strong>Name:</strong> <span id="deleteWarehouseNameSpan"></span></p>
                <p><strong>Location:</strong> <span id="deleteWarehouseLocationSpan"></span></p>
                <div id="deleteWarehouseWarningInventory" class="alert alert-warning d-none" role="alert">
                    This warehouse still contains inventory. Deletion is not allowed.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteWarehouseBtn">Delete Warehouse</button>
            </div>
        </div>
    </div>
</div>

<?php
// End output buffering and get the content for the main page body
$content = ob_get_clean();

// Start output buffering for additional scripts
ob_start();
?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/utility/DataTablesManager.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        let inventoryTable;
        let warehouseTable;
        let lowStockTable;
        
        // Load inventory data
        function initInventoryTable() {
            inventoryTable = new DataTablesManager('inventoryTable', {
                ajaxUrl: '/api/inventory',
                columns: [
                    { data: 'inve_id', title: 'ID' },
                    { data: 'prod_name', title: 'Product' },
                    { data: 'whouse_name', title: 'Warehouse' },
                    { data: 'quantity', title: 'Quantity' },
                    { 
                        data: 'inve_type', 
                        title: 'Type',
                        badge: {
                            type: 'secondary',
                            pill: true
                        }
                    },
                    { data: 'inve_updated_at', title: 'Last Updated' },
                ],
                viewRowCallback: viewInventory,
                editRowCallback: openMoveStockModal, // Typically 'edit' action, repurposed for 'move stock'
                deleteRowCallback: confirmDeleteInventory
            });
        }
        
        // Load warehouse data
        function initWarehouseTable() {
            warehouseTable = new DataTablesManager('warehouseTable', {
                ajaxUrl: '/api/warehouses',
                columns: [
                    { data: 'whouse_id', title: 'ID' },
                    { data: 'whouse_name', title: 'Name' },
                    { data: 'whouse_location', title: 'Location' },
                    { data: 'whouse_storage_capacity', title: 'Capacity' },
                    { data: 'total_inventory', title: 'Current Stock' },
                    { 
                        data: 'utilization_percentage', 
                        title: 'Utilization',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const num = parseFloat(data);
                                let badgeType = 'success';
                                if (isNaN(num)) return 'N/A';
                                if (num >= 90) badgeType = 'danger';
                                else if (num >= 70) badgeType = 'warning';
                                return `<span class="badge bg-${badgeType}">${num.toFixed(1)}%</span>`;
                            }
                            return data;
                        }
                    },
                    { data: 'whouse_restock_threshold', title: 'Threshold' },
                    {
                        data: null,
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-info view-warehouse-btn" title="View Warehouse" data-id="${row.whouse_id}">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning edit-warehouse-btn" title="Edit Warehouse" data-id="${row.whouse_id}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-warehouse-btn" title="Delete Warehouse" data-id="${row.whouse_id}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });
        }
        
        // Load low stock data
        function initLowStockTable() {
            lowStockTable = new DataTablesManager('lowStockTable', {
                ajaxUrl: '/api/inventory/low-stock',
                columns: [
                    { data: 'inve_id', title: 'ID' },
                    { data: 'prod_name', title: 'Product' },
                    { data: 'whouse_name', title: 'Warehouse' },
                    { data: 'quantity', title: 'Current Quantity' },
                    { data: 'whouse_restock_threshold', title: 'Threshold' },
                    { 
                        data: null, // Calculate Restock Needed
                        title: 'Restock Needed',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const needed = parseInt(row.whouse_restock_threshold) - parseInt(row.quantity);
                                if (needed > 0) {
                                    return `<span class="badge bg-danger">${needed}</span>`;
                                }
                                return '<span class="badge bg-success">OK</span>';
                            }
                            return parseInt(row.whouse_restock_threshold) - parseInt(row.quantity);
                        }
                    },
                ],
                viewRowCallback: viewInventory,
                editRowCallback: restockItem // Action to initiate restock for this item
            });
        }
        
        // Load summary data for dashboard
        function loadSummaryData() {
            fetch('/api/inventory/summary')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        console.log('Summary data received:', data.data); // Debug log
                        
                        // Handle both upper and lowercase keys for robustness
                        const summaryData = data.data;
                        
                        // Total Products
                        document.getElementById('totalProductsCount').textContent = 
                            summaryData.TOTAL_PRODUCTS || summaryData.total_products || 0;
                        
                        // Total Warehouses
                        document.getElementById('totalWarehousesCount').textContent = 
                            summaryData.TOTAL_WAREHOUSES || summaryData.total_warehouses || 0;
                        
                        // Total Inventory
                        document.getElementById('totalInventoryCount').textContent = 
                            summaryData.TOTAL_INVENTORY || summaryData.total_inventory || 0;
                        
                        // Low Stock Items
                        document.getElementById('lowStockCount').textContent = 
                            summaryData.LOW_STOCK_ITEMS || summaryData.low_stock_items || 0;
                    } else {
                        console.warn('Failed to load summary data or no data available:', data.message);
                        inventoryTable.showErrorToast('Warning', 'Could not load summary statistics.');
                    }
                })
                .catch(error => {
                    console.error('Error loading summary data:', error);
                    inventoryTable.showErrorToast('Error', 'Failed to load summary statistics.');
                });
        }
        
        // Initialize tables and load initial data
        if (document.getElementById('inventoryTable')) initInventoryTable();
        if (document.getElementById('warehouseTable')) initWarehouseTable();
        if (document.getElementById('lowStockTable')) initLowStockTable();
        loadSummaryData();
        
        // Load product and warehouse dropdowns for modals
        loadProductsForModal();
        loadWarehousesForModalsAndFilters();
        
        // Event listeners for tab switching to refresh tables if needed
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                const targetPaneId = event.target.getAttribute('data-bs-target');
                if (targetPaneId === '#inventory-pane' && inventoryTable) {
                    inventoryTable.refresh();
                } else if (targetPaneId === '#warehouses-pane' && warehouseTable) {
                    warehouseTable.refresh();
                } else if (targetPaneId === '#low-stock-pane' && lowStockTable) {
                    lowStockTable.refresh();
                }
            });
        });
        
        // Event listener for add stock form
        document.getElementById('saveStockBtn').addEventListener('click', addStock);
        // Event listener for add warehouse form
        document.getElementById('saveWarehouseBtn').addEventListener('click', addWarehouse);
        // Event listener for update warehouse form
        document.getElementById('updateWarehouseBtn').addEventListener('click', updateWarehouse);
        // Event listener for move stock form
        document.getElementById('moveStockBtn').addEventListener('click', moveStock);
        
        // Handle filter changes for Inventory table
        document.getElementById('warehouseFilter').addEventListener('change', applyInventoryFilters);
        document.getElementById('inventoryTypeFilter').addEventListener('change', applyInventoryFilters);
        
        // Function to load products for Add Stock modal dropdown
        function loadProductsForModal() {
            fetch('/api/products')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const productSelect = document.getElementById('productId');
                        productSelect.innerHTML = '<option value="">Select Product</option>'; 
                        data.data.forEach(product => {
                            const option = document.createElement('option');
                            option.value = product.prod_id;
                            option.textContent = product.prod_name;
                            productSelect.appendChild(option);
                        });
                    } else {
                         inventoryTable.showErrorToast('Error', 'Failed to load products for dropdown.');
                    }
                })
                .catch(error => {
                    console.error('Error loading products for modal:', error);
                    inventoryTable.showErrorToast('Error', 'Failed to load products for dropdown.');
                });
        }
        
        // Function to load warehouses for dropdowns (modals and filters)
        function loadWarehousesForModalsAndFilters() {
            fetch('/api/warehouses') 
                .then(response => response.json())
                .then(data => {
                    if (data.success && Array.isArray(data.data)) {
                        const warehouses = data.data;
                        const addStockWarehouseSelect = document.getElementById('warehouseId'); // Corrected ID for Add Stock Modal
                        const filterWarehouseSelect = document.getElementById('warehouseFilter'); // Correct ID for inventory filter

                        // Clear existing options, only if the element exists
                        if (addStockWarehouseSelect) {
                            addStockWarehouseSelect.innerHTML = '<option value="">Select Warehouse</option>';
                        }
                        if (filterWarehouseSelect) {
                            filterWarehouseSelect.innerHTML = '<option value="">All Warehouses</option>';
                        }

                        warehouses.forEach(warehouse => {
                            const option = document.createElement('option');
                            option.value = warehouse.whouse_id; 
                            option.textContent = warehouse.whouse_name; 
                            
                            if (addStockWarehouseSelect) {
                                addStockWarehouseSelect.appendChild(option.cloneNode(true));
                            }
                            if (filterWarehouseSelect) {
                                filterWarehouseSelect.appendChild(option.cloneNode(true));
                            }
                        });
                    } else {
                        console.error('Failed to load warehouses for dropdowns:', data.message || 'No data returned');
                        // Optionally show a toast or message to the user if a global toast manager is available
                        // e.g., if (typeof globalToastManager !== 'undefined') globalToastManager.showErrorToast('Error', 'Could not load warehouses for selection.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching warehouses for dropdowns:', error);
                    // Optionally show a toast or message to the user
                });
        }
        
        // Function to add stock
        function addStock() {
            const form = document.getElementById('addStockForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = {
                product_id: document.getElementById('productId').value,
                warehouse_id: document.getElementById('warehouseId').value,
                quantity: parseInt(document.getElementById('quantity').value),
                inventory_type: document.getElementById('inventoryType').value
            };
            
            fetch('/api/inventory/add-stock', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                // Check if the response is valid JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, get the text and throw an error with the text content
                    return response.text().then(text => {
                        console.error("Non-JSON response:", text);
                        throw new Error('Error adding stock: ' + (text.substring(0, 100) + '...'));
                    });
                }
            })
            .then(result => {
                if (result.success) {
                    $('#addStockModal').modal('hide');
                    
                    // Refresh the relevant tables
                    if(inventoryTable) inventoryTable.refresh();
                    
                    // Check if the item might have moved out of low stock based on the response
                    if (result.data && result.data.is_low_stock === false) {
                        console.log("Item no longer in low stock after update");
                    }
                    
                    // Always refresh low stock table
                    if(lowStockTable) lowStockTable.refresh();
                    
                    inventoryTable.showSuccessToast('Success', 'Stock added successfully');
                    form.reset();
                    loadSummaryData();
                } else {
                    inventoryTable.showErrorToast('Error', result.message || 'Failed to add stock');
                }
            })
            .catch(error => {
                console.error('Error adding stock:', error);
                inventoryTable.showErrorToast('Error', error.message || 'An error occurred while adding stock');
            });
        }
        
        // Function to add warehouse
        function addWarehouse() {
            const form = document.getElementById('addWarehouseForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = {
                WHOUSE_NAME: document.getElementById('warehouseName').value,
                WHOUSE_LOCATION: document.getElementById('warehouseLocation').value,
                WHOUSE_STORAGE_CAPACITY: document.getElementById('storageCapacity').value ? parseInt(document.getElementById('storageCapacity').value) : null,
                WHOUSE_RESTOCK_THRESHOLD: document.getElementById('restockThreshold').value ? parseInt(document.getElementById('restockThreshold').value) : null
            };
            
            fetch('/api/warehouses', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    $('#addWarehouseModal').modal('hide');
                    if(warehouseTable) warehouseTable.refresh();
                    warehouseTable.showSuccessToast('Success', 'Warehouse added successfully');
                    form.reset();
                    loadWarehousesForModalsAndFilters(); // Reload warehouses in dropdowns
                    loadSummaryData();
                } else {
                    warehouseTable.showErrorToast('Error', result.message || 'Failed to add warehouse');
                }
            })
            .catch(error => {
                console.error('Error adding warehouse:', error);
                warehouseTable.showErrorToast('Error', 'An error occurred while adding warehouse');
            });
        }
        
        // Function to edit warehouse (show modal with data)
        function editWarehouse(rowData) {
            document.getElementById('editWarehouseId').value = rowData.whouse_id;
            document.getElementById('editWarehouseName').value = rowData.whouse_name;
            document.getElementById('editWarehouseLocation').value = rowData.whouse_location;
            document.getElementById('editStorageCapacity').value = rowData.whouse_storage_capacity || '';
            document.getElementById('editRestockThreshold').value = rowData.whouse_restock_threshold || '';
            $('#editWarehouseModal').modal('show');
        }
        
        // Function to update warehouse
        function updateWarehouse() {
            const form = document.getElementById('editWarehouseForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const warehouseId = document.getElementById('editWarehouseId').value;
            const data = {
                WHOUSE_NAME: document.getElementById('editWarehouseName').value,
                WHOUSE_LOCATION: document.getElementById('editWarehouseLocation').value,
                WHOUSE_STORAGE_CAPACITY: document.getElementById('editStorageCapacity').value ? parseInt(document.getElementById('editStorageCapacity').value) : null,
                WHOUSE_RESTOCK_THRESHOLD: document.getElementById('editRestockThreshold').value ? parseInt(document.getElementById('editRestockThreshold').value) : null
            };
            
            fetch(`/api/warehouses/${warehouseId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    $('#editWarehouseModal').modal('hide');
                    if(warehouseTable) warehouseTable.refresh();
                    warehouseTable.showSuccessToast('Success', 'Warehouse updated successfully');
                    loadWarehousesForModalsAndFilters(); // Refresh warehouse names in filters if changed
                    loadSummaryData(); // Utilization might change
                } else {
                    warehouseTable.showErrorToast('Error', result.message || 'Failed to update warehouse');
                }
            })
            .catch(error => {
                console.error('Error updating warehouse:', error);
                warehouseTable.showErrorToast('Error', 'An error occurred while updating warehouse');
            });
        }
        
        // Function to view inventory details
        function viewInventory(rowData) {
            fetch(`/api/inventory/${rowData.inve_id}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        const item = result.data;
                        console.log('Inventory item data:', item); // Debug log
                        
                        // Product name and description
                        document.getElementById('productName').textContent = item.prod_name || 'N/A';
                        document.getElementById('productDescription').textContent = item.prod_description || 'No description available.';
                        
                        // Handle product image with proper URL path
                        if (item.prod_image) {
                            // Ensure the image path starts with a slash if it doesn't already
                            let imagePath = item.prod_image;
                            if (!imagePath.startsWith('/') && !imagePath.startsWith('http')) {
                                imagePath = '/' + imagePath;
                            }
                            document.getElementById('productImage').src = imagePath;
                        } else {
                            document.getElementById('productImage').src = '/assets/images/placeholder.png';
                        }
                        
                        // Inventory details
                        document.getElementById('detailWarehouse').textContent = item.whouse_name || 'N/A';
                        document.getElementById('detailQuantity').textContent = item.quantity || '0';
                        document.getElementById('detailType').textContent = item.inve_type || 'N/A';
                        document.getElementById('detailStatus').textContent = item.prod_availability_status || 'N/A';
                        document.getElementById('detailUpdated').textContent = item.inve_updated_at ? new Date(item.inve_updated_at).toLocaleString() : 'N/A';
                        
                        $('#viewInventoryModal').modal('show');
                    } else {
                        inventoryTable.showErrorToast('Error', result.message || 'Failed to load inventory details');
                    }
                })
                .catch(error => {
                    console.error('Error loading inventory details:', error);
                    inventoryTable.showErrorToast('Error', 'An error occurred loading inventory details');
                });
        }
        
        // Function to delete inventory (with confirmation)
        function confirmDeleteInventory(rowData) {
            if (confirm(`Are you sure you want to delete this inventory record for "${rowData.prod_name}" in "${rowData.whouse_name}"? Quantity: ${rowData.quantity}. This cannot be undone.`)) {
                fetch(`/api/inventory/${rowData.inve_id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        if(inventoryTable) inventoryTable.refresh();
                        inventoryTable.showSuccessToast('Success', 'Inventory record deleted');
                        loadSummaryData();
                        if (lowStockTable) lowStockTable.refresh();
                    } else {
                        inventoryTable.showErrorToast('Error', result.message || 'Failed to delete inventory');
                    }
                })
                .catch(error => {
                    console.error('Error deleting inventory:', error);
                    inventoryTable.showErrorToast('Error', 'An error occurred while deleting inventory');
                });
            }
        }
        
        // Function to prepare move stock modal
        function openMoveStockModal(rowData) {
            document.getElementById('sourceInventoryId').value = rowData.inve_id;
            document.getElementById('productDetails').value = `${rowData.prod_name} (ID: ${rowData.prod_id})`; // More info
            document.getElementById('sourceWarehouse').value = rowData.whouse_name;
            document.getElementById('availableQuantity').value = rowData.quantity;
            document.getElementById('moveQuantity').value = '1'; // Default to 1
            document.getElementById('moveQuantity').max = rowData.quantity;
            
            const targetWarehouseSelect = document.getElementById('targetWarehouseId');
            // Temporarily store current options to re-add non-source ones
            const options = Array.from(targetWarehouseSelect.options);
            targetWarehouseSelect.innerHTML = '<option value="">Select Target Warehouse</option>'; 

            fetch('/api/warehouses') // Re-fetch or use pre-loaded ones filtered
                .then(response => response.json())
                .then(warehouseData => {
                    if (warehouseData.success && warehouseData.data) {
                         warehouseData.data.forEach(warehouse => {
                            if (warehouse.whouse_id != rowData.whouse_id) { // Exclude source warehouse
                                const option = document.createElement('option');
                                option.value = warehouse.whouse_id;
                                option.textContent = warehouse.whouse_name;
                                targetWarehouseSelect.appendChild(option);
                            }
                        });
                    } else {
                        inventoryTable.showErrorToast('Error', 'Could not load target warehouses.');
                    }
                }).catch(err => inventoryTable.showErrorToast('Error', 'Could not load target warehouses.'));
            
            $('#moveStockModal').modal('show');
        }
        
        // Function to move stock
        function moveStock() {
            const form = document.getElementById('moveStockForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const quantity = parseInt(document.getElementById('moveQuantity').value);
            const availableQuantity = parseInt(document.getElementById('availableQuantity').value);
            
            if (quantity <= 0) {
                inventoryTable.showErrorToast('Error', 'Quantity to move must be positive.');
                return;
            }
            if (quantity > availableQuantity) {
                inventoryTable.showErrorToast('Error', 'Cannot move more than available quantity.');
                return;
            }
            
            // Log the data being sent for debugging
            const sourceInventoryId = document.getElementById('sourceInventoryId').value;
            const targetWarehouseId = document.getElementById('targetWarehouseId').value;
            
            console.log("Moving stock with parameters:", {
                source_inventory_id: sourceInventoryId,
                target_warehouse_id: targetWarehouseId, 
                quantity: quantity,
                available_quantity: availableQuantity
            });
            
            // Store original quantity for verification
            const originalQuantity = availableQuantity;
            
            const data = {
                source_inventory_id: sourceInventoryId,
                target_warehouse_id: targetWarehouseId,
                quantity: quantity
            };
            
            fetch('/api/inventory/move-stock', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                // Check if the response is valid JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, get the text and throw an error with the text content
                    return response.text().then(text => {
                        console.error("Non-JSON response:", text);
                        throw new Error('Invalid server response: ' + (text.substring(0, 100) + '...'));
                    });
                }
            })
            .then(result => {
                console.log("Move stock API response:", result);
                if (result.success) {
                    // Verify that quantities are correct
                    if (result.data && result.data.source_remaining !== undefined && result.data.target_quantity !== undefined) {
                        console.log("Quantity verification:", {
                            original_quantity: originalQuantity,
                            moved_quantity: quantity,
                            source_remaining: result.data.source_remaining,
                            target_quantity: result.data.target_quantity,
                            total_after: result.data.source_remaining + result.data.target_quantity
                        });
                        
                        // Check if total quantity after move matches expected total
                        const totalAfter = parseInt(result.data.source_remaining) + parseInt(result.data.target_quantity);
                        if (totalAfter !== originalQuantity) {
                            console.warn("Quantity mismatch after move! Original:", originalQuantity, "Total after:", totalAfter);
                        }
                    }
                    
                    $('#moveStockModal').modal('hide');
                    if(inventoryTable) inventoryTable.refresh();
                    inventoryTable.showSuccessToast('Success', 'Stock moved successfully');
                    if (warehouseTable) warehouseTable.refresh(); // Refresh warehouse stock levels
                    if (lowStockTable) lowStockTable.refresh();
                    loadSummaryData();
                } else {
                    inventoryTable.showErrorToast('Error', result.message || 'Failed to move stock');
                }
            })
            .catch(error => {
                console.error('Error moving stock:', error);
                inventoryTable.showErrorToast('Error', 'An error occurred while moving stock. ' + error.message);
            });
        }
        
        // Function to view warehouse details (placeholder, can be expanded to a modal)
        function viewWarehouseDetails(rowData) {
            // Populate the modal with data from rowData
            document.getElementById('detailWhId').textContent = rowData.whouse_id || 'N/A';
            document.getElementById('detailWhName').textContent = rowData.whouse_name || 'N/A';
            document.getElementById('detailWhLocation').textContent = rowData.whouse_location || 'N/A';
            document.getElementById('detailWhCapacity').textContent = rowData.whouse_storage_capacity || 'N/A';
            document.getElementById('detailWhCurrentStock').textContent = rowData.total_inventory || '0';
            
            // Format Utilization Percentage with a badge
            const utilizationPercent = parseFloat(rowData.utilization_percentage);
            let utilizationHtml = 'N/A';
            if (!isNaN(utilizationPercent)) {
                let badgeType = 'success';
                if (utilizationPercent >= 90) badgeType = 'danger';
                else if (utilizationPercent >= 70) badgeType = 'warning';
                utilizationHtml = `<span class="badge bg-${badgeType}">${utilizationPercent.toFixed(1)}%</span>`;
            }
            document.getElementById('detailWhUtilization').innerHTML = utilizationHtml;

            document.getElementById('detailWhThreshold').textContent = rowData.whouse_restock_threshold || 'N/A';
            document.getElementById('detailWhCreatedAt').textContent = rowData.whouse_created_at ? new Date(rowData.whouse_created_at).toLocaleString() : 'N/A';
            document.getElementById('detailWhUpdatedAt').textContent = rowData.whouse_updated_at ? new Date(rowData.whouse_updated_at).toLocaleString() : 'N/A';

            // Show the modal
            $('#viewWarehouseModal').modal('show');
        }
        
        // Function to initiate restocking an item (opens Add Stock modal pre-filled)
        function restockItem(rowData) {
            document.getElementById('productId').value = rowData.prod_id;
            document.getElementById('warehouseId').value = rowData.whouse_id;
            const currentQuantity = parseInt(rowData.quantity);
            const threshold = parseInt(rowData.whouse_restock_threshold);
            let suggestedQuantity = 1;
            if (!isNaN(threshold) && threshold > currentQuantity) {
                suggestedQuantity = Math.max(threshold - currentQuantity, 1);
            }
            document.getElementById('quantity').value = suggestedQuantity;
            document.getElementById('inventoryType').value = 'Regular'; // Default to Regular for restock
            $('#addStockModal').modal('show');
        }
        
        // Function to apply filters to the Inventory table
        function applyInventoryFilters() {
            const filters = {};
            const warehouseId = document.getElementById('warehouseFilter').value;
            const inventoryType = document.getElementById('inventoryTypeFilter').value;
            
            if (warehouseId) filters.whouse_id = warehouseId;
            if (inventoryType) filters.inve_type = inventoryType;
            
            if(inventoryTable) inventoryTable.applyFilters(filters);
        }

        // --- New/Modified Event Listeners and Functions for Warehouse Delete ---
        $('#warehouseTable').on('click', '.view-warehouse-btn', function() {
            const id = $(this).data('id');
            const rowData = warehouseTable.dataTable.row($(this).closest('tr')).data();
            if(rowData) viewWarehouseDetails(rowData); // Call existing view function
        });

        $('#warehouseTable').on('click', '.edit-warehouse-btn', function() {
            const id = $(this).data('id');
            const rowData = warehouseTable.dataTable.row($(this).closest('tr')).data();
            if(rowData) editWarehouse(rowData); // Call existing edit function
        });

        $('#warehouseTable').on('click', '.delete-warehouse-btn', function() {
            const rowData = warehouseTable.dataTable.row($(this).closest('tr')).data();
            if (rowData) {
                confirmDeleteWarehouseModal(rowData);
            }
        });

        let warehouseToDeleteData = null; // To store rowData for the delete operation

        function confirmDeleteWarehouseModal(rowData) {
            console.log("[DEBUG] confirmDeleteWarehouseModal called. rowData:", rowData);
            warehouseToDeleteData = rowData; // Store for later use by the actual delete function

            document.getElementById('deleteWarehouseIdSpan').textContent = rowData.whouse_id || 'N/A';
            document.getElementById('deleteWarehouseNameSpan').textContent = rowData.whouse_name || 'N/A';
            document.getElementById('deleteWarehouseLocationSpan').textContent = rowData.whouse_location || 'N/A';
            
            const warningDiv = document.getElementById('deleteWarehouseWarningInventory');
            const confirmBtn = document.getElementById('confirmDeleteWarehouseBtn');

            const currentStock = parseInt(rowData.total_inventory);
            if (currentStock > 0) {
                console.log("[DEBUG] Inventory exists (", currentStock, "), showing warning in modal.");
                warningDiv.classList.remove('d-none');
                confirmBtn.disabled = true;
                $(confirmBtn).prop('title', 'Cannot delete warehouse with inventory');
            } else {
                console.log("[DEBUG] Inventory check passed for modal (stock <= 0).");
                warningDiv.classList.add('d-none');
                confirmBtn.disabled = false;
                $(confirmBtn).prop('title', '');
            }
            
            $('#deleteWarehouseModal').modal('show');
        }

        document.getElementById('confirmDeleteWarehouseBtn').addEventListener('click', function() {
            if (warehouseToDeleteData) {
                performActualWarehouseDelete(warehouseToDeleteData);
            }
        });
        
        // Renamed from preDeleteWarehouseChecks and performWarehouseDelete to avoid confusion
        // This function is now called by the modal's confirm button
        function performActualWarehouseDelete(rowData) {
            console.log("[DEBUG] performActualWarehouseDelete called for whouse_id:", rowData.whouse_id);
            fetch(`/api/warehouses/${rowData.whouse_id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(result => {
                $('#deleteWarehouseModal').modal('hide');
                if (result.success) {
                    if(warehouseTable) warehouseTable.refresh();
                    warehouseTable.showSuccessToast('Success', 'Warehouse deleted successfully');
                    loadWarehousesForModalsAndFilters(); 
                    loadSummaryData();
                } else {
                    warehouseTable.showErrorToast('Delete Error', result.message || 'Failed to delete warehouse');
                }
            })
            .catch(error => {
                $('#deleteWarehouseModal').modal('hide');
                console.error('Error deleting warehouse:', error);
                warehouseTable.showErrorToast('Delete Error', 'An error occurred while deleting warehouse.');
            });
            warehouseToDeleteData = null; // Clear stored data
        }
    });
</script>
<?php
$additionalScripts = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?> 