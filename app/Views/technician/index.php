<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AC Service Pro - Technician Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
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
        
        /* Job item styles */
        .job-item {
            border-radius: 12px;
            background-color: white;
            margin-bottom: 16px;
            overflow: hidden;
        }
        
        .job-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .job-button {
            border-radius: 4px;
            font-weight: 500;
            padding: 8px 16px;
        }
        
        .location-item {
            background-color: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .location-icon {
            color: #ff3b30;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background-color: white;
        }
        
        .previous-report {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-tools me-2"></i>
                AC Service Pro
            </a>
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <i class="fas fa-bell text-white"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        2
                    </span>
                </div>
                <div class="dropdown profile-dropdown">
                    <button class="btn btn-link dropdown-toggle text-decoration-none text-white d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="/api/placeholder/40/40" alt="John Smith" class="rounded-circle me-2" width="32" height="32">
                        <div class="d-none d-md-block">
                            <div class="text-white">John Smith</div>
                            <div class="text-white-50 small">Senior Technician</div>
                        </div>
                    </button>
                    <span class="badge badge-active ms-2">Online</span>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Left Column - Assigned Jobs -->
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Assigned Jobs</h5>
                        <p class="text-muted mb-0">Total 3 jobs for today</p>
                    </div>
                    <div class="d-flex">
                        <div class="dropdown me-2">
                            <button class="btn btn-light dropdown-toggle" type="button" id="jobFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                All Jobs
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="jobFilterDropdown">
                                <li><a class="dropdown-item" href="#">All Jobs</a></li>
                                <li><a class="dropdown-item" href="#">In Progress</a></li>
                                <li><a class="dropdown-item" href="#">Pending</a></li>
                                <li><a class="dropdown-item" href="#">Completed</a></li>
                            </ul>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search jobs...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <button class="btn btn-outline-primary ms-2">
                            Today
                        </button>
                    </div>
                </div>

                <!-- Job List -->
                <div class="job-item p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge badge-high me-2">High Priority</span>
                            <span class="fw-bold">JOB-2024-001</span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="jobStatus1" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="badge badge-progress">In Progress</span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="jobStatus1">
                                <li><a class="dropdown-item" href="#">In Progress</a></li>
                                <li><a class="dropdown-item" href="#">Pending</a></li>
                                <li><a class="dropdown-item" href="#">Completed</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-2">TechCorp Industries</h6>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        123 Business Park, Suite 456
                    </p>
                    <p class="mb-3">Server maintenance and hardware upgrade</p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="far fa-clock me-1"></i> 10:00 AM
                            <span class="mx-2">|</span>
                            <i class="far fa-calendar me-1"></i> Feb 15, 2024
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-edit me-1"></i> Update Status
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-upload me-1"></i> Upload Report
                            </button>
                        </div>
                    </div>
                </div>

                <div class="job-item p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge badge-medium me-2">Medium Priority</span>
                            <span class="fw-bold">JOB-2024-002</span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="jobStatus2" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="badge badge-pending">Pending</span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="jobStatus2">
                                <li><a class="dropdown-item" href="#">In Progress</a></li>
                                <li><a class="dropdown-item" href="#">Pending</a></li>
                                <li><a class="dropdown-item" href="#">Completed</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-2">Global Solutions Ltd</h6>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        789 Innovation Drive
                    </p>
                    <p class="mb-3">Network infrastructure inspection</p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="far fa-clock me-1"></i> 2:00 PM
                            <span class="mx-2">|</span>
                            <i class="far fa-calendar me-1"></i> Feb 15, 2024
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-edit me-1"></i> Update Status
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-upload me-1"></i> Upload Report
                            </button>
                        </div>
                    </div>
                </div>

                <div class="job-item p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge badge-low me-2">Low Priority</span>
                            <span class="fw-bold">JOB-2024-003</span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="jobStatus3" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="badge badge-completed">Completed</span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="jobStatus3">
                                <li><a class="dropdown-item" href="#">In Progress</a></li>
                                <li><a class="dropdown-item" href="#">Pending</a></li>
                                <li><a class="dropdown-item" href="#">Completed</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-2">Digital Systems Co</h6>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        321 Tech Avenue
                    </p>
                    <p class="mb-3">Software system update and testing</p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="far fa-clock me-1"></i> 9:00 AM
                            <span class="mx-2">|</span>
                            <i class="far fa-calendar me-1"></i> Feb 15, 2024
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-edit me-1"></i> Update Status
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-upload me-1"></i> Upload Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Job Locations and Service Report -->
            <div class="col-lg-5">
                <!-- Job Locations -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Job Locations</h5>
                    </div>
                    <div class="card-body">
                        <div class="location-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <i class="fas fa-map-marker-alt location-icon me-2"></i>
                                    <span class="fw-bold">123 Business Park, Suite 456</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted me-3">Distance: 2.5 km</span>
                                <span class="text-muted">ETA: 15 minutes</span>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-primary me-2">
                                    <i class="fas fa-location-arrow me-1"></i> Mark On the Way
                                </button>
                                <button class="btn btn-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Report Delay
                                </button>
                            </div>
                        </div>

                        <div class="location-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <i class="fas fa-map-marker-alt location-icon me-2"></i>
                                    <span class="fw-bold">456 Shopping Avenue, Mall B, Service Area</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted me-3">Distance: 4.8 km</span>
                                <span class="text-muted">ETA: 25 minutes</span>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-location-arrow me-1"></i> On the Way
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Report -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Service Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="upload-area mb-3">
                            <i class="fas fa-upload fs-3 text-muted mb-3"></i>
                            <h6>Drop your service report here</h6>
                            <p class="text-muted small">Supported formats: PDF, DOC, DOCX</p>
                            <button class="btn btn-primary">
                                <i class="fas fa-file-upload me-1"></i> Upload Report
                            </button>
                        </div>

                        <h6 class="mb-3">Previous Reports</h6>
                        <div class="previous-report">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span>Service_Report_002.pdf</span>
                                <div class="text-muted small">2024-02-18</div>
                            </div>
                            <a href="#" class="text-primary">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        <div class="previous-report">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span>Service_Report_001.pdf</span>
                                <div class="text-muted small">2024-02-19</div>
                            </div>
                            <a href="#" class="text-primary">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>