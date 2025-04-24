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
        .nav-tabs {
            border-bottom: none;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 1rem 1.5rem;
            font-weight: 500;
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
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle text-white me-2"></i>
                    <span class="text-white">Admin User</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Tabs -->
    <div class="container-fluid">
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

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Total Service Requests</div>
                            <div class="stats-value mt-2">156</div>
                        </div>
                        <div class="stats-icon stats-icon-blue">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Active Technicians</div>
                            <div class="stats-value mt-2">12</div>
                        </div>
                        <div class="stats-icon stats-icon-red">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Pending Orders</div>
                            <div class="stats-value mt-2">34</div>
                        </div>
                        <div class="stats-icon stats-icon-blue">
                            <i class="bi bi-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stats-label">Revenue Today</div>
                            <div class="stats-value mt-2">$3,450</div>
                        </div>
                        <div class="stats-icon stats-icon-red">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-md-3">
                <button class="btn btn-red action-button w-100">
                    <i class="bi bi-plus me-2"></i>
                    New Service Request
                </button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-blue action-button w-100">
                    <i class="bi bi-person-plus me-2"></i>
                    Assign Technician
                </button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-red action-button w-100">
                    <i class="bi bi-box me-2"></i>
                    Add Product
                </button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-blue action-button w-100">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Generate Report
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row mt-4">
            <div class="col-md-7">
                <!-- Service Requests Table -->
                <div class="card p-3">
                    <h5 class="mb-3">Service Requests</h5>
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

                <!-- Revenue Chart -->
                <div class="card p-3 mt-4">
                    <h5 class="mb-3">Revenue Trends</h5>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <!-- Technician Status -->
                <div class="card p-3">
                    <h5 class="mb-3">Technician Status</h5>
                    
                    <div class="tech-card d-flex align-items-center">
                        <img src="/api/placeholder/50/50" alt="Mike Wilson" class="tech-avatar me-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0">Mike Wilson</h6>
                                <span class="badge badge-active">Active</span>
                            </div>
                            <div class="tech-info d-flex align-items-center mt-2">
                                <span class="me-3"><i class="bi bi-tools me-1"></i> 2 jobs</span>
                                <span><i class="bi bi-geo-alt me-1"></i> Downtown</span>
                            </div>
                        </div>
                    </div>

                    <div class="tech-card d-flex align-items-center mt-3">
                        <img src="/api/placeholder/50/50" alt="Tom Davis" class="tech-avatar me-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0">Tom Davis</h6>
                                <span class="badge badge-break">On Break</span>
                            </div>
                            <div class="tech-info d-flex align-items-center mt-2">
                                <span class="me-3"><i class="bi bi-tools me-1"></i> 0 jobs</span>
                                <span><i class="bi bi-geo-alt me-1"></i> North Side</span>
                            </div>
                        </div>
                    </div>

                    <div class="tech-card d-flex align-items-center mt-3">
                        <img src="/api/placeholder/50/50" alt="Lisa Chen" class="tech-avatar me-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0">Lisa Chen</h6>
                                <span class="badge badge-active">Active</span>
                            </div>
                            <div class="tech-info d-flex align-items-center mt-2">
                                <span class="me-3"><i class="bi bi-tools me-1"></i> 1 jobs</span>
                                <span><i class="bi bi-geo-alt me-1"></i> West End</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card p-3 mt-4">
                    <h5 class="mb-3">Recent Activities</h5>
                    
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Revenue Chart
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
                                return value === 0 ? '0' : value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
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
    </script>
</body>
</html>