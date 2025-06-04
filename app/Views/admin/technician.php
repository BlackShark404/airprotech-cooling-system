<?php
$title = 'Technician Management - AirProtech';
$activeTab = 'technician';

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
    .badge-available {
        background-color: #198754;
        color: #fff;
    }
    .badge-unavailable {
        background-color: #dc3545;
        color: #fff;
    }
    .badge-assigned {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-in-progress {
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
    
    /* Technician profile card */
    .technician-card {
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .technician-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .technician-header {
        padding: 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
    }
    .technician-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-right: 15px;
    }
    .technician-details {
        flex: 1;
    }
    .technician-name {
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 3px;
    }
    .technician-contact {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    .technician-status {
        display: inline-block;
        margin-top: 5px;
    }
    .technician-body {
        padding: 20px;
    }
    .technician-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .stat-item {
        text-align: center;
        padding: 10px;
        flex: 1;
        border-right: 1px solid #dee2e6;
    }
    .stat-item:last-child {
        border-right: none;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #007bff;
    }
    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .technician-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    
    /* Assignment tab styles */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 15px;
    }
    .nav-tabs .nav-link {
        margin-bottom: -1px;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.5rem 1rem;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .tab-content {
        padding: 15px 0;
    }
    .assignment-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .assignment-item {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s ease;
    }
    .assignment-item:hover {
        background-color: #f8f9fa;
    }
    .assignment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    .assignment-title {
        font-weight: 600;
    }
    .assignment-date {
        color: #6c757d;
        font-size: 0.85rem;
    }
    .assignment-details {
        margin-bottom: 10px;
        color: #495057;
    }
    .assignment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .assignment-customer {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .assignment-actions button {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
HTML;

// Start output buffering for content
ob_start();

?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Technician Management</h2>
                <div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
                        <i class="fas fa-plus me-1"></i> Add Technician
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Technician Listing -->
    <div class="row" id="techniciansList">
        <?php if (isset($technicians) && !empty($technicians)): ?>
            <?php foreach ($technicians as $technician): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="technician-card">
                        <div class="technician-header">
                            <img src="<?= htmlspecialchars($technician['ua_profile_url'] ?? '/assets/images/default-profile.jpg') ?>" 
                                alt="<?= htmlspecialchars($technician['ua_first_name']) ?>" 
                                class="technician-avatar">
                            <div class="technician-details">
                                <div class="technician-name">
                                    <?= htmlspecialchars($technician['ua_first_name'] . ' ' . $technician['ua_last_name']) ?>
                                </div>
                                <div class="technician-contact">
                                    <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($technician['ua_email']) ?><br>
                                    <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($technician['ua_phone_number'] ?? 'Not provided') ?>
                                </div>
                                <div class="technician-status">
                                    <?php if ($technician['te_is_available']): ?>
                                        <span class="badge badge-available">Available</span>
                                    <?php else: ?>
                                        <span class="badge badge-unavailable">Unavailable</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="technician-body">
                            <div class="technician-stats">
                                <div class="stat-item">
                                    <div class="stat-value" id="serviceCount-<?= $technician['te_account_id'] ?>">-</div>
                                    <div class="stat-label">Service Assignments</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="productCount-<?= $technician['te_account_id'] ?>">-</div>
                                    <div class="stat-label">Product Assignments</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="completedCount-<?= $technician['te_account_id'] ?>">-</div>
                                    <div class="stat-label">Completed Tasks</div>
                                </div>
                            </div>
                            <div class="technician-actions">
                                <button class="btn btn-primary btn-sm" onclick="viewTechnicianDetails(<?= $technician['te_account_id'] ?>)">
                                    <i class="fas fa-tasks me-1"></i> View Assignments
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="toggleTechnicianAvailability(<?= $technician['te_account_id'] ?>, <?= $technician['te_is_available'] ? 'false' : 'true' ?>)">
                                    <?php if ($technician['te_is_available']): ?>
                                        <i class="fas fa-user-slash me-1"></i> Set Unavailable
                                    <?php else: ?>
                                        <i class="fas fa-user-check me-1"></i> Set Available
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No technicians found. Add technicians to manage service assignments.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Technician Details Modal -->
<div class="modal fade" id="technicianDetailsModal" tabindex="-1" aria-labelledby="technicianDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="technicianDetailsModalLabel">Technician Assignments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="technicianModalContent">
                    <div class="technician-profile mb-4">
                        <div class="d-flex align-items-center">
                            <img id="technicianModalAvatar" src="/assets/images/default-profile.jpg" alt="Technician" class="technician-avatar">
                            <div class="ms-3">
                                <h4 id="technicianModalName">Technician Name</h4>
                                <p id="technicianModalContact" class="text-muted mb-0">Email / Phone</p>
                                <span id="technicianModalStatus" class="badge badge-available mt-2">Available</span>
                            </div>
                        </div>
                    </div>
                    
                    <ul class="nav nav-tabs" id="assignmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="service-tab" data-bs-toggle="tab" data-bs-target="#service-assignments" type="button" role="tab" aria-controls="service-assignments" aria-selected="true">
                                Service Assignments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="product-tab" data-bs-toggle="tab" data-bs-target="#product-assignments" type="button" role="tab" aria-controls="product-assignments" aria-selected="false">
                                Product Assignments
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="assignmentTabsContent">
                        <div class="tab-pane fade show active" id="service-assignments" role="tabpanel" aria-labelledby="service-tab">
                            <div class="assignment-list" id="serviceAssignmentsList">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading service assignments...</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="product-assignments" role="tabpanel" aria-labelledby="product-tab">
                            <div class="assignment-list" id="productAssignmentsList">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading product assignments...</p>
                                </div>
                            </div>
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

<!-- Add Technician Modal -->
<div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTechnicianModalLabel">Add New Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTechnicianForm">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitAddTechnician">Add Technician</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Global variables
    let currentTechnicianId = null;
    
    // Initialize the page
    $(document).ready(function() {
        // Load initial stats for all technicians
        loadAllTechnicianStats();
        
        // Add event listeners
        $('#submitAddTechnician').on('click', handleAddTechnician);
    });
    
    // Load stats for all technicians
    function loadAllTechnicianStats() {
        <?php if (isset($technicians) && !empty($technicians)): ?>
            <?php foreach ($technicians as $technician): ?>
                loadTechnicianStats(<?= $technician['te_account_id'] ?>);
            <?php endforeach; ?>
        <?php endif; ?>
    }
    
    // Load stats for a specific technician
    function loadTechnicianStats(technicianId) {
        $.ajax({
            url: `/api/admin/technicians/${technicianId}/assignments`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const serviceCount = response.data.serviceAssignments ? response.data.serviceAssignments.length : 0;
                    const productCount = response.data.productAssignments ? response.data.productAssignments.length : 0;
                    
                    // Count completed assignments
                    let completedCount = 0;
                    if (response.data.serviceAssignments) {
                        completedCount += response.data.serviceAssignments.filter(a => a.ba_status === 'completed').length;
                    }
                    if (response.data.productAssignments) {
                        completedCount += response.data.productAssignments.filter(a => a.pa_status === 'completed').length;
                    }
                    
                    // Update the stats in the UI
                    $(`#serviceCount-${technicianId}`).text(serviceCount);
                    $(`#productCount-${technicianId}`).text(productCount);
                    $(`#completedCount-${technicianId}`).text(completedCount);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading technician stats:', error);
            }
        });
    }
    
    // View technician details
    function viewTechnicianDetails(technicianId) {
        currentTechnicianId = technicianId;
        
        // Find the technician data
        <?php if (isset($technicians) && !empty($technicians)): ?>
        const technicians = <?= json_encode($technicians) ?>;
        const technician = technicians.find(t => t.te_account_id == technicianId);
        
        if (technician) {
            // Update modal with technician info
            $('#technicianModalName').text(technician.ua_first_name + ' ' + technician.ua_last_name);
            $('#technicianModalContact').text(technician.ua_email + (technician.ua_phone_number ? ' / ' + technician.ua_phone_number : ''));
            $('#technicianModalAvatar').attr('src', technician.ua_profile_url || '/assets/images/default-profile.jpg');
            
            if (technician.te_is_available) {
                $('#technicianModalStatus').removeClass('badge-unavailable').addClass('badge-available').text('Available');
            } else {
                $('#technicianModalStatus').removeClass('badge-available').addClass('badge-unavailable').text('Unavailable');
            }
        }
        <?php endif; ?>
        
        // Clear previous assignments
        $('#serviceAssignmentsList, #productAssignmentsList').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading assignments...</p>
            </div>
        `);
        
        // Show the modal
        $('#technicianDetailsModal').modal('show');
        
        // Load assignments data
        loadTechnicianAssignments(technicianId);
    }
    
    // Load technician assignments
    function loadTechnicianAssignments(technicianId) {
        $.ajax({
            url: `/api/admin/technicians/${technicianId}/assignments`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    renderServiceAssignments(response.data.serviceAssignments || []);
                    renderProductAssignments(response.data.productAssignments || []);
                } else {
                    $('#serviceAssignmentsList, #productAssignmentsList').html(`
                        <div class="alert alert-danger">
                            Failed to load assignments: ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#serviceAssignmentsList, #productAssignmentsList').html(`
                    <div class="alert alert-danger">
                        Error loading assignments. Please try again.
                    </div>
                `);
                console.error('Error loading assignments:', error);
            }
        });
    }
    
    // Render service assignments
    function renderServiceAssignments(assignments) {
        if (assignments.length === 0) {
            $('#serviceAssignmentsList').html(`
                <div class="alert alert-info">
                    No service assignments found for this technician.
                </div>
            `);
            return;
        }
        
        let html = '';
        assignments.forEach(assignment => {
            const statusBadge = getStatusBadge(assignment.ba_status);
            const date = new Date(assignment.sb_preferred_date + ' ' + assignment.sb_preferred_time);
            
            html += `
                <div class="assignment-item">
                    <div class="assignment-header">
                        <div class="assignment-title">${assignment.service_type_name}</div>
                        <div class="assignment-date">${formatDate(date)}</div>
                    </div>
                    <div class="assignment-details">
                        <strong>Address:</strong> ${assignment.sb_address}<br>
                        <strong>Description:</strong> ${assignment.sb_description}
                    </div>
                    <div class="assignment-footer">
                        <div class="assignment-customer">
                            <strong>Customer:</strong> ${assignment.customer_name}
                        </div>
                        <div class="assignment-actions">
                            <span class="me-2">${statusBadge}</span>
                            <button class="btn btn-sm btn-outline-primary" onclick="updateAssignmentStatus('service', ${assignment.ba_id})">
                                Update Status
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#serviceAssignmentsList').html(html);
    }
    
    // Render product assignments
    function renderProductAssignments(assignments) {
        if (assignments.length === 0) {
            $('#productAssignmentsList').html(`
                <div class="alert alert-info">
                    No product assignments found for this technician.
                </div>
            `);
            return;
        }
        
        let html = '';
        assignments.forEach(assignment => {
            const statusBadge = getStatusBadge(assignment.pa_status);
            const date = new Date(assignment.pb_preferred_date + ' ' + assignment.pb_preferred_time);
            
            html += `
                <div class="assignment-item">
                    <div class="assignment-header">
                        <div class="assignment-title">${assignment.prod_name} (${assignment.var_capacity})</div>
                        <div class="assignment-date">${formatDate(date)}</div>
                    </div>
                    <div class="assignment-details">
                        <strong>Quantity:</strong> ${assignment.pb_quantity}<br>
                        <strong>Address:</strong> ${assignment.pb_address}<br>
                        <strong>Description:</strong> ${assignment.pb_description || 'No description provided'}
                    </div>
                    <div class="assignment-footer">
                        <div class="assignment-customer">
                            <strong>Customer:</strong> ${assignment.customer_name}
                        </div>
                        <div class="assignment-actions">
                            <span class="me-2">${statusBadge}</span>
                            <button class="btn btn-sm btn-outline-primary" onclick="updateAssignmentStatus('product', ${assignment.pa_id})">
                                Update Status
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#productAssignmentsList').html(html);
    }
    
    // Get status badge HTML
    function getStatusBadge(status) {
        let badgeClass = '';
        let statusText = status.charAt(0).toUpperCase() + status.slice(1);
        
        switch (status) {
            case 'assigned':
                badgeClass = 'badge-assigned';
                break;
            case 'in-progress':
                badgeClass = 'badge-in-progress';
                statusText = 'In Progress';
                break;
            case 'completed':
                badgeClass = 'badge-completed';
                break;
            case 'cancelled':
                badgeClass = 'badge-cancelled';
                break;
            default:
                badgeClass = 'badge-secondary';
        }
        
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }
    
    // Format date
    function formatDate(date) {
        return date.toLocaleDateString('en-US', { 
            weekday: 'short',
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Update assignment status
    function updateAssignmentStatus(type, assignmentId) {
        // Create modal with assignment status options
        let statusUpdateModal = `
            <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Assignment Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateStatusForm">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="assigned">Assigned</option>
                                        <option value="in-progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="submitStatusUpdate">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add modal to body if it doesn't exist
        if (!$('#updateStatusModal').length) {
            $('body').append(statusUpdateModal);
        }
        
        // Show the modal
        const statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        statusModal.show();
        
        // Handle form submission
        $('#submitStatusUpdate').off('click').on('click', function() {
            const status = $('#status').val();
            const notes = $('#notes').val();
            
            // Determine API endpoint based on assignment type
            const endpoint = type === 'service' 
                ? '/api/technicians/service-assignment/update'
                : '/api/technicians/product-assignment/update';
            
            // Send AJAX request
            $.ajax({
                url: endpoint,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    assignment_id: assignmentId,
                    status: status,
                    notes: notes
                }),
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        statusModal.hide();
                        
                        // Reload assignments
                        loadTechnicianAssignments(currentTechnicianId);
                        
                        // Reload stats
                        loadTechnicianStats(currentTechnicianId);
                        
                        // Show success message
                        alert('Assignment status updated successfully');
                    } else {
                        alert('Failed to update assignment status: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error updating assignment status: ' + error);
                }
            });
        });
    }
    
    // Toggle technician availability
    function toggleTechnicianAvailability(technicianId, newStatus) {
        if (!confirm(`Are you sure you want to set this technician as ${newStatus ? 'Available' : 'Unavailable'}?`)) {
            return;
        }
        
        // Implement later with API endpoint
        alert('This feature is not implemented yet.');
        
        // Reload the page to reflect changes after implementing
        // location.reload();
    }
    
    // Handle adding a new technician
    function handleAddTechnician() {
        const firstName = $('#firstName').val();
        const lastName = $('#lastName').val();
        const email = $('#email').val();
        const phone = $('#phone').val();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        
        // Validate form
        if (!firstName || !lastName || !email || !password) {
            alert('Please fill in all required fields');
            return;
        }
        
        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }
        
        // Implement later with API endpoint
        alert('This feature is not implemented yet.');
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addTechnicianModal'));
        modal.hide();
        
        // Reload the page to reflect changes after implementing
        // location.reload();
    }
</script>


<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>