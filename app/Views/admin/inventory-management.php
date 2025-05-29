<?php
$title = 'Inventory Management - AirProtect';
$activeTab = 'inventory_management';

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
    
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
</style>
HTML;

// Add any additional scripts specific to this page
$additionalScripts = <<<HTML
<script src="/assets/js/utility/DataTablesManager.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for inventory
    const inventoryTableManager = new DataTablesManager('inventoryTable', {
        ajaxUrl: '/api/inventory',
        columns: [
            { data: 'INVE_ID', title: 'ID', width: '5%' },
            { 
                data: 'PROD_IMAGE', 
                title: 'Image', 
                width: '10%',
                render: function(data) {
                    return '<img src="' + data + '" alt="Product" class="img-fluid" style="max-height: 50px;">';
                }
            },
            { data: 'PROD_NAME', title: 'Product', width: '20%' },
            { data: 'WHOUSE_NAME', title: 'Warehouse', width: '15%' },
            { 
                data: 'INVE_TYPE', 
                title: 'Type', 
                width: '10%',
                render: function(data) {
                    const badgeClasses = {
                        'Regular': 'bg-primary',
                        'Display': 'bg-secondary',
                        'Reserve': 'bg-info',
                        'Damaged': 'bg-danger',
                        'Returned': 'bg-warning',
                        'Quarantine': 'bg-dark'
                    };
                    const badgeClass = badgeClasses[data] ? badgeClasses[data] : 'bg-primary';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { 
                data: 'QUANTITY', 
                title: 'Quantity', 
                width: '10%',
                render: function(data) {
                    return '<span class="fw-bold">' + data + '</span>';
                }
            },
            {
                data: 'LAST_UPDATED',
                title: 'Last Updated',
                width: '15%',
                render: function(data) {
                    return new Date(data).toLocaleString();
                }
            }
        ],
        viewRowCallback: function(rowData) {
            // Show inventory details modal
            showInventoryDetailModal(rowData);
        },
        editRowCallback: function(rowData) {
            // Show edit inventory modal
            showEditInventoryModal(rowData);
        },
        deleteRowCallback: function(rowData) {
            // Handle inventory deletion via API
            fetch('/api/inventory/delete/' + rowData.INVE_ID, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    inventoryTableManager.showSuccessToast('Success', 'Inventory record deleted successfully');
                    inventoryTableManager.refresh();
                } else {
                    inventoryTableManager.showErrorToast('Error', data.message || 'Failed to delete inventory record');
                }
            })
            .catch(error => {
                inventoryTableManager.showErrorToast('Error', 'An error occurred while deleting the inventory record');
                console.error('Error:', error);
            });
        },
        customButtons: {
            addStockButton: {
                text: '<i class="bi bi-plus-circle me-1"></i>Add Stock',
                className: 'btn btn-primary',
                action: function() {
                    showAddStockModal();
                }
            },
            moveStockButton: {
                text: '<i class="bi bi-arrow-left-right me-1"></i>Move Stock',
                className: 'btn btn-outline-primary',
                action: function() {
                    showMoveStockModal();
                }
            },
            refreshButton: {
                text: '<i class="bi bi-arrow-clockwise me-1"></i>Refresh',
                className: 'btn btn-outline-secondary',
                action: function() {
                    inventoryTableManager.refresh();
                    loadInventorySummary();
                }
            }
        }
    });
    
    // Initialize DataTable for low stock
    const lowStockTableManager = new DataTablesManager('lowStockTable', {
        ajaxUrl: '/api/inventory/low-stock',
        columns: [
            { data: 'PROD_NAME', title: 'Product', width: '25%' },
            { data: 'WHOUSE_NAME', title: 'Warehouse', width: '20%' },
            { 
                data: 'QUANTITY', 
                title: 'Current Quantity', 
                width: '15%',
                render: function(data, type, row) {
                    return '<span class="text-danger fw-bold">' + data + '</span>';
                }
            },
            { 
                data: 'WHOUSE_RESTOCK_THRESHOLD', 
                title: 'Threshold', 
                width: '15%' 
            },
            {
                title: 'Actions',
                width: '25%',
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-primary me-2" onclick="showAddStockModal(' + row.PROD_ID + ', ' + row.WHOUSE_ID + ')">Restock</button>' +
                           '<button class="btn btn-sm btn-outline-secondary" onclick="showProductDetails(' + row.PROD_ID + ')">View Product</button>';
                }
            }
        ]
    });
    
    // Load inventory summary and charts
    loadInventorySummary();
});

// Function to load inventory summary statistics
function loadInventorySummary() {
    fetch('/api/inventory/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSummaryCards(data.data);
            if (data.data.warehouse_utilization) {
                createWarehouseUtilizationChart(data.data.warehouse_utilization);
            }
        } else {
            console.error('Failed to load inventory statistics');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Update summary cards with data
function updateSummaryCards(data) {
    if (data.summary) {
        document.getElementById('totalProducts').textContent = data.summary.TOTAL_PRODUCTS || 0;
        document.getElementById('totalWarehouses').textContent = data.summary.TOTAL_WAREHOUSES || 0;
        document.getElementById('totalInventory').textContent = data.summary.TOTAL_INVENTORY || 0;
        document.getElementById('lowStockItems').textContent = data.low_stock_count || 0;
    }
}

// Create warehouse utilization chart
function createWarehouseUtilizationChart(warehouseData) {
    if (!warehouseData || warehouseData.length === 0) return;
    
    const labels = warehouseData.map(w => w.name);
    const utilizationData = warehouseData.map(w => w.utilization_percentage);
    const availableData = warehouseData.map(w => 100 - w.utilization_percentage);
    
    const ctx = document.getElementById('warehouseUtilizationChart').getContext('2d');
    
    if (window.utilizationChart) {
        window.utilizationChart.destroy();
    }
    
    window.utilizationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Used Space (%)',
                    data: utilizationData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Available Space (%)',
                    data: availableData,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Warehouse'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Warehouse Utilization'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
}

// Modal functions (implementation stubs)
function showInventoryDetailModal(rowData) {
    alert('View inventory details: ' + rowData.PROD_NAME + ' at ' + rowData.WHOUSE_NAME);
    // Implement actual modal display logic
}

function showEditInventoryModal(rowData) {
    alert('Edit inventory: ' + rowData.PROD_NAME + ' at ' + rowData.WHOUSE_NAME);
    // Implement actual modal display logic
}

function showAddStockModal(productId, warehouseId) {
    if (productId && warehouseId) {
        alert('Add stock for specific product and warehouse');
    } else {
        alert('Add stock (general)');
    }
    // Implement actual modal display logic
}

function showMoveStockModal() {
    alert('Move stock between warehouses');
    // Implement actual modal display logic
}

function showProductDetails(productId) {
    window.location.href = '/admin/add-product?id=' + productId;
}
</script>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Inventory Management</h1>
            <p class="mb-0 text-muted">Manage inventory across warehouses</p>
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
                                Products</div>
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

        <!-- Total Warehouses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Warehouses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalWarehouses">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-success-soft">
                                <i class="bi bi-building text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Inventory Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Units</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalInventory">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-info-soft">
                                <i class="bi bi-boxes text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lowStockItems">Loading...</div>
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
    </div>

    <!-- Row for Charts -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Warehouse Utilization</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="warehouseUtilizationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Inventory Table -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory</h6>
                </div>
                <div class="card-body">
                    <table id="inventoryTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Type</th>
                                <th>Quantity</th>
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
        </div>

        <!-- Low Stock Table -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header py-3 bg-warning-soft">
                    <h6 class="m-0 font-weight-bold text-warning">Low Stock Items</h6>
                </div>
                <div class="card-body">
                    <table id="lowStockTable" class="table table-sm table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Quantity</th>
                                <th>Threshold</th>
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
    </div>

    <!-- Add Stock Modal (stub) -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form content will go here -->
                    <p>Form for adding stock to inventory</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Add Stock</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Move Stock Modal (stub) -->
    <div class="modal fade" id="moveStockModal" tabindex="-1" aria-labelledby="moveStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveStockModalLabel">Move Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form content will go here -->
                    <p>Form for moving stock between warehouses</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Move Stock</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?> 