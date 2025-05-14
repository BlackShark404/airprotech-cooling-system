/**
 * WarehouseViewDataTables.js
 * Handles the DataTables initialization and functionality for the warehouses view
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable for warehouses view
    const warehousesTable = $('#warehousesTable').DataTable({
        processing: true,
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        language: {
            emptyTable: "No warehouses available",
            info: "Showing _START_ to _END_ of _TOTAL_ warehouses",
            infoEmpty: "Showing 0 to 0 of 0 warehouses",
            lengthMenu: "Show _MENU_ warehouses per page",
            search: "Search warehouses:",
            zeroRecords: "No matching warehouses found"
        },
        columns: [
            { data: 'name', title: 'Name' },
            { data: 'location', title: 'Location' },
            { data: 'storage_capacity', title: 'Storage Capacity' },
            { data: 'current_usage', title: 'Current Usage' },
            { data: 'low_stock', title: 'Items Below Threshold' },
            { data: 'actions', title: 'Actions', orderable: false }
        ],
        order: [[0, 'asc']], // Sort by name by default
        responsive: true,
        autoWidth: false
    });

    // Load warehouses data
    function loadWarehousesData() {
        $.ajax({
            url: '/api/warehouses/with-inventory',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log('Loaded warehouses with inventory:', response.data);
                    populateWarehousesTable(response.data);
                } else {
                    showErrorToast(response.message || 'Failed to load warehouses data');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading warehouses data: ' + error);
                console.error('Error loading warehouses data:', xhr.responseText);
            }
        });
    }

    // Populate warehouses table with data
    function populateWarehousesTable(data) {
        warehousesTable.clear();

        if (!data || data.length === 0) {
            warehousesTable.draw();
            return;
        }

        data.forEach(function (warehouse) {
            // Ensure all properties exist to avoid undefined errors
            const warehouseName = warehouse.WHOUSE_NAME || 'Unnamed Warehouse';
            const warehouseLocation = warehouse.WHOUSE_LOCATION || 'No location';
            const warehouseId = warehouse.WHOUSE_ID || 0;
            const storageCapacity = warehouse.WHOUSE_STORAGE_CAPACITY || 0;
            const totalItems = warehouse.total_items || 0;
            const lowStockCount = warehouse.low_stock_count || 0;

            // Calculate usage percentage
            let usagePercentage = 0;
            if (storageCapacity > 0 && totalItems) {
                usagePercentage = (totalItems / storageCapacity) * 100;
            }

            // Format capacity text
            let capacityText = storageCapacity ?
                storageCapacity + ' units' : 'Unlimited';

            // Format usage text and progress bar
            let usageHtml = '';

            if (storageCapacity) {
                // Determine progress bar class based on usage percentage
                let progressClass = 'bg-success';
                if (usagePercentage > 90) {
                    progressClass = 'bg-danger';
                } else if (usagePercentage > 70) {
                    progressClass = 'bg-warning';
                }

                usageHtml = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>${totalItems} of ${storageCapacity}</span>
                        <span>${usagePercentage.toFixed(1)}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar ${progressClass}" role="progressbar" 
                            style="width: ${usagePercentage}%" 
                            aria-valuenow="${usagePercentage}" 
                            aria-valuemin="0" 
                            aria-valuemax="100"></div>
                    </div>
                `;
            } else {
                usageHtml = `<span>${totalItems} items</span>`;
            }

            // Format low stock items with badge
            let lowStockHtml = '';

            if (lowStockCount > 0) {
                let badgeClass = 'bg-warning';
                if (lowStockCount > 5) {
                    badgeClass = 'bg-danger';
                }

                lowStockHtml = `<span class="badge ${badgeClass}">${lowStockCount} items</span>`;
            } else {
                lowStockHtml = '<span class="badge bg-success">None</span>';
            }

            warehousesTable.row.add({
                'name': warehouseName,
                'location': warehouseLocation,
                'storage_capacity': capacityText,
                'current_usage': usageHtml,
                'low_stock': lowStockHtml,
                'actions': `<div class="action-buttons">
                    <button class="btn btn-sm table-action-btn view-btn" data-id="${warehouseId}" title="View Warehouse">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn edit-btn" data-id="${warehouseId}" title="Edit Warehouse">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn delete-btn" data-id="${warehouseId}" title="Delete Warehouse">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn inventory-btn" data-id="${warehouseId}" title="View Inventory">
                        <i class="bi bi-boxes"></i>
                    </button>
                </div>`
            });
        });

        warehousesTable.draw();

        // After populating, attach event handlers to action buttons
        attachWarehouseActionHandlers();
    }

    // Attach event handlers to warehouse action buttons
    function attachWarehouseActionHandlers() {
        // View button handler
        $('#warehousesTable').on('click', '.view-btn', function () {
            const id = $(this).data('id');
            viewWarehouse(id);
        });

        // Edit button handler
        $('#warehousesTable').on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            editWarehouse(id);
        });

        // Delete button handler
        $('#warehousesTable').on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            confirmDeleteWarehouse(id);
        });

        // Inventory button handler
        $('#warehousesTable').on('click', '.inventory-btn', function () {
            const id = $(this).data('id');
            viewWarehouseInventory(id);
        });
    }

    // View warehouse details
    function viewWarehouse(id) {
        $.ajax({
            url: `/api/warehouses/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const warehouse = response.data;

                    // Display warehouse details in the warehouse form
                    displayWarehouseDetails(warehouse);

                    // Show the warehouse modal
                    $('#warehouseModal').modal('show');
                } else {
                    showErrorToast(response.message || 'Failed to load warehouse details');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading warehouse details: ' + error);
            }
        });
    }

    // Display warehouse details in the form
    function displayWarehouseDetails(warehouse) {
        // Fill in the form fields with warehouse data
        $('#warehouseId').val(warehouse.WHOUSE_ID);
        $('#warehouseName').val(warehouse.WHOUSE_NAME);
        $('#warehouseLocation').val(warehouse.WHOUSE_LOCATION);
        $('#warehouseCapacity').val(warehouse.WHOUSE_STORAGE_CAPACITY);
        $('#warehouseThreshold').val(warehouse.WHOUSE_RESTOCK_THRESHOLD);
    }

    // Edit warehouse
    function editWarehouse(id) {
        // This is similar to viewWarehouse, but might add additional functionality
        viewWarehouse(id);
    }

    // Confirm delete warehouse
    function confirmDeleteWarehouse(id) {
        if (confirm('Are you sure you want to delete this warehouse? This action cannot be undone.')) {
            deleteWarehouse(id);
        }
    }

    // Delete warehouse
    function deleteWarehouse(id) {
        $.ajax({
            url: `/api/warehouses/delete/${id}`,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccessToast(response.message || 'Warehouse deleted successfully');
                    loadWarehousesData(); // Refresh table data
                } else {
                    showErrorToast(response.message || 'Failed to delete warehouse');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error deleting warehouse: ' + error);
            }
        });
    }

    // View warehouse inventory
    function viewWarehouseInventory(id) {
        $.ajax({
            url: `/api/inventory/warehouse/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // This would typically populate a modal with inventory data
                    // For this example, just show a toast
                    showInfoToast(`Viewing inventory for warehouse ID: ${id}`);
                } else {
                    showErrorToast(response.message || 'Failed to load warehouse inventory');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading warehouse inventory: ' + error);
            }
        });
    }

    // Search warehouses
    $('#warehousesSearch').on('keyup', function () {
        warehousesTable.search($(this).val()).draw();
    });

    // Load all warehouses for the warehouse list table
    function loadWarehouseList() {
        $.ajax({
            url: '/api/warehouses',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const warehouses = response.data;
                    console.log('Loaded warehouses for list:', warehouses);

                    const warehouseListTable = $('#warehouseListTable tbody');
                    warehouseListTable.empty();

                    if (!warehouses || warehouses.length === 0) {
                        warehouseListTable.append('<tr><td colspan="3" class="text-center">No warehouses available</td></tr>');
                    } else {
                        warehouses.forEach(function (warehouse) {
                            // Ensure warehouse name and location have fallback values
                            const name = warehouse.WHOUSE_NAME || 'Unnamed Warehouse';
                            const location = warehouse.WHOUSE_LOCATION || 'No location';

                            const row = `
                                <tr>
                                    <td>${name}</td>
                                    <td>${location}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1 edit-warehouse-btn" data-id="${warehouse.WHOUSE_ID}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-warehouse-btn" data-id="${warehouse.WHOUSE_ID}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            warehouseListTable.append(row);
                        });

                        // Attach event handlers to action buttons
                        attachWarehouseListActionHandlers();
                    }
                } else {
                    showErrorToast(response.message || 'Failed to load warehouses list');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading warehouses list: ' + error);
                console.error('Error loading warehouse list:', xhr.responseText);
            }
        });
    }

    // Attach event handlers to warehouse list action buttons
    function attachWarehouseListActionHandlers() {
        // Edit button handler
        $('#warehouseListTable').on('click', '.edit-warehouse-btn', function () {
            const id = $(this).data('id');
            editWarehouse(id);
        });

        // Delete button handler
        $('#warehouseListTable').on('click', '.delete-warehouse-btn', function () {
            const id = $(this).data('id');
            confirmDeleteWarehouse(id);
        });
    }

    // Warehouse form submission
    $('#warehouseForm').on('submit', function (e) {
        e.preventDefault();

        const warehouseId = $('#warehouseId').val();
        const formData = {
            WHOUSE_NAME: $('#warehouseName').val(),
            WHOUSE_LOCATION: $('#warehouseLocation').val(),
            WHOUSE_STORAGE_CAPACITY: $('#warehouseCapacity').val(),
            WHOUSE_RESTOCK_THRESHOLD: $('#warehouseThreshold').val()
        };

        if (warehouseId) {
            // Update existing warehouse
            updateWarehouse(warehouseId, formData);
        } else {
            // Create new warehouse
            createWarehouse(formData);
        }
    });

    // Create new warehouse
    function createWarehouse(data) {
        // Log data for debugging
        console.log('Creating warehouse with data:', data);

        $.ajax({
            url: '/api/warehouses',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Get the newly created warehouse ID
                    const newWarehouseId = response.data.id;

                    // Show success message
                    showSuccessToast(response.message || 'Warehouse created successfully');

                    // Refresh warehouse list and warehouse table
                    // NOTE: Don't reset the form until after refreshing the data to ensure 
                    // the newly added warehouse has its data displayed correctly
                    loadWarehouseList();
                    loadWarehousesData();

                    // Reset the form only after data is refreshed
                    setTimeout(function () {
                        resetWarehouseForm();
                    }, 500);
                } else {
                    showErrorToast(response.message || 'Failed to create warehouse');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error creating warehouse: ' + error);
            }
        });
    }

    // Update existing warehouse
    function updateWarehouse(id, data) {
        $.ajax({
            url: `/api/warehouses/${id}`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccessToast(response.message || 'Warehouse updated successfully');
                    resetWarehouseForm();
                    loadWarehouseList();
                    loadWarehousesData(); // Refresh warehouses table
                } else {
                    showErrorToast(response.message || 'Failed to update warehouse');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error updating warehouse: ' + error);
            }
        });
    }

    // Reset warehouse form
    function resetWarehouseForm() {
        $('#warehouseId').val('');
        $('#warehouseName').val('');
        $('#warehouseLocation').val('');
        $('#warehouseCapacity').val('');
        $('#warehouseThreshold').val('');
    }

    // Reset warehouse form button
    $('#resetWarehouseBtn').on('click', function () {
        resetWarehouseForm();
    });

    // Initial loads
    loadWarehousesData();
    loadWarehouseList();
});
