<?php
$title = 'Technicians - AC Service Pro';
$activeTab = 'technicians';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .technician-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .technician-status {
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 12px;
        font-weight: 500;
    }
    .technician-status-active {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .technician-status-deactivated {
        background-color: #f1f3f5;
        color: #6c757d;
    }

    .action-button {
        background-color: #007bff;
        color: white;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
    }
    .action-button:hover {
        background-color: #0069d9;
        color: white;
        text-decoration: none;
    }
    .assign-job {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }
    .assign-job:hover {
        color: #0069d9;
        text-decoration: underline;
    }
    .status-badge {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 500;
    }
    .in-progress {
        background-color: #e7f1ff;
        color: #007bff;
    }
    .completed {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .pending {
        background-color: #fff8e6;
        color: #ffbb00;
    }
    .filter-card {
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 15px;
        margin-bottom: 20px;
    }
    .action-icons {
        display: flex;
        gap: 10px;
    }
    .action-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .view-icon {
        background-color: #e7f1ff;
        color: #007bff;
    }
    .view-icon:hover {
        background-color: #007bff;
        color: white;
    }
    .edit-icon {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .edit-icon:hover {
        background-color: #34c759;
        color: white;
    }
    .delete-icon {
        background-color: #ffeeee;
        color: #dc3545;
    }
    .delete-icon:hover {
        background-color: #dc3545;
        color: white;
    }
    .technician-profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
    }
    .detail-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
    }
    .stats-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        text-align: center;
    }
    .stats-value {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-label {
        font-size: 12px;
        color: #6c757d;
    }
</style>

HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Technician Management</h1>
        <p class="text-muted">Manage your service technicians</p>
    </div>

    <!-- Action Button -->
    <div class="row mb-4">
        <div class="col col-12 d-flex justify-content-end">
            <button class="btn btn-blue d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
                <i class="bi bi-person-plus me-2"></i>
                Add New Technician
            </button>
        </div>
    </div>

      <!-- Main Content Area -->
    <div class="row">
        <!-- Technician List (Now full width) -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Technicians</h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search technicians...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table" id="technicianTable">
                            <thead>
                                <tr>
                                    <th>TECHNICIAN</th>
                                    <th>STATUS</th>
                                    <th>ACCOUNT STATUS</th>
                                    <th>ADDRESS</th>
                                    <th>PHONE</th>
                                    <th>ACTIONS</th>
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
    </div>
</div>

<!-- Add Technician Modal -->
<div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTechnicianModalLabel">Add New Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTechnicianForm">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isAvailable" name="isAvailable" checked>
                                <label class="form-check-label" for="isAvailable">Available for Assignments</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive" name="isActive" checked>
                                <label class="form-check-label" for="isActive">Account Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Technician</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Technician Modal -->
<div class="modal fade" id="editTechnicianModal" tabindex="-1" aria-labelledby="editTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTechnicianModalLabel">Edit Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTechnicianForm">
                <div class="modal-body">
                    <input type="hidden" name="technicianId" id="editTechnicianId">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="firstName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current password">
                            <small class="text-muted">Only enter a new password if you want to change it</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="editAddress" name="address" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editIsAvailable" name="isAvailable">
                                <label class="form-check-label" for="editIsAvailable">Available for Assignments</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editIsActive" name="isActive">
                                <label class="form-check-label" for="editIsActive">Account Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Technician Modal -->
<div class="modal fade" id="viewTechnicianModal" tabindex="-1" aria-labelledby="viewTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTechnicianModalLabel">Technician Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Technician Profile -->
                    <div class="col-md-4">
                        <div class="detail-card text-center">
                            <img src="" id="viewTechnicianAvatar" class="technician-profile-avatar" alt="Technician">
                            <h5 id="viewTechnicianName" class="mb-1"></h5>
                            <p id="viewTechnicianEmail" class="text-muted small mb-2"></p>
                            <p id="viewTechnicianPhone" class="mb-2"></p>
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <span id="viewTechnicianAvailability" class="badge rounded-pill"></span>
                                <span id="viewTechnicianAccountStatus" class="badge rounded-pill"></span>
                            </div>
                            <div class="text-start">
                                <p class="mb-1"><strong>Address:</strong></p>
                                <p id="viewTechnicianAddress" class="mb-0 text-muted small"></p>
                            </div>
                        </div>
                        
                        <!-- Technician Stats -->
                        <div class="row">
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-value" id="viewTechnicianTotalAssignments">0</div>
                                    <div class="stats-label">Total Jobs</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-value" id="viewTechnicianCompletedAssignments">0</div>
                                    <div class="stats-label">Completed</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-value" id="viewTechnicianCurrentWorkload">0</div>
                                    <div class="stats-label">Current Jobs</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-value" id="viewTechnicianCompletionRate">0%</div>
                                    <div class="stats-label">Completion Rate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Assignments -->
                    <div class="col-md-8">
                        <div class="detail-card">
                            <h5 class="mb-3">Current Assignments</h5>
                            <ul class="list-group" id="viewTechnicianAssignments">
                                <!-- Will be populated dynamically -->
                            </ul>
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

<!-- Delete Technician Modal -->
<div class="modal fade" id="deleteTechnicianModal" tabindex="-1" aria-labelledby="deleteTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTechnicianModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this technician? This action cannot be undone.</p>
                <p class="text-danger"><strong>Note:</strong> Technicians with active assignments cannot be deleted. You must reassign or complete all assignments first.</p>
                <input type="hidden" id="deleteTechnicianId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteTechnician">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Service Modal -->
<div class="modal fade" id="assignServiceModal" tabindex="-1" aria-labelledby="assignServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignServiceModalLabel">Assign Service Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignServiceForm">
                <div class="modal-body">
                    <input type="hidden" name="technicianId" id="assignTechnicianId">
                    
                    <div class="alert alert-info">
                        <strong>Assigning service request to:</strong> <span id="assignTechnicianName"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignBookingId" class="form-label">Select Service Request</label>
                        <select class="form-select" id="assignBookingId" name="bookingId" required>
                            <option value="">-- Select a Service Request --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignNotes" class="form-label">Assignment Notes</label>
                        <textarea class="form-control" id="assignNotes" name="notes" rows="3" placeholder="Add any special instructions or notes for the technician..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmAssignTechnician">Assign Service</button>
                </div>
            </form>
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
<script src="/assets/js/utility/DataTablesManager.js"></script>

<script>
// Initialize the DataTablesManager for Technician Management
document.addEventListener('DOMContentLoaded', function() {
    // Define columns for the technician table with badge support
    const columns = [
        {
            data: null,
            title: 'TECHNICIAN',
            render: function(data, type, row) {
                // For sorting and filtering, use the full name
                if (type === 'sort' || type === 'filter') {
                    return row.full_name;
                }
                
                // For display, create a custom HTML with avatar
                return `
                    <div class="d-flex align-items-center">
                        <img src="${row.profile_url}" class="technician-avatar me-3" alt="${row.full_name}">
                        <div>
                            <div class="fw-bold">${row.full_name}</div>
                            <div class="text-muted small">${row.email}</div>
                        </div>
                    </div>
                `;
            }
        },
        {
            data: 'is_available',
            title: 'AVAILABILITY',
            badge: {
                valueMap: {
                    true: { 
                        display: 'Available', 
                        type: 'success'
                    },
                    false: { 
                        display: 'Unavailable', 
                        type: 'danger'
                    }
                },
                pill: true
            }
        },
        {
            data: 'is_active',
            title: 'ACCOUNT STATUS',
            badge: {
                valueMap: {
                    true: { 
                        display: 'Active', 
                        type: 'success'
                    },
                    false: { 
                        display: 'Inactive', 
                        type: 'secondary'
                    }
                },
                pill: true
            }
        },
        { 
            data: 'address', 
            title: 'ADDRESS'
        },
        { 
            data: 'phone', 
            title: 'PHONE'
        }
    ];
    
    // Initialize the DataTablesManager
    const technicianManager = new DataTablesManager('technicianTable', {
        columns: columns,
        ajaxUrl: '/api/technician',
        viewRowCallback: viewTechnicianDetails,
        editRowCallback: editTechnician,
        deleteRowCallback: deleteTechnician,
        customButtons: {
            assignService: {
                text: 'Assign Service',
                className: 'btn-primary',
                action: function(e, dt, node, config) {
                    const selectedRows = technicianManager.getSelectedRows();
                    if (selectedRows.length === 1) {
                        openAssignServiceModal(selectedRows[0]);
                    } else {
                        technicianManager.showWarningToast(
                            'Selection Required', 
                            'Please select a single technician to assign a service.'
                        );
                    }
                }
            }
        }
    });
    
    // Function to view technician details
    function viewTechnicianDetails(rowData) {
        // Fetch detailed technician data including stats and assignments
        $.ajax({
            url: `/api/technician/${rowData.id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const technician = response.data;
                    
                    // Populate basic information
                    $('#viewTechnicianAvatar').attr('src', technician.profile_url);
                    $('#viewTechnicianName').text(technician.full_name);
                    $('#viewTechnicianEmail').text(technician.email);
                    $('#viewTechnicianPhone').text(technician.phone);
                    $('#viewTechnicianAddress').text(technician.address || 'No address provided');
                    
                    // Set availability badge
                    const availabilityBadge = $('#viewTechnicianAvailability');
                    if (technician.is_available) {
                        availabilityBadge.text('Available').removeClass('bg-danger').addClass('bg-success');
                    } else {
                        availabilityBadge.text('Unavailable').removeClass('bg-success').addClass('bg-danger');
                    }
                    
                    // Set account status badge
                    const statusBadge = $('#viewTechnicianAccountStatus');
                    if (technician.is_active) {
                        statusBadge.text('Active').removeClass('bg-secondary').addClass('bg-success');
                    } else {
                        statusBadge.text('Inactive').removeClass('bg-success').addClass('bg-secondary');
                    }
                    
                    // Populate stats
                    $('#viewTechnicianTotalAssignments').text(technician.stats.total_assignments || 0);
                    $('#viewTechnicianCompletedAssignments').text(technician.stats.completed_assignments || 0);
                    $('#viewTechnicianCurrentWorkload').text(technician.stats.current_workload || 0);
                    
                    // Calculate completion rate
                    const completionRate = technician.stats.total_assignments > 0 
                        ? Math.round((technician.stats.completed_assignments / technician.stats.total_assignments) * 100) 
                        : 0;
                    $('#viewTechnicianCompletionRate').text(completionRate + '%');
                    
                    // Populate current assignments
                    const assignmentsList = $('#viewTechnicianAssignments');
                    assignmentsList.empty();
                    
                    if (technician.assignments && technician.assignments.length > 0) {
                        technician.assignments.forEach(assignment => {
                            // Create status badge class based on status
                            let statusClass = '';
                            switch (assignment.status) {
                                case 'pending':
                                    statusClass = 'pending';
                                    break;
                                case 'in_progress':
                                    statusClass = 'in-progress';
                                    break;
                                case 'completed':
                                    statusClass = 'completed';
                                    break;
                                default:
                                    statusClass = 'pending';
                            }
                            
                            const item = `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">${assignment.service_type}</div>
                                        <div class="text-muted small">${assignment.customer_name} - ${assignment.requested_date} ${assignment.requested_time}</div>
                                        <div class="text-muted small">${assignment.address}</div>
                                    </div>
                                    <span class="status-badge ${statusClass}">${assignment.status}</span>
                                </li>
                            `;
                            assignmentsList.append(item);
                        });
                    } else {
                        assignmentsList.append('<li class="list-group-item">No current assignments</li>');
                    }
                    
                    // Show the modal
                    $('#viewTechnicianModal').modal('show');
                } else {
                    technicianManager.showErrorToast(
                        'Error', 
                        response.message || 'Failed to load technician details'
                    );
                }
            },
            error: function(xhr, status, error) {
                technicianManager.showErrorToast(
                    'Error', 
                    'Failed to load technician details: ' + error
                );
            }
        });
    }
    
    // Function to open the edit technician modal with data
    function editTechnician(rowData) {
        // Populate form fields
        $('#editTechnicianId').val(rowData.id);
        $('#editFirstName').val(rowData.first_name);
        $('#editLastName').val(rowData.last_name);
        $('#editEmail').val(rowData.email);
        $('#editPhone').val(rowData.phone);
        $('#editAddress').val(rowData.address);
        $('#editIsAvailable').prop('checked', rowData.is_available);
        $('#editIsActive').prop('checked', rowData.is_active);
        
        // Clear password field
        $('#editPassword').val('');
        
        // Show the modal
        $('#editTechnicianModal').modal('show');
    }
    
    // Function to handle deletion
    function deleteTechnician(rowData) {
        // Set technician ID to delete
        $('#deleteTechnicianId').val(rowData.id);
        
        // Show confirmation modal
        $('#deleteTechnicianModal').modal('show');
    }
    
    // Function to open assign service modal
    function openAssignServiceModal(technicianData) {
        // Set technician ID and name
        $('#assignTechnicianId').val(technicianData.id);
        $('#assignTechnicianName').text(technicianData.full_name);
        
        // Load pending service requests
        $.ajax({
            url: '/api/technician/pending-bookings',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const bookingSelect = $('#assignBookingId');
                    bookingSelect.empty();
                    bookingSelect.append('<option value="">-- Select a Service Request --</option>');
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(booking => {
                            // Create option with customer name, service type and priority
                            let priorityLabel = '';
                            switch (booking.priority) {
                                case 'urgent':
                                    priorityLabel = '[URGENT]';
                                    break;
                                case 'moderate':
                                    priorityLabel = '[MODERATE]';
                                    break;
                                default:
                                    priorityLabel = '';
                            }
                            
                            const option = `<option value="${booking.id}">
                                ${priorityLabel} ${booking.service_type} - ${booking.customer_name} (${booking.requested_date})
                            </option>`;
                            
                            bookingSelect.append(option);
                        });
                        
                        // Show the modal
                        $('#assignServiceModal').modal('show');
                    } else {
                        technicianManager.showInfoToast(
                            'No Pending Requests', 
                            'There are no pending service requests to assign.'
                        );
                    }
                } else {
                    technicianManager.showErrorToast(
                        'Error', 
                        response.message || 'Failed to load pending service requests'
                    );
                }
            },
            error: function(xhr, status, error) {
                technicianManager.showErrorToast(
                    'Error', 
                    'Failed to load pending service requests: ' + error
                );
            }
        });
    }
    
    // Form submission handlers
    
    // Add Technician Form
    $('#addTechnicianForm').on('submit', function(e) {
        e.preventDefault();
        
        // Gather form data
        const formData = {
            first_name: $('#firstName').val(),
            last_name: $('#lastName').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            phone: $('#phone').val(),
            address: $('#address').val(),
            is_available: $('#isAvailable').is(':checked'),
            is_active: $('#isActive').is(':checked')
        };
        
        // Submit via AJAX
        $.ajax({
            url: '/api/technician',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#addTechnicianModal').modal('hide');
                    
                    // Reset form
                    $('#addTechnicianForm')[0].reset();
                    
                    // Add to table and show success message
                    technicianManager.addRow(response.data);
                    technicianManager.showSuccessToast(
                        'Success', 
                        'Technician added successfully'
                    );
                } else {
                    technicianManager.showErrorToast(
                        'Error', 
                        response.message || 'Failed to add technician'
                    );
                }
            },
            error: function(xhr, status, error) {
                technicianManager.showErrorToast(
                    'Error', 
                    'Failed to add technician: ' + error
                );
            }
        });
    });
    
    // Edit Technician Form
    $('#editTechnicianForm').on('submit', function(e) {
        e.preventDefault();
        
        const technicianId = $('#editTechnicianId').val();
        
        // Gather form data
        const formData = {
            first_name: $('#editFirstName').val(),
            last_name: $('#editLastName').val(),
            email: $('#editEmail').val(),
            phone: $('#editPhone').val(),
            address: $('#editAddress').val(),
            is_available: $('#editIsAvailable').is(':checked'),
            is_active: $('#editIsActive').is(':checked')
        };
        
        // Only include password if provided
        const password = $('#editPassword').val();
        if (password) {
            formData.password = password;
        }
        
        // Submit via AJAX
        $.ajax({
            url: `/api/technician/${technicianId}`,
            method: 'POST', // Using POST with _method override for PUT
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#editTechnicianModal').modal('hide');
                    
                    // Update row in table and show success message
                    technicianManager.updateRow(technicianId, response.data);
                    technicianManager.showSuccessToast(
                        'Success', 
                        'Technician updated successfully'
                    );
                } else {
                    technicianManager.showErrorToast(
                        'Error', 
                        response.message || 'Failed to update technician'
                    );
                }
            },
            error: function(xhr, status, error) {
                technicianManager.showErrorToast(
                    'Error', 
                    'Failed to update technician: ' + error
                );
            }
        });
    });
    
    // Delete Technician Confirmation
    $('#confirmDeleteTechnician').on('click', function() {
        const technicianId = $('#deleteTechnicianId').val();
        
        // Submit delete request
        $.ajax({
            url: `/api/technician/${technicianId}`,
            method: 'POST', // Using POST with _method override for DELETE
            data: JSON.stringify({ _method: 'DELETE' }),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#deleteTechnicianModal').modal('hide');
                    
                    // Remove from table and show success message
                    technicianManager.deleteRow(technicianId);
                    technicianManager.showSuccessToast(
                        'Success', 
                        'Technician deleted successfully'
                    );
                } else {
                    // Close modal
                    $('#deleteTechnicianModal').modal('hide');
                    
                    technicianManager.showErrorToast(
                        'Error', 
                        response.message || 'Failed to delete technician'
                    );
                }
            },
            error: function(xhr, status, error) {
                // Close modal
                $('#deleteTechnicianModal').modal('hide');
                
                technicianManager.showErrorToast(
                    'Error', 
                    'Failed to delete technician: ' + error
                );
            }
        });
    });
    
    // Assign Service Form
    $('#assignServiceForm').on('submit', function(e) {
        e.preventDefault();
        
        // Gather form data
        const formData = {
            technician_id: $('#assignTechnicianId').val(),
            booking_id: $('#assignBookingId').val(),
            notes: $('#assignNotes').val()
        };
        
        // Submit via AJAX
        $.ajax({
            url: '/api/technician/assign',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#assignServiceModal').modal('hide');
                    
                    // Reset form
                    $('#assignServiceForm')[0].reset();
                    
                    // Show success message
                    technicianManager.showSuccessToast(
                        'Success', 
                        'Service request assigned successfully'
                    );
                } else {
                    technicianManager.showErrorToast(
                        'Error', 
                        response.message || 'Failed to assign service request'
                    );
                }
            },
            error: function(xhr, status, error) {
                technicianManager.showErrorToast(
                    'Error', 
                    'Failed to assign service request: ' + error
                );
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';