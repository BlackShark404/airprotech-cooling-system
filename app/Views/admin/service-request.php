<?php
$title = 'Service Requests - AirProtech';
$activeTab = 'service_requests';

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
    .badge-progress {
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
    .badge-high {
        background-color: #dc3545;
        color: #fff;
    }
    .badge-medium {
        background-color: #fd7e14;
        color: #212529;
    }
    .badge-low {
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
    .technician-badge {
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
        padding: 5px 10px;
        border-radius: 15px;
        background-color: #e9ecef;
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
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Service Request Management</h1>
        <p class="text-muted">Manage service requests</p>
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
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="typeFilter" class="form-label">Service Type</label>
                    <select id="typeFilter" class="form-select filter-dropdown">
                        <option value="">All Types</option>
                        <!-- To be populated by AJAX -->
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="priorityFilter" class="form-label">Priority</label>
                    <select id="priorityFilter" class="form-select filter-dropdown">
                        <option value="">All Priorities</option>
                        <option value="urgent">Urgent</option>
                        <option value="moderate">Moderate</option>
                        <option value="normal">Normal</option>
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
            </div>
            <div class="row">
                <div class="col-md-6 d-flex align-items-end mb-3">
                    <button id="resetFilters" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Requests Table -->
    <div class="card">
        <div class="card-body">
            <table id="serviceRequestsTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Est. Cost</th>
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

<!-- View Service Request Modal -->
<div class="modal fade" id="viewServiceRequestModal" tabindex="-1" role="dialog" aria-labelledby="viewServiceRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewServiceRequestModalLabel">Service Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <span id="view-id"></span></p>
                        <p><strong>Customer:</strong> <span id="view-customer"></span></p>
                        <p><strong>Service Type:</strong> <span id="view-service-type"></span></p>
                        <p><strong>Preferred Date:</strong> <span id="view-date"></span></p>
                        <p><strong>Preferred Time:</strong> <span id="view-time"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span id="view-status"></span></p>
                        <p><strong>Priority:</strong> <span id="view-priority"></span></p>
                        <p><strong>Estimated Cost:</strong> <span id="view-cost"></span></p>
                        <p><strong>Created:</strong> <span id="view-created"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Address:</strong></p>
                        <p id="view-address" class="border p-2 bg-light"></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Description:</strong></p>
                        <p id="view-description" class="border p-2 bg-light"></p>
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

<!-- Edit Service Request Modal -->
<div class="modal fade" id="editServiceRequestModal" tabindex="-1" role="dialog" aria-labelledby="editServiceRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceRequestModalLabel">Edit Service Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editServiceRequestForm">
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
                        <div class="col-md-6">
                            <label for="edit-priority" class="form-label">Priority</label>
                            <select id="edit-priority" name="priority" class="form-select">
                                <option value="normal">Normal</option>
                                <option value="moderate">Moderate</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-cost" class="form-label">Estimated Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit-cost" name="estimatedCost" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit-date" class="form-label">Preferred Date</label>
                            <input type="date" class="form-control" id="edit-date" name="preferredDate">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-time" class="form-label">Preferred Time</label>
                            <input type="time" class="form-control" id="edit-time" name="preferredTime">
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
                <button type="button" class="btn btn-primary" id="saveServiceRequestBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteServiceRequestModal" tabindex="-1" role="dialog" aria-labelledby="deleteServiceRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteServiceRequestModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this service request? This action cannot be undone.</p>
                <p><strong>ID:</strong> <span id="delete-id"></span></p>
                <p><strong>Customer:</strong> <span id="delete-customer"></span></p>
                <p><strong>Service Type:</strong> <span id="delete-service-type"></span></p>
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

<!-- Initialize DataTables and handle service requests -->
<script>
let serviceRequestsManager;
let assignedTechnicians = []; // Track currently assigned technicians

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the DataTablesManager
    serviceRequestsManager = new DataTablesManager('serviceRequestsTable', {
        ajaxUrl: '/api/admin/service-requests',
        columns: [
            { data: 'sb_id', title: 'ID' },
            { data: 'customer_name', title: 'Customer' },
            { data: 'service_name', title: 'Service Type' },
            { data: 'sb_preferred_date', title: 'Date' },
            { data: 'sb_preferred_time', title: 'Time' },
            { 
                data: 'sb_status', 
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
                data: 'sb_priority', 
                title: 'Priority',
                badge: {
                    valueMap: {
                        'normal': { type: 'success', display: 'Normal' },
                        'moderate': { type: 'warning', display: 'Moderate' },
                        'urgent': { type: 'danger', display: 'Urgent' }
                    }
                }
            },
            { data: 'sb_estimated_cost', title: 'Est. Cost', render: function(data) {
                return data ? '$' + parseFloat(data).toFixed(2) : '-';
            }},
            {
                data: null,
                title: 'Actions',
                render: function(data, type, row) {
                    return `<div class="d-flex">
                        <button class="btn btn-sm btn-info me-1 view-btn" data-id="${row.sb_id}">View</button>
                        <button class="btn btn-sm btn-warning me-1 edit-btn" data-id="${row.sb_id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.sb_id}">Delete</button>
                    </div>`;
                }
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    });

    // Manually attach event listeners for action buttons
    $('#serviceRequestsTable').on('click', '.view-btn', function() {
        const id = $(this).data('id');
        viewServiceRequest({sb_id: id});
    });

    $('#serviceRequestsTable').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        editServiceRequest({sb_id: id});
    });

    $('#serviceRequestsTable').on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        // Find the full row data from the DataTable
        const rowData = serviceRequestsManager.dataTable.row($(this).closest('tr')).data();
        confirmDeleteServiceRequest(rowData);
    });

    // Load service types for filter
    loadServiceTypes();
    
    // Load technicians for filter and assignment
    loadTechnicians();

    // Handle filter changes
    $('#statusFilter, #typeFilter, #priorityFilter, #technicianFilter').on('change', applyFilters);
    
    // Reset filters
    $('#resetFilters').on('click', resetFilters);
    
    // Add technician to the edit form
    $('#add-technician-btn').on('click', addTechnicianToList);
    
    // Save service request changes
    $('#saveServiceRequestBtn').on('click', saveServiceRequest);
    
    // Confirm delete
    $('#confirmDeleteBtn').on('click', deleteServiceRequest);
});

// Load service types for the filter dropdown
function loadServiceTypes() {
    $.ajax({
        url: '/api/service-types',
        method: 'GET',
        success: function(response) {
            const typeSelect = $('#typeFilter');
            typeSelect.find('option:not(:first)').remove();
            
            response.data.forEach(type => {
                typeSelect.append(`<option value="${type.st_id}">${type.st_name}</option>`);
            });
        },
        error: function(xhr) {
            serviceRequestsManager.showErrorToast('Error', 'Failed to load service types');
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
            serviceRequestsManager.showErrorToast('Error', 'Failed to load technicians');
        }
    });
}

// Apply filters to the table
function applyFilters() {
    const filters = {};
    
    const status = $('#statusFilter').val();
    const type = $('#typeFilter').val();
    const priority = $('#priorityFilter').val();
    const technician = $('#technicianFilter').val();
    
    if (status) filters.status = status;
    if (type) filters.service_type_id = type;
    if (priority) filters.priority = priority;
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
        url: '/api/admin/service-requests',
        method: 'GET',
        data: filters,
        success: function(response) {
            serviceRequestsManager.refresh(response.data);
        },
        error: function(xhr) {
            serviceRequestsManager.showErrorToast('Error', 'Failed to apply filters');
        }
    });
}

// Reset all filters
function resetFilters() {
    $('#statusFilter, #typeFilter, #priorityFilter, #technicianFilter').val('');
    serviceRequestsManager.refresh();
}

// View service request details
function viewServiceRequest(rowData) {
    // Load detailed service request data
    $.ajax({
        url: `/api/admin/service-requests/${rowData.sb_id}`,
        method: 'GET',
        success: function(response) {
            const data = response.data;
            
            // Populate the view modal
            $('#view-id').text(data.sb_id);
            $('#view-customer').text(data.customer_name);
            $('#view-service-type').text(data.service_name);
            $('#view-date').text(data.sb_preferred_date);
            $('#view-time').text(data.sb_preferred_time);
            $('#view-status').text(data.sb_status.charAt(0).toUpperCase() + data.sb_status.slice(1));
            $('#view-priority').text(data.sb_priority.charAt(0).toUpperCase() + data.sb_priority.slice(1));
            $('#view-cost').text(data.sb_estimated_cost ? '$' + parseFloat(data.sb_estimated_cost).toFixed(2) : '-');
            $('#view-created').text(data.sb_created_at);
            $('#view-address').text(data.sb_address);
            $('#view-description').text(data.sb_description);
            
            // Display assigned technicians
            const techContainer = $('#view-technicians');
            techContainer.empty();
            
            console.log("Technicians data:", data.technicians); // Debug log
            
            if (data.technicians && data.technicians.length > 0) {
                data.technicians.forEach(tech => {
                    techContainer.append(`<div class="technician-badge">${tech.name}</div>`);
                });
            } else {
                techContainer.text('No technicians assigned');
            }
            
            // Show the modal
            $('#viewServiceRequestModal').modal('show');
        },
        error: function(xhr) {
            console.error("Error fetching service request details:", xhr);
            alert('Failed to load service request details');
        }
    });
}

// Edit service request
function editServiceRequest(rowData) {
    // Load detailed service request data for editing
    $.ajax({
        url: `/api/admin/service-requests/${rowData.sb_id}`,
        method: 'GET',
        success: function(response) {
            const data = response.data;
            
            // Populate the edit form
            $('#edit-id').val(data.sb_id);
            $('#edit-status').val(data.sb_status);
            $('#edit-priority').val(data.sb_priority);
            $('#edit-cost').val(data.sb_estimated_cost || '');
            
            // Set the date and time values
            $('#edit-date').val(data.sb_preferred_date);
            $('#edit-time').val(data.sb_preferred_time);
            
            // Clear and populate assigned technicians
            assignedTechnicians = [];
            const techList = $('#technician-list');
            techList.empty();
            
            if (data.technicians && data.technicians.length > 0) {
                data.technicians.forEach(tech => {
                    assignedTechnicians.push({
                        id: tech.id,
                        name: tech.name
                    });
                    
                    addTechnicianBadge(tech.id, tech.name);
                });
            }
            
            // Show the modal
            $('#editServiceRequestModal').modal('show');
        },
        error: function(xhr) {
            serviceRequestsManager.showErrorToast('Error', 'Failed to load service request for editing');
        }
    });
}

// Add a technician to the list in the edit form
function addTechnicianToList() {
    const techSelect = $('#technician-select');
    const techId = techSelect.val();
    
    if (!techId) {
        // Show toast notification for empty selection
        if (typeof serviceRequestsManager !== 'undefined') {
            serviceRequestsManager.showWarningToast('Warning', 'Please select a technician');
        } else {
            alert('Please select a technician');
        }
        return;
    }
    
    const techName = techSelect.find('option:selected').data('name');
    
    // Check if technician is already in the list
    const alreadyAssigned = assignedTechnicians.some(tech => tech.id === techId || tech.id === parseInt(techId));
    
    if (alreadyAssigned) {
        // Show toast notification for duplicate technician
        if (typeof serviceRequestsManager !== 'undefined') {
            serviceRequestsManager.showWarningToast('Warning', `${techName} is already assigned to this request`);
        } else {
            alert(`${techName} is already assigned to this request`);
        }
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
function addTechnicianBadge(techId, techName) {
    const techList = $('#technician-list');
    const badge = $(`
        <div class="technician-badge" data-id="${techId}">
            ${techName}
            <span class="technician-remove">×</span>
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

// Save service request changes
function saveServiceRequest() {
    const bookingId = $('#edit-id').val();
    const status = $('#edit-status').val();
    const priority = $('#edit-priority').val();
    const estimatedCost = $('#edit-cost').val();
    const preferredDate = $('#edit-date').val();
    const preferredTime = $('#edit-time').val();
    const technicianIds = assignedTechnicians.map(tech => tech.id);
    
    // Prepare data for update
    const updateData = {
        bookingId: bookingId,
        status: status,
        priority: priority,
        estimatedCost: estimatedCost,
        preferredDate: preferredDate,
        preferredTime: preferredTime,
        technicians: technicianIds
    };
    
    // Send update request
    $.ajax({
        url: '/api/admin/service-requests/update',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(updateData),
        success: function(response) {
            $('#editServiceRequestModal').modal('hide');
            serviceRequestsManager.showSuccessToast('Success', response.message);
            serviceRequestsManager.refresh();
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Failed to update service request';
            serviceRequestsManager.showErrorToast('Error', errorMsg);
        }
    });
}

// Confirm service request deletion
function confirmDeleteServiceRequest(rowData) {
    $('#delete-id').text(rowData.sb_id);
    $('#delete-customer').text(rowData.customer_name);
    $('#delete-service-type').text(rowData.service_name);
    
    $('#deleteServiceRequestModal').modal('show');
}

// Delete service request
function deleteServiceRequest() {
    const bookingId = $('#delete-id').text();
    
    $.ajax({
        url: `/api/admin/service-requests/delete/${bookingId}`,
        method: 'POST',
        success: function(response) {
            $('#deleteServiceRequestModal').modal('hide');
            serviceRequestsManager.showSuccessToast('Success', response.message);
            serviceRequestsManager.refresh();
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Failed to delete service request';
            serviceRequestsManager.showErrorToast('Error', errorMsg);
        }
    });
}
</script>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>