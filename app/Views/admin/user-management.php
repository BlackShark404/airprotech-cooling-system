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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="addUserModalLabel">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" class="form-control border-start-0" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield text-muted"></i></span>
                                <select class="form-select border-start-0" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <option value="3">Admin</option>
                                    <option value="2">Technician</option>
                                    <option value="1">Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-toggle2-on text-muted"></i></span>
                                <select class="form-select border-start-0" id="is_active" name="is_active" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">
                    <i class="bi bi-save me-2"></i>Save User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="editUserModalLabel">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit_user_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="edit_first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="edit_last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control border-start-0" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0" id="edit_password" name="password">
                            <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_role_id" class="form-label">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield text-muted"></i></span>
                                <select class="form-select border-start-0" id="edit_role_id" name="role_id" required>
                                    <option value="3">Admin</option>
                                    <option value="2">Technician</option>
                                    <option value="1">Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_is_active" class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-toggle2-on text-muted"></i></span>
                                <select class="form-select border-start-0" id="edit_is_active" name="is_active" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateUserBtn">
                    <i class="bi bi-save me-2"></i>Update User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="viewUserModalLabel">
                    <i class="bi bi-person-badge me-2 text-primary"></i>User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar-placeholder d-flex align-items-center justify-content-center me-3 rounded-circle bg-primary text-white" style="width: 64px; height: 64px; font-size: 24px;" id="userInitials">
                                        JD
                                    </div>
                                    <div>
                                        <h5 class="mb-1" id="viewUserName">John Doe</h5>
                                        <p class="mb-0 text-muted" id="viewUserEmail">john.doe@example.com</p>
                                    </div>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">User ID:</span>
                                    <span class="fw-medium" id="viewUserId">12345</span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Role:</span>
                                    <span class="fw-medium" id="viewUserRole">Admin</span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span class="fw-medium" id="viewUserStatus">Active</span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Registered:</span>
                                    <span class="fw-medium" id="viewUserRegistered">2023-01-15</span>
                                </div>
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Last Login:</span>
                                    <span class="fw-medium" id="viewUserLastLogin">2023-05-20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Activity Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-2 small">Total Logins</label>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="fw-semibold" id="viewUserLogins">32</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-2 small">Services Requested</label>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="fw-semibold" id="viewUserServices">8</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="d-block text-muted mb-2 small">Active Services</label>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="fw-semibold" id="viewUserActiveServices">3</span>
                                    </div>
                                </div>
                                <div class="alert alert-light mt-4 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-history me-3 fs-4 text-primary"></i>
                                        <div>
                                            <p class="mb-0 small">Last Activity</p>
                                            <p class="mb-0 fw-medium" id="viewUserLastActivity">2023-05-20 10:45 AM</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewUserEditBtn">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="bi bi-trash fs-4 text-danger"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Delete User</h5>
                        <p class="mb-0 text-muted">This action cannot be undone</p>
                    </div>
                </div>
                
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    Deleting this user will remove all associated data including access logs, service requests, and account details.
                </div>
                
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDeleteCheck">
                    <label class="form-check-label" for="confirmDeleteCheck">
                        I understand the consequences of this action
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash me-2"></i>Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Required JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>    
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>


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



<!-- DataTablesManager -->
 <script>  	
let table = new DataTable('#myTable');
</script>
<script src="/assets/js/utility/DataTablesManager.js"></script>
<script src="/assets/js/utility/user-management.js"></script>


<?php
// Close the output buffer and include footer
$content = ob_get_clean();
echo $content;
    