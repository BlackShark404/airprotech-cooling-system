<?php
$title = 'Technicians - AC Service Pro';
$activeTab = 'technicians';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-available {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .status-busy {
        background-color: #fff0e5;
        color: #ff9500;
    }
    .status-pending {
        background-color: #fff8e6;
        color: #ffbb00;
    }
    .status-completed {
        background-color: #e6f7e9;
        color: #34c759;
    }
    .status-progress {
        background-color: #e7f1ff;
        color: #007bff;
    }
    .tech-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
    }
    .tech-stats-card {
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .tech-stats-icon {
        font-size: 24px;
        margin-bottom: 10px;
    }
    .tech-stats-value {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .tech-stats-label {
        font-size: 14px;
        color: #6c757d;
    }
    .skill-badge {
        background-color: #e7f1ff;
        color: #007bff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-right: 5px;
        margin-bottom: 5px;
        display: inline-block;
    }
    .assign-btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 14px;
    }
    .assign-btn:hover {
        background-color: #0069d9;
    }
    .filter-section {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .filter-label {
        font-weight: 500;
        margin-bottom: 8px;
    }
    .section-heading {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Technician Stats -->
    <div class="row g-3">
        <div class="col-md-3">
            <div class="tech-stats-card">
                <div class="tech-stats-icon text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div class="tech-stats-value">24</div>
                <div class="tech-stats-label">Total Technicians</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tech-stats-card">
                <div class="tech-stats-icon" style="color: #34c759;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="tech-stats-value">12</div>
                <div class="tech-stats-label">Available Now</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tech-stats-card">
                <div class="tech-stats-icon" style="color: #ff9500;">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="tech-stats-value">8</div>
                <div class="tech-stats-label">On Assignment</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="tech-stats-card">
                <div class="tech-stats-icon" style="color: #8e8e93;">
                    <i class="bi bi-moon"></i>
                </div>
                <div class="tech-stats-value">4</div>
                <div class="tech-stats-label">Off Duty</div>
            </div>
        </div>
    </div>

    <!-- Add Technician Button -->
    <div class="mt-4">
        <button class="btn btn-blue">
            <i class="bi bi-person-plus me-2"></i>Add New Technician
        </button>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <!-- Technician List -->
        <div class="col-lg-8">
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
                                            <img src="/api/placeholder/48/48" alt="Mike Wilson" class="tech-avatar me-3">
                                            <div>
                                                <h6 class="mb-0">Mike Wilson</h6>
                                                <small class="text-muted">TECH001</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="status-badge status-available">Available</span></td>
                                    <td>Downtown</td>
                                    <td>
                                        <span class="skill-badge">HVAC</span>
                                        <span class="skill-badge">Installation</span>
                                        <span class="skill-badge">Repair</span>
                                    </td>
                                    <td>9 AM - 5 PM</td>
                                    <td><button class="assign-btn">Assign Job</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/api/placeholder/48/48" alt="Tom Davis" class="tech-avatar me-3">
                                            <div>
                                                <h6 class="mb-0">Tom Davis</h6>
                                                <small class="text-muted">TECH002</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="status-badge status-busy">Busy</span></td>
                                    <td>North Side</td>
                                    <td>
                                        <span class="skill-badge">Maintenance</span>
                                        <span class="skill-badge">Emergency</span>
                                    </td>
                                    <td>10 AM - 6 PM</td>
                                    <td><button class="assign-btn">Assign Job</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/api/placeholder/48/48" alt="Lisa Chen" class="tech-avatar me-3">
                                            <div>
                                                <h6 class="mb-0">Lisa Chen</h6>
                                                <small class="text-muted">TECH003</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="status-badge status-available">Available</span></td>
                                    <td>West End</td>
                                    <td>
                                        <span class="skill-badge">Installation</span>
                                        <span class="skill-badge">HVAC</span>
                                    </td>
                                    <td>8 AM - 4 PM</td>
                                    <td><button class="assign-btn">Assign Job</button></td>
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
                    <a href="#" class="view-all">View all <i class="bi bi-chevron-right"></i></a>
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
                                    <td><span class="status-badge status-progress">In Progress</span></td>
                                    <td>123 Main St</td>
                                </tr>
                                <tr>
                                    <td>SR002</td>
                                    <td>Tom Davis</td>
                                    <td>2024-01-19</td>
                                    <td><span class="status-badge status-completed">Completed</span></td>
                                    <td>456 Oak Ave</td>
                                </tr>
                                <tr>
                                    <td>SR003</td>
                                    <td>Lisa Chen</td>
                                    <td>2024-01-19</td>
                                    <td><span class="status-badge status-pending">Pending</span></td>
                                    <td>789 Pine Rd</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="filter-section">
                <h5 class="section-heading">Quick Filters</h5>
                
                <!-- Availability Status Filter -->
                <div class="mb-3">
                    <label class="filter-label">Availability Status</label>
                    <select class="form-select">
                        <option>All Statuses</option>
                        <option>Available</option>
                        <option>Busy</option>
                        <option>Off Duty</option>
                    </select>
                </div>
                
                <!-- Skills Filter -->
                <div class="mb-3">
                    <label class="filter-label">Skills</label>
                    <select class="form-select">
                        <option>All Skills</option>
                        <option>HVAC</option>
                        <option>Installation</option>
                        <option>Repair</option>
                        <option>Maintenance</option>
                        <option>Emergency</option>
                    </select>
                </div>
                
                <!-- Location Filter -->
                <div class="mb-3">
                    <label class="filter-label">Location</label>
                    <select class="form-select">
                        <option>All Locations</option>
                        <option>Downtown</option>
                        <option>North Side</option>
                        <option>West End</option>
                        <option>East Side</option>
                        <option>South District</option>
                    </select>
                </div>
                
                <!-- Experience Level Filter -->
                <div class="mb-3">
                    <label class="filter-label">Experience Level</label>
                    <select class="form-select">
                        <option>All Levels</option>
                        <option>Junior</option>
                        <option>Mid-level</option>
                        <option>Senior</option>
                        <option>Expert</option>
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
    // Any technician page specific JavaScript would go here
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Add event listener for assign job buttons
        const assignButtons = document.querySelectorAll('.assign-btn');
        assignButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Would normally open a modal or redirect to assignment page
                alert('Assignment functionality would open here');
            });
        });
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>