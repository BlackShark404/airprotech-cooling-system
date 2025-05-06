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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>TECHNICIAN</th>
                                    <th>STATUS</th>
                                    <th>Current Address</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatars/mike-wilson.jpg" class="technician-avatar me-3" alt="Mike Wilson">
                                            <div>
                                                <div class="fw-bold">Mike Wilson</div>
                                                <div class="text-muted small">mike.wilson@example.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="technician-status technician-status-active">Active</span></td>
                                    <td>123 Main St, Downtown</td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="#" class="action-icon view-icon" data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="#" class="action-icon edit-icon" data-bs-toggle="tooltip" title="Edit Technician">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="action-icon delete-icon" data-bs-toggle="tooltip" title="Delete Technician">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatars/tom-davis.jpg" class="technician-avatar me-3" alt="Tom Davis">
                                            <div>
                                                <div class="fw-bold">Tom Davis</div>
                                                <div class="text-muted small">tom.davis@example.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="technician-status technician-status-active">Active</span></td>

                                    <td>456 Oak Ave, North Side</td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="#" class="action-icon view-icon" data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="#" class="action-icon edit-icon" data-bs-toggle="tooltip" title="Edit Technician">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="action-icon delete-icon" data-bs-toggle="tooltip" title="Delete Technician">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatars/james-rodriguez.jpg" class="technician-avatar me-3" alt="James Rodriguez">
                                            <div>
                                                <div class="fw-bold">James Rodriguez</div>
                                                <div class="text-muted small">james.rodriguez@example.com</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="technician-status technician-status-active">Active</span></td>

                                    <td>567 Elm St, East Side</td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="#" class="action-icon view-icon" data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="#" class="action-icon edit-icon" data-bs-toggle="tooltip" title="Edit Technician">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="action-icon delete-icon" data-bs-toggle="tooltip" title="Delete Technician">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';