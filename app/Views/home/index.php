<?php
// Start session

// Set page specific variables
$pageTitle = 'Dashboard';
$pageDescription = 'Air-Protech Customer Dashboard';
$pageHeader = 'My Dashboard';
$pageSubheader = 'Welcome to your Air-Protech Cooling Services Dashboard';

// Additional page-specific styles
$pageStyles = '
    .stats-card {
        border-left: 4px solid var(--primary);
    }
    .upcoming-service {
        border-left: 4px solid var(--secondary);
    }
    .service-history-item:hover {
        background-color: rgba(0, 102, 204, 0.1);
    }
';

// Start output buffering to capture content
ob_start();
?>

<div class="row">
    <!-- Dashboard Stats -->
    <div class="col-md-4 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Active Services</h5>
                    <i class="bi bi-tools fs-1 text-primary"></i>
                </div>
                <h2 class="mt-3 mb-0">3</h2>
                <p class="text-muted">Services in progress</p>
                <a href="my-services.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Product Orders</h5>
                    <i class="bi bi-box-seam fs-1 text-primary"></i>
                </div>
                <h2 class="mt-3 mb-0">2</h2>
                <p class="text-muted">Orders pending delivery</p>
                <a href="my-orders.php" class="btn btn-sm btn-outline-primary mt-3">Track Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Next Service Date</h5>
                    <i class="bi bi-calendar-event fs-1 text-primary"></i>
                </div>
                <h2 class="mt-3 mb-0">Jun 15</h2>
                <p class="text-muted">Annual maintenance</p>
                <a href="service-details.php?id=123" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Service -->
    <div class="col-md-6 mb-4">
        <div class="card upcoming-service">
            <div class="card-header bg-white">
                <h5 class="mb-0">Upcoming Service</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary text-white rounded p-3 me-3">
                        <i class="bi bi-calendar2-check fs-3"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Annual AC Maintenance</h5>
                        <p class="mb-0 text-muted">June 15, 2025 • 10:00 AM - 12:00 PM</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <img src="assets/images/technician-avatar.jpg" class="rounded-circle" width="50" height="50" alt="Technician" onerror="this.src='https://via.placeholder.com/50?text=Tech'">
                    </div>
                    <div>
                        <h6 class="mb-1">Technician: John Smith</h6>
                        <p class="mb-0"><i class="bi bi-star-fill text-warning"></i> 4.9 (120 reviews)</p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <a href="reschedule.php?id=123" class="btn btn-outline-primary me-2">Reschedule</a>
                    <a href="service-details.php?id=123" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="service-request.php" class="btn btn-outline-primary w-100 p-3">
                            <i class="bi bi-calendar-plus fs-3 d-block mb-2"></i>
                            Schedule Service
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="product-order.php" class="btn btn-outline-secondary w-100 p-3">
                            <i class="bi bi-cart-plus fs-3 d-block mb-2"></i>
                            Order Products
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="emergency-service.php" class="btn btn-outline-danger w-100 p-3">
                            <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                            Emergency Service
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="support.php" class="btn btn-outline-info w-100 p-3">
                            <i class="bi bi-headset fs-3 d-block mb-2"></i>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service History -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Service History</h5>
                <a href="service-history.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="service-details.php?id=122" class="list-group-item list-group-item-action service-history-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">AC Repair - Cooling Fan Replacement</h6>
                            <small class="text-muted">May 10, 2025</small>
                        </div>
                        <p class="mb-1">Technician: Michael Johnson</p>
                        <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Completed</small>
                    </a>
                    <a href="service-details.php?id=121" class="list-group-item list-group-item-action service-history-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Refrigerant Refill</h6>
                            <small class="text-muted">April 23, 2025</small>
                        </div>
                        <p class="mb-1">Technician: Sarah Williams</p>
                        <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Completed</small>
                    </a>
                    <a href="service-details.php?id=120" class="list-group-item list-group-item-action service-history-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">AC System Regular Maintenance</h6>
                            <small class="text-muted">March 15, 2025</small>
                        </div>
                        <p class="mb-1">Technician: John Smith</p>
                        <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Completed</small>
                    </a>
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
    // This would be for displaying any analytics like service history, etc.
    console.log("Dashboard page loaded");
    
    // Example of dashboard-specific JavaScript
    document.addEventListener("DOMContentLoaded", function() {
        // Notification handling
        const notificationBtns = document.querySelectorAll(".notification-dismiss");
        notificationBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                this.closest(".notification-item").style.display = "none";
            });
        });
    });
';

// Include the base layout
include $basePath;
?>