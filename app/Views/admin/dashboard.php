<?php
$title = 'Dashboard - AC Service Pro';
$activeTab = 'dashboard';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    /* Any additional dashboard-specific styles would go here */
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="col">
        <h1 class="h3 mb-0">Admin Dashboard</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 py-4">
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stats-label">Total Service Requests</div>
                        <div class="d-flex align-items-center mt-2">
                            <div class="stats-value">156</div>
                            <span class="stats-percent stats-percent-up">+12%</span>
                        </div>
                        <div class="text-muted small">vs. last month</div>
                    </div>
                    <div class="stats-icon stats-icon-blue">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stats-label">Active Technicians</div>
                        <div class="d-flex align-items-center mt-2">
                            <div class="stats-value">12</div>
                            <span class="stats-percent stats-percent-up">+5%</span>
                        </div>
                        <div class="text-muted small">vs. last month</div>
                    </div>
                    <div class="stats-icon stats-icon-red">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stats-label">Pending Orders</div>
                        <div class="d-flex align-items-center mt-2">
                            <div class="stats-value">34</div>
                            <span class="stats-percent stats-percent-down">-8%</span>
                        </div>
                        <div class="text-muted small">vs. last month</div>
                    </div>
                    <div class="stats-icon stats-icon-blue">
                        <i class="bi bi-cart"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stats-label">Revenue Today</div>
                        <div class="d-flex align-items-center mt-2">
                            <div class="stats-value">$3,450</div>
                            <span class="stats-percent stats-percent-up">+15%</span>
                        </div>
                        <div class="text-muted small">vs. yesterday</div>
                    </div>
                    <div class="stats-icon stats-icon-red">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <!-- Order matters for mobile: Move technician status above the service requests table on mobile -->
        <div class="col-lg-5 order-lg-2">
            <!-- Technician Status -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Technician Status</h5>
                    <a href="#" class="view-all">View all <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="card-body">
                    <div class="tech-card d-flex align-items-center">
                        <img src="/api/placeholder/50/50" alt="Mike Wilson" class="tech-avatar me-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h6 class="mb-0">Mike Wilson</h6>
                                <span class="badge badge-active mt-1 mt-sm-0">Active</span>
                            </div>
                            <div class="tech-info d-flex align-items-center mt-2 flex-wrap">
                                <span class="me-3 mb-1 mb-sm-0"><i class="bi bi-tools me-1"></i> 2 jobs</span>
                                <span><i class="bi bi-geo-alt me-1"></i> Downtown</span>
                            </div>
                        </div>
                    </div>

                    <div class="tech-card d-flex align-items-center mt-3">
                        <img src="/api/placeholder/50/50" alt="Tom Davis" class="tech-avatar me-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h6 class="mb-0">Tom Davis</h6>
                                <span class="badge badge-break mt-1 mt-sm-0">On Break</span>
                            </div>
                            <div class="tech-info d-flex align-items-center mt-2 flex-wrap">
                                <span class="me-3 mb-1 mb-sm-0"><i class="bi bi-tools me-1"></i> 0 jobs</span>
                                <span><i class="bi bi-geo-alt me-1"></i> North Side</span>
                            </div>
                        </div>
                    </div>

                    <div class="tech-card d-flex align-items-center mt-3">
                        <img src="/api/placeholder/50/50" alt="Lisa Chen" class="tech-avatar me-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h6 class="mb-0">Lisa Chen</h6>
                                <span class="badge badge-active mt-1 mt-sm-0">Active</span>
                            </div>
                            <div class="tech-info d-flex align-items-center mt-2 flex-wrap">
                                <span class="me-3 mb-1 mb-sm-0"><i class="bi bi-tools me-1"></i> 1 jobs</span>
                                <span><i class="bi bi-geo-alt me-1"></i> West End</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activities</h5>
                    <a href="#" class="view-all">View all <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="card-body">
                    <div class="activity-item d-flex align-items-center">
                        <div class="activity-icon activity-icon-green me-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Service Completed</h6>
                            <p class="mb-0 text-muted">AC Installation at 123 Main St</p>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex align-items-center">
                        <div class="activity-icon activity-icon-orange me-3">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">New Request</h6>
                            <p class="mb-0 text-muted">Emergency Repair Request</p>
                            <small class="text-muted">3 hours ago</small>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex align-items-center">
                        <div class="activity-icon activity-icon-green me-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Order Updated</h6>
                            <p class="mb-0 text-muted">Parts Order #45678 Delivered</p>
                            <small class="text-muted">4 hours ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 order-lg-1 mt-4 mt-lg-0">
            <!-- Service Requests Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Service Requests</h5>
                    <a href="#" class="view-all">View all <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Technician</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SR001</td>
                                    <td>John Smith</td>
                                    <td>Installation</td>
                                    <td>Mike Wilson</td>
                                    <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                                    <td><span class="badge badge-high rounded-pill px-3 py-2">High</span></td>
                                </tr>
                                <tr>
                                    <td>SR002</td>
                                    <td>Sarah Johnson</td>
                                    <td>Repair</td>
                                    <td>Unassigned</td>
                                    <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                                    <td><span class="badge badge-medium rounded-pill px-3 py-2">Medium</span></td>
                                </tr>
                                <tr>
                                    <td>SR003</td>
                                    <td>David Brown</td>
                                    <td>Maintenance</td>
                                    <td>Tom Davis</td>
                                    <td><span class="badge badge-completed rounded-pill px-3 py-2">Completed</span></td>
                                    <td><span class="badge badge-low rounded-pill px-3 py-2">Low</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Revenue Trends</h5>
                    <a href="#" class="view-all">View report <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
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
    // Revenue Chart with responsive options
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'Revenue',
                data: [33000, 41000, 38000, 45000, 52000],
                backgroundColor: '#1a73e8',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0'
                    },
                    ticks: {
                        callback: function(value) {
                            return value === 0 ? '0' : '$' + (value/1000) + 'k';
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: function() {
                                // Smaller font on mobile
                                return window.innerWidth < 768 ? 10 : 12;
                            }
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Make chart responsive on resize
    window.addEventListener('resize', function() {
        revenueChart.resize();
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>