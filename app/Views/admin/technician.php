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
    .technician-status-available {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .technician-status-busy {
        background-color: #fff8e6;
        color: #ffbb00;
    }
    .technician-status-offline {
        background-color: #f1f3f5;
        color: #6c757d;
    }
    .skill-badge {
        background-color: #e7f1ff;
        color: #007bff;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
        margin-right: 4px;
        margin-bottom: 4px;
        display: inline-block;
    }
    .stats-card {
        border-radius: 12px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
    }
    .stats-icon {
        font-size: 24px;
        padding: 12px;
        border-radius: 50%;
        position: absolute;
        top: 20px;
        right: 20px;
    }
    .stats-icon-blue {
        background-color: #e7f1ff;
        color: #007bff;
    }
    .stats-icon-green {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .stats-icon-orange {
        background-color: #fff8e6;
        color: #ffbb00;
    }
    .stats-icon-purple {
        background-color: #f4e7ff;
        color: #7b68ee;
    }
    .filter-dropdown {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 1rem;
        width: 100%;
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
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Technician Management</h1>
        <p class="text-muted">Manage technicians</p>
    </div>

    <!-- Action Button -->
    <div class="row mb-4">
        <div class="col col-12 d-flex justify-content-end">
            <button class="btn btn-blue d-flex align-items-center">
                <i class="bi bi-person-plus me-2"></i>
                Add New Technician
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row">
        <!-- Technician List -->
        <div class="col-lg-9 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Technicians</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>TECHNICIAN</th>
                                    <th>STATUS</th>
                                    <th>LOCATION</th>
                                    <th>SKILLS</th>
                                    <th>AVAILABILITY</th>
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
                                                <div class="text-muted small">TECH001</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="technician-status technician-status-available">Available</span></td>
                                    <td>Downtown</td>
                                    <td>
                                        <span class="skill-badge">HVAC</span>
                                        <span class="skill-badge">Installation</span>
                                        <span class="skill-badge">Repair</span>
                                    </td>
                                    <td>9 AM - 5 PM</td>
                                    <td><a href="#" class="assign-job">Assign Job</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatars/tom-davis.jpg" class="technician-avatar me-3" alt="Tom Davis">
                                            <div>
                                                <div class="fw-bold">Tom Davis</div>
                                                <div class="text-muted small">TECH002</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="technician-status technician-status-busy">Busy</span></td>
                                    <td>North Side</td>
                                    <td>
                                        <span class="skill-badge">Maintenance</span>
                                        <span class="skill-badge">Emergency</span>
                                    </td>
                                    <td>10 AM - 6 PM</td>
                                    <td><a href="#" class="assign-job">Assign Job</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatars/lisa-chen.jpg" class="technician-avatar me-3" alt="Lisa Chen">
                                            <div>
                                                <div class="fw-bold">Lisa Chen</div>
                                                <div class="text-muted small">TECH003</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="technician-status technician-status-available">Available</span></td>
                                    <td>West End</td>
                                    <td>
                                        <span class="skill-badge">Installation</span>
                                        <span class="skill-badge">HVAC</span>
                                    </td>
                                    <td>8 AM - 4 PM</td>
                                    <td><a href="#" class="assign-job">Assign Job</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recent Assignments -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Assignments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TECHNICIAN</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                    <th>LOCATION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SR001</td>
                                    <td>Mike Wilson</td>
                                    <td>2024-01-20</td>
                                    <td><span class="status-badge in-progress">In Progress</span></td>
                                    <td>123 Main St</td>
                                </tr>
                                <tr>
                                    <td>SR002</td>
                                    <td>Tom Davis</td>
                                    <td>2024-01-19</td>
                                    <td><span class="status-badge completed">Completed</span></td>
                                    <td>456 Oak Ave</td>
                                </tr>
                                <tr>
                                    <td>SR003</td>
                                    <td>Lisa Chen</td>
                                    <td>2024-01-19</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>789 Pine Rd</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="filter-card">
                <h6 class="mb-3">Quick Filters</h6>
                
                <div class="mb-3">
                    <label class="form-label">Availability Status</label>
                    <select class="form-select filter-dropdown">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="busy">Busy</option>
                        <option value="offline">Off Duty</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Skills</label>
                    <select class="form-select filter-dropdown">
                        <option value="">All Skills</option>
                        <option value="hvac">HVAC</option>
                        <option value="installation">Installation</option>
                        <option value="repair">Repair</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <select class="form-select filter-dropdown">
                        <option value="">All Locations</option>
                        <option value="downtown">Downtown</option>
                        <option value="north">North Side</option>
                        <option value="west">West End</option>
                        <option value="east">East Side</option>
                        <option value="south">South Area</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Experience Level</label>
                    <select class="form-select filter-dropdown">
                        <option value="">All Levels</option>
                        <option value="junior">Junior</option>
                        <option value="mid">Mid-Level</option>
                        <option value="senior">Senior</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional scripts specific to this page
$additionalScripts = <<<HTML
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Example functionality for the assign job buttons
        const assignButtons = document.querySelectorAll('.assign-job');
        assignButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // In a real app, this could open a modal or redirect to an assignment page
                alert('Assign job functionality would go here');
            });
        });
        
        // Filter functionality
        document.querySelector('.btn-blue.w-100').addEventListener('click', function() {
            // In a real app, this would filter the technician list
            alert('Filter functionality would be implemented here');
        });
        
        // Reset filters
        document.querySelector('.btn-outline-secondary.w-100').addEventListener('click', function() {
            const selects = document.querySelectorAll('.filter-dropdown');
            selects.forEach(select => {
                select.value = '';
            });
        });
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>