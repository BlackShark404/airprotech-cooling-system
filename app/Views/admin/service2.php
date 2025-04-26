<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Requests - AC Service Pro</title>
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
        .btn-success {
            background-color: #34c759;
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
        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            cursor: pointer;
        }
        .action-icon-view {
            background-color: #e7f1ff;
            color: #007bff;
        }
        .action-icon-edit {
            background-color: #e6f7e9;
            color: #34c759;
        }
        .action-icon-delete {
            background-color: #ffe5e5;
            color: #ff3b30;
        }
        .form-label {
            font-weight: 500;
            color: #343a40;
        }
        .form-select, .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .form-select:focus, .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15);
            border-color: #80bdff;
        }
        .filter-card {
            border-radius: 12px;
            background-color: white;
            padding: 16px;
            margin-bottom: 20px;
        }
        .pagination {
            justify-content: center;
        }
        .page-item .page-link {
            border: none;
            color: #6c757d;
            padding: 8px 12px;
            margin: 0 4px;
            border-radius: 8px;
        }
        .page-item.active .page-link {
            background-color: #007bff;
            color: white;
        }
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
        
        /* Responsive styles */
        @media (max-width: 767.98px) {
            .table-responsive {
                overflow-x: auto;
            }
            .table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="/api/placeholder/36/36" alt="AirProtect logo" height="36" width="36">
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
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="service-requests.php">Service Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="technicians.php">Technicians</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="inventory.php">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">Reports</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between flex-wrap">
                    <h4 class="mb-3 mb-md-0">Service Requests</h4>
                    <div>
                        <button class="btn btn-red" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                            <i class="bi bi-plus-circle me-2"></i> New Service Request
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="all">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Service Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="all">All Types</option>
                                <option value="installation">Installation</option>
                                <option value="repair">Repair</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priorityFilter" class="form-label">Priority</label>
                            <select class="form-select" id="priorityFilter">
                                <option value="all">All Priorities</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchFilter" class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchFilter" placeholder="Search by ID, customer...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Requests Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Address</th>
                                        <th>Type</th>
                                        <th>Scheduled Date</th>
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
                                        <td>123 Main St, Apt 4B</td>
                                        <td>Installation</td>
                                        <td>Apr 26, 2025</td>
                                        <td>Mike Wilson</td>
                                        <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                                        <td><span class="badge badge-high rounded-pill px-3 py-2">High</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SR002</td>
                                        <td>Sarah Johnson</td>
                                        <td>456 Oak Avenue</td>
                                        <td>Repair</td>
                                        <td>Apr 28, 2025</td>
                                        <td>Unassigned</td>
                                        <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                                        <td><span class="badge badge-medium rounded-pill px-3 py-2">Medium</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SR003</td>
                                        <td>David Brown</td>
                                        <td>789 Pine Street</td>
                                        <td>Maintenance</td>
                                        <td>Apr 25, 2025</td>
                                        <td>Tom Davis</td>
                                        <td><span class="badge badge-completed rounded-pill px-3 py-2">Completed</span></td>
                                        <td><span class="badge badge-low rounded-pill px-3 py-2">Low</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SR004</td>
                                        <td>Michael Jones</td>
                                        <td>321 Elm Street</td>
                                        <td>Installation</td>
                                        <td>Apr 30, 2025</td>
                                        <td>Lisa Chen</td>
                                        <td><span class="badge badge-pending rounded-pill px-3 py-2">Pending</span></td>
                                        <td><span class="badge badge-high rounded-pill px-3 py-2">High</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SR005</td>
                                        <td>Jennifer Williams</td>
                                        <td>654 Maple Road</td>
                                        <td>Repair</td>
                                        <td>May 2, 2025</td>
                                        <td>Mike Wilson</td>
                                        <td><span class="badge badge-progress rounded-pill px-3 py-2">In Progress</span></td>
                                        <td><span class="badge badge-medium rounded-pill px-3 py-2">Medium</span></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="action-icon action-icon-view"><i class="bi bi-eye"></i></div>
                                                <div class="action-icon action-icon-edit"><i class="bi bi-pencil"></i></div>
                                                <div class="action-icon action-icon-delete"><i class="bi bi-trash"></i></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="text-center text-muted small">
                    Showing 1 to 5 of 156 entries
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>