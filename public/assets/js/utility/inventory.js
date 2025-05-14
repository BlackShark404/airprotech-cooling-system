// AirProtect Inventory Management System JavaScript - Revised Version

document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM content loaded, initializing inventory system...");
    /**
 * Initialize global event delegation for all action buttons
 */
    function initializeGlobalEventDelegation() {
        console.log("Initializing global event delegation...");

        // Use a single event listener on the document body
        document.body.addEventListener('click', function (event) {
            const target = event.target;

            // Check if click was on a button or within a button
            const button = target.closest('button');
            if (!button) return;

            // View product buttons
            if (button.classList.contains('view-product')) {
                const productId = button.getAttribute('data-id');
                console.log("View product button clicked, ID:", productId);
                if (window.inventoryHandler) {
                    window.inventoryHandler.viewProductDetails(productId);
                } else {
                    viewProductDetails(productId);
                }
                event.preventDefault();
            }

            // Manage inventory buttons
            else if (button.classList.contains('manage-inventory')) {
                const productId = button.getAttribute('data-id');
                console.log("Manage inventory button clicked, ID:", productId);
                if (window.inventoryHandler) {
                    window.inventoryHandler.openManageInventory(productId);
                } else {
                    openManageInventory(productId);
                }
                event.preventDefault();
            }

            // Delete product buttons
            else if (button.classList.contains('delete-product')) {
                const productId = button.getAttribute('data-id');
                console.log("Delete product button clicked, ID:", productId);
                if (window.inventoryHandler) {
                    window.inventoryHandler.confirmDeleteProduct(productId);
                } else {
                    confirmDeleteProduct(productId);
                }
                event.preventDefault();
            }

            // View warehouse buttons
            else if (button.classList.contains('view-warehouse')) {
                const warehouseId = button.getAttribute('data-id');
                console.log("View warehouse button clicked, ID:", warehouseId);
                if (window.inventoryHandler) {
                    window.inventoryHandler.viewWarehouseDetails(warehouseId);
                } else {
                    viewWarehouseDetails(warehouseId);
                }
                event.preventDefault();
            }

            // Edit warehouse buttons
            else if (button.classList.contains('edit-warehouse')) {
                const warehouseId = button.getAttribute('data-id');
                console.log("Edit warehouse button clicked, ID:", warehouseId);

                // Get warehouse data from attributes
                const name = button.getAttribute('data-name');
                const location = button.getAttribute('data-location');
                const capacity = button.getAttribute('data-capacity');
                const threshold = button.getAttribute('data-threshold');

                // Populate form
                document.getElementById('warehouseId').value = warehouseId || '';
                document.getElementById('warehouseName').value = name || '';
                document.getElementById('warehouseLocation').value = location || '';
                document.getElementById('warehouseCapacity').value = capacity || '';
                document.getElementById('warehouseThreshold').value = threshold || '';

                // Show warehouse modal
                const modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
                modal.show();

                event.preventDefault();
            }

            // Delete warehouse buttons
            else if (button.classList.contains('delete-warehouse')) {
                const warehouseId = button.getAttribute('data-id');
                console.log("Delete warehouse button clicked, ID:", warehouseId);
                confirmDeleteWarehouse(warehouseId);
                event.preventDefault();
            }

            // Remove variant buttons
            else if (button.classList.contains('remove-variant')) {
                const variantForm = button.closest('.variant-form');
                if (variantForm) {
                    // Only remove if there's more than one variant form
                    const variantForms = document.querySelectorAll('.variant-form');
                    if (variantForms.length > 1) {
                        variantForm.remove();
                        updateInventoryVariants();
                    } else {
                        showAlert('warning', 'At least one variant is required');
                    }
                }
                event.preventDefault();
            }

            // Remove feature buttons
            else if (button.classList.contains('remove-feature')) {
                const featureGroup = button.closest('.input-group');
                if (featureGroup) {
                    featureGroup.remove();
                }
                event.preventDefault();
            }

            // Remove spec buttons
            else if (button.classList.contains('remove-spec')) {
                const specRow = button.closest('.row');
                if (specRow) {
                    specRow.remove();
                }
                event.preventDefault();
            }
        });

        console.log("Global event delegation initialized");
    }

    // The problematic duplicate window.onerror is removed from here

    initializeGlobalEventDelegation();
    // Initialize API endpoints
    const API_ENDPOINTS = {
        GET_ALL_INVENTORY: '/inventory/getAllInventory',
        GET_INVENTORY_BY_PRODUCT: '/inventory/getInventoryByProduct/',
        GET_INVENTORY_BY_WAREHOUSE: '/inventory/getInventoryByWarehouse/',
        GET_INVENTORY_BY_TYPE: '/inventory/getInventoryByType/',
        GET_LOW_STOCK: '/inventory/getLowStockProducts',
        GET_STATS: '/inventory/getStats',
        GET_WAREHOUSES: '/inventory/getWarehouses',
        GET_PRODUCTS: '/inventory/getProductsWithVariants',
        ADD_STOCK: '/inventory/addStock',
        MOVE_STOCK: '/inventory/moveStock',
        PRODUCT_DETAILS: '/inventory/viewProduct/',
        CREATE_PRODUCT: '/inventory/createProduct',
        UPDATE_PRODUCT: '/inventory/updateProduct/',
        DELETE_PRODUCT: '/inventory/deleteProduct/',

        // Warehouse endpoints
        GET_ALL_WAREHOUSES: '/warehouse/getAllWarehouses',
        GET_WAREHOUSE: '/warehouse/getWarehouse/',
        CREATE_WAREHOUSE: '/warehouse/createWarehouse',
        UPDATE_WAREHOUSE: '/warehouse/updateWarehouse/',
        DELETE_WAREHOUSE: '/warehouse/deleteWarehouse/',
        GET_WAREHOUSE_INVENTORY: '/warehouse/getWarehouseInventory/',
        GET_WAREHOUSE_SUMMARY: '/warehouse/getWarehousesWithSummary',
        GET_WAREHOUSES_LOW_STOCK: '/warehouse/getWarehousesWithLowStock'
    };

    // State management
    let currentView = 'inventory';
    let currentProductId = null;
    let currentWarehouseId = null;
    let variantCounter = 0;
    let featureCounter = 0;
    let specCounter = 0;
    let warehouseModalInitialized = false; // Declare this variable here so it's available for all functions

    // Initialize the InventoryDataTablesHandler
    try {
        const inventoryHandler = new InventoryDataTablesHandler({
            tableId: 'inventoryTable',
            ajaxUrl: API_ENDPOINTS.GET_ALL_INVENTORY,
            apiEndpoints: API_ENDPOINTS,
            modalIds: {
                viewDetails: 'productDetailsModal',
                manageInventory: 'manageInventoryModal',
                addProduct: 'addProductModal',
                deleteConfirmation: 'deleteConfirmationModal',
                importInventory: 'importInventoryModal',
                warehouseModal: 'warehouseModal'
            }
        });

        // Store handler in window object for debugging
        window.inventoryHandler = inventoryHandler;
        console.log("InventoryDataTablesHandler initialized successfully");
    } catch (error) {
        console.error("Error initializing InventoryDataTablesHandler:", error);
        showGlobalErrorBanner("Error initializing inventory system. Please refresh the page.");

        // Initialize fallback functionality
        initializeFallbackFunctionality();
    }

    // Initialize tabs and buttons
    initializeTabsAndButtons();

    // Initialize warehouse modal if it exists
    initializeWarehouseModal();

    // Load dashboard stats
    loadDashboardStats();

    /**
     * Initialize tabs and view selector buttons
     */
    function initializeTabsAndButtons() {
        console.log("Initializing tabs and buttons...");

        // View Selector Toggle
        const viewButtons = document.querySelectorAll('.view-selector .btn');
        if (viewButtons.length) {
            viewButtons.forEach(button => {
                button.addEventListener('click', function () {
                    console.log("View button clicked:", this.getAttribute('data-view'));

                    // Remove active class from all buttons
                    viewButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Get the view type
                    const viewType = this.getAttribute('data-view');
                    currentView = viewType;

                    // Hide all views
                    document.querySelectorAll('.view-card').forEach(card => {
                        card.classList.add('d-none');
                    });

                    // Show the selected view
                    const selectedView = document.getElementById(`${viewType}View`);
                    if (selectedView) {
                        selectedView.classList.remove('d-none');

                        // Load appropriate data based on view
                        if (viewType === 'warehouses') {
                            loadWarehouseData();
                        } else if (viewType === 'products') {
                            loadProductData();
                        } else if (viewType === 'inventory') {
                            loadInventoryData();
                        }
                    }

                    // If handler exists, notify it of view change
                    if (window.inventoryHandler) {
                        window.inventoryHandler.changeView(viewType);
                    }
                });
            });
        }

        // Stock Type Filter
        const stockTypeFilter = document.getElementById('stockTypeFilter');
        if (stockTypeFilter) {
            stockTypeFilter.addEventListener('change', function () {
                const selectedType = this.value;
                console.log("Stock type filter changed:", selectedType);

                if (window.inventoryHandler) {
                    window.inventoryHandler.filterByStockType(selectedType);
                } else {
                    filterInventoryByType(selectedType);
                }
            });
        }

        // Warehouses Button
        const warehousesBtn = document.querySelector('[data-bs-target="#warehouseModal"]');
        if (warehousesBtn) {
            warehousesBtn.addEventListener('click', function () {
                console.log("Warehouses button clicked");
                loadWarehouseListForModal();
            });
        } else {
            // For standalone warehouses button
            const standaloneWarehousesBtn = document.querySelector('.btn[data-action="warehouses"], .warehouses-btn');
            if (standaloneWarehousesBtn) {
                standaloneWarehousesBtn.addEventListener('click', function () {
                    console.log("Standalone warehouses button clicked");

                    // Check if modal exists, if not create it
                    if (!document.getElementById('warehouseModal')) {
                        createWarehouseModal();
                    }

                    // Load warehouse list and open modal
                    loadWarehouseListForModal();

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
                    modal.show();
                });
            }
        }

        // Add Product Button
        const addProductBtn = document.querySelector('.btn-red, .add-product-btn, [data-bs-target="#addProductModal"]');
        if (addProductBtn) {
            addProductBtn.addEventListener('click', function () {
                console.log("Add product button clicked");

                // Reset form before showing
                if (window.inventoryHandler) {
                    window.inventoryHandler.resetProductForm();
                } else {
                    resetProductForm();
                }

                // Ensure modal title is set correctly
                document.getElementById('addProductModalLabel').textContent = 'Add New Product';
                document.getElementById('saveProductBtn').textContent = 'Save Product';

                // Load warehouses for dropdown
                loadWarehousesForDropdown();
            });
        }

        // Product form save button
        const saveProductBtn = document.getElementById('saveProductBtn');
        if (saveProductBtn) {
            saveProductBtn.addEventListener('click', function () {
                console.log("Save product button clicked");
                if (window.inventoryHandler) {
                    window.inventoryHandler.saveProduct();
                } else {
                    saveProduct();
                }
            });
        }

        // Add variant button
        const addVariantBtn = document.querySelector('.add-variant-btn');
        if (addVariantBtn) {
            addVariantBtn.addEventListener('click', function () {
                console.log("Add variant button clicked");
                if (window.inventoryHandler) {
                    window.inventoryHandler.addNewVariantForm();
                } else {
                    addNewVariantForm();
                }
            });
        }

        // Add feature button
        const addFeatureBtn = document.querySelector('.add-feature-btn');
        if (addFeatureBtn) {
            addFeatureBtn.addEventListener('click', function () {
                console.log("Add feature button clicked");
                if (window.inventoryHandler) {
                    window.inventoryHandler.addNewFeatureInput();
                } else {
                    addNewFeatureInput();
                }
            });
        }

        // Add spec button
        const addSpecBtn = document.querySelector('.add-spec-btn');
        if (addSpecBtn) {
            addSpecBtn.addEventListener('click', function () {
                console.log("Add spec button clicked");
                if (window.inventoryHandler) {
                    window.inventoryHandler.addNewSpecInput();
                } else {
                    addNewSpecInput();
                }
            });
        }

        console.log("Tabs and buttons initialized");
    }

    /**
     * Initialize warehouse modal
     */
    function initializeWarehouseModal() {
        console.log("Initializing warehouse modal...");

        // Don't initialize multiple times
        if (warehouseModalInitialized) {
            console.log("Warehouse modal already initialized, skipping");
            return;
        }

        // Create modal if it doesn't exist
        if (!document.getElementById('warehouseModal')) {
            createWarehouseModal();
        }

        // Initialize warehouse form submission - using event delegation to prevent duplicate listeners
        document.body.addEventListener('submit', function (event) {
            const warehouseForm = event.target.closest('#warehouseForm');
            if (warehouseForm) {
                event.preventDefault();
                console.log("Warehouse form submitted");
                saveWarehouse();
            }
        });

        // Reset warehouse form button - using event delegation
        document.body.addEventListener('click', function (event) {
            if (event.target.closest('#resetWarehouseBtn')) {
                console.log("Reset warehouse form button clicked");
                resetWarehouseForm();
            }
        });

        // Mark as initialized
        warehouseModalInitialized = true;
        console.log("Warehouse modal initialized");
    }

    /**
     * Create warehouse modal if it doesn't exist
     */
    function createWarehouseModal() {
        console.log("Creating warehouse modal...");

        const modalHTML = `
            <div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Manage Warehouses</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="warehouseForm">
                                <input type="hidden" id="warehouseId" name="whouse_id" value="">
                                <div class="mb-3">
                                    <label for="warehouseName" class="form-label">Warehouse Name</label>
                                    <input type="text" class="form-control" id="warehouseName" name="whouse_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="warehouseLocation" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="warehouseLocation" name="whouse_location" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="warehouseCapacity" class="form-label">Storage Capacity</label>
                                        <input type="number" class="form-control" id="warehouseCapacity" name="whouse_storage_capacity" min="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="warehouseThreshold" class="form-label">Restock Threshold</label>
                                        <input type="number" class="form-control" id="warehouseThreshold" name="whouse_restock_threshold" min="1">
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary" id="saveWarehouseBtn">Save Warehouse</button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" id="resetWarehouseBtn">Reset</button>
                                </div>
                            </form>
                            
                            <h6 class="mb-3 mt-4">Existing Warehouses</h6>
                            <div class="table-responsive">
                                <table class="table table-sm" id="warehouseListTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Location</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        console.log("Warehouse modal created");

        // We'll initialize the event listeners in initializeWarehouseModal instead
        // This prevents duplicate event listeners
    }


    /**
     * Load warehouse list for modal
     */
    function loadWarehouseListForModal() {
        console.log("Loading warehouse list for modal...");

        const warehouseListTable = document.getElementById('warehouseListTable')?.querySelector('tbody');
        if (!warehouseListTable) {
            console.error("Warehouse list table not found");
            return;
        }

        // Mark as loading to prevent duplicate calls
        if (warehouseListTable.dataset.loading === 'true') {
            console.log("Already loading warehouse list, skipping...");
            return;
        }
        warehouseListTable.dataset.loading = 'true';

        // Show loading indicator
        warehouseListTable.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

        // Fetch warehouses with error handling and timeout
        fetchWithTimeout(API_ENDPOINTS.GET_ALL_WAREHOUSES, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Warehouse data received:", data);

                if (data.success && data.data && data.data.length > 0) {
                    // Clear the table completely before adding new rows
                    warehouseListTable.innerHTML = '';

                    // Use a document fragment to build the rows
                    const fragment = document.createDocumentFragment();

                    data.data.forEach(warehouse => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${warehouse.whouse_name}</td>
                        <td>${warehouse.whouse_location}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary edit-warehouse" 
                                        data-id="${warehouse.whouse_id}"
                                        data-name="${warehouse.whouse_name}"
                                        data-location="${warehouse.whouse_location}"
                                        data-capacity="${warehouse.whouse_storage_capacity || ''}"
                                        data-threshold="${warehouse.whouse_restock_threshold || ''}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-warehouse" 
                                        data-id="${warehouse.whouse_id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                        fragment.appendChild(row);
                    });

                    // Add all rows at once
                    warehouseListTable.appendChild(fragment);
                } else {
                    // No warehouses found
                    warehouseListTable.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center py-3">No warehouses found</td>
                    </tr>
                `;
                }
            })
            .catch(error => {
                console.error("Error loading warehouses:", error);

                warehouseListTable.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center py-3">
                        <div class="alert alert-danger mb-0">
                            Error loading warehouses. Please try again.
                        </div>
                    </td>
                </tr>
            `;
            })
            .finally(() => {
                // Remove loading flag
                warehouseListTable.dataset.loading = 'false';
            });
    }

    /**
     * Load warehouses for dropdown
     */
    // In inventory.js
    /**
     * Load warehouses for dropdown - FIXED to prevent duplicates
     */
    function loadWarehousesForDropdown() {
        console.log("Loading warehouses for dropdown...");

        const warehouseSelect = document.getElementById('warehouseSelect');
        if (!warehouseSelect) {
            console.error("Warehouse select not found");
            return;
        }

        // Prevent duplicate calls
        if (warehouseSelect.dataset.loading === 'true') {
            console.log("Already loading warehouses, skipping...");
            return;
        }

        // Mark as loading
        warehouseSelect.dataset.loading = 'true';

        // Clear and show loading indicator
        warehouseSelect.innerHTML = '<option value="">Loading warehouses...</option>';

        // Fetch warehouses
        fetchWithTimeout(API_ENDPOINTS.GET_ALL_WAREHOUSES, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Warehouses for dropdown received:", data);

                // Clear again before populating
                warehouseSelect.innerHTML = '';

                if (data.success && data.data && data.data.length > 0) {
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Select warehouse';
                    warehouseSelect.appendChild(defaultOption);

                    // Use a Set to track added warehouse IDs
                    const addedWarehouses = new Set();

                    // Add warehouses, ensuring no duplicates
                    data.data.forEach(warehouse => {
                        if (!addedWarehouses.has(warehouse.whouse_id)) {
                            const option = document.createElement('option');
                            option.value = warehouse.whouse_id;
                            option.textContent = warehouse.whouse_name;
                            warehouseSelect.appendChild(option);
                            addedWarehouses.add(warehouse.whouse_id);
                        }
                    });
                } else {
                    warehouseSelect.innerHTML = '<option value="">No warehouses available</option>';
                }
            })
            .catch(error => {
                console.error("Error loading warehouses for dropdown:", error);
                warehouseSelect.innerHTML = '<option value="">Error loading warehouses</option>';
            })
            .finally(() => {
                // Remove loading flag
                warehouseSelect.dataset.loading = 'false';
            });
    }
    /**
     * Save warehouse
     */
    function saveWarehouse() {
        console.log("Saving warehouse...");

        // Check if already submitting
        if (document.getElementById('saveWarehouseBtn').hasAttribute('data-submitting')) {
            console.log("Already submitting warehouse, preventing duplicate submission");
            return;
        }

        const form = document.getElementById('warehouseForm');
        if (!form) {
            console.error("Warehouse form not found");
            return;
        }

        // Mark as submitting
        document.getElementById('saveWarehouseBtn').setAttribute('data-submitting', 'true');
        document.getElementById('saveWarehouseBtn').textContent = 'Saving...';

        // Show saving indicator
        showAlert('info', 'Saving warehouse...');

        // Get form field values directly
        const warehouseId = form.querySelector('#warehouseId').value;
        const warehouseName = form.querySelector('#warehouseName').value;
        const warehouseLocation = form.querySelector('#warehouseLocation').value;
        const storageCapacity = form.querySelector('#warehouseCapacity').value;
        const restockThreshold = form.querySelector('#warehouseThreshold').value;

        console.log("Form values:", {
            warehouseId,
            warehouseName,
            warehouseLocation,
            storageCapacity,
            restockThreshold
        });

        // Validate form fields
        if (!warehouseName) {
            showAlert('error', 'Warehouse name is required');
            document.getElementById('saveWarehouseBtn').removeAttribute('data-submitting');
            document.getElementById('saveWarehouseBtn').textContent = 'Save Warehouse';
            return;
        }

        if (!warehouseLocation) {
            showAlert('error', 'Warehouse location is required');
            document.getElementById('saveWarehouseBtn').removeAttribute('data-submitting');
            document.getElementById('saveWarehouseBtn').textContent = 'Save Warehouse';
            return;
        }

        // Determine if this is a create or update operation
        const isUpdate = warehouseId !== '';
        const url = isUpdate
            ? API_ENDPOINTS.UPDATE_WAREHOUSE + warehouseId
            : API_ENDPOINTS.CREATE_WAREHOUSE;

        console.log(`${isUpdate ? 'Updating' : 'Creating'} warehouse via ${url}`);

        // Create form data for traditional form submission instead of JSON
        const formData = new FormData();
        formData.append('whouse_name', warehouseName);
        formData.append('whouse_location', warehouseLocation);
        if (storageCapacity) formData.append('whouse_storage_capacity', storageCapacity);
        if (restockThreshold) formData.append('whouse_restock_threshold', restockThreshold);
        // For updates, include the ID
        if (warehouseId) formData.append('whouse_id', warehouseId);

        // Send the request using form data instead of JSON
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log("Server response status:", response.status);

                // Get the raw response text first
                return response.text().then(rawText => {
                    console.log("Raw server response:", rawText);

                    // Check if response is successful
                    if (!response.ok) {
                        throw new Error(`HTTP error ${response.status}: ${rawText || 'Unknown error'}`);
                    }

                    // Try to parse as JSON if not empty
                    if (rawText.trim()) {
                        try {
                            return JSON.parse(rawText);
                        } catch (e) {
                            console.error("Failed to parse JSON:", e);
                            // If we can't parse as JSON but response is OK, assume it succeeded
                            return {
                                success: true,
                                message: `Warehouse ${isUpdate ? 'updated' : 'created'} successfully`
                            };
                        }
                    } else {
                        // Empty response but HTTP status was OK, assume success
                        return {
                            success: true,
                            message: `Warehouse ${isUpdate ? 'updated' : 'created'} successfully`
                        };
                    }
                });
            })
            .then(data => {
                console.log("Processed response data:", data);

                if (data.success) {
                    // Show success message
                    showAlert('success', data.message || `Warehouse ${isUpdate ? 'updated' : 'created'} successfully`);

                    // Reset form for new entry
                    resetWarehouseForm();

                    // Refresh warehouse list
                    loadWarehouseListForModal();

                    // Also refresh warehouse data table if on warehouse view
                    if (currentView === 'warehouses') {
                        loadWarehouseData();
                    }
                } else {
                    throw new Error(data.message || 'Error saving warehouse');
                }
            })
            .catch(error => {
                console.error("Error saving warehouse:", error);
                showAlert('error', `Error ${isUpdate ? 'updating' : 'creating'} warehouse: ${error.message}`);
            })
            .finally(() => {
                // Reset submitting state
                document.getElementById('saveWarehouseBtn').removeAttribute('data-submitting');
                document.getElementById('saveWarehouseBtn').textContent = 'Save Warehouse';
            });
    }

    /**
     * Reset warehouse form
     */
    function resetWarehouseForm() {
        console.log("Resetting warehouse form");

        const form = document.getElementById('warehouseForm');
        if (form) {
            form.reset();
            form.querySelector('#warehouseId').value = '';
            document.getElementById('saveWarehouseBtn').textContent = 'Save Warehouse';
            document.getElementById('saveWarehouseBtn').removeAttribute('data-submitting');
        }
    }

    /**
     * Confirm delete warehouse
     */
    function confirmDeleteWarehouse(warehouseId) {
        console.log("Confirming warehouse deletion:", warehouseId);

        if (confirm('Are you sure you want to delete this warehouse? This action cannot be undone.')) {
            // Show deleting indicator
            showAlert('info', 'Deleting warehouse...');

            // Send delete request
            fetchWithTimeout(API_ENDPOINTS.DELETE_WAREHOUSE + warehouseId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `HTTP error ${response.status}`);
                        }).catch(() => {
                            throw new Error(`HTTP error ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Warehouse delete response:", data);

                    if (data.success) {
                        // Show success message
                        showAlert('success', 'Warehouse deleted successfully');

                        // Refresh warehouse list
                        loadWarehouseListForModal();

                        // Refresh warehouses dropdown in product form
                        loadWarehousesForDropdown();

                        // Refresh dashboard stats
                        loadDashboardStats();

                        // Refresh warehouses view if visible
                        if (currentView === 'warehouses') {
                            loadWarehouseData();
                        }
                    } else {
                        showAlert('error', data.message || 'Failed to delete warehouse');
                    }
                })
                .catch(error => {
                    console.error("Error deleting warehouse:", error);
                    showAlert('error', `Error deleting warehouse: ${error.message}`);
                });
        }
    }

    /**
     * Load dashboard stats
     */
    function loadDashboardStats() {
        console.log("Loading dashboard stats...");

        fetchWithTimeout(API_ENDPOINTS.GET_STATS, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Dashboard stats received:", data);

                if (data.success) {
                    updateStatCards(data.data);
                } else {
                    console.warn("Failed to load stats:", data.message);
                    // Use fallback stats
                    updateStatCards({
                        total_products: 0,
                        total_variants: 0,
                        total_warehouses: 0,
                        low_stock_count: 0
                    });
                }
            })
            .catch(error => {
                console.error("Error loading dashboard stats:", error);
                // Use fallback stats
                updateStatCards({
                    total_products: 0,
                    total_variants: 0,
                    total_warehouses: 0,
                    low_stock_count: 0
                });
            });
    }

    /**
     * Update stat cards
     */
    function updateStatCards(stats) {
        console.log("Updating stat cards with data:", stats);

        // Get all stat cards elements
        const totalProductsElem = document.querySelector('#totalProducts, .stats-card:nth-child(1) h3, [data-stat="total_products"]');
        const totalVariantsElem = document.querySelector('#totalVariants, .stats-card:nth-child(2) h3, [data-stat="total_variants"]');
        const totalWarehousesElem = document.querySelector('#totalWarehouses, .stats-card:nth-child(3) h3, [data-stat="total_warehouses"]');
        const lowStockItemsElem = document.querySelector('#lowStockItems, .stats-card:nth-child(4) h3, [data-stat="low_stock_count"]');

        // Update values if elements exist
        if (totalProductsElem && stats.total_products !== undefined) {
            totalProductsElem.textContent = stats.total_products;
        }

        if (totalVariantsElem && stats.total_variants !== undefined) {
            totalVariantsElem.textContent = stats.total_variants;
        }

        if (totalWarehousesElem && stats.total_warehouses !== undefined) {
            totalWarehousesElem.textContent = stats.total_warehouses;
        }

        if (lowStockItemsElem && stats.low_stock_count !== undefined) {
            lowStockItemsElem.textContent = stats.low_stock_count;
        }
    }

    /**
     * Load warehouse data (for warehouses view)
     */
    function loadWarehouseData() {
        console.log("Loading warehouse data...");

        const warehousesView = document.getElementById('warehousesView');
        if (!warehousesView || warehousesView.classList.contains('d-none')) {
            console.log("Warehouses view not visible, skipping data load");
            return;
        }

        const warehousesDashboard = document.querySelector('#warehousesView .card-body');
        if (!warehousesDashboard) {
            console.error("Warehouses dashboard container not found");
            return;
        }

        // Show loading indicator
        warehousesDashboard.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading warehouse data...</span>
                </div>
                <p class="mt-2">Loading warehouse data...</p>
            </div>
        `;

        // Fetch warehouses
        fetchWithTimeout(API_ENDPOINTS.GET_WAREHOUSE_SUMMARY, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Warehouses data received:", data);

                if (data.success && data.data && data.data.length > 0) {
                    // Create table
                    let html = `
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Storage Capacity</th>
                                    <th>Current Usage</th>
                                    <th>Items Below Threshold</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    // Add rows
                    data.data.forEach(warehouse => {
                        const capacity = parseInt(warehouse.whouse_storage_capacity) || 0;
                        const used = parseInt(warehouse.total_quantity) || 0;
                        let percent = 0;

                        if (capacity > 0 && used > 0) {
                            percent = Math.min(Math.round((used / capacity) * 100), 100);
                        }

                        let bgClass = 'bg-primary';
                        if (percent > 90) bgClass = 'bg-danger';
                        else if (percent > 70) bgClass = 'bg-warning';

                        html += `
                            <tr>
                                <td>${warehouse.whouse_name}</td>
                                <td>${warehouse.whouse_location}</td>
                                <td>${capacity > 0 ? capacity + ' units' : 'Unlimited'}</td>
                                <td>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar ${bgClass}" role="progressbar" 
                                             style="width: ${percent}%;" aria-valuenow="${percent}" aria-valuemin="0" 
                                             aria-valuemax="100">${percent}%</div>
                                    </div>
                                    <div class="text-muted small mt-1">${used} / ${capacity || '∞'} units</div>
                                </td>
                                <td>
                                    <span class="badge ${parseInt(warehouse.low_stock_product_count) > 0 ? 'bg-warning' : 'bg-success'}">
                                        ${warehouse.low_stock_product_count || 0}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary view-warehouse" 
                                                data-id="${warehouse.whouse_id}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success edit-warehouse" 
                                                data-id="${warehouse.whouse_id}"
                                                data-name="${warehouse.whouse_name}"
                                                data-location="${warehouse.whouse_location}"
                                                data-capacity="${warehouse.whouse_storage_capacity || ''}"
                                                data-threshold="${warehouse.whouse_restock_threshold || ''}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;

                    warehousesDashboard.innerHTML = html;

                    // Add event listeners to buttons
                    warehousesDashboard.querySelectorAll('.view-warehouse').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            console.log("View warehouse button clicked:", id);

                            // View warehouse details
                            if (window.inventoryHandler) {
                                window.inventoryHandler.viewWarehouseDetails(id);
                            } else {
                                viewWarehouseDetails(id);
                            }
                        });
                    });

                    warehousesDashboard.querySelectorAll('.edit-warehouse').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            const name = this.getAttribute('data-name');
                            const location = this.getAttribute('data-location');
                            const capacity = this.getAttribute('data-capacity');
                            const threshold = this.getAttribute('data-threshold');

                            console.log("Edit warehouse button clicked:", id);

                            // Open warehouse modal for editing
                            if (!document.getElementById('warehouseModal')) {
                                createWarehouseModal();
                            }

                            // Populate form fields
                            document.getElementById('warehouseId').value = id;
                            document.getElementById('warehouseName').value = name;
                            document.getElementById('warehouseLocation').value = location;
                            document.getElementById('warehouseCapacity').value = capacity;
                            document.getElementById('warehouseThreshold').value = threshold;

                            // Load warehouse list
                            loadWarehouseListForModal();

                            // Show modal
                            const modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
                            modal.show();
                        });
                    });
                } else {
                    // No warehouses found
                    warehousesDashboard.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No warehouses available. Click the "Warehouses" button to add a new warehouse.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error("Error loading warehouses data:", error);

                warehousesDashboard.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading warehouses data. Please try again.
                    </div>
                `;
            });
    }

    /**
     * Load product data (for products view)
     */
    function loadProductData() {
        console.log("Loading product data...");

        const productsView = document.getElementById('productsView');
        if (!productsView || productsView.classList.contains('d-none')) {
            console.log("Products view not visible, skipping data load");
            return;
        }

        const productsDashboard = document.querySelector('#productsView .card-body');
        if (!productsDashboard) {
            console.error("Products dashboard container not found");
            return;
        }

        // Show loading indicator
        productsDashboard.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading product data...</span>
                </div>
                <p class="mt-2">Loading product data...</p>
            </div>
        `;

        // Fetch products
        fetchWithTimeout(API_ENDPOINTS.GET_PRODUCTS, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Products data received:", data);

                if (data.success && data.data && data.data.length > 0) {
                    // Create table
                    let html = `
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Variants</th>
                                    <th>Total Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    // Add rows
                    data.data.forEach(product => {
                        const statusClasses = {
                            'Available': 'bg-success',
                            'Out of Stock': 'bg-danger',
                            'Discontinued': 'bg-secondary'
                        };

                        const bgClass = statusClasses[product.prod_availability_status] || 'bg-secondary';

                        html += `
                            <tr>
                                <td>
                                    <img src="${product.prod_image || '/api/placeholder/50/50'}" 
                                         alt="${product.prod_name}" 
                                         class="img-thumbnail" style="width: 50px; height: 50px;">
                                </td>
                                <td>${product.prod_name}</td>
                                <td>${product.prod_description ? (product.prod_description.length > 100 ? product.prod_description.substring(0, 100) + '...' : product.prod_description) : 'No description available'}</td>
                                <td><span class="badge bg-info">${product.variant_count || 0}</span></td>
                                <td>${product.total_stock || 0}</td>
                                <td><span class="badge ${bgClass}">${product.prod_availability_status}</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary view-product" 
                                                data-id="${product.prod_id}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning manage-inventory" 
                                                data-id="${product.prod_id}">
                                            <i class="bi bi-box-seam"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-product" 
                                                data-id="${product.prod_id}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;

                    productsDashboard.innerHTML = html;

                    // Add event listeners to buttons
                    productsDashboard.querySelectorAll('.view-product').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            console.log("View product button clicked:", id);

                            // View product details
                            if (window.inventoryHandler) {
                                window.inventoryHandler.viewProductDetails(id);
                            } else {
                                viewProductDetails(id);
                            }
                        });
                    });

                    productsDashboard.querySelectorAll('.manage-inventory').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            console.log("Manage inventory button clicked:", id);

                            // Open manage inventory modal
                            if (window.inventoryHandler) {
                                window.inventoryHandler.openManageInventory(id);
                            } else {
                                openManageInventory(id);
                            }
                        });
                    });

                    productsDashboard.querySelectorAll('.delete-product').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            console.log("Delete product button clicked:", id);

                            // Confirm product deletion
                            if (window.inventoryHandler) {
                                window.inventoryHandler.confirmDeleteProduct(id);
                            } else {
                                confirmDeleteProduct(id);
                            }
                        });
                    });
                } else {
                    // No products found
                    productsDashboard.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No products available. Click the "Add Product" button to add a new product.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error("Error loading products data:", error);

                productsDashboard.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading products data. Please try again.
                    </div>
                `;
            });
    }

    /**
     * Load inventory data (for inventory view)
     */
    function loadInventoryData() {
        console.log("Loading inventory data...");

        const inventoryView = document.getElementById('inventoryView');
        if (!inventoryView || inventoryView.classList.contains('d-none')) {
            console.log("Inventory view not visible, skipping data load");
            return;
        }

        const inventoryDashboard = document.querySelector('#inventoryView .card-body');
        if (!inventoryDashboard) {
            console.error("Inventory dashboard container not found");
            return;
        }

        // Show loading indicator
        inventoryDashboard.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading inventory data...</span>
                </div>
                <p class="mt-2">Loading inventory data...</p>
            </div>
        `;

        // Fetch inventory
        fetchWithTimeout(API_ENDPOINTS.GET_ALL_INVENTORY, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Inventory data received:", data);

                if (data.success && data.data && data.data.length > 0) {
                    // Create table
                    let html = `
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Warehouse</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    // Add rows
                    data.data.forEach(item => {
                        const typeClasses = {
                            'Regular': 'bg-primary',
                            'Display': 'bg-info',
                            'Reserve': 'bg-secondary',
                            'Damaged': 'bg-danger',
                            'Returned': 'bg-warning',
                            'Quarantine': 'bg-dark'
                        };

                        const bgClass = typeClasses[item.inve_type] || 'bg-secondary';

                        let statusClass = 'bg-success';
                        let statusText = 'In Stock';

                        if (parseInt(item.quantity) <= 0) {
                            statusClass = 'bg-danger';
                            statusText = 'Out of Stock';
                        } else if (parseInt(item.quantity) <= 5) {
                            statusClass = 'bg-warning';
                            statusText = 'Low Stock';
                        }

                        html += `
                            <tr>
                                <td>
                                    <div class="product-info d-flex align-items-center">
                                        <img src="${item.prod_image || '/api/placeholder/40/40'}" 
                                             alt="${item.prod_name}" 
                                             class="product-thumbnail me-2" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <div>
                                            <span class="product-name fw-medium">${item.prod_name}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>${item.var_capacity || 'N/A'}</td>
                                <td>${item.whouse_name || 'N/A'}</td>
                                <td><span class="badge ${bgClass}">${item.inve_type}</span></td>
                                <td><span class="fw-medium">${item.quantity || 0}</span></td>
                                <td><span class="badge ${statusClass}">${statusText}</span></td>
                                <td>${item.last_updated ? new Date(item.last_updated).toLocaleString() : 'N/A'}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary view-product" 
                                                data-id="${item.prod_id}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning manage-inventory" 
                                                data-id="${item.prod_id}">
                                            <i class="bi bi-box-seam"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;

                    inventoryDashboard.innerHTML = html;

                    // Add event listeners to buttons
                    inventoryDashboard.querySelectorAll('.view-product').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            console.log("View product button clicked:", id);

                            // View product details
                            if (window.inventoryHandler) {
                                window.inventoryHandler.viewProductDetails(id);
                            } else {
                                viewProductDetails(id);
                            }
                        });
                    });

                    inventoryDashboard.querySelectorAll('.manage-inventory').forEach(button => {
                        button.addEventListener('click', function () {
                            const id = this.getAttribute('data-id');
                            console.log("Manage inventory button clicked:", id);

                            // Open manage inventory modal
                            if (window.inventoryHandler) {
                                window.inventoryHandler.openManageInventory(id);
                            } else {
                                openManageInventory(id);
                            }
                        });
                    });
                } else {
                    // No inventory found
                    inventoryDashboard.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No inventory data available. Add products and stock to see inventory here.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error("Error loading inventory data:", error);

                inventoryDashboard.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading inventory data. Please try again.
                    </div>
                `;
            });
    }

    /**
     * View product details
     */
    function viewProductDetails(productId) {
        console.log("Viewing product details for ID:", productId);

        // If handler exists, use it
        if (window.inventoryHandler) {
            window.inventoryHandler.viewProductDetails(productId);
            return;
        }

        // Store current product ID
        currentProductId = productId;

        // Show loading indicator
        showAlert('info', 'Loading product details...');

        // Fetch product details
        fetchWithTimeout(API_ENDPOINTS.PRODUCT_DETAILS + productId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Product details received:", data);

                if (data.success) {
                    // Show product details modal
                    showProductDetailsModal(data.data);
                } else {
                    showAlert('error', 'Could not load product details: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error loading product details:", error);
                showAlert('error', 'Error loading product details');
            });
    }

    /**
     * Show product details modal
     */
    function showProductDetailsModal(data) {
        console.log("Showing product details modal with data:", data);

        // Check if modal exists, if not create it
        if (!document.getElementById('productDetailsModal')) {
            createProductDetailsModal();
        }

        const { product, variants, inventory } = data;

        // Basic product details
        document.getElementById('productDetailName').textContent = product.prod_name;
        document.getElementById('productDetailDescription').textContent = product.prod_description || 'No description available';
        document.getElementById('productDetailId').textContent = 'Product ID: ' + product.prod_id;
        document.getElementById('productDetailImage').src = product.prod_image || '/api/placeholder/150/150';

        // Status badge
        const statusBadge = document.getElementById('productDetailStatus');
        const statusClasses = {
            'Available': 'bg-success',
            'Out of Stock': 'bg-danger',
            'Discontinued': 'bg-secondary'
        };
        statusBadge.innerHTML = `<span class="badge ${statusClasses[product.prod_availability_status] || 'bg-secondary'}">${product.prod_availability_status}</span>`;

        // Features
        const featuresList = document.getElementById('productDetailFeatures');
        if (product.features && product.features.length > 0) {
            featuresList.innerHTML = product.features.map(feature =>
                `<li>${feature.feature_name}</li>`
            ).join('');
        } else {
            featuresList.innerHTML = '<li class="text-muted">No features specified</li>';
        }

        // Specifications
        const specsTable = document.getElementById('productDetailSpecs');
        if (product.specs && product.specs.length > 0) {
            specsTable.innerHTML = product.specs.map(spec =>
                `<tr><td class="fw-bold">${spec.spec_name}</td><td>${spec.spec_value}</td></tr>`
            ).join('');
        } else {
            specsTable.innerHTML = '<tr><td colspan="2" class="text-center text-muted">No specifications available</td></tr>';
        }

        // Variants
        const variantsTable = document.getElementById('productDetailVariants');
        if (variants && variants.length > 0) {
            variantsTable.innerHTML = variants.map(variant =>
                `<tr>
                    <td>${variant.var_capacity || 'N/A'}</td>
                    <td>${variant.var_power_consumption || 'N/A'}</td>
                    <td>${formatCurrency(variant.var_srp_price)}</td>
                    <td>${variant.var_price_free_install ? formatCurrency(variant.var_price_free_install) : 'N/A'}</td>
                    <td>${variant.var_price_with_install ? formatCurrency(variant.var_price_with_install) : 'N/A'}</td>
                </tr>`
            ).join('');
        } else {
            variantsTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No variants available</td></tr>';
        }

        // Inventory summary
        const inventoryContainer = document.getElementById('productDetailInventory');
        if (inventory && inventory.length > 0) {
            // Group inventory by warehouse and variant
            const inventoryByWarehouse = {};
            inventory.forEach(item => {
                const warehouseId = item.whouse_id;
                if (!inventoryByWarehouse[warehouseId]) {
                    inventoryByWarehouse[warehouseId] = {
                        name: item.whouse_name,
                        items: []
                    };
                }
                inventoryByWarehouse[warehouseId].items.push(item);
            });

            // Generate inventory cards
            let inventoryHTML = '';
            for (const warehouseId in inventoryByWarehouse) {
                const warehouse = inventoryByWarehouse[warehouseId];
                let totalQuantity = 0;
                warehouse.items.forEach(item => totalQuantity += parseInt(item.quantity || 0));

                inventoryHTML += `
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">${warehouse.name}</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Stock:</span>
                                    <span class="fw-bold">${totalQuantity} units</span>
                                </div>
                                <div class="inventory-types">
                                    ${warehouse.items.map(item => `
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>${item.inve_type}:</span>
                                            <span>${item.quantity || 0} units</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            inventoryContainer.innerHTML = inventoryHTML;
        } else {
            inventoryContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        No inventory data available for this product.
                    </div>
                </div>
            `;
        }

        // Set up edit button
        const editBtn = document.getElementById('editProductBtn');
        editBtn.setAttribute('data-id', product.prod_id);

        // Remove any existing event listeners to prevent duplicates
        const newEditBtn = editBtn.cloneNode(true);
        editBtn.parentNode.replaceChild(newEditBtn, editBtn);

        // Add new event listener
        newEditBtn.addEventListener('click', () => {
            // Edit product
            if (window.inventoryHandler) {
                window.inventoryHandler.editProduct(product.prod_id);
            } else {
                editProduct(product.prod_id);
            }

            // Close details modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('productDetailsModal'));
            if (modal) {
                modal.hide();
            }
        });

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
        modal.show();
    }

    /**
     * Create product details modal
     */
    function createProductDetailsModal() {
        console.log("Creating product details modal...");

        const modalHTML = `
            <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Product Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="product-detail-header d-flex mb-4">
                                <div class="product-detail-image me-3">
                                    <img src="/api/placeholder/150/150" alt="Product Image" id="productDetailImage" class="img-fluid rounded">
                                </div>
                                <div class="product-detail-info">
                                    <h4 class="product-detail-name" id="productDetailName">Product Name</h4>
                                    <p class="product-detail-description" id="productDetailDescription">Description will be loaded here.</p>
                                    <div class="product-detail-id text-muted" id="productDetailId">Product ID: --</div>
                                    <div class="product-detail-status" id="productDetailStatus"><span class="badge bg-success">Available</span></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Features</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="feature-list" id="productDetailFeatures">
                                                <!-- Features will be loaded dynamically -->
                                                <li class="text-muted">No features specified</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Specifications</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm spec-table">
                                                <tbody id="productDetailSpecs">
                                                    <!-- Specs will be loaded dynamically -->
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">No specifications available</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mb-3 mt-2">Product Variants</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered variant-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Capacity</th>
                                            <th>CSPF</th>
                                            <th>SRP Price</th>
                                            <th>Free Install Price</th>
                                            <th>With Install Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productDetailVariants">
                                        <!-- Variants will be loaded dynamically -->
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No variants available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="inventory-detail-cards">
                                <div class="row" id="productDetailInventory">
                                    <!-- Inventory cards will be loaded dynamically -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="editProductBtn">Edit Product</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Open manage inventory modal
     */
    function openManageInventory(productId) {
        console.log("Opening manage inventory modal for product ID:", productId);

        // If handler exists, use it
        if (window.inventoryHandler) {
            window.inventoryHandler.openManageInventory(productId);
            return;
        }

        // Store current product ID
        currentProductId = productId;

        // Show loading indicator
        showAlert('info', 'Loading inventory data...');

        // Fetch product details
        fetchWithTimeout(API_ENDPOINTS.PRODUCT_DETAILS + productId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Product details received for inventory management:", data);

                if (data.success) {
                    // Check if manage inventory modal exists, if not create it
                    if (!document.getElementById('manageInventoryModal')) {
                        createManageInventoryModal();
                    }

                    // Show manage inventory modal
                    showManageInventoryModal(data.data, productId);
                } else {
                    showAlert('error', 'Could not load product inventory: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error loading product inventory:", error);
                showAlert('error', 'Error loading product inventory');
            });
    }

    /**
     * Create manage inventory modal
     */
    function createManageInventoryModal() {
        console.log("Creating manage inventory modal...");

        const modalHTML = `
            <div class="modal fade" id="manageInventoryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Manage Inventory</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="current-stock-tab" data-bs-toggle="tab" data-bs-target="#current-stock" type="button" role="tab">Current Stock</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="add-stock-tab" data-bs-toggle="tab" data-bs-target="#add-stock" type="button" role="tab">Add Stock</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="move-stock-tab" data-bs-toggle="tab" data-bs-target="#move-stock" type="button" role="tab">Move Stock</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="stock-history-tab" data-bs-toggle="tab" data-bs-target="#stock-history" type="button" role="tab">Stock History</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="inventoryTabsContent">
                                <!-- Current Stock Tab -->
                                <div class="tab-pane fade show active" id="current-stock" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Variant</th>
                                                    <th>Warehouse</th>
                                                    <th>Inventory Type</th>
                                                    <th>Quantity</th>
                                                    <th>Last Updated</th>
                                                </tr>
                                            </thead>
                                            <tbody id="currentStockTable">
                                                <!-- Current stock will be loaded dynamically -->
                                                <tr>
                                                    <td colspan="5" class="text-center py-3">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Add Stock Tab -->
                                <div class="tab-pane fade" id="add-stock" role="tabpanel">
                                    <form id="addStockForm">
                                        <input type="hidden" id="addStockProductId" name="prod_id">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="addVariantSelect" class="form-label">Select Variant</label>
                                                <select class="form-select" id="addVariantSelect" name="var_id" required>
                                                    <!-- Variants will be loaded dynamically -->
                                                    <option value="">Loading variants...</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="addWarehouseSelect" class="form-label">Select Warehouse</label>
                                                <select class="form-select" id="addWarehouseSelect" name="whouse_id" required>
                                                    <!-- Warehouses will be loaded dynamically -->
                                                    <option value="">Loading warehouses...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="addInventoryType" class="form-label">Inventory Type</label>
                                                <select class="form-select" id="addInventoryType" name="inve_type">
                                                    <option value="Regular">Regular</option>
                                                    <option value="Display">Display</option>
                                                    <option value="Reserve">Reserve</option>
                                                    <option value="Damaged">Damaged</option>
                                                    <option value="Returned">Returned</option>
                                                    <option value="Quarantine">Quarantine</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="addQuantity" class="form-label">Quantity to Add</label>
                                                <input type="number" class="form-control" id="addQuantity" name="quantity" min="1" value="1" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addReason" class="form-label">Reason</label>
                                            <select class="form-select" id="addReason" name="reason">
                                                <option value="Initial">Initial Stock</option>
                                                <option value="Restock">Restock</option>
                                                <option value="Return">Customer Return</option>
                                                <option value="Adjustment">Inventory Adjustment</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addNotes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="addNotes" name="notes" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Stock</button>
                                    </form>
                                </div>
                                
                                <!-- Move Stock Tab -->
                                <div class="tab-pane fade" id="move-stock" role="tabpanel">
                                    <form id="moveStockForm">
                                        <input type="hidden" id="moveStockProductId" name="prod_id">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="moveVariantSelect" class="form-label">Select Variant</label>
                                                <select class="form-select" id="moveVariantSelect" name="var_id" required>
                                                    <!-- Variants will be loaded dynamically -->
                                                    <option value="">Loading variants...</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="moveInventoryType" class="form-label">Inventory Type</label>
                                                <select class="form-select" id="moveInventoryType" name="inve_type">
                                                    <option value="Regular">Regular</option>
                                                    <option value="Display">Display</option>
                                                    <option value="Reserve">Reserve</option>
                                                    <option value="Damaged">Damaged</option>
                                                    <option value="Returned">Returned</option>
                                                    <option value="Quarantine">Quarantine</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="sourceWarehouse" class="form-label">Source Warehouse</label>
                                                <select class="form-select" id="sourceWarehouse" name="source_warehouse_id" required>
                                                    <!-- Warehouses will be loaded dynamically -->
                                                    <option value="">Loading warehouses...</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="destinationWarehouse" class="form-label">Destination Warehouse</label>
                                                <select class="form-select" id="destinationWarehouse" name="destination_warehouse_id" required>
                                                    <!-- Warehouses will be loaded dynamically -->
                                                    <option value="">Loading warehouses...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="moveQuantity" class="form-label">Quantity to Move</label>
                                            <input type="number" class="form-control" id="moveQuantity" name="quantity" min="1" value="1" required>
                                            <small class="form-text text-muted available-quantity">Available in source: -- units</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="moveNotes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="moveNotes" name="notes" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Move Stock</button>
                                    </form>
                                </div>
                                
                                <!-- Stock History Tab -->
                                <div class="tab-pane fade" id="stock-history" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Variant</th>
                                                    <th>Warehouse</th>
                                                    <th>Type</th>
                                                    <th>Action</th>
                                                    <th>Quantity</th>
                                                    <th>User</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stockHistoryTable">
                                                <!-- Stock history will be loaded dynamically -->
                                                <tr>
                                                    <td colspan="7" class="text-center py-3">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="bi bi-info-circle me-2"></i>
                                                            Stock history feature coming soon.
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Add event listeners
        const addStockForm = document.getElementById('addStockForm');
        if (addStockForm) {
            addStockForm.addEventListener('submit', function (event) {
                event.preventDefault();
                submitAddStock();
            });
        }

        const moveStockForm = document.getElementById('moveStockForm');
        if (moveStockForm) {
            moveStockForm.addEventListener('submit', function (event) {
                event.preventDefault();
                submitMoveStock();
            });
        }

        // Add tab change listeners
        document.querySelectorAll('#inventoryTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-bs-target');

                if (targetTab === '#add-stock') {
                    loadVariantsForStock(currentProductId);
                    loadWarehousesForStockDropdown();
                } else if (targetTab === '#move-stock') {
                    loadVariantsForStock(currentProductId);
                    loadWarehousesForStockDropdown();
                }
            });
        });
    }

    /**
     * Show manage inventory modal
     */
    function showManageInventoryModal(data, productId) {
        console.log("Showing manage inventory modal for product ID:", productId);

        const { product, variants, inventory } = data;

        // Set product IDs in forms
        document.getElementById('addStockProductId').value = productId;
        document.getElementById('moveStockProductId').value = productId;

        // Load current stock
        const currentStockTable = document.getElementById('currentStockTable');
        if (inventory && inventory.length > 0) {
            let html = '';
            inventory.forEach(item => {
                // Find variant name
                let variantName = 'Default';
                if (variants && variants.length > 0) {
                    const variant = variants.find(v => v.var_id === item.var_id);
                    if (variant) {
                        variantName = variant.var_capacity || 'Default';
                    }
                }

                html += `
                    <tr>
                        <td>${variantName}</td>
                        <td>${item.whouse_name}</td>
                        <td><span class="badge ${getTypeClass(item.inve_type)}">${item.inve_type}</span></td>
                        <td>${item.quantity}</td>
                        <td>${formatDate(item.last_updated)}</td>
                    </tr>
                `;
            });
            currentStockTable.innerHTML = html;
        } else {
            currentStockTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-3">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            No inventory data available for this product.
                        </div>
                    </td>
                </tr>
            `;
        }

        // Load variants for dropdowns
        loadVariantsForStock(productId, variants);

        // Load warehouses for dropdowns
        loadWarehousesForStockDropdown();

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('manageInventoryModal'));
        modal.show();

        // Activate first tab
        const firstTab = document.querySelector('#inventoryTabs .nav-link');
        if (firstTab && bootstrap && bootstrap.Tab) {
            const tab = new bootstrap.Tab(firstTab);
            tab.show();
        }
    }

    /**
     * Load variants for stock management
     */
    function loadVariantsForStock(productId, variants = null) {
        console.log("Loading variants for stock management...");

        const addVariantSelect = document.getElementById('addVariantSelect');
        const moveVariantSelect = document.getElementById('moveVariantSelect');

        if (!addVariantSelect || !moveVariantSelect) {
            console.error("Variant select elements not found");
            return;
        }

        // Show loading indicator
        addVariantSelect.innerHTML = '<option value="">Loading variants...</option>';
        moveVariantSelect.innerHTML = '<option value="">Loading variants...</option>';

        if (variants) {
            // Use provided variants
            let html = '';
            variants.forEach(variant => {
                html += `<option value="${variant.var_id}">${variant.var_capacity || 'Default Variant'}</option>`;
            });

            addVariantSelect.innerHTML = html;
            moveVariantSelect.innerHTML = html;
        } else {
            // Fetch variants from server
            fetchWithTimeout(API_ENDPOINTS.PRODUCT_DETAILS + productId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Variants data received:", data);

                    if (data.success && data.data.variants && data.data.variants.length > 0) {
                        let html = '';
                        data.data.variants.forEach(variant => {
                            html += `<option value="${variant.var_id}">${variant.var_capacity || 'Default Variant'}</option>`;
                        });

                        addVariantSelect.innerHTML = html;
                        moveVariantSelect.innerHTML = html;
                    } else {
                        addVariantSelect.innerHTML = '<option value="">No variants available</option>';
                        moveVariantSelect.innerHTML = '<option value="">No variants available</option>';
                    }
                })
                .catch(error => {
                    console.error("Error loading variants:", error);
                    addVariantSelect.innerHTML = '<option value="">Error loading variants</option>';
                    moveVariantSelect.innerHTML = '<option value="">Error loading variants</option>';
                });
        }
    }

    /**
     * Load warehouses for stock management dropdowns
     */
    function loadWarehousesForStockDropdown() {
        console.log("Loading warehouses for stock management...");

        const addWarehouseSelect = document.getElementById('addWarehouseSelect');
        const sourceWarehouse = document.getElementById('sourceWarehouse');
        const destinationWarehouse = document.getElementById('destinationWarehouse');

        if (!addWarehouseSelect || !sourceWarehouse || !destinationWarehouse) {
            console.error("Warehouse select elements not found");
            return;
        }

        // Show loading indicator
        addWarehouseSelect.innerHTML = '<option value="">Loading warehouses...</option>';
        sourceWarehouse.innerHTML = '<option value="">Loading warehouses...</option>';
        destinationWarehouse.innerHTML = '<option value="">Loading warehouses...</option>';

        // Fetch warehouses
        fetchWithTimeout(API_ENDPOINTS.GET_ALL_WAREHOUSES, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Warehouses for stock management received:", data);

                if (data.success && data.data && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(warehouse => {
                        html += `<option value="${warehouse.whouse_id}">${warehouse.whouse_name}</option>`;
                    });

                    addWarehouseSelect.innerHTML = html;
                    sourceWarehouse.innerHTML = html;
                    destinationWarehouse.innerHTML = html;
                } else {
                    const noWarehouseOption = '<option value="">No warehouses available</option>';
                    addWarehouseSelect.innerHTML = noWarehouseOption;
                    sourceWarehouse.innerHTML = noWarehouseOption;
                    destinationWarehouse.innerHTML = noWarehouseOption;
                }
            })
            .catch(error => {
                console.error("Error loading warehouses for stock management:", error);
                const errorOption = '<option value="">Error loading warehouses</option>';
                addWarehouseSelect.innerHTML = errorOption;
                sourceWarehouse.innerHTML = errorOption;
                destinationWarehouse.innerHTML = errorOption;
            });
    }

    // Add Stock Form Submission
    function submitAddStock() {
        console.log("Submitting add stock form...");

        const form = document.getElementById('addStockForm');
        if (!form) {
            console.error("Add stock form not found");
            return;
        }

        // Show adding indicator
        showAlert('info', 'Adding stock...');

        const formData = new FormData(form);
        const addStockData = {
            prod_id: formData.get('prod_id'),
            var_id: formData.get('var_id'),  // Ensure that variant ID is included
            whouse_id: formData.get('whouse_id'),
            inve_type: formData.get('inve_type'),  // Ensure that inventory type is included
            quantity: formData.get('quantity'),
            reason: formData.get('reason'),
            notes: formData.get('notes')
        };

        // Validate required fields
        if (!addStockData.var_id || !addStockData.whouse_id || !addStockData.quantity || !addStockData.inve_type) {
            showAlert('error', 'Please fill all required fields');
            return;
        }

        // Send add stock request
        fetchWithTimeout(API_ENDPOINTS.ADD_STOCK, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(addStockData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || `HTTP error ${response.status}`);
                    }).catch(() => {
                        throw new Error(`HTTP error ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Stock added successfully');
                    form.reset();
                    loadInventoryData();  // Refresh inventory after adding stock
                } else {
                    showAlert('error', data.message || 'Failed to add stock');
                }
            })
            .catch(error => {
                console.error("Error adding stock:", error);
                showAlert('error', `Error adding stock: ${error.message}`);
            });
    }

    // Move Stock Form Submission
    function submitMoveStock() {
        console.log("Submitting move stock form...");

        const form = document.getElementById('moveStockForm');
        if (!form) {
            console.error("Move stock form not found");
            return;
        }

        // Show moving indicator
        showAlert('info', 'Moving stock...');

        const formData = new FormData(form);
        const moveStockData = {
            prod_id: formData.get('prod_id'),
            var_id: formData.get('var_id'),  // Ensure that variant ID is included
            source_warehouse_id: formData.get('source_warehouse_id'),
            destination_warehouse_id: formData.get('destination_warehouse_id'),
            inve_type: formData.get('inve_type'),  // Ensure that inventory type is included
            quantity: formData.get('quantity'),
            notes: formData.get('notes')
        };

        // Validate required fields
        if (!moveStockData.var_id || !moveStockData.source_warehouse_id || !moveStockData.destination_warehouse_id || !moveStockData.quantity || !moveStockData.inve_type) {
            showAlert('error', 'Please fill all required fields');
            return;
        }

        // Check if source and destination are different
        if (moveStockData.source_warehouse_id === moveStockData.destination_warehouse_id) {
            showAlert('error', 'Source and destination warehouses must be different');
            return;
        }

        fetchWithTimeout(API_ENDPOINTS.MOVE_STOCK, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(moveStockData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || `HTTP error ${response.status}`);
                    }).catch(() => {
                        throw new Error(`HTTP error ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Stock moved successfully');
                    form.reset();
                    loadInventoryData();  // Refresh inventory after moving stock
                } else {
                    showAlert('error', data.message || 'Failed to move stock');
                }
            })
            .catch(error => {
                console.error("Error moving stock:", error);
                showAlert('error', `Error moving stock: ${error.message}`);
            });
    }

    /**
     * Save product
     */
    function saveProduct() {
        console.log("Saving product...");

        const form = document.getElementById('productForm');
        if (!form) {
            console.error("Product form not found");
            return;
        }

        // Show saving indicator
        showAlert('info', 'Saving product...');

        // Validate form
        const productName = document.getElementById('productName').value;
        if (!productName) {
            showAlert('error', 'Product name is required');
            return;
        }

        // Check if at least one variant exists
        const variantForms = document.querySelectorAll('.variant-form');
        if (variantForms.length === 0) {
            showAlert('error', 'At least one variant is required');
            return;
        }

        // Create FormData object
        const formData = new FormData(form);

        // Add JSON data for variants, features, and specs
        const variants = collectVariantsData();
        formData.append('variants', JSON.stringify(variants));

        const features = collectFeaturesData();
        formData.append('features', JSON.stringify(features));

        const specs = collectSpecsData();
        formData.append('specs', JSON.stringify(specs));

        // Add inventory data
        const inventory = collectInventoryData();
        formData.append('inventory', JSON.stringify(inventory));

        // Determine if this is create or update
        const isUpdate = currentProductId !== null;
        const url = isUpdate ? API_ENDPOINTS.UPDATE_PRODUCT + currentProductId : API_ENDPOINTS.CREATE_PRODUCT;
        const method = isUpdate ? 'PUT' : 'POST';

        // Submit form with timeout handling
        const submitPromise = fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const timeoutPromise = new Promise((_, reject) =>
            setTimeout(() => reject(new Error("Request timeout")), 30000)
        );

        Promise.race([submitPromise, timeoutPromise])
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || `HTTP error ${response.status}`);
                    }).catch(() => {
                        throw new Error(`HTTP error ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log("Product save response:", data);

                if (data.success) {
                    // Show success message
                    showAlert('success', `Product ${isUpdate ? 'updated' : 'created'} successfully`);

                    // Reset form
                    resetProductForm();

                    // Reset current product ID if update
                    if (isUpdate) {
                        currentProductId = null;
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Refresh dashboard stats
                    loadDashboardStats();

                    // Refresh current view
                    if (currentView === 'inventory') {
                        loadInventoryData();
                    } else if (currentView === 'products') {
                        loadProductData();
                    }
                } else {
                    showAlert('error', `Could not ${isUpdate ? 'update' : 'create'} product: ${data.message}`);
                }
            })
            .catch(error => {
                console.error(`Error ${isUpdate ? 'updating' : 'creating'} product:`, error);
                showAlert('error', `Error ${isUpdate ? 'updating' : 'creating'} product: ${error.message}`);
            });
    }

    /**
     * Collect variants data from form
     */
    function collectVariantsData() {
        const variants = [];
        const variantForms = document.querySelectorAll('.variant-form');

        variantForms.forEach((form) => {
            const capacityInput = form.querySelector('[name^="variants"][name$="[var_capacity]"]');
            const powerInput = form.querySelector('[name^="variants"][name$="[var_power_consumption]"]');
            const srpPriceInput = form.querySelector('[name^="variants"][name$="[var_srp_price]"]');
            const freeInstallInput = form.querySelector('[name^="variants"][name$="[var_price_free_install]"]');
            const withInstallInput = form.querySelector('[name^="variants"][name$="[var_price_with_install]"]');

            variants.push({
                var_capacity: capacityInput ? capacityInput.value : '',
                var_power_consumption: powerInput ? powerInput.value : '',
                var_srp_price: srpPriceInput ? srpPriceInput.value : '',
                var_price_free_install: freeInstallInput ? freeInstallInput.value : '',
                var_price_with_install: withInstallInput ? withInstallInput.value : ''
            });
        });

        return variants;
    }

    /**
     * Collect features data from form
     */
    function collectFeaturesData() {
        const features = [];
        const featureInputs = document.querySelectorAll('.features-container input');

        featureInputs.forEach(input => {
            if (input.value) {
                features.push(input.value);
            }
        });

        return features;
    }

    /**
     * Collect specifications data from form
     */
    function collectSpecsData() {
        const specs = [];
        const specRows = document.querySelectorAll('.specs-container .row');

        specRows.forEach(row => {
            const nameInput = row.querySelector('[name$="[spec_name]"]');
            const valueInput = row.querySelector('[name$="[spec_value]"]');

            if (nameInput && valueInput && nameInput.value && valueInput.value) {
                specs.push({
                    spec_name: nameInput.value,
                    spec_value: valueInput.value
                });
            }
        });

        return specs;
    }

    /**
     * Collect inventory data from form
     */
    function collectInventoryData() {
        const inventory = [];
        const variantInventories = document.querySelectorAll('.variant-inventory');
        const warehouseId = document.getElementById('warehouseSelect').value;
        const inventoryType = document.getElementById('inventoryType').value;

        if (!warehouseId) {
            return inventory;
        }

        variantInventories.forEach((invDiv) => {
            const quantityInput = invDiv.querySelector('[name$="[quantity]"]');
            const variantIndexInput = invDiv.querySelector('[name$="[variant_index]"]');

            if (quantityInput && parseInt(quantityInput.value) > 0 && variantIndexInput) {
                inventory.push({
                    variant_id: variantIndexInput.value,
                    warehouse_id: warehouseId,
                    type: inventoryType,
                    quantity: quantityInput.value
                });
            }
        });

        return inventory;
    }

    /**
     * Reset product form
     */
    function resetProductForm() {
        console.log("Resetting product form...");

        const form = document.getElementById('productForm');
        if (form) {
            form.reset();
        }

        // Reset basic fields
        const productNameElem = document.getElementById('productName');
        const productDescriptionElem = document.getElementById('productDescription');
        const productAvailabilityElem = document.getElementById('productAvailability');
        const productImageElem = document.getElementById('productImage');

        if (productNameElem) productNameElem.value = '';
        if (productDescriptionElem) productDescriptionElem.value = '';
        if (productAvailabilityElem) productAvailabilityElem.value = 'Available';
        if (productImageElem) productImageElem.value = '';

        // Reset variants (keep only first one)
        const variantsContainer = document.querySelector('.variants-container');
        if (variantsContainer) {
            const variantForms = variantsContainer.querySelectorAll('.variant-form');

            if (variantForms.length > 0) {
                // Clear first form fields
                const firstForm = variantForms[0];
                const inputs = firstForm.querySelectorAll('input');
                inputs.forEach(input => input.value = '');

                // Remove additional forms
                for (let i = 1; i < variantForms.length; i++) {
                    variantForms[i].remove();
                }
            }
        }

        // Reset features
        const featuresContainer = document.querySelector('.features-container');
        if (featuresContainer) {
            const featureInputs = featuresContainer.querySelectorAll('.input-group');

            if (featureInputs.length > 0) {
                // Clear first feature
                featureInputs[0].querySelector('input').value = '';

                // Remove additional features
                for (let i = 1; i < featureInputs.length; i++) {
                    featureInputs[i].remove();
                }
            }
        }

        // Reset specifications
        const specsContainer = document.querySelector('.specs-container');
        if (specsContainer) {
            const specRows = specsContainer.querySelectorAll('.row');

            if (specRows.length > 0) {
                // Clear first spec
                const inputs = specRows[0].querySelectorAll('input');
                inputs.forEach(input => input.value = '');

                // Remove additional specs
                for (let i = 1; i < specRows.length; i++) {
                    specRows[i].remove();
                }
            }
        }

        // Reset inventory tab
        updateInventoryVariants();

        // Reset counters
        variantCounter = 0;
        featureCounter = 0;
        specCounter = 0;

        // Reset current product ID
        currentProductId = null;

        // Switch to first tab
        const firstTab = document.querySelector('#product-info-tab');
        if (firstTab && bootstrap && bootstrap.Tab) {
            const tab = new bootstrap.Tab(firstTab);
            tab.show();
        }
    }

    /**
     * Edit product
     */
    /**
 * Edit product
 */
    function editProduct(productId) {
        console.log("Editing product:", productId);

        // Store current product ID
        currentProductId = productId;

        // Show loading indicator
        showAlert('info', 'Loading product data...');

        // Fetch product details
        fetchWithTimeout(API_ENDPOINTS.PRODUCT_DETAILS + productId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Product data for editing received:", data);

                if (data.success) {
                    // Close the product details modal if it's open
                    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('productDetailsModal'));
                    if (detailsModal) {
                        detailsModal.hide();
                    }

                    // Reset form first
                    resetProductForm();

                    // Clear warehouse dropdown and reset its loading state
                    const warehouseSelect = document.getElementById('warehouseSelect');
                    if (warehouseSelect) {
                        warehouseSelect.innerHTML = '<option value="">Select warehouse</option>';
                        warehouseSelect.dataset.loading = 'false';
                    }

                    // Populate form with product data
                    populateProductForm(data.data);

                    // Update modal title
                    document.getElementById('addProductModalLabel').textContent = 'Edit Product';
                    document.getElementById('saveProductBtn').textContent = 'Update Product';

                    // Show edit modal
                    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
                    modal.show();

                    // Load warehouses for dropdown after modal is shown
                    setTimeout(() => {
                        loadWarehousesForDropdown();
                    }, 100);
                } else {
                    showAlert('error', 'Could not load product details: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error loading product details for editing:", error);
                showAlert('error', 'Error loading product details');
            });
    }
    /**
     * Populate product form with data for editing
     */
    function populateProductForm(data) {
        console.log("Populating product form with data:", data);

        const { product, variants, inventory } = data;

        // Basic product details
        document.getElementById('productName').value = product.prod_name || '';
        document.getElementById('productDescription').value = product.prod_description || '';
        document.getElementById('productAvailability').value = product.prod_availability_status || 'Available';

        // Variants data
        if (variants && variants.length > 0) {
            // Fill first variant form
            const firstVariantForm = document.querySelector('.variant-form');
            if (firstVariantForm) {
                firstVariantForm.querySelector('[name^="variants"][name$="[var_capacity]"]').value = variants[0].var_capacity || '';
                firstVariantForm.querySelector('[name^="variants"][name$="[var_power_consumption]"]').value = variants[0].var_power_consumption || '';
                firstVariantForm.querySelector('[name^="variants"][name$="[var_srp_price]"]').value = variants[0].var_srp_price || '';
                firstVariantForm.querySelector('[name^="variants"][name$="[var_price_free_install]"]').value = variants[0].var_price_free_install || '';
                firstVariantForm.querySelector('[name^="variants"][name$="[var_price_with_install]"]').value = variants[0].var_price_with_install || '';
            }

            // Add additional variant forms for each variant
            for (let i = 1; i < variants.length; i++) {
                addNewVariantForm();
                const newForm = document.querySelectorAll('.variant-form')[i];
                if (newForm) {
                    newForm.querySelector('[name^="variants"][name$="[var_capacity]"]').value = variants[i].var_capacity || '';
                    newForm.querySelector('[name^="variants"][name$="[var_power_consumption]"]').value = variants[i].var_power_consumption || '';
                    newForm.querySelector('[name^="variants"][name$="[var_srp_price]"]').value = variants[i].var_srp_price || '';
                    newForm.querySelector('[name^="variants"][name$="[var_price_free_install]"]').value = variants[i].var_price_free_install || '';
                    newForm.querySelector('[name^="variants"][name$="[var_price_with_install]"]').value = variants[i].var_price_with_install || '';
                }
            }
        }

        // Features
        if (product.features && product.features.length > 0) {
            const featuresContainer = document.querySelector('.features-container');

            // Clear container
            featuresContainer.innerHTML = '';

            // Add feature inputs
            product.features.forEach((feature, index) => {
                const featureHTML = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="features[${index}]" value="${feature.feature_name || ''}" placeholder="Enter feature">
                        <button class="btn btn-outline-danger remove-feature" type="button">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                featuresContainer.insertAdjacentHTML('beforeend', featureHTML);
                featureCounter = index;
            });
        }

        // Specifications
        if (product.specs && product.specs.length > 0) {
            const specsContainer = document.querySelector('.specs-container');

            // Clear container
            specsContainer.innerHTML = '';

            // Add spec inputs
            product.specs.forEach((spec, index) => {
                const specHTML = `
                    <div class="row mb-2">
                        <div class="col-5">
                            <input type="text" class="form-control" name="specs[${index}][spec_name]" value="${spec.spec_name || ''}" placeholder="Spec name">
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="specs[${index}][spec_value]" value="${spec.spec_value || ''}" placeholder="Spec value">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-outline-danger w-100 remove-spec" type="button">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `;
                specsContainer.insertAdjacentHTML('beforeend', specHTML);
                specCounter = index;
            });
        }

        // Update inventory variants tab
        updateInventoryVariants();
    }

    /**
     * Confirm delete product
     */
    function confirmDeleteProduct(productId) {
        console.log("Confirming product deletion:", productId);

        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            // Show deleting indicator
            showAlert('info', 'Deleting product...');

            // Send delete request
            fetchWithTimeout(API_ENDPOINTS.DELETE_PRODUCT + productId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `HTTP error ${response.status}`);
                        }).catch(() => {
                            throw new Error(`HTTP error ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Product delete response:", data);

                    if (data.success) {
                        // Show success message
                        showAlert('success', 'Product deleted successfully');

                        // Refresh dashboard stats
                        loadDashboardStats();

                        // Refresh current view
                        if (currentView === 'inventory') {
                            loadInventoryData();
                        } else if (currentView === 'products') {
                            loadProductData();
                        }
                    } else {
                        showAlert('error', data.message || 'Failed to delete product');
                    }
                })
                .catch(error => {
                    console.error("Error deleting product:", error);
                    showAlert('error', `Error deleting product: ${error.message}`);
                });
        }
    }

    /**
     * View warehouse details
     */
    function viewWarehouseDetails(warehouseId) {
        console.log("View warehouse details (ID: " + warehouseId + ")");

        // Store current warehouse ID
        currentWarehouseId = warehouseId;

        // Show loading indicator
        showAlert('info', 'Loading warehouse details...');

        // Fetch warehouse data
        fetchWithTimeout(API_ENDPOINTS.GET_WAREHOUSE + warehouseId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Warehouse data received:", data);

                if (data.success) {
                    // Fetch warehouse inventory
                    fetchWithTimeout(API_ENDPOINTS.GET_WAREHOUSE_INVENTORY + warehouseId, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(inventoryData => {
                            console.log("Warehouse inventory received:", inventoryData);

                            // TODO: Implement warehouse details view
                            showAlert('info', 'Warehouse details view will be implemented in the next update');
                        })
                        .catch(error => {
                            console.error("Error loading warehouse inventory:", error);
                            showAlert('error', 'Error loading warehouse inventory');
                        });
                } else {
                    showAlert('error', 'Could not load warehouse details: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error loading warehouse details:", error);
                showAlert('error', 'Error loading warehouse details');
            });
    }

    /**
     * Initialize fallback functionality
     */
    function initializeFallbackFunctionality() {
        console.log("Initializing fallback functionality...");

        // Load default view
        const activeView = document.querySelector('.view-selector .btn.active');
        if (activeView) {
            const viewType = activeView.getAttribute('data-view');
            currentView = viewType;

            if (viewType === 'inventory') {
                loadInventoryData();
            } else if (viewType === 'products') {
                loadProductData();
            } else if (viewType === 'warehouses') {
                loadWarehouseData();
            }
        } else {
            // Default to inventory view
            loadInventoryData();
        }
    }


    /**
     * Helper function to get CSS class for inventory type
     */
    function getTypeClass(type) {
        const typeClasses = {
            'Regular': 'bg-primary',
            'Display': 'bg-info',
            'Reserve': 'bg-secondary',
            'Damaged': 'bg-danger',
            'Returned': 'bg-warning',
            'Quarantine': 'bg-dark'
        };

        return typeClasses[type] || 'bg-secondary';
    }

    /**
     * Format currency value
     */
    function formatCurrency(value) {
        if (!value && value !== 0) return 'N/A';

        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(value);
    }

    /**
     * Format date value
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';

        return new Date(dateString).toLocaleString();
    }

    /**
     * Fetch with timeout
     */
    function fetchWithTimeout(url, options, timeout = 15000) {
        return Promise.race([
            fetch(url, options),
            new Promise((_, reject) =>
                setTimeout(() => reject(new Error('Request timed out')), timeout)
            )
        ]);
    }

    /**
     * Filter inventory by type
     */
    function filterInventoryByType(type) {
        console.log("Filtering inventory by type:", type);

        const url = type === 'all' ? API_ENDPOINTS.GET_ALL_INVENTORY : API_ENDPOINTS.GET_INVENTORY_BY_TYPE + '/' + type;

        fetchWithTimeout(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Filtered inventory data received:", data);

                if (data.success) {
                    // Update inventory view
                    const inventoryDashboard = document.querySelector('#inventoryView .card-body');
                    if (inventoryDashboard) {
                        // Create filtered table
                        let html = `
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Warehouse</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        if (data.data && data.data.length > 0) {
                            // Add rows
                            data.data.forEach(item => {
                                const typeClasses = {
                                    'Regular': 'bg-primary',
                                    'Display': 'bg-info',
                                    'Reserve': 'bg-secondary',
                                    'Damaged': 'bg-danger',
                                    'Returned': 'bg-warning',
                                    'Quarantine': 'bg-dark'
                                };

                                const bgClass = typeClasses[item.inve_type] || 'bg-secondary';

                                let statusClass = 'bg-success';
                                let statusText = 'In Stock';

                                if (parseInt(item.quantity) <= 0) {
                                    statusClass = 'bg-danger';
                                    statusText = 'Out of Stock';
                                } else if (parseInt(item.quantity) <= 5) {
                                    statusClass = 'bg-warning';
                                    statusText = 'Low Stock';
                                }

                                html += `
                                    <tr>
                                        <td>
                                            <div class="product-info d-flex align-items-center">
                                                <img src="${item.prod_image || '/api/placeholder/40/40'}" 
                                                     alt="${item.prod_name}" 
                                                     class="product-thumbnail me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                <div>
                                                    <span class="product-name fw-medium">${item.prod_name}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${item.var_capacity || 'N/A'}</td>
                                        <td>${item.whouse_name || 'N/A'}</td>
                                        <td><span class="badge ${bgClass}">${item.inve_type}</span></td>
                                        <td><span class="fw-medium">${item.quantity || 0}</span></td>
                                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                                        <td>${item.last_updated ? new Date(item.last_updated).toLocaleString() : 'N/A'}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary view-product" 
                                                        data-id="${item.prod_id}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning manage-inventory" 
                                                        data-id="${item.prod_id}">
                                                    <i class="bi bi-box-seam"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `
                                <tr>
                                    <td colspan="8" class="text-center py-3">
                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle me-2"></i>
                                            No inventory items found for type: ${type}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }

                        html += `
                                </tbody>
                            </table>
                        `;

                        inventoryDashboard.innerHTML = html;

                        // Add event listeners
                        inventoryDashboard.querySelectorAll('.view-product').forEach(button => {
                            button.addEventListener('click', function () {
                                const id = this.getAttribute('data-id');
                                viewProductDetails(id);
                            });
                        });

                        inventoryDashboard.querySelectorAll('.manage-inventory').forEach(button => {
                            button.addEventListener('click', function () {
                                const id = this.getAttribute('data-id');
                                openManageInventory(id);
                            });
                        });
                    }
                } else {
                    showAlert('error', 'Could not filter inventory: ' + data.message);
                }
            })
            .catch(error => {
                console.error("Error filtering inventory:", error);
                showAlert('error', 'Error filtering inventory');
            });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        console.log(`Showing ${type} alert:`, message);

        const alertClasses = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'info': 'alert-info',
            'warning': 'alert-warning'
        };

        const alertClass = alertClasses[type] || 'alert-info';

        // Create alert element
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alertContainer.role = 'alert';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.maxWidth = '400px';
        alertContainer.style.zIndex = '9999';
        alertContainer.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';

        alertContainer.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Add to body
        document.body.appendChild(alertContainer);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertContainer && alertContainer.parentNode) {
                alertContainer.remove();
            }
        }, 5000);
    }
});

/**
 * Global error handler for better debugging
 * This ensures any uncaught errors will still show a message to the user
 */
window.onerror = function (message, source, lineno, colno, error) {
    console.error("Global error caught:", {
        message: message,
        source: source,
        lineno: lineno,
        colno: colno,
        error: error
    });

    // Display an error message to the user
    showGlobalErrorBanner("An unexpected error occurred. Please refresh the page or try again later.");

    return true; // Prevents the default error handling
};

/**
 * Show a global error banner for serious errors
 * @param {string} message - Error message to show
 */
function showGlobalErrorBanner(message) {
    // Remove any existing error banners
    document.querySelectorAll('.global-error-banner').forEach(banner => banner.remove());

    // Create new error banner
    const errorBanner = document.createElement('div');
    errorBanner.className = 'alert alert-danger alert-dismissible fade show global-error-banner';
    errorBanner.setAttribute('role', 'alert');
    errorBanner.style.position = 'fixed';
    errorBanner.style.top = '10px';
    errorBanner.style.left = '50%';
    errorBanner.style.transform = 'translateX(-50%)';
    errorBanner.style.zIndex = '9999';
    errorBanner.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    errorBanner.style.maxWidth = '90%';

    errorBanner.innerHTML = `
        <strong>Error:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Add to body
    document.body.appendChild(errorBanner);

    // Auto-dismiss after 10 seconds
    setTimeout(() => {
        if (errorBanner && errorBanner.parentNode) {
            errorBanner.remove();
        }
    }, 10000);
}