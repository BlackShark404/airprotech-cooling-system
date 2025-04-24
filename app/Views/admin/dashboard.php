<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AC Service Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #212529;
            padding: 0.75rem 1rem;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            color: white;
            font-weight: 600;
        }
        .nav-scroll {
            overflow-x: auto;
            flex-wrap: nowrap;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        .nav-scroll::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        .nav-tabs {
            border-bottom: none;
            min-width: max-content;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 1rem 1.5rem;
            font-weight: 500;
            white-space: nowrap;
        }
        .nav-tabs .nav-link.active {
            color: #ff3b30;
            border-bottom: 2px solid #ff3b30;
            background-color: transparent;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .stats-icon {
            font-size: 24px;
        }
        .stats-icon-blue {
            color: #007bff;
        }
        .stats-icon-red {
            color: #ff3b30;
        }
        .stats-icon-orange {
            color: #ff9500;
        }
        .stats-icon-green {
            color: #34c759;
        }
        .stats-value {
            font-size: 28px;
            font-weight: 700;
        }
        .stats-label {
            color: #6c757d;
            font-weight: 500;
        }
        .stats-percent {
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
            margin-left: 8px;
        }
        .stats-percent-up {
            background-color: #e6f7e9;
            color: #34c759;
        }
        .stats-percent-down {
            background-color: #ffe5e5;
            color: #ff3b30;
        }
        .action-button {
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-red {
            background-color: #ff3b30;
            color: white;
        }
        .btn-blue {
            background-color: #007bff;
            color: white;
        }
        .badge-progress {
            background-color: #e7f1ff;
            color: #007bff;
        }
        .badge-pending {
            background-color: #fff8e6;
            color: #ffbb00;
        }
        .badge-completed {
            background-color: #e6f7e9;
            color: #34c759;
        }
        .badge-high {
            background-color: #ffe5e5;
            color: #ff3b30;
        }
        .badge-medium {
            background-color: #fff0e5;
            color: #ff9500;
        }
        .badge-low {
            background-color: #e6f7e9;
            color: #34c759;
        }
        .badge-active {
            background-color: #e6f7e9;
            color: #34c759;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .badge-break {
            background-color: #fff0e5;
            color: #ff9500;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .table th {
            font-weight: 600;
            color: #6c757d;
            border-top: none;
            border-bottom: 1px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
            padding: 16px 12px;
        }
        .tech-card {
            background-color: white;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 10px;
        }
        .tech-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }
        .tech-info {
            font-size: 14px;
        }
        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .activity-icon-green {
            background-color: #e6f7e9;
            color: #34c759;
        }
        .activity-icon-orange {
            background-color: #fff8e6;
            color: #ffbb00;
        }
        .chart-container {
            height: 250px;
        }
        .view-all {
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .card-header {
            background-color: transparent;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
        }
        .view-all:hover {
            text-decoration: underline;
        }
        
        /* Profile dropdown styles */
        .profile-dropdown .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
            padding: 8px 0;
            min-width: 180px;
        }
        
        .profile-dropdown .dropdown-item {
            padding: 8px 16px;
            color: #343a40;
            font-size: 14px;
        }
        
        .profile-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .profile-dropdown .dropdown-item i {
            margin-right: 8px;
            color: #6c757d;
        }
        
        .profile-dropdown .dropdown-toggle::after {
            vertical-align: middle;
        }
        
        .profile-dropdown .dropdown-divider {
            margin: 4px 0;
        }
        
        /* Responsive styles */
        @media (max-width: 767.98px) {
            .stats-value {
                font-size: 22px;
            }
            .stats-percent {
                font-size: 12px;
                padding: 2px 6px;
            }
            .stats-icon {
                font-size: 20px;
            }
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .card-header .view-all {
                margin-top: 0.5rem;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .table {
                min-width: 600px;
            }
        }
        
        /* Handle smaller text on very small devices */
        @media (max-width: 375px) {
            .stats-label {
                font-size: 14px;
            }
            .stats-value {
                font-size: 18px;
            }
            .nav-tabs .nav-link {
                padding: 0.75rem 1rem;
            }
        }
        
        /* Make technician cards more compact on small screens */
        @media (max-width: 575.98px) {
            .tech-card {
                padding: 8px;
            }
            .tech-avatar {
                width: 40px;
                height: 40px;
            }
            .tech-info {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-tools me-2"></i>
                AC Service Pro
            </a>
            <div class="d-flex">
                <div class="me-3">
                    <i class="bi bi-bell text-white"></i>
                </div>
                <div class="dropdown profile-dropdown">
                    <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-2"></i>
                        <span class="d-none d-sm-inline">Admin User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/auth/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Tabs with horizontal scroll for mobile -->
    <div class="container-fluid">
        <div class="nav-scroll">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Service Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Technicians</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Reports</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Stats Cards -->
        <div class="row g-3">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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
</body>
</html>