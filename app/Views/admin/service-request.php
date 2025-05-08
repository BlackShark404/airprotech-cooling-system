<?php
$title = 'Service Requests - AC Service Pro';
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
                <div class="col-md-3 mb-3">
                    <label for="priorityFilter" class="form-label">Priority</label>
                    <select id="priorityFilter" class="form-select filter-dropdown">
                        <option value="">All Priorities</option>
                        <option value="high">Urgent</option>
                        <option value="medium">Moderate</option>
                        <option value="low">Normal</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="technicianFilter" class="form-label">Technician</label>
                    <select id="technicianFilter" class="form-select filter-dropdown">
                        <option value="">All Technicians</option>
                        <option value="unassigned">Unassigned</option>
                        <!-- To be populated by AJAX -->
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" id="startDate" class="form-control date-input">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" id="endDate" class="form-control date-input">
                </div>
                <div class="col-md-6 d-flex align-items-end mb-3">
                    <button id="applyFilters" class="btn btn-primary me-2">Apply Filters</button>
                    <button id="resetFilters" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Requests Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Service Requests</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="serviceRequestsTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Technician</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Service Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>Customer Information</h6>
                        <p><strong>Name:</strong> <span id="viewCustomerName"></span></p>
                        <p><strong>Email:</strong> <span id="viewCustomerEmail"></span></p>
                        <p><strong>Phone:</strong> <span id="viewCustomerPhone"></span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6>Service Information</h6>
                        <p><strong>Type:</strong> <span id="viewServiceType"></span></p>
                        <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                        <p><strong>Priority:</strong> <span id="viewPriority"></span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>Schedule Information</h6>
                        <p><strong>Date:</strong> <span id="viewDate"></span></p>
                        <p><strong>Time:</strong> <span id="viewTime"></span></p>
                        <p><strong>Technician:</strong> <span id="viewTechnician"></span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6>Address</h6>
                        <p id="viewAddress"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p id="viewDescription"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Service Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editServiceRequestForm">
                    <input type="hidden" id="editBookingId" name="bookingId">
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select id="editStatus" name="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editPriority" class="form-label">Priority</label>
                        <select id="editPriority" name="priority" class="form-select" required>
                            <option value="high">Urgent</option>
                            <option value="medium">Moderate</option>
                            <option value="low">Normal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editDate" class="form-label">Date</label>
                        <input type="date" id="editDate" name="requestedDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTime" class="form-label">Time</label>
                        <input type="time" id="editTime" name="requestedTime" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Technician Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignTechnicianForm">
                    <input type="hidden" id="assignBookingId" name="bookingId">
                    <div class="mb-3">
                        <label for="assignTechnician" class="form-label">Select Technician</label>
                        <select id="assignTechnician" name="technicianId" class="form-select" required>
                            <option value="">Select a technician</option>
                            <!-- To be populated by AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignNotes" class="form-label">Assignment Notes</label>
                        <textarea id="assignNotes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAssignBtn">Assign Technician</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this service request? This action cannot be undone.</p>
                <input type="hidden" id="deleteBookingId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- Include ToastManager -->
<script src="/assets/js/utility/ToastManager.js"></script>

<!-- Initialize DataTables and handle service requests -->
<script>
$(document).ready(function() {
    // Load service types for filter dropdown
    $.ajax({
        url: '/admin/service-types/get-active',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateServiceTypes(response.data);
            }
        },
        error: function(xhr, status, error) {
            toastManager.showErrorToast('Error', 'Failed to load service types.');
        }
    });

    // Load technicians for filter and assign dropdowns
    $.ajax({
        url: '/admin/technicians/get-active',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateTechnicians(response.data);
            }
        },
        error: function(xhr, status, error) {
            toastManager.showErrorToast('Error', 'Failed to load technicians.');
        }
    });

    // Initialize DataTable
    let dataTable = $('#serviceRequestsTable').DataTable({
        processing: true,
        serverSide: false, // Using client-side processing
        ajax: {
            url: '/admin/service-requests/data',
            type: 'POST',
            data: function(d) {
                // Add filter parameters
                d.status = $('#statusFilter').val();
                d.serviceType = $('#typeFilter').val();
                d.priority = $('#priorityFilter').val();
                d.technician = $('#technicianFilter').val();
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                return d;
            },
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'sb_id' },
            { 
                data: null,
                render: function(data, type, row) {
                    return row.customer_first_name + ' ' + row.customer_last_name;
                } 
            },
            { data: 'service_name' },
            { data: 'sb_requested_date' },
            { data: 'sb_requested_time' },
            { 
                data: null,
                render: function(data, type, row) {
                    return row.technician_name || 'Unassigned';
                } 
            },
            { 
                data: 'sb_status',
                render: function(data, type, row) {
                    let badgeClass = 'badge-pending';
                    let statusText = 'Pending';
                    
                    switch(data) {
                        case 'in-progress':
                            badgeClass = 'badge-progress';
                            statusText = 'In Progress';
                            break;
                        case 'completed':
                            badgeClass = 'badge-completed';
                            statusText = 'Completed';
                            break;
                        case 'cancelled':
                            badgeClass = 'badge-cancelled';
                            statusText = 'Cancelled';
                            break;
                    }
                    
                    return '<span class="badge ' + badgeClass + ' rounded-pill px-3 py-2">' + statusText + '</span>';
                }
            },
            { 
                data: 'sb_priority',
                render: function(data, type, row) {
                    let badgeClass = 'badge-medium';
                    let priorityText = 'Moderate';
                    
                    switch(data) {
                        case 'high':
                            badgeClass = 'badge-high';
                            priorityText = 'Urgent';
                            break;
                        case 'low':
                            badgeClass = 'badge-low';
                            priorityText = 'Normal';
                            break;
                    }
                    
                    return '<span class="badge ' + badgeClass + ' rounded-pill px-3 py-2">' + priorityText + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="action-icon action-icon-view" data-id="${row.sb_id}"><i class="bi bi-eye"></i></div>
                        <div class="action-icon action-icon-edit" data-id="${row.sb_id}"><i class="bi bi-pencil"></i></div>
                        <div class="action-icon action-icon-assign" data-id="${row.sb_id}"><i class="bi bi-person-check"></i></div>
                        <div class="action-icon action-icon-delete" data-id="${row.sb_id}"><i class="bi bi-trash"></i></div>
                    `;
                }
            }
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>t<"d-flex justify-content-between mt-3"lip>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                className: 'btn btn-sm btn-danger'
            },
            {
                text: '<i class="bi bi-arrow-repeat me-1"></i> Refresh',
                className: 'btn btn-sm btn-info text-white',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        responsive: true
    });

    // Apply filters
    $('#applyFilters').on('click', function() {
        dataTable.ajax.reload();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#typeFilter').val('');
        $('#priorityFilter').val('');
        $('#technicianFilter').val('');
        $('#startDate').val('');
        $('#endDate').val('');
        dataTable.ajax.reload();
    });

    // View service request
    $(document).on('click', '.action-icon-view', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/service-requests/get/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#viewCustomerName').text(data.customer_first_name + ' ' + data.customer_last_name);
                    $('#viewCustomerEmail').text(data.customer_email);
                    $('#viewCustomerPhone').text(data.customer_phone);
                    $('#viewServiceType').text(data.service_name);
                    
                    let statusText = 'Pending';
                    switch(data.sb_status) {
                        case 'in-progress': statusText = 'In Progress'; break;
                        case 'completed': statusText = 'Completed'; break;
                        case 'cancelled': statusText = 'Cancelled'; break;
                    }
                    $('#viewStatus').text(statusText);
                    
                    let priorityText = 'Moderate';
                    switch(data.sb_priority) {
                        case 'high': priorityText = 'Urgent'; break;
                        case 'low': priorityText = 'Normal'; break;
                    }
                    $('#viewPriority').text(priorityText);
                    
                    $('#viewDate').text(formatDate(data.sb_requested_date));
                    $('#viewTime').text(formatTime(data.sb_requested_time));
                    $('#viewTechnician').text(data.technician_name || 'Unassigned');
                    $('#viewAddress').text(data.sb_address);
                    $('#viewDescription').text(data.sb_description);
                    
                    $('#viewModal').modal('show');
                } else {
                    toastManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                toastManager.showErrorToast('Error', 'Failed to load service request details.');
            }
        });
    });

    // Edit service request
    $(document).on('click', '.action-icon-edit', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/service-requests/get/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#editBookingId').val(data.sb_id);
                    $('#editStatus').val(data.sb_status);
                    $('#editPriority').val(data.sb_priority);
                    $('#editDate').val(data.sb_requested_date);
                    $('#editTime').val(data.sb_requested_time);
                    $('#editModal').modal('show');
                } else {
                    toastManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                toastManager.showErrorToast('Error', 'Failed to load service request details.');
            }
        });
    });

    // Save edited service request
    $('#saveEditBtn').on('click', function() {
        const formData = {
            bookingId: $('#editBookingId').val(),
            status: $('#editStatus').val(),
            priority: $('#editPriority').val(),
            requestedDate: $('#editDate').val(),
            requestedTime: $('#editTime').val()
        };
        
        $.ajax({
            url: '/admin/service-requests/update',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    dataTable.ajax.reload();
                    toastManager.showSuccessToast('Success', response.message);
                } else {
                    toastManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                toastManager.showErrorToast('Error', 'Failed to update service request.');
            }
        });
    });

    // Assign technician
    $(document).on('click', '.action-icon-assign', function() {
        const id = $(this).data('id');
        $('#assignBookingId').val(id);
        $('#assignTechnician').val('');
        $('#assignNotes').val('');
        $('#assignModal').modal('show');
    });

    // Save technician assignment
    $('#saveAssignBtn').on('click', function() {
        const formData = {
            bookingId: $('#assignBookingId').val(),
            technicianId: $('#assignTechnician').val(),
            notes: $('#assignNotes').val()
        };
        
        if (!formData.technicianId) {
            toastManager.showErrorToast('Error', 'Please select a technician.');
            return;
        }
        
        $.ajax({
            url: '/admin/service-requests/assign',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#assignModal').modal('hide');
                    dataTable.ajax.reload();
                    toastManager.showSuccessToast('Success', response.message);
                } else {
                    toastManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                toastManager.showErrorToast('Error', 'Failed to assign technician.');
            }
        });
    });

    // Delete service request
    $(document).on('click', '.action-icon-delete', function() {
        const id = $(this).data('id');
        $('#deleteBookingId').val(id);
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        const bookingId = $('#deleteBookingId').val();
        
        $.ajax({
            url: `/admin/service-requests/delete/${bookingId}`,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    dataTable.ajax.reload();
                    toastManager.showSuccessToast('Success', response.message);
                } else {
                    toastManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                toastManager.showErrorToast('Error', 'Failed to delete service request.');
            }
        });
    });

    // Helper function to populate service types dropdown
    function populateServiceTypes(types) {
        const typeDropdown = $('#typeFilter');
        const assignDropdown = $('#assignTechnician');
        
        types.forEach(function(type) {
            typeDropdown.append(`<option value="${type.st_code}">${type.st_name}</option>`);
        });
    }

    // Helper function to populate technicians dropdown
    function populateTechnicians(technicians) {
        const techDropdown = $('#technicianFilter');
        const assignDropdown = $('#assignTechnician');
        
        technicians.forEach(function(tech) {
            techDropdown.append(`<option value="${tech.tech_id}">${tech.tech_first_name} ${tech.tech_last_name}</option>`);
            assignDropdown.append(`<option value="${tech.tech_id}">${tech.tech_first_name} ${tech.tech_last_name}</option>`);
        });
    }

    // Helper function to format date
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Helper function to format time
    function formatTime(timeString) {
        const options = { hour: '2-digit', minute: '2-digit' };
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString(undefined, options);
    }
});
</script>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>