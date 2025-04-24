<?php

// Set page specific variables
$pageTitle = 'Admin Dashboard';
$pageDescription = 'Air-Protech Administrator Dashboard';
$pageHeader = 'Admin Dashboard';
$pageSubheader = 'System Overview and Management';

// Set user type
$userType = 'admin';

// Additional page-specific styles
$pageStyles = '
    .stats-card {
        border-left: 4px solid var(--secondary);
    }
    .urgent-service {
        border-left: 4px solid var(--danger);
    }
    .pending-approval {
        border-left: 4px solid var(--warning);
    }
    .service-item:hover {
        background-color: rgba(229, 57, 53, 0.1);
    }
    .admin-quick-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
';

// Start output buffering to capture content
ob_start();
?>

<div class="row">
    <!-- Dashboard Stats -->
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Active Services</h5>
                    <i class="bi bi-tools fs-1 text-secondary"></i>
                </div>
                <h2 class="mt-3 mb-0">24</h2>
                <p class="text-muted">Services in progress</p>
                <a href="manage-services.php" class="btn btn-sm btn-outline-secondary mt-3">Manage Services</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Pending Orders</h5>
                    <i class="bi bi-box-seam fs-1 text-secondary"></i>
                </div>
                <h2 class="mt-3 mb-0">18</h2>
                <p class="text-muted">Orders requiring action</p>
                <a href="manage-orders.php" class="btn btn-sm btn-outline-secondary mt-3">Process Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Customers</h5>
                    <i class="bi bi-people fs-1 text-secondary"></i>
                </div>
                <h2 class="mt-3 mb-0">156</h2>
                <p class="text-muted">Total active customers</p>
                <a href="manage-customers.php" class="btn btn-sm btn-outline-secondary mt-3">View Customers</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Technicians</h5>
                    <i class="bi bi-person-badge fs-1 text-secondary"></i>
                </div>
                <h2 class="mt-3 mb-0">12</h2>
                <p class="text-muted">Staff on duty today</p>
                <a href="manage-technicians.php" class="btn btn-sm btn-outline-secondary mt-3">Manage Staff</a>
            </div>
        </div>
    </div>
    
    <!-- Urgent Service Requests -->
    <div class="col-md-6 mb-4">
        <div class="card urgent-service">
            <div class="card-header bg-white">
                <h5 class="mb-0">Urgent Service Requests</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="service-details.php?id=187" class="list-group-item list-group-item-action service-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Emergency AC Repair</h6>
                            <span class="badge bg-danger">Critical</span>
                        </div>
                        <p class="mb-1">Customer: Robert Johnson • Submitted: 2 hours ago</p>
                        <small>Address: 1234 Maple Avenue, Downtown</small>
                    </a>
                    <a href="service-details.php?id=186" class="list-group-item list-group-item-action service-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Commercial Unit Failure</h6>
                            <span class="badge bg-danger">Critical</span>
                        </div>
                        <p class="mb-1">Customer: Horizon Restaurant • Submitted: 3 hours ago</p>
                        <small>Address: 567 Main Street, Business District</small>
                    </a>
                    <a href="service-details.php?id=185" class="list-group-item list-group-item-action service-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">No Cooling - Elderly Customer</h6>
                            <span class="badge bg-warning">High Priority</span>
                        </div>
                        <p class="mb-1">Customer: Martha Williams • Submitted: 4 hours ago</p>
                        <small>Address: 890 Oak Drive, Westside</small>
                    </a>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="emergency-queue.php" class="btn btn-danger">Manage Emergency Queue</a>
            </div>
        </div>
    </div>
    
    <!-- Pending Approvals -->
    <div class="col-md-6 mb-4">
        <div class="card pending-approval">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pending Approvals</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="approve-quote.php?id=245" class="list-group-item list-group-item-action service-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Service Quote #245</h6>
                            <span class="badge bg-warning">Quote Pending</span>
                        </div>
                        <p class="mb-1">Customer: James Wilson • Submitted by: Michael Rodriguez</p>
                        <small>Quote Amount: $1,450.00 • Full System Replacement</small>
                    </a>
                    <a href="approve-discount.php?id=124" class="list-group-item list-group-item-action service-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Discount Request #124</h6>
                            <span class="badge bg-warning">Approval Needed</span>
                        </div>
                        <p class="mb-1">Customer: Emily Thompson • Requested by: Sarah Williams</p>
                        <small>Requested: 15% Loyalty Discount on $3,200.00 installation</small>
                    </a>
                    <a href="approve-refund.php?id=87" class="list-group-item list-group-item-action service-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Refund Request #87</h6>
                            <span class="badge bg-warning">Approval Needed</span>
                        </div>
                        <p class="mb-1">Customer: David Anderson • Submitted by: Customer Service</p>
                        <small>Amount: $350.00 • Reason: Service call fee dispute</small>
                    </a>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="pending-approvals.php" class="btn btn-warning">Review All Pending Items</a>
            </div>
        </div>
    </div>
    
    <!-- Admin Quick Actions -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Administration Tools</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="schedule-management.php" class="card admin-quick-action text-decoration-none text-center p-4">
                            <i class="bi bi-calendar-week fs-1 text-secondary mb-3"></i>
                            <h6 class="mb-0">Scheduling</h6>
                            <p class="small text-muted mb-0">Manage technician schedules</p>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="inventory-management.php" class="card admin-quick-action text-decoration-none text-center p-4">
                            <i class="bi bi-boxes fs-1 text-secondary mb-3"></i>
                            <h6 class="mb-0">Inventory</h6>
                            <p class="small text-muted mb-0">Stock and parts management</p>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="reports.php" class="card admin-quick-action text-decoration-none text-center p-4">
                            <i class="bi bi-graph-up fs-1 text-secondary mb-3"></i>
                            <h6 class="mb-0">Reports</h6>
                            <p class="small text-muted mb-0">Generate system reports</p>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="system-settings.php" class="card admin-quick-action text-decoration-none text-center p-4">
                            <i class="bi bi-gear fs-1 text-secondary mb-3"></i>
                            <h6 class="mb-0">Settings</h6>
                            <p class="small text-muted mb-0">Configure system options</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Schedule -->
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Today's Schedule</h5>
                <a href="schedule-view.php" class="btn btn-sm btn-outline-secondary">Full Calendar</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Technician</th>
                                <th>Customer</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>08:00 AM</td>
                                <td>John Smith</td>
                                <td>Michael Davis</td>
                                <td>AC Maintenance</td>
                                <td><span class="badge bg-success">In Progress</span></td>
                                <td class="text-end">
                                    <a href="service-details.php?id=190" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td>09:30 AM</td>
                                <td>Sarah Williams</td>
                                <td>Jennifer Miller</td>
                                <td>Installation</td>
                                <td><span class="badge bg-warning">En Route</span></td>
                                <td class="text-end">
                                    <a href="service-details.php?id=191" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td>11:00 AM</td>
                                <td>Michael Rodriguez</td>
                                <td>Thomas Wilson</td>
                                <td>Repair</td>
                                <td><span class="badge bg-secondary">Scheduled</span></td>
                                <td class="text-end">
                                    <a href="service-details.php?id=192" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td>01:30 PM</td>
                                <td>James Johnson</td>
                                <td>Apex Office Park</td>
                                <td>Commercial Maintenance</td>
                                <td><span class="badge bg-secondary">Scheduled</span></td>
                                <td class="text-end">
                                    <a href="service-details.php?id=193" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td>03:00 PM</td>
                                <td>Elizabeth Brown</td>
                                <td>Robert Thompson</td>
                                <td>Estimate</td>
                                <td><span class="badge bg-secondary">Scheduled</span></td>
                                <td class="text-end">
                                    <a href="service-details.php?id=194" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Notifications -->
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System Notifications</h5>
                <a href="all-notifications.php" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Inventory Alert</h6>
                            <small class="text-muted">30 minutes ago</small>
                        </div>
                        <p class="mb-1">5 items are below reorder threshold</p>
                        <div class="d-flex justify-content-end">
                            <a href="inventory-management.php?filter=low" class="btn btn-sm btn-outline-secondary">View Items</a>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Technician Update</h6>
                            <small class="text-muted">1 hour ago</small>
                        </div>
                        <p class="mb-1">James Johnson has marked job #189 as complete</p>
                        <div class="d-flex justify-content-end">
                            <a href="service-details.php?id=189" class="btn btn-sm btn-outline-secondary">Review</a>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">New Customer</h6>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                        <p class="mb-1">Sandra Martinez registered a new account</p>
                        <div class="d-flex justify-content-end">
                            <a href="customer-details.php?id=157" class="btn btn-sm btn-outline-secondary">View Profile</a>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Payment Alert</h6>
                            <small class="text-muted">3 hours ago</small>
                        </div>
                        <p class="mb-1">Invoice #4587 is 30 days overdue</p>
                        <div class="d-flex justify-content-end">
                            <a href="invoice-details.php?id=4587" class="btn btn-sm btn-outline-secondary">View Invoice</a>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">System Update</h6>
                            <small class="text-muted">Yesterday</small>
                        </div>
                        <p class="mb-1">Scheduled maintenance completed successfully</p>
                        <div class="d-flex justify-content-end">
                            <a href="system-logs.php" class="btn btn-sm btn-outline-secondary">View Logs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Capture the content
$content = ob_get_clean();

// Additional page-specific scripts
$pageScripts = '
    // Chart.js implementation could go here
    // This would be for displaying administrative analytics
    console.log("Admin Dashboard page loaded");
    
    // Example of admin dashboard-specific JavaScript
    document.addEventListener("DOMContentLoaded", function() {
        // Notification handling
        const notificationItems = document.querySelectorAll(".notification-dismiss");
        notificationItems.forEach(item => {
            item.addEventListener("click", function() {
                this.closest(".list-group-item").style.display = "none";
            });
        });
        
        // Potential admin-specific functionality:
        // - Real-time service status updates
        // - Emergency service notifications
        // - Inventory tracking alerts
    });
';

// Include the base layout
include $basePath;
?>