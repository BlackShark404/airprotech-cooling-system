<?php
$title = 'Warehouse Management - AirProtect';
$activeTab = 'warehouse_management';

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
    
    .location-label {
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .utilization-container {
        margin-top: 10px;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
    
    .warehouse-card {
        height: 100%;
    }
</style>
HTML;

// Add any additional scripts specific to this page
$additionalScripts = <<<HTML
<script src="/assets/js/utility/DataTablesManager.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for warehouses
    const warehouseTableManager = new DataTablesManager('warehousesTable', {
        ajaxUrl: '/api/warehouses/with-inventory',
        columns: [
            { data: 'WHOUSE_ID', title: 'ID', width: '5%' },
            { data: 'WHOUSE_NAME', title: 'Name', width: '20%' },
            { data: 'WHOUSE_LOCATION', title: 'Location', width: '25%' },
            { 
                data: 'WHOUSE_STORAGE_CAPACITY', 
                title: 'Capacity', 
                width: '10%',
                render: function(data) {
                    return data ? data : 'Unlimited';
                }
            },
            { 
                data: 'TOTAL_INVENTORY', 
                title: 'Current Stock', 
                width: '10%',
                render: function(data) {
                    return '<span class="fw-bold">' + (data || 0) + '</span>';
                }
            },
            { 
                title: 'Utilization', 
                width: '15%',
                render: function(data, type, row) {
                    if (!row.WHOUSE_STORAGE_CAPACITY) {
                        return 'N/A';
                    }
                    
                    const percentage = Math.round((row.TOTAL_INVENTORY || 0) * 100 / row.WHOUSE_STORAGE_CAPACITY);
                    let colorClass = 'bg-success';
                    
                    if (percentage > 90) {
                        colorClass = 'bg-danger';
                    } else if (percentage > 70) {
                        colorClass = 'bg-warning';
                    }
                    
                    return '<div class="utilization-container">' +
                           '<small>' + percentage + '% used</small>' +
                           '<div class="progress">' +
                           '<div class="progress-bar ' + colorClass + '" role="progressbar" style="width: ' + percentage + '%"></div>' +
                           '</div>' +
                           '</div>';
                }
            }
        ],
        viewRowCallback: function(rowData) {
            // Redirect to warehouse detail page
            window.location.href = '/admin/inventory-management?warehouse=' + rowData.WHOUSE_ID;
        },
        editRowCallback: function(rowData) {
            // Show edit warehouse modal
            showEditWarehouseModal(rowData);
        },
        deleteRowCallback: function(rowData) {
            // Handle warehouse deletion via API
            if (rowData.TOTAL_INVENTORY > 0) {
                warehouseTableManager.showErrorToast('Error', 'Cannot delete warehouse with existing inventory');
                return;
            }
            
            fetch('/api/warehouses/delete/' + rowData.WHOUSE_ID, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    warehouseTableManager.showSuccessToast('Success', 'Warehouse deleted successfully');
                    warehouseTableManager.refresh();
                    loadWarehouseSummary();
                } else {
                    warehouseTableManager.showErrorToast('Error', data.message || 'Failed to delete warehouse');
                }
            })
            .catch(error => {
                warehouseTableManager.showErrorToast('Error', 'An error occurred while deleting the warehouse');
                console.error('Error:', error);
            });
        },
        customButtons: {
            addButton: {
                text: '<i class="bi bi-plus-circle me-1"></i>Add Warehouse',
                className: 'btn btn-primary',
                action: function() {
                    showAddWarehouseModal();
                }
            },
            refreshButton: {
                text: '<i class="bi bi-arrow-clockwise me-1"></i>Refresh',
                className: 'btn btn-outline-secondary',
                action: function() {
                    warehouseTableManager.refresh();
                    loadWarehouseSummary();
                }
            }
        }
    });
    
    loadWarehouseSummary();
});

// Function to load warehouse summary data
function loadWarehouseSummary() {
    fetch('/api/warehouses/summary', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalWarehouses').textContent = data.data.total_warehouses || 0;
            document.getElementById('totalCapacity').textContent = data.data.total_capacity || 0;
            document.getElementById('totalStored').textContent = data.data.total_inventory || 0;
            document.getElementById('avgUtilization').textContent = (data.data.avg_utilization || 0) + '%';
        } else {
            console.error('Failed to load warehouse summary');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Modal functions (implementation stubs)
function showAddWarehouseModal() {
    // Reset form
    document.getElementById('addWarehouseForm').reset();
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
    document.getElementById('warehouseModalLabel').textContent = 'Add New Warehouse';
    modal.show();
}

function showEditWarehouseModal(warehouseData) {
    // Fill form with data
    document.getElementById('warehouseId').value = warehouseData.WHOUSE_ID;
    document.getElementById('warehouseName').value = warehouseData.WHOUSE_NAME;
    document.getElementById('warehouseLocation').value = warehouseData.WHOUSE_LOCATION;
    document.getElementById('warehouseCapacity').value = warehouseData.WHOUSE_STORAGE_CAPACITY || '';
    document.getElementById('warehouseThreshold').value = warehouseData.WHOUSE_RESTOCK_THRESHOLD || '';
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
    document.getElementById('warehouseModalLabel').textContent = 'Edit Warehouse';
    modal.show();
}

// Save warehouse function
function saveWarehouse() {
    const warehouseId = document.getElementById('warehouseId').value;
    const isNewWarehouse = !warehouseId;
    
    const warehouseData = {
        WHOUSE_NAME: document.getElementById('warehouseName').value,
        WHOUSE_LOCATION: document.getElementById('warehouseLocation').value,
        WHOUSE_STORAGE_CAPACITY: document.getElementById('warehouseCapacity').value || null,
        WHOUSE_RESTOCK_THRESHOLD: document.getElementById('warehouseThreshold').value || null
    };
    
    // Validate required fields
    if (!warehouseData.WHOUSE_NAME || !warehouseData.WHOUSE_LOCATION) {
        alert('Name and location are required fields');
        return;
    }
    
    const url = isNewWarehouse ? '/api/warehouses' : '/api/warehouses/' + warehouseId;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(warehouseData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide modal
            var modalEl = document.getElementById('warehouseModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            // Refresh data
            const warehouseTableManager = new DataTablesManager('warehousesTable');
            warehouseTableManager.showSuccessToast('Success', isNewWarehouse ? 'Warehouse created successfully' : 'Warehouse updated successfully');
            warehouseTableManager.refresh();
            loadWarehouseSummary();
        } else {
            alert(data.message || 'Failed to save warehouse');
        }
    })
    .catch(error => {
        alert('An error occurred while saving the warehouse');
        console.error('Error:', error);
    });
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
            <h1 class="h3 mb-0 text-gray-800">Warehouse Management</h1>
            <p class="mb-0 text-muted">Manage storage locations for products</p>
        </div>
    </div>

    <!-- Content Row - Summary Cards -->
    <div class="row mb-4">
        <!-- Total Warehouses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Warehouses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalWarehouses">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-primary-soft">
                                <i class="bi bi-building text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Capacity Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Capacity</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCapacity">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-success-soft">
                                <i class="bi bi-box-seam text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stored Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Stored Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStored">Loading...</div>
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

        <!-- Average Utilization Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning card-hover h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg. Utilization</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgUtilization">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-container bg-warning-soft">
                                <i class="bi bi-graph-up text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouses Table -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Warehouses</h6>
        </div>
        <div class="card-body">
            <table id="warehousesTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Current Stock</th>
                        <th>Utilization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Warehouse Form Modal -->
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="warehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="warehouseModalLabel">Add Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addWarehouseForm">
                        <input type="hidden" id="warehouseId">
                        
                        <div class="mb-3">
                            <label for="warehouseName" class="form-label">Warehouse Name*</label>
                            <input type="text" class="form-control" id="warehouseName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="warehouseLocation" class="form-label">Location*</label>
                            <input type="text" class="form-control" id="warehouseLocation" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="warehouseCapacity" class="form-label">Storage Capacity</label>
                            <input type="number" class="form-control" id="warehouseCapacity" min="0" placeholder="Leave empty for unlimited">
                            <div class="form-text">Maximum number of units this warehouse can store</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="warehouseThreshold" class="form-label">Restock Threshold</label>
                            <input type="number" class="form-control" id="warehouseThreshold" min="0">
                            <div class="form-text">Level at which to generate low stock alerts</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveWarehouse()">Save</button>
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