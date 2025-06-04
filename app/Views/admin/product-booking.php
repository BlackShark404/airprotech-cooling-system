<?php
$title = 'Product Bookings - AirProtech';
$activeTab = 'product_bookings';

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
    .date-input {
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
    .action-icon-assign {
        color: #17a2b8;
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
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-confirmed {
        background-color: #0dcaf0;
        color: #212529;
    }
    .badge-completed {
        background-color: #198754;
        color: #fff;
    }
    .badge-cancelled {
        background-color: #dc3545;
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
    .technician-badge {
        display: block;
        width: 100%;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .technician-remove {
        margin-left: 5px;
        cursor: pointer;
    }
    .technician-list {
        margin-top: 10px;
    }
    .add-technician-btn {
        margin-left: 10px;
    }
    
    /* Responsive table styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Table styling */
    #productBookingsTable {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #productBookingsTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    #productBookingsTable tbody td {
        padding: 15px 8px;
        vertical-align: middle;
    }
    
    #productBookingsTable tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }
    
    /* Customer info styles */
    .customer-info {
        display: flex;
        align-items: center;
    }
    .customer-avatar {
        width: 43px;
        height: 43px;
        border-radius: 50%;
        margin-right: 12px;
        object-fit: cover;
        border: 1px solid #eee;
    }
    .customer-details {
        display: flex;
        flex-direction: column;
    }
    .customer-name {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 2px;
    }
    .customer-contact {
        font-size: 0.8rem;
        color: #6c757d;
        line-height: 1.4;
    }
    
    /* Technician badges */
    .technician-chip {
        display: inline-flex;
        align-items: center;
        background: #e9ecef;
        border-radius: 50px;
        padding: 4px 10px;
        margin: 3px;
        font-size: 0.85rem;
        border: 1px solid #dee2e6;
    }
    .technician-chip img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 7px;
        border: 1px solid #fff;
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Product Booking Management</h1>
        <p class="text-muted">Manage product bookings and deliveries</p>
    </div>

    <!-- Filters Card -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Filters</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select id="statusFilter" class="form-select filter-dropdown">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="productFilter" class="form-label">Product</label>
                    <select id="productFilter" class="form-select filter-dropdown">
                        <option value="">All Products</option>
                        <!-- To be populated by AJAX -->
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="dateRangeFilter" class="form-label">Date Range</label>
                    <select id="dateRangeFilter" class="form-select filter-dropdown">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last7days">Last 7 Days</option>
                        <option value="last30days">Last 30 Days</option>
                        <option value="thisMonth">This Month</option>
                        <option value="lastMonth">Last Month</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="technicianFilter" class="form-label">Technician</label>
                    <select id="technicianFilter" class="form-select filter-dropdown">
                        <option value="">All Technicians</option>
                        <option value="assigned">Assigned</option>
                        <option value="unassigned">Unassigned</option>
                        <!-- More options populated by AJAX -->
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                <button id="resetFilters" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- Product Bookings Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="productBookingsTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Delivery Date</th>
                            <th>Delivery Time</th>
                            <th>Technicians</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by DataTablesManager -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Product Booking Modal -->
<div class="modal fade" id="viewProductBookingModal" tabindex="-1" role="dialog" aria-labelledby="viewProductBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProductBookingModalLabel">Product Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Customer Information Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img id="view-customer-avatar" src="/assets/images/default-profile.jpg" alt="Customer" class="rounded-circle me-3" width="80" height="80" style="border: 2px solid #eee; object-fit: cover;">
                            <div>
                                <h5 class="mb-1 fw-bold fs-4" id="view-customer"></h5>
                                <div class="text-muted mb-1" id="view-customer-email"><i class="fas fa-envelope me-2"></i><span></span></div>
                                <div class="text-muted" id="view-customer-phone"><i class="fas fa-phone me-2"></i><span></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <img id="view-product-image" src="" alt="Product Image" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                    </div>
                    <div class="col-md-8">
                        <h4 id="view-product-name" class="fw-bold"></h4>
                        <p><strong>Variant:</strong> <span id="view-product-variant"></span></p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Booking ID:</strong> <span id="view-id"></span></p>
                                <p><strong>Quantity:</strong> <span id="view-quantity"></span></p>
                                <p><strong>Unit Price:</strong> <span id="view-unit-price"></span></p>
                                <p><strong>Total Amount:</strong> <span id="view-total-amount" class="fw-bold text-primary"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <span id="view-status"></span></p>
                                <p><strong>Order Date:</strong> <span id="view-order-date"></span></p>
                                <p><strong>Preferred Delivery Date:</strong> <span id="view-delivery-date"></span></p>
                                <p><strong>Preferred Delivery Time:</strong> <span id="view-delivery-time"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Delivery Address:</strong></p>
                        <p id="view-address" class="border p-2 bg-light"></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Assigned Technicians:</strong></p>
                        <div id="view-technicians" class="border p-2 bg-light">
                            <!-- Technicians will be listed here -->
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

<!-- Edit Product Booking Modal -->
<div class="modal fade" id="editProductBookingModal" tabindex="-1" role="dialog" aria-labelledby="editProductBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductBookingModalLabel">Edit Product Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductBookingForm">
                    <input type="hidden" id="edit-id" name="bookingId">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-status" class="form-label">Status</label>
                            <select id="edit-status" name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-delivery-date" class="form-label">Delivery Date</label>
                            <input type="date" class="form-control" id="edit-delivery-date" name="deliveryDate">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-delivery-time" class="form-label">Delivery Time</label>
                            <input type="time" class="form-control" id="edit-delivery-time" name="deliveryTime">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Assigned Technicians</label>
                            <div class="d-flex align-items-center">
                                <select id="technician-select" class="form-select">
                                    <option value="">Select a technician</option>
                                    <!-- Populated by AJAX -->
                                </select>
                                <button type="button" id="add-technician-btn" class="btn btn-primary add-technician-btn">Add</button>
                            </div>
                            <div id="technician-list" class="technician-list">
                                <!-- Assigned technicians will be listed here -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveProductBookingBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductBookingModal" tabindex="-1" role="dialog" aria-labelledby="deleteProductBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductBookingModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product booking? This action cannot be undone.</p>
                <p><strong>ID:</strong> <span id="delete-id"></span></p>
                <p><strong>Customer:</strong> <span id="delete-customer"></span></p>
                <p><strong>Product:</strong> <span id="delete-product"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/DataTablesManager.js"></script>

<!-- Initialize DataTables and handle product bookings -->
<script>
let productBookingsManager;
let assignedTechnicians = []; // Track currently assigned technicians

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the DataTablesManager
    productBookingsManager = new DataTablesManager('productBookingsTable', {
        ajaxUrl: '/api/admin/product-bookings',
        columns: [
            { data: 'pb_id', title: 'ID' },
            { 
                data: null, 
                title: 'Customer',
                render: function(data, type, row) {
                    const profileUrl = row.customer_profile_url || '/assets/images/default-profile.jpg';
                    return `
                        <div class="customer-info">
                            <img src="${profileUrl}" alt="Profile" class="customer-avatar">
                            <div class="customer-details">
                                <div class="customer-name">${row.customer_name}</div>
                                <div class="customer-contact">${row.customer_email || ''}</div>
                                <div class="customer-contact">${row.customer_phone || ''}</div>
                            </div>
                        </div>
                    `;
                }
            },
            { data: 'prod_name', title: 'Product' },
            { data: 'pb_quantity', title: 'Quantity' },
            { data: 'pb_total_amount', title: 'Total Amount', render: function(data) {
                return data ? '$' + parseFloat(data).toFixed(2) : '-';
            }},
            { data: 'pb_preferred_date', title: 'Delivery Date' },
            { data: 'pb_preferred_time', title: 'Delivery Time' },
            {
                data: 'technicians',
                title: 'Technicians',
                render: function(data, type, row) {
                    if (!data || data.length === 0) {
                        return '<span class="badge bg-secondary">Unassigned</span>';
                    }
                    
                    let techHtml = '';
                    data.forEach(tech => {
                        const profileImg = tech.profile_url || '/assets/images/default-profile.jpg';
                        techHtml += `
                            <div class="technician-chip" title="${tech.name}">
                                <img src="${profileImg}" alt="${tech.name}">
                                <span>${tech.name.split(' ')[0]}</span>
                            </div>
                        `;
                    });
                    
                    return techHtml;
                }
            },
            { 
                data: 'pb_status', 
                title: 'Status',
                badge: {
                    valueMap: {
                        'pending': { type: 'warning', display: 'Pending' },
                        'confirmed': { type: 'info', display: 'Confirmed' },
                        'in-progress': { type: 'primary', display: 'In Progress' },
                        'completed': { type: 'success', display: 'Completed' },
                        'cancelled': { type: 'danger', display: 'Cancelled' }
                    }
                }
            },
            {
                data: null,
                title: 'Actions',
                render: function(data, type, row) {
                    return `<div class="d-flex">
                        <div class="action-icon action-icon-view view-btn me-1" data-id="${row.pb_id}">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div class="action-icon action-icon-edit edit-btn me-1" data-id="${row.pb_id}">
                            <i class="bi bi-pencil"></i>
                        </div>
                        <div class="action-icon action-icon-delete delete-btn" data-id="${row.pb_id}">
                            <i class="bi bi-trash"></i>
                        </div>
                    </div>`;
                }
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ],
        responsive: true
    });

    // Manually attach event listeners for action buttons
    $('#productBookingsTable').on('click', '.view-btn', function() {
        const id = $(this).data('id');
        viewProductBooking({pb_id: id});
    });

    $('#productBookingsTable').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        editProductBooking({pb_id: id});
    });

    $('#productBookingsTable').on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        // Find the full row data from the DataTable
        const rowData = productBookingsManager.dataTable.row($(this).closest('tr')).data();
        confirmDeleteProductBooking(rowData);
    });

    // Load products for filter
    loadProducts();
    
    // Load technicians for filter and assignment
    loadTechnicians();

    // Handle filter changes
    $('#statusFilter, #productFilter, #dateRangeFilter, #technicianFilter').on('change', applyFilters);
    
    // Reset filters
    $('#resetFilters').on('click', resetFilters);
    
    // Add technician to the edit form
    $('#add-technician-btn').on('click', addTechnicianToList);
    
    // Save product booking changes
    $('#saveProductBookingBtn').on('click', saveProductBooking);
    
    // Confirm delete
    $('#confirmDeleteBtn').on('click', deleteProductBooking);
});

// Load products for the filter dropdown
function loadProducts() {
    $.ajax({
        url: '/api/products',
        method: 'GET',
        success: function(response) {
            const productSelect = $('#productFilter');
            productSelect.find('option:not(:first)').remove();
            
            response.data.forEach(product => {
                productSelect.append(`<option value="${product.prod_id}">${product.prod_name}</option>`);
            });
        },
        error: function(xhr) {
            productBookingsManager.showErrorToast('Error', 'Failed to load products');
        }
    });
}

// Load technicians for the filter and assignment dropdowns
function loadTechnicians() {
    $.ajax({
        url: '/api/technicians',
        method: 'GET',
        success: function(response) {
            const techSelect = $('#technicianFilter');
            const editTechSelect = $('#technician-select');
            
            techSelect.find('option:not(:first-child):not(:nth-child(2)):not(:nth-child(3))').remove();
            editTechSelect.find('option:not(:first)').remove();
            
            response.data.forEach(tech => {
                const techName = `${tech.ua_first_name} ${tech.ua_last_name}`;
                techSelect.append(`<option value="${tech.te_account_id}">${techName}</option>`);
                editTechSelect.append(`<option value="${tech.te_account_id}" data-name="${techName}">${techName}</option>`);
            });
        },
        error: function(xhr) {
            productBookingsManager.showErrorToast('Error', 'Failed to load technicians');
        }
    });
}

// Apply filters to the table
function applyFilters() {
    const filters = {};
    
    const status = $('#statusFilter').val();
    const product = $('#productFilter').val();
    const dateRange = $('#dateRangeFilter').val();
    const technician = $('#technicianFilter').val();
    
    if (status) filters.status = status;
    if (product) filters.product_id = product;
    if (dateRange) filters.date_range = dateRange;
    if (technician) {
        if (technician === 'assigned') {
            filters.has_technician = true;
        } else if (technician === 'unassigned') {
            filters.has_technician = false;
        } else {
            filters.technician_id = technician;
        }
    }
    
    // Update the AJAX URL with filter parameters
    $.ajax({
        url: '/api/admin/product-bookings',
        method: 'GET',
        data: filters,
        success: function(response) {
            productBookingsManager.refresh(response.data);
        },
        error: function(xhr) {
            productBookingsManager.showErrorToast('Error', 'Failed to apply filters');
        }
    });
}

// Reset all filters
function resetFilters() {
    $('#statusFilter, #productFilter, #dateRangeFilter, #technicianFilter').val('');
    productBookingsManager.refresh();
}

// View product booking details
function viewProductBooking(rowData) {
    // Load detailed product booking data
    $.ajax({
        url: `/api/admin/product-bookings/${rowData.pb_id}`,
        method: 'GET',
        success: function(response) {
            const data = response.data;
            
            // Populate the view modal
            $('#view-id').text(data.pb_id);
            $('#view-customer').text(data.customer_name);
            $('#view-customer-email span').text(data.customer_email || '');
            $('#view-customer-phone span').text(data.customer_phone || '');
            $('#view-customer-avatar').attr('src', data.customer_profile_url || '/assets/images/default-profile.jpg');
            $('#view-product-name').text(data.prod_name);
            $('#view-product-variant').text(data.var_capacity);
            $('#view-product-image').attr('src', data.prod_image || '/assets/images/product-placeholder.jpg');
            $('#view-quantity').text(data.pb_quantity);
            $('#view-unit-price').text(data.pb_unit_price ? '₱' + parseFloat(data.pb_unit_price).toFixed(2) : '-');
            $('#view-total-amount').text(data.pb_total_amount ? '₱' + parseFloat(data.pb_total_amount).toFixed(2) : '-');
            $('#view-status').text(data.pb_status.charAt(0).toUpperCase() + data.pb_status.slice(1));
            $('#view-order-date').text(data.pb_order_date);
            $('#view-delivery-date').text(data.pb_preferred_date);
            $('#view-delivery-time').text(data.pb_preferred_time);
            $('#view-address').text(data.pb_address);
            
            // Display assigned technicians
            const techContainer = $('#view-technicians');
            techContainer.empty();
            
            if (data.technicians && data.technicians.length > 0) {
                const techHtml = data.technicians.map(tech => {
                    const profileImg = tech.profile_url || '/assets/images/default-profile.jpg';
                    return `
                        <div class="d-flex align-items-center mb-3 p-3 bg-white rounded border">
                            <img src="${profileImg}" alt="${tech.name}" class="rounded-circle me-3" width="48" height="48" style="border: 1px solid #eee;">
                            <div>
                                <div class="fw-bold fs-5">${tech.name}</div>
                                <div class="text-muted mb-1"><i class="fas fa-envelope me-2"></i>${tech.email || 'N/A'}</div>
                                <div class="text-muted"><i class="fas fa-phone me-2"></i>${tech.phone || 'N/A'}</div>
                                ${tech.notes ? `<div class="text-muted mt-2 border-top pt-2">${tech.notes}</div>` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
                
                techContainer.html(techHtml);
            } else {
                techContainer.html('<p class="text-muted mb-0">No technicians assigned</p>');
            }
            
            // Show the modal
            $('#viewProductBookingModal').modal('show');
        },
        error: function(xhr) {
            console.error("Error fetching product booking details:", xhr);
            alert('Failed to load product booking details');
        }
    });
}

// Edit product booking
function editProductBooking(rowData) {
    // Load detailed product booking data for editing
    $.ajax({
        url: `/api/admin/product-bookings/${rowData.pb_id}`,
        method: 'GET',
        success: function(response) {
            const data = response.data;
            
            // Populate the edit form
            $('#edit-id').val(data.pb_id);
            $('#edit-status').val(data.pb_status);
            
            // Set min date to current date
            const now = new Date();
            const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD format
            $('#edit-delivery-date').attr('min', currentDate);
            
            // Set the date and time values
            $('#edit-delivery-date').val(data.pb_preferred_date);
            $('#edit-delivery-time').val(data.pb_preferred_time);
            
            // Clear and populate assigned technicians
            assignedTechnicians = [];
            const techList = $('#technician-list');
            techList.empty();
            
            if (data.technicians && data.technicians.length > 0) {
                data.technicians.forEach(tech => {
                    assignedTechnicians.push({
                        id: tech.id,
                        name: tech.name,
                        notes: tech.notes
                    });
                    
                    addTechnicianBadge(tech.id, tech.name, tech.notes);
                });
            }
            
            // Show the modal
            $('#editProductBookingModal').modal('show');
        },
        error: function(xhr) {
            productBookingsManager.showErrorToast('Error', 'Failed to load product booking for editing');
        }
    });
}

// Add a technician to the list in the edit form
function addTechnicianToList() {
    const techSelect = $('#technician-select');
    const techId = techSelect.val();
    
    if (!techId) {
        // Show toast notification for empty selection
        productBookingsManager.showWarningToast('Warning', 'Please select a technician');
        return;
    }
    
    const techName = techSelect.find('option:selected').data('name');
    
    // Check if technician is already in the list
    const alreadyAssigned = assignedTechnicians.some(tech => tech.id === techId || tech.id === parseInt(techId));
    
    if (alreadyAssigned) {
        // Show toast notification for duplicate technician
        productBookingsManager.showWarningToast('Warning', `${techName} is already assigned to this booking`);
        return;
    }
    
    // Add to our tracking array
    assignedTechnicians.push({
        id: techId,
        name: techName
    });
    
    // Add badge to the UI
    addTechnicianBadge(techId, techName);
    
    // Reset the select
    techSelect.val('');
}

// Create and add a technician badge to the UI
function addTechnicianBadge(techId, techName, notes = '') {
    const techList = $('#technician-list');
    const badge = $(`
        <div class="technician-badge" data-id="${techId}">
            <div>
                <span>${techName}</span>
                <span class="technician-remove" style="cursor: pointer; margin-left: 5px;">×</span>
            </div>
            <textarea class="form-control mt-1 technician-notes" placeholder="Add notes for ${techName}" rows="2">${notes || ''}</textarea>
        </div>
    `);
    
    // Add remove functionality
    badge.find('.technician-remove').on('click', function() {
        // Remove from tracking array
        assignedTechnicians = assignedTechnicians.filter(tech => tech.id !== techId);
        // Remove badge from UI
        badge.remove();
    });
    
    techList.append(badge);
}

// Save product booking changes
function saveProductBooking() {
    const bookingId = $('#edit-id').val();
    const status = $('#edit-status').val();
    const preferredDate = $('#edit-delivery-date').val();
    const preferredTime = $('#edit-delivery-time').val();
    
    // Get technician IDs and their notes
    const techniciansData = [];
    $('#technician-list .technician-badge').each(function() {
        const techId = $(this).data('id');
        const notes = $(this).find('.technician-notes').val();
        techniciansData.push({
            id: techId,
            notes: notes
        });
    });
    
    // Validate date and time
    const now = new Date();
    const selectedDateTime = new Date(`${preferredDate}T${preferredTime}`);
    
    if (selectedDateTime < now) {
        productBookingsManager.showErrorToast('Validation Error', 'Delivery date and time cannot be in the past');
        return;
    }
    
    // Prepare data for update
    const updateData = {
        bookingId: bookingId,
        status: status,
        preferredDate: preferredDate,
        preferredTime: preferredTime,
        technicians: techniciansData
    };
    
    // Send update request
    $.ajax({
        url: '/api/admin/product-bookings/update',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(updateData),
        success: function(response) {
            $('#editProductBookingModal').modal('hide');
            productBookingsManager.showSuccessToast('Success', response.message);
            productBookingsManager.refresh();
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Failed to update product booking';
            productBookingsManager.showErrorToast('Error', errorMsg);
        }
    });
}

// Confirm product booking deletion
function confirmDeleteProductBooking(rowData) {
    $('#delete-id').text(rowData.pb_id);
    $('#delete-customer').text(rowData.customer_name);
    $('#delete-product').text(rowData.prod_name);
    
    $('#deleteProductBookingModal').modal('show');
}

// Delete product booking
function deleteProductBooking() {
    const bookingId = $('#delete-id').text();
    
    $.ajax({
        url: `/api/admin/product-bookings/delete/${bookingId}`,
        method: 'POST',
        success: function(response) {
            $('#deleteProductBookingModal').modal('hide');
            productBookingsManager.showSuccessToast('Success', response.message);
            productBookingsManager.refresh();
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Failed to delete product booking';
            productBookingsManager.showErrorToast('Error', errorMsg);
        }
    });
}
</script>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?> 