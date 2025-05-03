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
    .pagination-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 1rem;
    }
    .pagination-button {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        cursor: pointer;
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    .pagination-button.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .pagination-button:hover:not(.active) {
        background-color: #f8f9fa;
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

    <!-- Service Requests Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Service Requests</h5>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <select class="form-select form-select-sm">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Quick search...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
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
                        <tr>
                            <td>SR001</td>
                            <td>John Smith</td>
                            <td>Installation</td>
                            <td>20-Apr-2025</td>
                            <td>09:00 AM</td>
                            <td>Mike Wilson</td>
                            <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                            <td><span class="badge badge-high rounded-pill px-3 py-2">Urgent</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR002</td>
                            <td>Sarah Johnson</td>
                            <td>Repair</td>
                            <td>22-Apr-2025</td>
                            <td>11:30 AM</td>
                            <td>Unassigned</td>
                            <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                            <td><span class="badge badge-medium rounded-pill px-3 py-2">Moderate</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>

                            </td>
                        </tr>
                        <tr>
                            <td>SR003</td>
                            <td>David Brown</td>
                            <td>Maintenance</td>
                            <td>19-Apr-2025</td>
                            <td>02:00 PM</td>
                            <td>Tom Davis</td>
                            <td><span class="badge badge-completed rounded-pill px-3 py-2">Completed</span></td>
                            <td><span class="badge badge-low rounded-pill px-3 py-2">Normal</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR004</td>
                            <td>Amanda Wilson</td>
                            <td>Repair</td>
                            <td>23-Apr-2025</td>
                            <td>10:15 AM</td>
                            <td>Lisa Chen</td>
                            <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                            <td><span class="badge badge-high rounded-pill px-3 py-2">Urgent</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR005</td>
                            <td>Michael Andrews</td>
                            <td>Installation</td>
                            <td>25-Apr-2025</td>
                            <td>01:00 PM</td>
                            <td>Unassigned</td>
                            <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                            <td><span class="badge badge-medium rounded-pill px-3 py-2">Moderate</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>

                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    Showing 1 to 5 of 156 entries
                </div>
                <div class="pagination-container">
                    <div class="pagination-button"><i class="bi bi-chevron-left"></i></div>
                    <div class="pagination-button active">1</div>
                    <div class="pagination-button">2</div>
                    <div class="pagination-button">3</div>
                    <div class="pagination-button">...</div>
                    <div class="pagination-button">32</div>
                    <div class="pagination-button"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>