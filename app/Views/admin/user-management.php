<?php
// Set page title and active tab
$title = 'User Management - AirProtect';
$activeTab = 'user_management';

// Include base template
ob_start();
include __DIR__ . '/../includes/admin/base.php';

?>
<link rel="stylesheet" href="/assets/css/user-management.css">

<!-- Main Content -->
<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0 fw-bold text-dark">User Management</h3>
            <p class="text-muted mb-0">Manage your system users and access permissions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg me-2"></i>Add User
            </button>
        </div>
    </div>
    
    <!-- Filters & Search Row -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="roleFilter" class="form-label small text-muted">Role</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-person-badge text-muted"></i></span>
                        <select class="form-select" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="technician">Technician</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label small text-muted">Status</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-toggle2-on text-muted"></i></span>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="search-box w-100">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button id="applyFilters" class="btn btn-primary w-100">
                            <i class="bi bi-funnel-fill me-2"></i>Apply Filters
                        </button>
                        <button id="resetFilters" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title mb-0">Users</h5>
            <span class="badge bg-light text-dark" id="userCount">Loading...</span>
        </div>
        <div class="card-body table-container">
            <!-- Using empty thead to let DataTables build it properly -->
            <table id="usersTable" class="table align-middle table-hover display nowrap" style="width:100%">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<?php
// Close the output buffer and include footer
$content = ob_get_clean();
echo $content;
    