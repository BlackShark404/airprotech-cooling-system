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

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col d-flex justify-content-end">
            <button class="btn btn-red d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#newRequestModal">
            <i class="bi bi-plus me-2"></i>
            New Service Request
            </button>
        </div>
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
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Date</th>
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
                            <td>(555) 123-4567</td>
                            <td>Installation</td>
                            <td>20-Apr-2025</td>
                            <td>Mike Wilson</td>
                            <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                            <td><span class="badge badge-high rounded-pill px-3 py-2">High</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR002</td>
                            <td>Sarah Johnson</td>
                            <td>(555) 987-6543</td>
                            <td>Repair</td>
                            <td>22-Apr-2025</td>
                            <td>Unassigned</td>
                            <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                            <td><span class="badge badge-medium rounded-pill px-3 py-2">Medium</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-assign"><i class="bi bi-person-plus"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR003</td>
                            <td>David Brown</td>
                            <td>(555) 456-7890</td>
                            <td>Maintenance</td>
                            <td>19-Apr-2025</td>
                            <td>Tom Davis</td>
                            <td><span class="badge badge-completed rounded-pill px-3 py-2">Completed</span></td>
                            <td><span class="badge badge-low rounded-pill px-3 py-2">Low</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR004</td>
                            <td>Amanda Wilson</td>
                            <td>(555) 234-5678</td>
                            <td>Repair</td>
                            <td>23-Apr-2025</td>
                            <td>Lisa Chen</td>
                            <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                            <td><span class="badge badge-high rounded-pill px-3 py-2">High</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                            </td>
                        </tr>
                        <tr>
                            <td>SR005</td>
                            <td>Michael Andrews</td>
                            <td>(555) 876-5432</td>
                            <td>Installation</td>
                            <td>25-Apr-2025</td>
                            <td>Unassigned</td>
                            <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                            <td><span class="badge badge-medium rounded-pill px-3 py-2">Medium</span></td>
                            <td>
                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                <div class="action-icon action-icon-assign"><i class="bi bi-person-plus"></i></div>
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

<!-- New Service Request Modal -->
<div class="modal fade" id="newRequestModal" tabindex="-1" aria-labelledby="newRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newRequestModalLabel">Create New Service Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <!-- Customer Information -->
                        <div class="col-12">
                            <h6 class="text-muted fw-bold mb-3">Customer Information</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="customerName" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customerName" placeholder="Enter full name">
                        </div>
                        <div class="col-md-6">
                            <label for="customerPhone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="customerPhone" placeholder="(123) 456-7890">
                        </div>
                        <div class="col-md-6">
                            <label for="customerEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="customerEmail" placeholder="email@example.com">
                        </div>
                        <div class="col-md-6">
                            <label for="customerType" class="form-label">Customer Type</label>
                            <select class="form-select" id="customerType">
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>

                        <!-- Service Location -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-bold mb-3">Service Location</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="serviceAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="serviceAddress" placeholder="Street address">
                        </div>
                        <div class="col-md-6">
                            <label for="serviceUnit" class="form-label">Unit/Apartment #</label>
                            <input type="text" class="form-control" id="serviceUnit" placeholder="Apt/Unit number">
                        </div>
                        <div class="col-md-4">
                            <label for="serviceCity" class="form-label">City</label>
                            <input type="text" class="form-control" id="serviceCity">
                        </div>
                        <div class="col-md-4">
                            <label for="serviceState" class="form-label">State</label>
                            <select class="form-select" id="serviceState">
                                <option value="">Select State</option>
                                <option value="AL">Alabama</option>
                                <option value="AK">Alaska</option>
                                <option value="AZ">Arizona</option>
                                <!-- More states would go here -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="serviceZip" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" id="serviceZip">
                        </div>

                        <!-- Service Details -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-bold mb-3">Service Details</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="serviceType" class="form-label">Service Type</label>
                            <select class="form-select" id="serviceType">
                                <option value="installation">Installation</option>
                                <option value="repair">Repair</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inspection">Inspection</option>
                                <option value="emergency">Emergency Service</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="servicePriority" class="form-label">Priority</label>
                            <select class="form-select" id="servicePriority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="serviceDate" class="form-label">Preferred Date</label>
                            <input type="date" class="form-control" id="serviceDate">
                        </div>
                        <div class="col-md-6">
                            <label for="serviceTime" class="form-label">Preferred Time</label>
                            <select class="form-select" id="serviceTime">
                                <option value="morning">Morning (8AM - 12PM)</option>
                                <option value="afternoon">Afternoon (12PM - 4PM)</option>
                                <option value="evening">Evening (4PM - 8PM)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="unitType" class="form-label">AC Unit Type</label>
                            <select class="form-select" id="unitType">
                                <option value="central">Central AC</option>
                                <option value="split">Split System</option>
                                <option value="window">Window Unit</option>
                                <option value="portable">Portable AC</option>
                                <option value="heatpump">Heat Pump</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="unitModel" class="form-label">Model/Make (if known)</label>
                            <input type="text" class="form-control" id="unitModel" placeholder="e.g. Carrier, Trane, etc.">
                        </div>
                        <div class="col-12">
                            <label for="serviceDescription" class="form-label">Description of Issue/Request</label>
                            <textarea class="form-control" id="serviceDescription" rows="3" placeholder="Please describe the service needed or issue experienced"></textarea>
                        </div>

                        <!-- Technician Assignment -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-bold mb-3">Technician Assignment</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="technicianAssignment" class="form-label">Assign Technician</label>
                            <select class="form-select" id="technicianAssignment">
                                <option value="">Unassigned</option>
                                <option value="tech1">Mike Wilson</option>
                                <option value="tech2">Tom Davis</option>
                                <option value="tech3">Lisa Chen</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="estimatedHours" class="form-label">Estimated Hours</label>
                            <input type="number" class="form-control" id="estimatedHours" placeholder="Enter estimated hours">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Create Request</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional scripts specific to this page
$additionalScripts = <<<HTML
<script>
    // Initialize any JavaScript functionality for the service requests page
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Add confirmation dialog for delete actions
        const deleteButtons = document.querySelectorAll('.action-icon-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this service request?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Example: Add assignment modal functionality
        const assignButtons = document.querySelectorAll('.action-icon-assign');
        assignButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Code to show assignment modal would go here
                alert('Assign technician modal would appear here');
            });
        });
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>