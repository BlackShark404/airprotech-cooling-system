/**
 * InventoryViewDataTables.js
 * Handles the DataTables initialization and functionality for the inventory view
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for inventory view
    const inventoryTable = $('#inventoryTable').DataTable({
        processing: true,
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        language: {
            emptyTable: "No inventory data available",
            info: "Showing _START_ to _END_ of _TOTAL_ inventory items",
            infoEmpty: "Showing 0 to 0 of 0 inventory items",
            lengthMenu: "Show _MENU_ inventory items per page",
            search: "Search inventory:",
            zeroRecords: "No matching inventory items found"
        },
        columns: [
            { data: 'product', title: 'Product' },
            { data: 'variant', title: 'Variant' },
            { data: 'warehouse', title: 'Warehouse' },
            { data: 'type', title: 'Type' },
            { data: 'quantity', title: 'Quantity' },
            { data: 'status', title: 'Status' },
            { data: 'last_updated', title: 'Last Updated' },
            { data: 'actions', title: 'Actions', orderable: false }
        ],
        order: [[6, 'desc']], // Sort by last updated by default
        responsive: true,
        autoWidth: false
    });

    // Load inventory data
    function loadInventoryData() {
        $.ajax({
            url: '/api/inventory',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateInventoryTable(response.data);
                } else {
                    showErrorToast(response.message || 'Failed to load inventory data');
                }
            },
            error: function(xhr, status, error) {
                showErrorToast('Error loading inventory data: ' + error);
            }
        });
    }

    // Populate inventory table with data
    function populateInventoryTable(data) {
        inventoryTable.clear();

        data.forEach(function(item) {
            const statusClass = getStatusClass(item.QUANTITY, item.threshold || 10);
            const typeClass = item.INVE_TYPE ? `inventory-${item.INVE_TYPE.toLowerCase()}` : 'inventory-unknown';
            
            inventoryTable.row.add({
                'product': `<div class="d-flex align-items-center">
                    <img src="${item.PROD_IMAGE || '/assets/img/no-image.png'}" class="me-2" width="40" height="40" style="object-fit: cover; border-radius: 4px;">
                    <span>${item.PROD_NAME || 'Unknown Product'}</span>
                </div>`,
                'variant': item.VAR_CAPACITY || 'Standard',
                'warehouse': item.WHOUSE_NAME || 'Unknown Warehouse',
                'type': `<span class="badge ${typeClass}">${item.INVE_TYPE || 'Unknown'}</span>`,
                'quantity': item.QUANTITY,
                'status': `<span class="badge ${statusClass}">${getStatusText(item.QUANTITY, item.threshold || 10)}</span>`,
                'last_updated': formatDate(item.LAST_UPDATED),
                'actions': `<div class="action-buttons">
                    <button class="btn btn-sm table-action-btn view-btn" data-id="${item.INVE_ID}" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn edit-btn" data-id="${item.INVE_ID}" title="Edit Inventory">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn manage-btn" 
                        data-id="${item.INVE_ID}" 
                        data-prod-id="${item.PROD_ID}"
                        data-whouse-id="${item.WHOUSE_ID}"
                        data-type="${item.INVE_TYPE || ''}"
                        title="Manage Stock">
                        <i class="bi bi-boxes"></i>
                    </button>
                </div>`
            });
        });

        inventoryTable.draw();
        
        // After populating, attach event handlers to action buttons
        attachInventoryActionHandlers();
    }

    // Attach event handlers to inventory action buttons
    function attachInventoryActionHandlers() {
        // View button handler
        $('#inventoryTable').on('click', '.view-btn', function() {
            const id = $(this).data('id');
            viewInventoryItem(id);
        });

        // Edit button handler
        $('#inventoryTable').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            editInventoryItem(id);
        });

        // Manage stock button handler
        $('#inventoryTable').on('click', '.manage-btn', function() {
            const inventoryId = $(this).data('id');
            const productId = $(this).data('prod-id');
            const warehouseId = $(this).data('whouse-id');
            const type = $(this).data('type');
            openManageInventoryModal(inventoryId, productId, warehouseId, type);
        });
    }

    // View inventory item details
    function viewInventoryItem(id) {
        // Implementation of viewing inventory details
        showInfoToast('Viewing inventory details for ID: ' + id);
    }

    // Edit inventory item
    function editInventoryItem(id) {
        // Implementation of editing inventory item
        showInfoToast('Editing inventory item with ID: ' + id);
    }

    // Open the manage inventory modal
    function openManageInventoryModal(inventoryId, productId, warehouseId, type) {
        // Set hidden fields in the form
        $('#moveStockProductId').val(productId);
        $('#addStockProductId').val(productId);
        
        // Load product variants
        loadProductVariants(productId);
        
        // Load current stock information
        loadCurrentStock(productId);
        
        // Show the modal
        $('#manageInventoryModal').modal('show');
    }

    // Load product variants for the manage inventory modal
    function loadProductVariants(productId) {
        $.ajax({
            url: `/api/products/${productId}/variants`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const variants = response.data;
                    
                    // Populate variant dropdowns
                    const addVariantSelect = $('#addVariantSelect');
                    const moveVariantSelect = $('#moveVariantSelect');
                    
                    addVariantSelect.empty();
                    moveVariantSelect.empty();
                    
                    variants.forEach(function(variant) {
                        const option = `<option value="${variant.VAR_ID}">${variant.VAR_CAPACITY}</option>`;
                        addVariantSelect.append(option);
                        moveVariantSelect.append(option);
                    });
                } else {
                    showErrorToast(response.message || 'Failed to load product variants');
                }
            },
            error: function(xhr, status, error) {
                showErrorToast('Error loading product variants: ' + error);
            }
        });
    }

    // Load current stock information for the manage inventory modal
    function loadCurrentStock(productId) {
        $.ajax({
            url: `/api/inventory/product/${productId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const inventory = response.data;
                    const currentStockTable = $('#currentStockTable');
                    
                    currentStockTable.empty();
                    
                    if (inventory.length === 0) {
                        currentStockTable.append('<tr><td colspan="5" class="text-center">No stock data available</td></tr>');
                    } else {
                        inventory.forEach(function(item) {
                            const typeClass = item.INVE_TYPE ? `inventory-${item.INVE_TYPE.toLowerCase()}` : 'inventory-unknown';
                            const row = `<tr>
                                <td>${item.VAR_CAPACITY || 'Standard'}</td>
                                <td>${item.WHOUSE_NAME}</td>
                                <td><span class="badge ${typeClass}">${item.INVE_TYPE || 'Unknown'}</span></td>
                                <td>${item.QUANTITY}</td>
                                <td>${formatDate(item.LAST_UPDATED)}</td>
                            </tr>`;
                            currentStockTable.append(row);
                        });
                    }
                } else {
                    showErrorToast(response.message || 'Failed to load current stock data');
                }
            },
            error: function(xhr, status, error) {
                showErrorToast('Error loading current stock data: ' + error);
            }
        });
    }

    // Load warehouses for the manage inventory modal
    function loadWarehouses() {
        $.ajax({
            url: '/api/warehouses',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const warehouses = response.data;
                    console.log('Loaded warehouses:', warehouses);
                    
                    // Populate warehouse dropdowns
                    const addWarehouseSelect = $('#addWarehouseSelect');
                    const sourceWarehouse = $('#sourceWarehouse');
                    const destinationWarehouse = $('#destinationWarehouse');
                    const warehouseSelect = $('#warehouseSelect'); // For product form
                    
                    addWarehouseSelect.empty();
                    sourceWarehouse.empty();
                    destinationWarehouse.empty();
                    warehouseSelect.empty();
                    
                    warehouseSelect.append('<option value="">Select warehouse</option>');
                    
                    warehouses.forEach(function(warehouse) {
                        // Handle both uppercase and lowercase property names
                        const id = warehouse.WHOUSE_ID || warehouse.whouse_id || '';
                        const name = warehouse.WHOUSE_NAME || warehouse.whouse_name || 'Unnamed Warehouse';
                        const location = warehouse.WHOUSE_LOCATION || warehouse.whouse_location || 'No location';
                        
                        const option = `<option value="${id}">${name} (${location})</option>`;
                        addWarehouseSelect.append(option);
                        sourceWarehouse.append(option);
                        destinationWarehouse.append(option);
                        warehouseSelect.append(option);
                    });
                } else {
                    showErrorToast(response.message || 'Failed to load warehouses');
                }
            },
            error: function(xhr, status, error) {
                showErrorToast('Error loading warehouses: ' + error);
                console.error('Error loading warehouses:', xhr.responseText);
            }
        });
    }

    // Add stock form submission
    $('#addStockForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            prod_id: $('#addStockProductId').val(),
            whouse_id: $('#addWarehouseSelect').val(),
            inve_type: $('#addInventoryType').val(),
            quantity: $('#addQuantity').val(),
            reason: $('#addReason').val(),
            notes: $('#addNotes').val()
        };
        
        $.ajax({
            url: '/api/inventory/add-stock',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccessToast(response.message || 'Stock added successfully');
                    loadCurrentStock(formData.prod_id);
                    loadInventoryData(); // Refresh inventory table
                } else {
                    showErrorToast(response.message || 'Failed to add stock');
                }
            },
            error: function(xhr, status, error) {
                showErrorToast('Error adding stock: ' + error);
            }
        });
    });

    // Move stock form submission
    $('#moveStockForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            prod_id: $('#moveStockProductId').val(),
            source_whouse_id: $('#sourceWarehouse').val(),
            dest_whouse_id: $('#destinationWarehouse').val(),
            inve_type: $('#moveInventoryType').val(),
            quantity: $('#moveQuantity').val(),
            notes: $('#moveNotes').val()
        };
        
        $.ajax({
            url: '/api/inventory/move-stock',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccessToast(response.message || 'Stock moved successfully');
                    loadCurrentStock(formData.prod_id);
                    loadInventoryData(); // Refresh inventory table
                } else {
                    showErrorToast(response.message || 'Failed to move stock');
                }
            },
            error: function(xhr, status, error) {
                showErrorToast('Error moving stock: ' + error);
            }
        });
    });

    // Filter by stock type
    $('#stockTypeFilter').on('change', function() {
        const type = $(this).val();
        
        if (type === 'all') {
            inventoryTable.column(3).search('').draw();
        } else {
            inventoryTable.column(3).search(type).draw();
        }
    });

    // Search inventory
    $('#inventorySearch').on('keyup', function() {
        inventoryTable.search($(this).val()).draw();
    });

    // Helper function to get status class based on quantity
    function getStatusClass(quantity, threshold) {
        if (quantity <= 0) {
            return 'bg-danger';
        } else if (quantity <= threshold) {
            return 'bg-warning';
        } else {
            return 'bg-success';
        }
    }

    // Helper function to get status text based on quantity
    function getStatusText(quantity, threshold) {
        if (quantity <= 0) {
            return 'Out of Stock';
        } else if (quantity <= threshold) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Initial load of inventory data
    loadInventoryData();
    
    // Load warehouses for dropdown menus
    loadWarehouses();
});
