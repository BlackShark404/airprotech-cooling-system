
class InventoryDataTablesHandler {

    constructor(config) {
        // Configuration
        this.tableId = config.tableId || 'inventoryTable';
        this.ajaxUrl = config.ajaxUrl || '/inventory/getAllInventory';
        this.apiEndpoints = config.apiEndpoints || {};
        this.modalIds = config.modalIds || {
            viewDetails: 'productDetailsModal',
            manageInventory: 'manageInventoryModal',
            addProduct: 'addProductModal',
            deleteConfirmation: 'deleteConfirmationModal',
            importInventory: 'importInventoryModal',
            warehouseModal: 'warehouseModal'
        };
        
        // State management
        this.currentView = 'inventory'; // inventory, products, warehouses
        this.currentProductId = null;
        this.currentWarehouseId = null;
        this.variantCounter = 0;
        this.featureCounter = 0;
        this.specCounter = 0;
        
        // Tables references
        this.inventoryTable = null;
        this.productsTable = null;
        this.warehousesTable = null;
        
        // Initialize tables and event listeners
        this.initialize();
    }
    
    /**
     * Initialize the handler
     */
    initialize() {
        console.log("Initializing InventoryDataTablesHandler...");
        
        // Check if DataTables is available
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            this.initTables();
        } else {
            console.warn("DataTables not available, using fallback tables");
            this.createFallbackTables();
        }
        
        // Initialize event listeners
        this.initEventListeners();
        
        // Load dashboard statistics
        this.loadDashboardStats();
        
        console.log("InventoryDataTablesHandler initialized successfully");
    }
    
    /**
     * Load dashboard statistics
     */
    loadDashboardStats() {
        console.log("Loading dashboard statistics...");
        
        this.fetchData(this.apiEndpoints.GET_STATS, {})
            .then(response => {
                if (response.success) {
                    this.updateStatCards(response.data);
                } else {
                    console.warn('Failed to load stats:', response.message);
                    // Use fallback stats
                    this.updateStatCards({
                        total_products: 0,
                        total_variants: 0,
                        total_warehouses: 0,
                        low_stock_count: 0
                    });
                }
            })
            .catch(error => {
                console.error('Error loading dashboard stats:', error);
                // Use fallback stats
                this.updateStatCards({
                    total_products: 0,
                    total_variants: 0,
                    total_warehouses: 0,
                    low_stock_count: 0
                });
            });
    }
    
    /**
     * Update dashboard stat cards
     */
    updateStatCards(stats) {
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
     * Initialize DataTables instances
     */
    initTables() {
        try {
            // Initialize Inventory Table
            if (document.getElementById(this.tableId)) {
                this.inventoryTable = $(`#${this.tableId}`).DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: this.ajaxUrl,
                        dataSrc: (json) => {
                            return json.success ? (json.data || []) : [];
                        },
                        error: (xhr, error, thrown) => {
                            console.error('DataTables Ajax error:', error, thrown);
                            this.showAlert('error', 'Failed to load inventory data. Please refresh the page or try again later.');
                            return [];
                        }
                    },
                    columns: [
                        { 
                            title: 'Product', 
                            data: null, 
                            render: (data) => {
                                return `
                                    <div class="product-info d-flex align-items-center">
                                        <img src="${data.prod_image || '/api/placeholder/40/40'}" 
                                             alt="${data.prod_name}" 
                                             class="product-thumbnail me-2" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <div>
                                            <span class="product-name fw-medium">${data.prod_name}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        { 
                            title: 'Variant', 
                            data: 'var_capacity',
                            defaultContent: 'N/A' 
                        },
                        { 
                            title: 'Warehouse', 
                            data: 'whouse_name',
                            defaultContent: 'N/A' 
                        },
                        { 
                            title: 'Type', 
                            data: 'inve_type',
                            render: (data) => {
                                const typeClasses = {
                                    'Regular': 'bg-primary',
                                    'Display': 'bg-info',
                                    'Reserve': 'bg-secondary',
                                    'Damaged': 'bg-danger',
                                    'Returned': 'bg-warning',
                                    'Quarantine': 'bg-dark'
                                };
                                const bgClass = typeClasses[data] || 'bg-secondary';
                                return `<span class="badge ${bgClass}">${data}</span>`;
                            }
                        },
                        { 
                            title: 'Quantity', 
                            data: 'quantity',
                            defaultContent: '0',
                            render: (data) => {
                                return `<span class="fw-medium">${data}</span>`;
                            }
                        },
                        { 
                            title: 'Status', 
                            data: null, 
                            render: (data) => {
                                const quantity = parseInt(data.quantity) || 0;
                                if (quantity <= 0) {
                                    return '<span class="badge bg-danger">Out of Stock</span>';
                                } else if (quantity <= 5) {
                                    return '<span class="badge bg-warning">Low Stock</span>';
                                } else {
                                    return '<span class="badge bg-success">In Stock</span>';
                                }
                            }
                        },
                        { 
                            title: 'Last Updated', 
                            data: 'last_updated',
                            render: (data) => {
                                return data ? new Date(data).toLocaleString() : 'N/A';
                            }
                        },
                        { 
                            title: 'Actions', 
                            data: null, 
                            orderable: false,
                            render: (data) => {
                                return `
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary view-product" 
                                                data-id="${data.prod_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning manage-inventory" 
                                                data-id="${data.prod_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="Manage Inventory">
                                            <i class="bi bi-box-seam"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    language: {
                        emptyTable: "No inventory data available",
                        zeroRecords: "No matching records found",
                        loadingRecords: "Loading...",
                        processing: "Processing...",
                        search: "Search inventory:"
                    },
                    responsive: true,
                    drawCallback: () => {
                        this.initActionButtons();
                        this.initTooltips();
                    }
                });
            }
            
            // Initialize Products Table
            if (document.getElementById('productsTable')) {
                this.productsTable = $('#productsTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: this.apiEndpoints.GET_PRODUCTS,
                        dataSrc: (json) => {
                            return json.success ? (json.data || []) : [];
                        },
                        error: (xhr, error, thrown) => {
                            console.error('DataTables Ajax error:', error, thrown);
                            this.showAlert('error', 'Failed to load product data');
                            return [];
                        }
                    },
                    columns: [
                        { 
                            title: 'Image', 
                            data: 'prod_image',
                            render: (data, type, row) => {
                                return `<img src="${data || '/api/placeholder/50/50'}" alt="${row.prod_name}" 
                                         class="img-thumbnail" style="width: 50px; height: 50px;">`;
                            }
                        },
                        { title: 'Name', data: 'prod_name' },
                        { 
                            title: 'Description', 
                            data: 'prod_description',
                            render: (data) => {
                                if (!data) return 'No description available';
                                return data.length > 100 ? data.substring(0, 100) + '...' : data;
                            }
                        },
                        { 
                            title: 'Variants', 
                            data: 'variant_count',
                            render: (data) => {
                                return `<span class="badge bg-info">${data}</span>`;
                            }
                        },
                        { 
                            title: 'Total Stock', 
                            data: 'total_stock',
                            defaultContent: '0',
                            render: (data) => {
                                return `<span class="fw-medium">${data || 0}</span>`;
                            }
                        },
                        { 
                            title: 'Status', 
                            data: 'prod_availability_status',
                            render: (data) => {
                                const statusClasses = {
                                    'Available': 'bg-success',
                                    'Out of Stock': 'bg-danger',
                                    'Discontinued': 'bg-secondary'
                                };
                                const bgClass = statusClasses[data] || 'bg-secondary';
                                return `<span class="badge ${bgClass}">${data}</span>`;
                            }
                        },
                        { 
                            title: 'Actions', 
                            data: null, 
                            orderable: false,
                            render: (data) => {
                                return `
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary view-product" 
                                                data-id="${data.prod_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning manage-inventory" 
                                                data-id="${data.prod_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="Manage Inventory">
                                            <i class="bi bi-box-seam"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-product" 
                                                data-id="${data.prod_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="Delete Product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    responsive: true,
                    drawCallback: () => {
                        this.initActionButtons();
                        this.initTooltips();
                    }
                });
            }
            
            // Initialize Warehouses Table
            if (document.getElementById('warehousesTable')) {
                this.warehousesTable = $('#warehousesTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: this.apiEndpoints.GET_WAREHOUSE_SUMMARY,
                        dataSrc: (json) => {
                            return json.success ? (json.data || []) : [];
                        },
                        error: (xhr, error, thrown) => {
                            console.error('DataTables Ajax error:', error, thrown);
                            this.showAlert('error', 'Failed to load warehouse data');
                            return [];
                        }
                    },
                    columns: [
                        { title: 'Name', data: 'whouse_name' },
                        { title: 'Location', data: 'whouse_location' },
                        { 
                            title: 'Storage Capacity', 
                            data: 'whouse_storage_capacity',
                            render: (data) => {
                                return data ? data + ' units' : 'Unlimited';
                            }
                        },
                        { 
                            title: 'Current Usage', 
                            data: null,
                            render: (data) => {
                                const capacity = parseInt(data.whouse_storage_capacity) || 0;
                                const used = parseInt(data.total_quantity) || 0;
                                let percent = 0;
                                
                                if (capacity > 0 && used > 0) {
                                    percent = Math.min(Math.round((used / capacity) * 100), 100);
                                }
                                
                                let bgClass = 'bg-primary';
                                if (percent > 90) bgClass = 'bg-danger';
                                else if (percent > 70) bgClass = 'bg-warning';
                                
                                return `
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar ${bgClass}" role="progressbar" 
                                             style="width: ${percent}%;" aria-valuenow="${percent}" aria-valuemin="0" 
                                             aria-valuemax="100">${percent}%</div>
                                    </div>
                                    <div class="text-muted small mt-1">${used} / ${capacity || '∞'} units</div>
                                `;
                            }
                        },
                        { 
                            title: 'Items Below Threshold', 
                            data: 'low_stock_product_count',
                            defaultContent: '0',
                            render: (data) => {
                                const count = parseInt(data) || 0;
                                let badgeClass = 'bg-success';
                                if (count > 0) badgeClass = 'bg-warning';
                                if (count > 5) badgeClass = 'bg-danger';
                                
                                return `<span class="badge ${badgeClass}">${count}</span>`;
                            }
                        },
                        { 
                            title: 'Actions', 
                            data: null, 
                            orderable: false,
                            render: (data) => {
                                return `
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary view-warehouse" 
                                                data-id="${data.whouse_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success edit-warehouse" 
                                                data-id="${data.whouse_id}" 
                                                data-bs-toggle="tooltip" 
                                                title="Edit Warehouse">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    responsive: true,
                    drawCallback: () => {
                        this.initActionButtons();
                        this.initTooltips();
                    }
                });
            }
        } catch (error) {
            console.error('Error initializing DataTables:', error);
            this.showAlert('error', 'Failed to initialize tables');
            this.createFallbackTables();
        }
    }
    
    /**
     * Create fallback tables if DataTables initialization fails
     */
    createFallbackTables() {
        console.log("Creating fallback tables...");
        
        const tables = [
            { id: this.tableId, endpoint: this.apiEndpoints.GET_ALL_INVENTORY },
            { id: 'productsTable', endpoint: this.apiEndpoints.GET_PRODUCTS },
            { id: 'warehousesTable', endpoint: this.apiEndpoints.GET_WAREHOUSE_SUMMARY }
        ];
        
        tables.forEach(table => {
            const tableElement = document.getElementById(table.id);
            if (!tableElement) return;
            
            // Clear any existing content
            tableElement.innerHTML = '';
            
            // Create basic table structure
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');
            
            // Add loading row
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-3">
                        Loading data...
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `;
            
            tableElement.appendChild(thead);
            tableElement.appendChild(tbody);
            
            // Load data directly
            this.fetchData(table.endpoint, {})
                .then(response => {
                    if (response.success && response.data && response.data.length > 0) {
                        this.updateFallbackTable(tableElement, response.data, table.id);
                    } else {
                        this.showNoDataMessage(tableElement);
                    }
                })
                .catch(error => {
                    console.error(`Error loading data for ${table.id}:`, error);
                    this.showNoDataMessage(tableElement);
                });
        });
    }
    
    /**
     * Update fallback table with data
     */
    updateFallbackTable(table, data, tableId) {
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        
        // Clear existing content
        thead.innerHTML = '';
        tbody.innerHTML = '';
        
        // Create headers based on table type
        const headerRow = document.createElement('tr');
        let headers = [];
        
        if (tableId === this.tableId) {
            headers = ['Product', 'Variant', 'Warehouse', 'Type', 'Quantity', 'Status', 'Last Updated', 'Actions'];
        } else if (tableId === 'productsTable') {
            headers = ['Image', 'Name', 'Description', 'Variants', 'Total Stock', 'Status', 'Actions'];
        } else if (tableId === 'warehousesTable') {
            headers = ['Name', 'Location', 'Storage Capacity', 'Current Usage', 'Items Below Threshold', 'Actions'];
        }
        
        headers.forEach(header => {
            const th = document.createElement('th');
            th.textContent = header;
            headerRow.appendChild(th);
        });
        
        thead.appendChild(headerRow);
        
        // Create rows
        data.forEach(item => {
            const row = document.createElement('tr');
            
            if (tableId === this.tableId) {
                this.createInventoryTableRow(row, item);
            } else if (tableId === 'productsTable') {
                this.createProductsTableRow(row, item);
            } else if (tableId === 'warehousesTable') {
                this.createWarehousesTableRow(row, item);
            }
            
            tbody.appendChild(row);
        });
        
        // Add event listeners
        this.initActionButtons();
    }
    
    /**
     * Create row for inventory table
     */
    createInventoryTableRow(row, item) {
        // Product column
        const productCell = document.createElement('td');
        productCell.innerHTML = `
            <div class="product-info d-flex align-items-center">
                <img src="${item.prod_image || '/api/placeholder/40/40'}" 
                     alt="${item.prod_name}" 
                     class="product-thumbnail me-2" 
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                <div>
                    <span class="product-name fw-medium">${item.prod_name}</span>
                </div>
            </div>
        `;
        row.appendChild(productCell);
        
        // Variant column
        const variantCell = document.createElement('td');
        variantCell.textContent = item.var_capacity || 'N/A';
        row.appendChild(variantCell);
        
        // Warehouse column
        const warehouseCell = document.createElement('td');
        warehouseCell.textContent = item.whouse_name || 'N/A';
        row.appendChild(warehouseCell);
        
        // Type column
        const typeCell = document.createElement('td');
        const typeClasses = {
            'Regular': 'bg-primary',
            'Display': 'bg-info',
            'Reserve': 'bg-secondary',
            'Damaged': 'bg-danger',
            'Returned': 'bg-warning',
            'Quarantine': 'bg-dark'
        };
        const bgClass = typeClasses[item.inve_type] || 'bg-secondary';
        typeCell.innerHTML = `<span class="badge ${bgClass}">${item.inve_type}</span>`;
        row.appendChild(typeCell);
        
        // Quantity column
        const quantityCell = document.createElement('td');
        quantityCell.innerHTML = `<span class="fw-medium">${item.quantity || 0}</span>`;
        row.appendChild(quantityCell);
        
        // Status column
        const statusCell = document.createElement('td');
        const quantity = parseInt(item.quantity) || 0;
        if (quantity <= 0) {
            statusCell.innerHTML = '<span class="badge bg-danger">Out of Stock</span>';
        } else if (quantity <= 5) {
            statusCell.innerHTML = '<span class="badge bg-warning">Low Stock</span>';
        } else {
            statusCell.innerHTML = '<span class="badge bg-success">In Stock</span>';
        }
        row.appendChild(statusCell);
        
        // Last Updated column
        const lastUpdatedCell = document.createElement('td');
        lastUpdatedCell.textContent = item.last_updated ? new Date(item.last_updated).toLocaleString() : 'N/A';
        row.appendChild(lastUpdatedCell);
        
        // Actions column
        const actionsCell = document.createElement('td');
        actionsCell.innerHTML = `
            <div class="action-buttons">
                <button class="btn btn-sm btn-outline-primary view-product" 
                        data-id="${item.prod_id}" 
                        title="View Details">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning manage-inventory" 
                        data-id="${item.prod_id}" 
                        title="Manage Inventory">
                    <i class="bi bi-box-seam"></i>
                </button>
            </div>
        `;
        row.appendChild(actionsCell);
    }
    
    /**
     * Create row for products table
     */
    createProductsTableRow(row, item) {
        // Image column
        const imageCell = document.createElement('td');
        imageCell.innerHTML = `<img src="${item.prod_image || '/api/placeholder/50/50'}" alt="${item.prod_name}" 
                                class="img-thumbnail" style="width: 50px; height: 50px;">`;
        row.appendChild(imageCell);
        
        // Name column
        const nameCell = document.createElement('td');
        nameCell.textContent = item.prod_name;
        row.appendChild(nameCell);
        
        // Description column
        const descCell = document.createElement('td');
        if (!item.prod_description) {
            descCell.textContent = 'No description available';
        } else {
            descCell.textContent = item.prod_description.length > 100 ? 
                item.prod_description.substring(0, 100) + '...' : item.prod_description;
        }
        row.appendChild(descCell);
        
        // Variants column
        const variantsCell = document.createElement('td');
        variantsCell.innerHTML = `<span class="badge bg-info">${item.variant_count || 0}</span>`;
        row.appendChild(variantsCell);
        
        // Total Stock column
        const stockCell = document.createElement('td');
        stockCell.innerHTML = `<span class="fw-medium">${item.total_stock || 0}</span>`;
        row.appendChild(stockCell);
        
        // Status column
        const statusCell = document.createElement('td');
        const statusClasses = {
            'Available': 'bg-success',
            'Out of Stock': 'bg-danger',
            'Discontinued': 'bg-secondary'
        };
        const bgClass = statusClasses[item.prod_availability_status] || 'bg-secondary';
        statusCell.innerHTML = `<span class="badge ${bgClass}">${item.prod_availability_status}</span>`;
        row.appendChild(statusCell);
        
        // Actions column
        const actionsCell = document.createElement('td');
        actionsCell.innerHTML = `
            <div class="action-buttons">
                <button class="btn btn-sm btn-outline-primary view-product" 
                        data-id="${item.prod_id}" 
                        title="View Details">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning manage-inventory" 
                        data-id="${item.prod_id}" 
                        title="Manage Inventory">
                    <i class="bi bi-box-seam"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger delete-product" 
                        data-id="${item.prod_id}" 
                        title="Delete Product">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        row.appendChild(actionsCell);
    }
    
    /**
     * Create row for warehouses table
     */
    createWarehousesTableRow(row, item) {
        // Name column
        const nameCell = document.createElement('td');
        nameCell.textContent = item.whouse_name;
        row.appendChild(nameCell);
        
        // Location column
        const locationCell = document.createElement('td');
        locationCell.textContent = item.whouse_location;
        row.appendChild(locationCell);
        
        // Storage Capacity column
        const capacityCell = document.createElement('td');
        capacityCell.textContent = item.whouse_storage_capacity ? item.whouse_storage_capacity + ' units' : 'Unlimited';
        row.appendChild(capacityCell);
        
        // Current Usage column
        const usageCell = document.createElement('td');
        const capacity = parseInt(item.whouse_storage_capacity) || 0;
        const used = parseInt(item.total_quantity) || 0;
        let percent = 0;
        
        if (capacity > 0 && used > 0) {
            percent = Math.min(Math.round((used / capacity) * 100), 100);
        }
        
        let bgClass = 'bg-primary';
        if (percent > 90) bgClass = 'bg-danger';
        else if (percent > 70) bgClass = 'bg-warning';
        
        usageCell.innerHTML = `
            <div class="progress" style="height: 10px;">
                <div class="progress-bar ${bgClass}" role="progressbar" 
                     style="width: ${percent}%;" aria-valuenow="${percent}" aria-valuemin="0" 
                     aria-valuemax="100">${percent}%</div>
            </div>
            <div class="text-muted small mt-1">${used} / ${capacity || '∞'} units</div>
        `;
        row.appendChild(usageCell);
        
        // Items Below Threshold column
        const thresholdCell = document.createElement('td');
        const count = parseInt(item.low_stock_product_count) || 0;
        let badgeClass = 'bg-success';
        if (count > 0) badgeClass = 'bg-warning';
        if (count > 5) badgeClass = 'bg-danger';
        
        thresholdCell.innerHTML = `<span class="badge ${badgeClass}">${count}</span>`;
        row.appendChild(thresholdCell);
        
        // Actions column
        const actionsCell = document.createElement('td');
        actionsCell.innerHTML = `
            <div class="action-buttons">
                <button class="btn btn-sm btn-outline-primary view-warehouse" 
                        data-id="${item.whouse_id}" 
                        title="View Details">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success edit-warehouse" 
                        data-id="${item.whouse_id}" 
                        title="Edit Warehouse">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        `;
        row.appendChild(actionsCell);
    }
    
    /**
     * Show no data message in table
     */
    showNoDataMessage(table) {
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        No data available. 
                    </div>
                </td>
            </tr>
        `;
    }
    
    /**
     * Initialize tooltips
     */
    initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }
    
    /**
     * Initialize event listeners for action buttons
     */
    initEventListeners() {
        // Global event delegation for action buttons
        document.addEventListener('click', (event) => {
            // View product
            if (event.target.closest('.view-product')) {
                const button = event.target.closest('.view-product');
                const productId = button.getAttribute('data-id');
                this.viewProductDetails(productId);
            }
            
            // Manage inventory
            if (event.target.closest('.manage-inventory')) {
                const button = event.target.closest('.manage-inventory');
                const productId = button.getAttribute('data-id');
                this.openManageInventory(productId);
            }
            
            // Delete product
            if (event.target.closest('.delete-product')) {
                const button = event.target.closest('.delete-product');
                const productId = button.getAttribute('data-id');
                this.confirmDeleteProduct(productId);
            }
            
            // View warehouse
            if (event.target.closest('.view-warehouse')) {
                const button = event.target.closest('.view-warehouse');
                const warehouseId = button.getAttribute('data-id');
                this.viewWarehouseDetails(warehouseId);
            }
            
            // Edit warehouse
            if (event.target.closest('.edit-warehouse')) {
                const button = event.target.closest('.edit-warehouse');
                const warehouseId = button.getAttribute('data-id');
                this.editWarehouse(warehouseId);
            }
            
            // Remove variant
            if (event.target.closest('.remove-variant')) {
                const variantForm = event.target.closest('.variant-form');
                if (variantForm && document.querySelectorAll('.variant-form').length > 1) {
                    variantForm.remove();
                    this.updateVariantIndexes();
                }
            }
            
            // Remove feature
            if (event.target.closest('.remove-feature')) {
                const featureInput = event.target.closest('.input-group');
                if (featureInput && document.querySelectorAll('.features-container .input-group').length > 1) {
                    featureInput.remove();
                }
            }
            
            // Remove spec
            if (event.target.closest('.remove-spec')) {
                const specRow = event.target.closest('.row');
                if (specRow && document.querySelectorAll('.specs-container .row').length > 1) {
                    specRow.remove();
                }
            }
        });
        
        // Form submissions
        document.addEventListener('submit', (event) => {
            // Add stock form
            if (event.target.id === 'addStockForm') {
                event.preventDefault();
                this.submitAddStock();
            }
            
            // Move stock form
            if (event.target.id === 'moveStockForm') {
                event.preventDefault();
                this.submitMoveStock();
            }
            
            // Warehouse form
            if (event.target.id === 'warehouseForm') {
                event.preventDefault();
                this.saveWarehouse();
            }
        });
    }
    
    /**
     * Initialize action buttons in tables
     */
    initActionButtons() {
        // This is now handled by the global click event delegation
    }
    
    /**
     * Change the current view (inventory, products, warehouses)
     */
    changeView(viewType) {
        this.currentView = viewType;
        
        // Refresh corresponding table
        if (viewType === 'inventory' && this.inventoryTable) {
            this.inventoryTable.ajax.reload();
        } else if (viewType === 'products' && this.productsTable) {
            this.productsTable.ajax.reload();
        } else if (viewType === 'warehouses' && this.warehousesTable) {
            this.warehousesTable.ajax.reload();
        }
    }
    
    /**
     * Filter inventory by stock type
     */
    filterByStockType(stockType) {
        if (this.inventoryTable) {
            if (stockType === 'all') {
                this.inventoryTable.column(3).search('').draw();
            } else {
                this.inventoryTable.column(3).search(stockType).draw();
            }
        } else {
            // Fallback for when DataTables isn't available
            this.fetchData(
                stockType === 'all' 
                    ? this.apiEndpoints.GET_ALL_INVENTORY 
                    : this.apiEndpoints.GET_INVENTORY_BY_TYPE + '/' + stockType,
                {}
            )
                .then(response => {
                    if (response.success) {
                        const table = document.getElementById(this.tableId);
                        if (table) {
                            this.updateFallbackTable(table, response.data, this.tableId);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error filtering inventory:', error);
                    this.showAlert('error', 'Error filtering inventory data');
                });
        }
    }
    
    /**
     * View product details
     */
    viewProductDetails(productId) {
        this.currentProductId = productId;
        
        this.fetchData(this.apiEndpoints.PRODUCT_DETAILS + productId, {})
            .then(response => {
                if (response.success) {
                    this.populateProductDetailsModal(response.data);
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById(this.modalIds.viewDetails));
                    modal.show();
                } else {
                    this.showAlert('error', 'Could not load product details: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error loading product details:', error);
                this.showAlert('error', 'Error loading product details');
            });
    }
    
    /**
     * Populate product details modal
     */
    populateProductDetailsModal(data) {
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
                    <td>${this.formatCurrency(variant.var_srp_price)}</td>
                    <td>${variant.var_price_free_install ? this.formatCurrency(variant.var_price_free_install) : 'N/A'}</td>
                    <td>${variant.var_price_with_install ? this.formatCurrency(variant.var_price_with_install) : 'N/A'}</td>
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
            this.editProduct(product.prod_id);
        });
    }
    
    /**
     * Open manage inventory modal
     */
    openManageInventory(productId) {
        this.currentProductId = productId;
        
        this.fetchData(this.apiEndpoints.PRODUCT_DETAILS + productId, {})
            .then(response => {
                if (response.success) {
                    // Set product ID for forms
                    document.getElementById('addStockProductId').value = productId;
                    document.getElementById('moveStockProductId').value = productId;
                    
                    // Load current stock data
                    this.loadCurrentStock(response.data);
                    
                    // Load variants for dropdown
                    this.loadVariantsForProduct(productId, response.data.variants);
                    
                    // Load warehouses for dropdowns
                    this.loadWarehouses();
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById(this.modalIds.manageInventory));
                    modal.show();
                    
                    // Activate first tab
                    const firstTab = document.querySelector('#inventoryTabs .nav-link');
                    if (firstTab && typeof bootstrap !== 'undefined') {
                        const tabInstance = new bootstrap.Tab(firstTab);
                        tabInstance.show();
                    }
                } else {
                    this.showAlert('error', 'Could not load product inventory: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error loading product inventory:', error);
                this.showAlert('error', 'Error loading product inventory');
            });
    }
    
    /**
     * Load current stock data
     */
    loadCurrentStock(data) {
        const { product, variants, inventory } = data;
        const currentStockTable = document.getElementById('currentStockTable');
        
        if (inventory && inventory.length > 0) {
            // Create a map of variant IDs to capacity names
            const variantMap = {};
            if (variants && variants.length > 0) {
                variants.forEach(variant => {
                    variantMap[variant.var_id] = variant.var_capacity;
                });
            }
            
            // Generate table rows
            let html = '';
            inventory.forEach(item => {
                const variantCapacity = variantMap[item.var_id] || 'Default';
                html += `
                    <tr>
                        <td>${variantCapacity}</td>
                        <td>${item.whouse_name}</td>
                        <td><span class="badge ${this.getTypeClass(item.inve_type)}">${item.inve_type}</span></td>
                        <td>${item.quantity || 0}</td>
                        <td>${item.last_updated ? new Date(item.last_updated).toLocaleString() : 'N/A'}</td>
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
    }
    
    /**
     * Load variants for product in dropdown
     */
    loadVariantsForProduct(productId, variants = null) {
        const addVariantSelect = document.getElementById('addVariantSelect');
        const moveVariantSelect = document.getElementById('moveVariantSelect');
        
        if (!addVariantSelect || !moveVariantSelect) return;
        
        if (variants) {
            // Use provided variants
            addVariantSelect.innerHTML = '';
            moveVariantSelect.innerHTML = '';
            
            variants.forEach(variant => {
                const option = document.createElement('option');
                option.value = variant.var_id;
                option.textContent = variant.var_capacity || 'Default Variant';
                addVariantSelect.appendChild(option.cloneNode(true));
                moveVariantSelect.appendChild(option);
            });
        } else {
            // Fetch variants from server
            this.fetchData(this.apiEndpoints.PRODUCT_DETAILS + productId, {})
                .then(response => {
                    if (response.success && response.data.variants) {
                        addVariantSelect.innerHTML = '';
                        moveVariantSelect.innerHTML = '';
                        
                        response.data.variants.forEach(variant => {
                            const option = document.createElement('option');
                            option.value = variant.var_id;
                            option.textContent = variant.var_capacity || 'Default Variant';
                            addVariantSelect.appendChild(option.cloneNode(true));
                            moveVariantSelect.appendChild(option);
                        });
                    } else {
                        addVariantSelect.innerHTML = '<option value="">No variants available</option>';
                        moveVariantSelect.innerHTML = '<option value="">No variants available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading variants:', error);
                    addVariantSelect.innerHTML = '<option value="">Error loading variants</option>';
                    moveVariantSelect.innerHTML = '<option value="">Error loading variants</option>';
                });
        }
    }
    
    /**
     * Load warehouses for dropdowns
     */
    loadWarehouses() {
        const addWarehouseSelect = document.getElementById('addWarehouseSelect');
        const sourceWarehouse = document.getElementById('sourceWarehouse');
        const destinationWarehouse = document.getElementById('destinationWarehouse');
        
        if (!addWarehouseSelect || !sourceWarehouse || !destinationWarehouse) return;
        
        this.fetchData(this.apiEndpoints.GET_ALL_WAREHOUSES, {})
            .then(response => {
                if (response.success && response.data) {
                    addWarehouseSelect.innerHTML = '';
                    sourceWarehouse.innerHTML = '';
                    destinationWarehouse.innerHTML = '';
                    
                    response.data.forEach(warehouse => {
                        const option = document.createElement('option');
                        option.value = warehouse.whouse_id;
                        option.textContent = warehouse.whouse_name;
                        addWarehouseSelect.appendChild(option.cloneNode(true));
                        sourceWarehouse.appendChild(option.cloneNode(true));
                        destinationWarehouse.appendChild(option);
                    });
                } else {
                    const noWarehouseOption = '<option value="">No warehouses available</option>';
                    addWarehouseSelect.innerHTML = noWarehouseOption;
                    sourceWarehouse.innerHTML = noWarehouseOption;
                    destinationWarehouse.innerHTML = noWarehouseOption;
                }
            })
            .catch(error => {
                console.error('Error loading warehouses:', error);
                const errorOption = '<option value="">Error loading warehouses</option>';
                addWarehouseSelect.innerHTML = errorOption;
                sourceWarehouse.innerHTML = errorOption;
                destinationWarehouse.innerHTML = errorOption;
            });
    }
    
    /**
     * Submit add stock form
     */
    submitAddStock() {
        const form = document.getElementById('addStockForm');
        const formData = this.getFormData(form);
        
        // Validate form
        if (!formData.var_id || !formData.whouse_id || !formData.quantity) {
            this.showAlert('error', 'Please fill all required fields');
            return;
        }
        
        // Submit to server
        this.fetchData(this.apiEndpoints.ADD_STOCK, formData, 'POST')
            .then(response => {
                if (response.success) {
                    // Reload current stock tab
                    this.loadCurrentStock({ inventory: response.data });
                    
                    // Show success message
                    this.showAlert('success', 'Stock added successfully');
                    
                    // Reset form
                    form.reset();
                    
                    // Refresh inventory table
                    if (this.inventoryTable) {
                        this.inventoryTable.ajax.reload();
                    }
                    
                    // Activate first tab
                    const firstTab = document.querySelector('#inventoryTabs .nav-link');
                    if (firstTab && typeof bootstrap !== 'undefined') {
                        const tabInstance = new bootstrap.Tab(firstTab);
                        tabInstance.show();
                    }
                } else {
                    this.showAlert('error', 'Could not add stock: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error adding stock:', error);
                this.showAlert('error', 'Error adding stock');
            });
    }
    
    /**
     * Submit move stock form
     */
    submitMoveStock() {
        const form = document.getElementById('moveStockForm');
        const formData = this.getFormData(form);
        
        // Validate form
        if (!formData.var_id || !formData.source_warehouse_id || !formData.destination_warehouse_id || !formData.quantity) {
            this.showAlert('error', 'Please fill all required fields');
            return;
        }
        
        // Check if source and destination are different
        if (formData.source_warehouse_id === formData.destination_warehouse_id) {
            this.showAlert('error', 'Source and destination warehouses must be different');
            return;
        }
        
        // Submit to server
        this.fetchData(this.apiEndpoints.MOVE_STOCK, formData, 'POST')
            .then(response => {
                if (response.success) {
                    // Reload current stock tab
                    this.loadCurrentStock({ inventory: response.data });
                    
                    // Show success message
                    this.showAlert('success', 'Stock moved successfully');
                    
                    // Reset form
                    form.reset();
                    
                    // Refresh inventory table
                    if (this.inventoryTable) {
                        this.inventoryTable.ajax.reload();
                    }
                    
                    // Activate first tab
                    // Activate first tab
                    const firstTab = document.querySelector('#inventoryTabs .nav-link');
                    if (firstTab && typeof bootstrap !== 'undefined') {
                        const tabInstance = new bootstrap.Tab(firstTab);
                        tabInstance.show();
                    }
                } else {
                    this.showAlert('error', 'Could not move stock: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error moving stock:', error);
                this.showAlert('error', 'Error moving stock');
            });
    }
    
    /**
     * Add a new variant form
     */
    addNewVariantForm() {
        const variantsContainer = document.querySelector('.variants-container');
        if (!variantsContainer) return;
        
        // Get the last variant form as template
        const lastVariantForm = variantsContainer.querySelector('.variant-form:last-child');
        if (!lastVariantForm) return;
        
        // Create new variant form
        const newVariantForm = lastVariantForm.cloneNode(true);
        this.variantCounter++;
        
        // Update input names with new index
        const inputs = newVariantForm.querySelectorAll('input');
        inputs.forEach(input => {
            const currentName = input.getAttribute('name');
            if (currentName) {
                // Update index in name, e.g., variants[0][var_capacity] to variants[1][var_capacity]
                const newName = currentName.replace(/variants\[\d+\]/, `variants[${this.variantCounter}]`);
                input.setAttribute('name', newName);
                // Clear value
                input.value = '';
            }
        });
        
        // Add remove button if not already present
        if (!newVariantForm.querySelector('.remove-variant-btn')) {
            const buttonDiv = document.createElement('div');
            buttonDiv.className = 'text-end mt-2';
            buttonDiv.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger remove-variant">
                    <i class="bi bi-x"></i> Remove Variant
                </button>
            `;
            newVariantForm.appendChild(buttonDiv);
        }
        
        // Insert new form before the add button
        const addVariantBtn = variantsContainer.querySelector('.add-variant-btn');
        variantsContainer.insertBefore(newVariantForm, addVariantBtn);
        
        // Update inventory variants
        this.updateInventoryVariants();
    }
    
    /**
     * Add a new feature input
     */
    addNewFeatureInput() {
        const featuresContainer = document.querySelector('.features-container');
        if (!featuresContainer) return;
        
        this.featureCounter++;
        
        // Create new feature input
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="features[${this.featureCounter}]" placeholder="Enter feature">
            <button class="btn btn-outline-danger remove-feature" type="button">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        featuresContainer.appendChild(newFeature);
    }
    
    /**
     * Add a new specification input
     */
    addNewSpecInput() {
        const specsContainer = document.querySelector('.specs-container');
        if (!specsContainer) return;
        
        this.specCounter++;
        
        // Create new spec input
        const newSpec = document.createElement('div');
        newSpec.className = 'row mb-2';
        newSpec.innerHTML = `
            <div class="col-5">
                <input type="text" class="form-control" name="specs[${this.specCounter}][spec_name]" placeholder="Spec name">
            </div>
            <div class="col-5">
                <input type="text" class="form-control" name="specs[${this.specCounter}][spec_value]" placeholder="Spec value">
            </div>
            <div class="col-2">
                <button class="btn btn-outline-danger w-100 remove-spec" type="button">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
        
        specsContainer.appendChild(newSpec);
    }
    
    /**
     * Update variant indices after removing a variant
     */
    updateVariantIndexes() {
        const variantForms = document.querySelectorAll('.variant-form');
        variantForms.forEach((form, index) => {
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                const currentName = input.getAttribute('name');
                if (currentName) {
                    // Update index in name, e.g., variants[0][var_capacity] to variants[1][var_capacity]
                    const newName = currentName.replace(/variants\[\d+\]/, `variants[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        
        // Update inventory variants
        this.updateInventoryVariants();
    }
    
    /**
     * Update inventory variants in add product form
     */
    updateInventoryVariants() {
        const variantForms = document.querySelectorAll('.variant-form');
        const variantsInventoryContainer = document.querySelector('.variants-inventory-container');
        
        if (!variantsInventoryContainer) return;
        
        // If no variants, show placeholder
        if (variantForms.length === 0) {
            variantsInventoryContainer.innerHTML = `
                <div class="alert alert-secondary">
                    Please add variants in the Variants tab first.
                </div>
            `;
            return;
        }
        
        // Create inventory inputs for each variant
        let html = '';
        variantForms.forEach((form, index) => {
            const capacityInput = form.querySelector('[name^="variants"][name$="[var_capacity]"]');
            const capacity = capacityInput ? capacityInput.value : `Variant ${index + 1}`;
            
            html += `
                <div class="variant-inventory mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <h6>${capacity || `Variant ${index + 1}`}</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Initial Quantity</label>
                            <input type="number" class="form-control" name="inventory[${index}][quantity]" min="0" value="0">
                        </div>
                        <input type="hidden" name="inventory[${index}][variant_index]" value="${index}">
                    </div>
                </div>
            `;
        });
        
        variantsInventoryContainer.innerHTML = html;
    }
    
    /**
     * Save product (create or update)
     */
    saveProduct() {
        const form = document.getElementById('productForm');
        if (!form) return;
        
        // Show saving indicator
        this.showAlert('info', 'Saving product...');
        
        // Validate form
        const productName = document.getElementById('productName').value;
        if (!productName) {
            this.showAlert('error', 'Product name is required');
            return;
        }
        
        // Check if at least one variant exists
        const variantForms = document.querySelectorAll('.variant-form');
        if (variantForms.length === 0) {
            this.showAlert('error', 'At least one variant is required');
            return;
        }
        
        // Create FormData object
        const formData = new FormData(form);
        
        // Add JSON data for variants, features, and specs
        const variants = this.collectVariantsData();
        formData.append('variants', JSON.stringify(variants));
        
        const features = this.collectFeaturesData();
        formData.append('features', JSON.stringify(features));
        
        const specs = this.collectSpecsData();
        formData.append('specs', JSON.stringify(specs));
        
        // Add inventory data
        const inventory = this.collectInventoryData();
        formData.append('inventory', JSON.stringify(inventory));
        
        // Determine if this is create or update
        const isUpdate = this.currentProductId !== null;
        const url = isUpdate 
            ? this.apiEndpoints.UPDATE_PRODUCT + this.currentProductId 
            : this.apiEndpoints.CREATE_PRODUCT;
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
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    this.showAlert('success', `Product ${isUpdate ? 'updated' : 'created'} successfully`);
                    
                    // Reset form
                    this.resetProductForm();
                    
                    // Reset current product ID if update
                    if (isUpdate) {
                        this.currentProductId = null;
                    }
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(this.modalIds.addProduct));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Refresh dashboard stats
                    this.loadDashboardStats();
                    
                    // Refresh tables with delay
                    setTimeout(() => {
                        if (this.inventoryTable) {
                            this.inventoryTable.ajax.reload();
                        }
                        if (this.productsTable) {
                            this.productsTable.ajax.reload();
                        }
                    }, 1000);
                } else {
                    this.showAlert('error', data.message || `Could not ${isUpdate ? 'update' : 'create'} product`);
                }
            })
            .catch(error => {
                console.error(`Error ${isUpdate ? 'updating' : 'creating'} product:`, error);
                this.showAlert('error', `Error ${isUpdate ? 'updating' : 'creating'} product. Please try again.`);
            });
    }
    
    /**
     * Collect variants data from form
     */
    collectVariantsData() {
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
    collectFeaturesData() {
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
    collectSpecsData() {
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
    collectInventoryData() {
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
    resetProductForm() {
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
        this.updateInventoryVariants();
        
        // Reset counters
        this.variantCounter = 0;
        this.featureCounter = 0;
        this.specCounter = 0;
        
        // Reset current product ID
        this.currentProductId = null;
        
        // Switch to first tab
        const firstTab = document.querySelector('#product-info-tab');
        if (firstTab && typeof bootstrap !== 'undefined') {
            const tabInstance = new bootstrap.Tab(firstTab);
            tabInstance.show();
        }
    }
    
    /**
     * Edit product
     */
    editProduct(productId) {
        this.currentProductId = productId;
        
        // Show loading indicator
        this.showAlert('info', 'Loading product data...');
        
        // Fetch product details
        this.fetchData(this.apiEndpoints.PRODUCT_DETAILS + productId, {})
            .then(response => {
                if (response.success) {
                    // Reset form first
                    this.resetProductForm();
                    
                    // Populate form with product data
                    this.populateProductForm(response.data);
                    
                    // Update modal title
                    document.getElementById('addProductModalLabel').textContent = 'Edit Product';
                    document.getElementById('saveProductBtn').textContent = 'Update Product';
                    
                    // Load warehouses for dropdown
                    this.loadWarehousesForProductForm();
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById(this.modalIds.addProduct));
                    modal.show();
                } else {
                    this.showAlert('error', 'Could not load product details: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error loading product details for editing:', error);
                this.showAlert('error', 'Error loading product details');
            });
    }
    
    /**
     * Load warehouses for product form
     */
    loadWarehousesForProductForm() {
        const warehouseSelect = document.getElementById('warehouseSelect');
        if (!warehouseSelect) return;
        
        this.fetchData(this.apiEndpoints.GET_ALL_WAREHOUSES, {})
            .then(response => {
                if (response.success && response.data && response.data.length > 0) {
                    warehouseSelect.innerHTML = '<option value="">Select warehouse</option>';
                    
                    response.data.forEach(warehouse => {
                        const option = document.createElement('option');
                        option.value = warehouse.whouse_id;
                        option.textContent = warehouse.whouse_name;
                        warehouseSelect.appendChild(option);
                    });
                } else {
                    warehouseSelect.innerHTML = '<option value="">No warehouses available</option>';
                }
            })
            .catch(error => {
                console.error('Error loading warehouses for product form:', error);
                warehouseSelect.innerHTML = '<option value="">Error loading warehouses</option>';
            });
    }
    
    /**
     * Populate product form with data for editing
     */
    populateProductForm(data) {
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
                this.addNewVariantForm();
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
                this.featureCounter = index;
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
                this.specCounter = index;
            });
        }
        
        // Update inventory variants tab
        this.updateInventoryVariants();
    }
    
    /**
     * Confirm delete product
     */
    confirmDeleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            // Show deleting indicator
            this.showAlert('info', 'Deleting product...');
            
            // Send delete request
            this.fetchData(this.apiEndpoints.DELETE_PRODUCT + productId, {}, 'POST')
                .then(response => {
                    if (response.success) {
                        // Show success message
                        this.showAlert('success', 'Product deleted successfully');
                        
                        // Refresh dashboard stats
                        this.loadDashboardStats();
                        
                        // Refresh tables
                        if (this.inventoryTable) {
                            this.inventoryTable.ajax.reload();
                        }
                        if (this.productsTable) {
                            this.productsTable.ajax.reload();
                        }
                    } else {
                        this.showAlert('error', response.message || 'Failed to delete product');
                    }
                })
                .catch(error => {
                    console.error('Error deleting product:', error);
                    this.showAlert('error', 'Error deleting product');
                });
        }
    }
    
    /**
     * Show import modal
     */
    showImportModal() {
        // Create modal HTML if it doesn't exist
        if (!document.getElementById(this.modalIds.importInventory)) {
            const modalHTML = `
                <div class="modal fade" id="${this.modalIds.importInventory}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Import Inventory</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="importForm" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="importFile" class="form-label">CSV File</label>
                                        <input type="file" class="form-control" id="importFile" name="inventory_file" accept=".csv" required>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        CSV should have columns: product_id, warehouse_id, type, quantity
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="importInventoryBtn">Import</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Add event listener to import button
            document.getElementById('importInventoryBtn').addEventListener('click', () => {
                this.importInventory();
            });
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById(this.modalIds.importInventory));
        modal.show();
    }
    
    /**
     * Import inventory from CSV
     */
    importInventory() {
        const form = document.getElementById('importForm');
        const fileInput = document.getElementById('importFile');
        
        if (!fileInput.files || fileInput.files.length === 0) {
            this.showAlert('error', 'Please select a CSV file');
            return;
        }
        
        // Show importing indicator
        this.showAlert('info', 'Importing inventory...');
        
        const formData = new FormData(form);
        
        // Send import request
        fetch(this.apiEndpoints.IMPORT_INVENTORY, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    this.showAlert('success', `Successfully imported ${data.data.count} inventory records`);
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(this.modalIds.importInventory));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Refresh tables
                    if (this.inventoryTable) {
                        this.inventoryTable.ajax.reload();
                    }
                } else {
                    this.showAlert('error', data.message || 'Import failed');
                }
            })
            .catch(error => {
                console.error('Error importing inventory:', error);
                this.showAlert('error', 'Error importing inventory');
            });
    }
    
    /**
     * View warehouse details
     */
    viewWarehouseDetails(warehouseId) {
        this.currentWarehouseId = warehouseId;
        
        // Show loading indicator
        this.showAlert('info', 'Loading warehouse details...');
        
        // Fetch warehouse data
        this.fetchData(this.apiEndpoints.GET_WAREHOUSE + warehouseId, {})
            .then(response => {
                if (response.success) {
                    // Fetch warehouse inventory
                    this.fetchData(this.apiEndpoints.GET_WAREHOUSE_INVENTORY + warehouseId, {})
                        .then(inventoryResponse => {
                            if (inventoryResponse.success) {
                                // TODO: Implement warehouse details view
                                this.showAlert('info', 'Warehouse details functionality will be implemented in the next update');
                            } else {
                                this.showAlert('error', 'Could not load warehouse inventory: ' + inventoryResponse.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading warehouse inventory:', error);
                            this.showAlert('error', 'Error loading warehouse inventory');
                        });
                } else {
                    this.showAlert('error', 'Could not load warehouse details: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error loading warehouse details:', error);
                this.showAlert('error', 'Error loading warehouse details');
            });
    }
    
    /**
     * Edit warehouse
     */
    editWarehouse(warehouseId) {
        this.currentWarehouseId = warehouseId;
        
        // Fetch warehouse data
        this.fetchData(this.apiEndpoints.GET_WAREHOUSE + warehouseId, {})
            .then(response => {
                if (response.success) {
                    // Populate form
                    document.getElementById('warehouseId').value = response.data.whouse_id;
                    document.getElementById('warehouseName').value = response.data.whouse_name;
                    document.getElementById('warehouseLocation').value = response.data.whouse_location;
                    document.getElementById('warehouseCapacity').value = response.data.whouse_storage_capacity || '';
                    document.getElementById('warehouseThreshold').value = response.data.whouse_restock_threshold || '';
                    
                    // Show warehouse modal
                    const modal = new bootstrap.Modal(document.getElementById(this.modalIds.warehouseModal));
                    modal.show();
                } else {
                    this.showAlert('error', 'Could not load warehouse data: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error loading warehouse data:', error);
                this.showAlert('error', 'Error loading warehouse data');
            });
    }
    
    /**
     * Save warehouse (create or update)
     */
    saveWarehouse() {
        const form = document.getElementById('warehouseForm');
        if (!form) return;
        
        // Show saving indicator
        this.showAlert('info', 'Saving warehouse...');
        
        // Get form data
        const formData = this.getFormData(form);
        const warehouseId = formData.whouse_id;
        const isUpdate = warehouseId !== '';
        
        // Validate required fields
        if (!formData.whouse_name || !formData.whouse_location) {
            this.showAlert('error', 'Warehouse name and location are required');
            return;
        }
        
        // Determine URL and method
        const url = isUpdate ? this.apiEndpoints.UPDATE_WAREHOUSE + warehouseId : this.apiEndpoints.CREATE_WAREHOUSE;
        const method = isUpdate ? 'PUT' : 'POST';
        
        // Send request
        this.fetchData(url, formData, method)
            .then(response => {
                if (response.success) {
                    // Show success message
                    this.showAlert('success', `Warehouse ${isUpdate ? 'updated' : 'created'} successfully`);
                    
                    // Reset form
                    this.resetWarehouseForm();
                    
                    // Refresh warehouse list
                    this.refreshWarehouseList();
                    
                    // Refresh warehouses dropdown in product form
                    this.loadWarehousesForProductForm();
                    
                    // Refresh dashboard stats
                    this.loadDashboardStats();
                    
                    // Refresh warehouses table
                    if (this.warehousesTable) {
                        this.warehousesTable.ajax.reload();
                    }
                } else {
                    this.showAlert('error', response.message || `Failed to ${isUpdate ? 'update' : 'create'} warehouse`);
                }
            })
            .catch(error => {
                console.error(`Error ${isUpdate ? 'updating' : 'creating'} warehouse:`, error);
                this.showAlert('error', `Error ${isUpdate ? 'updating' : 'creating'} warehouse`);
            });
    }
    
    /**
     * Reset warehouse form
     */
    resetWarehouseForm() {
        const form = document.getElementById('warehouseForm');
        if (form) {
            form.reset();
            document.getElementById('warehouseId').value = '';
        }
    }
    
    /**
     * Refresh warehouse list
     */
    refreshWarehouseList() {
        this.fetchData(this.apiEndpoints.GET_ALL_WAREHOUSES, {})
            .then(response => {
                if (response.success) {
                    const warehouseListTable = document.getElementById('warehouseListTable')?.querySelector('tbody');
                    if (!warehouseListTable) return;
                    
                    if (response.data && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(warehouse => {
                            html += `
                                <tr>
                                    <td>${warehouse.whouse_name}</td>
                                    <td>${warehouse.whouse_location}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary edit-warehouse" 
                                                    data-id="${warehouse.whouse_id}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-warehouse" 
                                                    data-id="${warehouse.whouse_id}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        warehouseListTable.innerHTML = html;
                        
                        // Add event listeners to buttons
                        warehouseListTable.querySelectorAll('.edit-warehouse').forEach(button => {
                            button.addEventListener('click', () => {
                                this.editWarehouse(button.getAttribute('data-id'));
                            });
                        });
                        
                        warehouseListTable.querySelectorAll('.delete-warehouse').forEach(button => {
                            button.addEventListener('click', () => {
                                this.confirmDeleteWarehouse(button.getAttribute('data-id'));
                            });
                        });
                    } else {
                        warehouseListTable.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center py-3">No warehouses found</td>
                            </tr>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing warehouse list:', error);
                // Silently fail - don't show alert for this operation
            });
    }
    
    /**
     * Confirm delete warehouse
     */
    confirmDeleteWarehouse(warehouseId) {
        if (confirm('Are you sure you want to delete this warehouse? This may affect inventory records.')) {
            // Show deleting indicator
            this.showAlert('info', 'Deleting warehouse...');
            
            // Send delete request
            this.fetchData(this.apiEndpoints.DELETE_WAREHOUSE + warehouseId, {}, 'POST')
                .then(response => {
                    if (response.success) {
                        // Show success message
                        this.showAlert('success', 'Warehouse deleted successfully');
                        
                        // Refresh warehouse list
                        this.refreshWarehouseList();
                        
                        // Refresh dashboard stats
                        this.loadDashboardStats();
                        
                        // Refresh warehouses table
                        if (this.warehousesTable) {
                            this.warehousesTable.ajax.reload();
                        }
                    } else {
                        this.showAlert('error', response.message || 'Failed to delete warehouse');
                    }
                })
                .catch(error => {
                    console.error('Error deleting warehouse:', error);
                    this.showAlert('error', 'Error deleting warehouse');
                });
        }
    }
    
    /**
     * Helper function to get form data as object
     */
    getFormData(form) {
        const formData = new FormData(form);
        const object = {};
        
        formData.forEach((value, key) => {
            object[key] = value;
        });
        
        return object;
    }
    
 
    fetchData(url, data, method = 'GET') {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        // Add data to request
        if (method === 'GET') {
            // Add query parameters for GET request
            if (Object.keys(data).length > 0) {
                const params = new URLSearchParams(data);
                url += '?' + params.toString();
            }
        } else {
            // Add body data for POST, PUT, DELETE requests
            options.body = JSON.stringify(data);
        }
        
        // Add timeout handling
        return this.fetchWithTimeout(url, options);
    }
    
    /**
     * Fetch with timeout
     */
    fetchWithTimeout(url, options, timeout = 15000) {
        return Promise.race([
            fetch(url, options).then(response => response.json()),
            new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Request timed out')), timeout)
            )
        ]);
    }
    
    /**
     * Format currency value
     */
    formatCurrency(value) {
        if (!value && value !== 0) return 'N/A';
        
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(value);
    }
    
    /**
     * Get CSS class for inventory type badge
     */
    getTypeClass(type) {
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
     * Show alert message
     */
    showAlert(type, message) {
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
}