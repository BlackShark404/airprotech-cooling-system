<?php
<<<<<<< HEAD
$title = 'Technician Management - AirProtech';
=======
$title = 'Technicians - AC Service Pro';
>>>>>>> parent of 43c6deb (remove the technician page ui)
$activeTab = 'technician';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
<<<<<<< HEAD
=======
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
>>>>>>> parent of 43c6deb (remove the technician page ui)
    .filter-card {
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
<<<<<<< HEAD
        margin-bottom: 20px;
    }
    .filter-dropdown {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 1rem;
        width: 100%;
=======
        padding: 15px;
        margin-bottom: 20px;
    }
    .action-icons {
        display: flex;
        gap: 10px;
>>>>>>> parent of 43c6deb (remove the technician page ui)
    }
    .action-icon {
        width: 32px;
        height: 32px;
<<<<<<< HEAD
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
=======
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

>>>>>>> parent of 43c6deb (remove the technician page ui)
HTML;

// Start output buffering for content
ob_start();
<<<<<<< HEAD

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
=======
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
>>>>>>> parent of 43c6deb (remove the technician page ui)
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
    
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
=======
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
>>>>>>> parent of 43c6deb (remove the technician page ui)
                            </div>
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
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
=======
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
>>>>>>> parent of 43c6deb (remove the technician page ui)
                            </div>
                        </div>
                    </div>
                    
<<<<<<< HEAD
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
=======
                    <!-- Current Assignments -->
                    <div class="col-md-8">
                        <div class="detail-card">
                            <h5 class="mb-3">Current Assignments</h5>
                            <ul class="list-group" id="viewTechnicianAssignments">
                                <!-- Will be populated dynamically -->
                            </ul>
>>>>>>> parent of 43c6deb (remove the technician page ui)
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

<<<<<<< HEAD
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
=======
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
>>>>>>> parent of 43c6deb (remove the technician page ui)
            </div>
        </div>
    </div>
</div>

<<<<<<< HEAD

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


=======
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

>>>>>>> parent of 43c6deb (remove the technician page ui)
<?php
$content = ob_get_clean();

// Include the base template
<<<<<<< HEAD
include __DIR__ . '/../includes/admin/base.php';
?>
=======
include __DIR__ . '/../includes/admin/base.php';
>>>>>>> parent of 43c6deb (remove the technician page ui)
