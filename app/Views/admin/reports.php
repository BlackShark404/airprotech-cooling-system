<?php
$title = 'Reports - AC Service Pro';
$activeTab = 'reports';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .filter-container {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .filter-dropdown {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        background-color: white;
        cursor: pointer;
        font-size: 14px;
    }
    
    .filter-dropdown i {
        margin-left: 8px;
    }
    
    .export-btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }
    
    .export-btn i {
        margin-right: 8px;
    }
    
    .stats-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .stats-info {
        flex-grow: 1;
    }
    
    .stats-title {
        color: #6c757d;
        font-size: 16px;
        margin-bottom: 8px;
    }
    
    .stats-value {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .bg-tools {
        background-color: rgba(0, 123, 255, 0.1);
        color: #007bff;
    }
    
    .bg-people {
        background-color: rgba(255, 59, 48, 0.1);
        color: #ff3b30;
    }
    
    .bg-cart {
        background-color: rgba(255, 149, 0, 0.1);
        color: #ff9500;
    }
    
    .bg-money {
        background-color: rgba(52, 199, 89, 0.1);
        color: #34c759;
    }
    
    .chart-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }
    
    .chart-area {
        height: 300px;
        position: relative;
    }
    
    .technician-table {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .technician-table h5 {
        margin-bottom: 20px;
    }
    
    .tech-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .tech-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .activity-list {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px;
    }
    
    .activity-item {
        display: flex;
        align-items: flex-start;
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
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .activity-green {
        background-color: #e6f7e9;
        color: #34c759;
    }
    
    .activity-orange {
        background-color: #fff8e6;
        color: #ffbb00;
    }
    
    .activity-blue {
        background-color: #e7f1ff;
        color: #007bff;
    }
    
    .activity-content {
        flex-grow: 1;
    }
    
    .activity-title {
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .activity-description {
        color: #6c757d;
        margin-bottom: 4px;
    }
    
    .activity-time {
        font-size: 12px;
        color: #adb5bd;
    }
    
    /* Make it responsive */
    @media (max-width: 767.98px) {
        .filter-container {
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .filter-dropdown, .export-btn {
            margin-bottom: 10px;
        }
        
        .stats-value {
            font-size: 24px;
        }
        
        .chart-area {
            height: 250px;
        }
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col">
            <h1 class="h3 mb-0">Reports & Analytics</h1>
            <p class="text-muted">Track service performance and business metrics</p>
        </div>
    </div>
    
    <div class="filter-container">
        <button class="filter-dropdown">
            <i class="bi bi-calendar"></i> Last 30 Days <i class="bi bi-chevron-down"></i>
        </button>
        <button class="filter-dropdown">
            <i class="bi bi-geo-alt"></i> All Locations <i class="bi bi-chevron-down"></i>
        </button>
        <button class="filter-dropdown">
            <i class="bi bi-funnel"></i> Filters
        </button>
        <button class="export-btn">
            <i class="bi bi-download"></i> Export Reports
        </button>
    </div>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <div class="stats-info">
                    <div class="stats-title">Total Service Requests</div>
                    <h2 class="stats-value">156</h2>
                </div>
                <div class="stats-icon bg-tools">
                    <i class="bi bi-tools"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <div class="stats-info">
                    <div class="stats-title">Active Technicians</div>
                    <h2 class="stats-value">12</h2>
                </div>
                <div class="stats-icon bg-people">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <div class="stats-info">
                    <div class="stats-title">Pending Orders</div>
                    <h2 class="stats-value">34</h2>
                </div>
                <div class="stats-icon bg-cart">
                    <i class="bi bi-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <div class="stats-info">
                    <div class="stats-title">Revenue Today</div>
                    <h2 class="stats-value">$3,450</h2>
                </div>
                <div class="stats-icon bg-money">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Revenue Trends</h5>
                </div>
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5 class="chart-title">Service Request Trends</h5>
                </div>
                <div class="chart-area">
                    <canvas id="serviceRequestChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Technician Performance Table -->
    <div class="technician-table">
        <h5>Technician Performance</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Technician</th>
                        <th>Completion Rate</th>
                        <th>Avg Response Time</th>
                        <th>Satisfaction</th>
                        <th>Jobs Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="tech-name">
                                <img src="/api/placeholder/50/50" alt="Mike Wilson" class="tech-avatar">
                                <span>Mike Wilson</span>
                            </div>
                        </td>
                        <td>95%</td>
                        <td>25 mins</td>
                        <td>4.8</td>
                        <td>156</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="tech-name">
                                <img src="/api/placeholder/50/50" alt="Tom Davis" class="tech-avatar">
                                <span>Tom Davis</span>
                            </div>
                        </td>
                        <td>92%</td>
                        <td>28 mins</td>
                        <td>4.7</td>
                        <td>142</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="tech-name">
                                <img src="/api/placeholder/50/50" alt="Lisa Chen" class="tech-avatar">
                                <span>Lisa Chen</span>
                            </div>
                        </td>
                        <td>97%</td>
                        <td>22 mins</td>
                        <td>4.9</td>
                        <td>168</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="activity-list">
        <h5 class="mb-4">Recent Activities</h5>
        
        <div class="activity-item">
            <div class="activity-icon activity-green">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="activity-content">
                <h6 class="activity-title">Service Completed</h6>
                <p class="activity-description">AC Installation on 123 Main St</p>
                <p class="activity-time">2 hours ago</p>
            </div>
        </div>
        
        <div class="activity-item">
            <div class="activity-icon activity-orange">
                <i class="bi bi-clock"></i>
            </div>
            <div class="activity-content">
                <h6 class="activity-title">New Request</h6>
                <p class="activity-description">Emergency Repair Request</p>
                <p class="activity-time">3 hours ago</p>
            </div>
        </div>
        
        <div class="activity-item">
            <div class="activity-icon activity-blue">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="activity-content">
                <h6 class="activity-title">Order Updated</h6>
                <p class="activity-description">Parts Order #4567 Delivered</p>
                <p class="activity-time">4 hours ago</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional scripts specific to this page
$additionalScripts = <<<HTML
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'Revenue',
                data: [33000, 37000, 35000, 37500, 45000],
                backgroundColor: '#007bff',
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
                            size: 11
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

    // Service Request Chart
    const serviceCtx = document.getElementById('serviceRequestChart').getContext('2d');
    const serviceRequestChart = new Chart(serviceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [
                {
                    label: 'Completed',
                    data: [45, 50, 47, 53, 60],
                    borderColor: '#34c759',
                    backgroundColor: 'transparent',
                    pointBackgroundColor: '#34c759',
                    tension: 0.4
                },
                {
                    label: 'In Progress',
                    data: [20, 18, 22, 25, 30],
                    borderColor: '#ffbb00',
                    backgroundColor: 'transparent',
                    pointBackgroundColor: '#ffbb00',
                    tension: 0.4
                },
                {
                    label: 'Pending',
                    data: [10, 8, 12, 15, 10],
                    borderColor: '#ff3b30',
                    backgroundColor: 'transparent',
                    pointBackgroundColor: '#ff3b30',
                    tension: 0.4
                }
            ]
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
                            size: 11
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Make charts responsive on resize
    window.addEventListener('resize', function() {
        revenueChart.resize();
        serviceRequestChart.resize();
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>