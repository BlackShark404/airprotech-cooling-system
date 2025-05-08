<?php
// Set page title and active tab
$title = 'User Management - AirProtect';
$activeTab = 'user_management';

$additionalStyles = <<<HTML
<style>
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
        background-color: #f8f9fa;
        margin-right: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-icon:hover {
        background-color: #e9ecef;
    }
    .action-icon-view {
        color: #007bff;
    }
    .action-icon-edit {
        color: #28a745;
    }
    .action-icon-delete {
        color: #dc3545;
    }
    .action-icon-assign {
        color: #17a2b8;
    }
    .pagination-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 1rem;
    }
    .pagination-button {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        cursor: pointer;
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    .pagination-button.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .pagination-button:hover:not(.active) {
        background-color: #f8f9fa;
    }
    
    /* Avatar style */
    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #fff;
        margin-right: 10px;
    }
    
    /* Badge styles */
    .badge-active {
        background-color: #28a745;
        color: white;
    }
    .badge-inactive {
        background-color: #dc3545;
        color: white;
    }
    
    /* Filter styles */
    .filter-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .filter-title {
        font-weight: 600;
        margin-bottom: 12px;
    }
    
    /* Modal styles */
    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-group label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
    
    /* Responsive table styles */
    @media (max-width: 768px) {
        .action-icon {
            width: 28px;
            height: 28px;
        }
        .table td, .table th {
            padding: 10px 8px;
        }
    }
</style>
HTML;

// Include base template
ob_start();
?>

<!-- Main Content -->
<div class="container-fluid py-4 fade-in">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
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
    
    <!-- Filters -->
    <div class="filter-container shadow-sm">
        <div class="filter-title">Filters</div>
        <div class="row g-3">
            <div class="col-md-3">
                <label for="roleFilter" class="form-label">Role</label>
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                    <option value="technician">Technician</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button id="applyFilters" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-2"></i>Apply Filters
                </button>
                <button id="resetFilters" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-2"></i>Reset
                </button>
            </div>
        </div>
    </div>
    
    <!-- Users Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">User Accounts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" selected disabled>Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="technician">Technician</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                        <label class="form-check-label" for="isActive">Active Account</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current password">
                        <small class="form-text text-muted">Leave blank to keep current password</small>
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="" selected disabled>Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="technician">Technician</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active">
                        <label class="form-check-label" for="editIsActive">Active Account</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="updateUserBtn">Update User</button>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;" id="userAvatar">
                        <!-- Avatar will be set dynamically -->
                    </div>
                    <h5 class="mb-1" id="userName">User Name</h5>
                    <p class="text-muted" id="userRole">Role</p>
                    <span class="badge" id="userStatus">Status</span>
                </div>
                
                <div class="border-top pt-3">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Email</div>
                        <div class="col-sm-8" id="userEmail">email@example.com</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Last Login</div>
                        <div class="col-sm-8" id="userLastLogin">Never</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Created At</div>
                        <div class="col-sm-8" id="userCreatedAt">yyyy-mm-dd</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editUserBtn">Edit User</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <input type="hidden" id="deleteUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- Include ToastManager -->
<script src="/assets/js/utility/ToastManager.js"></script>

<!-- User Management JS -->
<script>
    // Initialize Toast Manager with custom options
    const userToastManager = new ToastManager({
        position: 'bottom-right',
        autoClose: 3500,
        pauseOnHover: true,
        closeOnClick: true,
        draggable: true
    });
    
    // Initialize DataTables Manager when document is ready
    $(document).ready(function() {
        // Define columns with custom rendering for status badge
        const columns = [
            { data: 'id', title: 'ID' },
            { data: 'name', title: 'Name' },
            { data: 'email', title: 'Email' },
            { 
                data: 'role', 
                title: 'Role',
                badge: {
                    type: 'primary',
                    valueMap: {
                        'admin': { type: 'dark', display: 'Admin' },
                        'technician': { type: 'info', display: 'Technician' },
                        'customer': { type: 'secondary', display: 'Customer' }
                    }
                }
            },
            { 
                data: 'status', 
                title: 'Status',
                badge: {
                    valueMap: {
                        'Active': { type: 'success', display: 'Active' },
                        'Inactive': { type: 'danger', display: 'Inactive' }
                    }
                }
            },
            { data: 'last_login', title: 'Last Login' },
        ];

        // Custom DataTables initialization with responsive features
        $('#usersTable').DataTable({
            processing: true,
            serverSide: false, // We're not using server-side processing for simplicity
            ajax: {
                url: '/admin/users/data',
                type: 'POST',
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { 
                    data: 'role',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let badgeClass = 'badge bg-primary';
                            
                            if (data === 'admin') {
                                badgeClass = 'badge bg-dark';
                            } else if (data === 'technician') {
                                badgeClass = 'badge bg-info';
                            } else if (data === 'customer') {
                                badgeClass = 'badge bg-secondary';
                            }
                            
                            return '<span class="' + badgeClass + '">' + data + '</span>';
                        }
                        return data;
                    }
                },
                { 
                    data: 'status',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let badgeClass = data === 'Active' ? 'badge bg-success' : 'badge bg-danger';
                            return '<span class="' + badgeClass + '">' + data + '</span>';
                        }
                        return data;
                    }
                },
                { data: 'last_login' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex">
                                <button class="action-icon action-icon-view view-user" data-id="${row.id}" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-icon action-icon-edit edit-user" data-id="${row.id}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-icon action-icon-delete delete-user" data-id="${row.id}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        
        
        // Filter functionality
        $('#applyFilters').on('click', function() {
            const roleFilter = $('#roleFilter').val();
            const statusFilter = $('#statusFilter').val();
            
            // Clear previous filters
            const table = $('#usersTable').DataTable();
            
            // Apply custom filtering
            $.fn.dataTable.ext.search.pop(); // Remove any existing filters
            
            if (roleFilter || statusFilter) {
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData) {
                    // Match role filter if specified
                    if (roleFilter && rowData.role !== roleFilter) {
                        return false;
                    }
                    
                    // Match status filter if specified
                    if (statusFilter && rowData.status !== statusFilter) {
                        return false;
                    }
                    
                    return true;
                });
                
                userToastManager.showInfoToast('Filters Applied', 'Table data has been filtered');
            } else {
                userToastManager.showInfoToast('Filters Removed', 'All filters have been cleared');
            }
            
            table.draw();
        });
        
        $('#resetFilters').on('click', function() {
            // Reset filter form inputs
            $('#roleFilter').val('');
            $('#statusFilter').val('');
            
            // Clear filters
            $.fn.dataTable.ext.search.pop();
            $('#usersTable').DataTable().draw();
            
            userToastManager.showInfoToast('Filters Reset', 'All filters have been cleared');
        });

        // Add User functionality
        $('#saveUserBtn').on('click', function() {
            const formData = {
                first_name: $('#firstName').val(),
                last_name: $('#lastName').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                role: $('#role').val(),
                is_active: $('#isActive').is(':checked')
            };
            
            // Validate form
            if (!formData.first_name || !formData.last_name || !formData.email || !formData.password || !formData.role) {
                userToastManager.showErrorToast('Validation Error', 'Please fill all required fields');
                return;
            }
            
            // Send AJAX request to create user
            $.ajax({
                url: '/admin/users/create',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        // Refresh the table to show the new user
                        $('#usersTable').DataTable().ajax.reload();
                        
                        // Reset form and close modal
                        $('#addUserForm')[0].reset();
                        $('#addUserModal').modal('hide');
                        
                        // Show success toast
                        userToastManager.showSuccessToast('Success', response.message);
                    } else {
                        userToastManager.showErrorToast('Error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || { message: 'An error occurred while creating user' };
                    userToastManager.showErrorToast('Error', response.message);
                }
            });
        });

        // Edit User functionality
        $('#updateUserBtn').on('click', function() {
            const userId = $('#editUserId').val();
            const formData = {
                first_name: $('#editFirstName').val(),
                last_name: $('#editLastName').val(),
                email: $('#editEmail').val(),
                password: $('#editPassword').val(), // Will be empty if unchanged
                role: $('#editRole').val(),
                is_active: $('#editIsActive').is(':checked')
            };
            
            // Validate form
            if (!formData.first_name || !formData.last_name || !formData.email || !formData.role) {
                userToastManager.showErrorToast('Validation Error', 'Please fill all required fields');
                return;
            }
            
            // Remove password if empty (means unchanged)
            if (!formData.password) {
                delete formData.password;
            }
            
            // Send AJAX request to update user
            $.ajax({
                url: `/admin/users/update/${userId}`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        // Refresh the table to show the updated user
                        $('#usersTable').DataTable().ajax.reload();
                        
                        // Close modal
                        $('#editUserModal').modal('hide');
                        
                        // Show success toast
                        userToastManager.showSuccessToast('Success', response.message);
                    } else {
                        userToastManager.showErrorToast('Error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || { message: 'An error occurred while updating user' };
                    userToastManager.showErrorToast('Error', response.message);
                }
            });
        });

        // Confirm Delete button
        $('#confirmDeleteBtn').on('click', function() {
            const userId = $('#deleteUserId').val();
            
            // Send AJAX request to delete user
            $.ajax({
                url: `/admin/users/delete/${userId}`,
                type: 'POST',
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        // Refresh the table after deletion
                        $('#usersTable').DataTable().ajax.reload();
                        
                        // Close modal
                        $('#deleteConfirmModal').modal('hide');
                        
                        // Show success toast
                        userToastManager.showSuccessToast('Success', response.message);
                    } else {
                        userToastManager.showErrorToast('Error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || { message: 'An error occurred while deleting user' };
                    userToastManager.showErrorToast('Error', response.message);
                }
            });
        });

        // View User button in modal
        $('#editUserBtn').on('click', function() {
            const userId = $('#viewUserModal').data('userId');
            
            // Close view modal
            $('#viewUserModal').modal('hide');
            
            // Get user data for edit modal
            fetchUserData(userId, function(userData) {
                populateEditForm(userData);
                $('#editUserModal').modal('show');
            });
        });

        // --- Helper functions ---

        // Add event listeners for the action buttons
        $('#usersTable').on('click', '.view-user', function() {
            const userId = $(this).data('id');
            handleViewUser({ id: userId });
        });
        
        $('#usersTable').on('click', '.edit-user', function() {
            const userId = $(this).data('id');
            handleEditUser({ id: userId });
        });
        
        $('#usersTable').on('click', '.delete-user', function() {
            const userId = $(this).data('id');
            handleDeleteUser({ id: userId });
        });

        // Handle View User callback
        function handleViewUser(userData) {
            // Fetch full user details if needed
            fetchUserData(userData.id, function(user) {
                // Store user ID for edit button
                $('#viewUserModal').data('userId', user.id);
                
                // Set user details in modal
                $('#userName').text(user.first_name + ' ' + user.last_name);
                $('#userEmail').text(user.email);
                $('#userRole').text(user.role);
                $('#userLastLogin').text(user.last_login || 'Never');
                $('#userCreatedAt').text(formatDate(user.created_at));
                
                // Set status badge
                const statusBadge = $('#userStatus');
                statusBadge.text(user.status);
                
                if (user.status === 'Active') {
                    statusBadge.removeClass('bg-danger').addClass('bg-success');
                } else {
                    statusBadge.removeClass('bg-success').addClass('bg-danger');
                }
                
                // Set avatar with initials
                const initials = getInitials(user.first_name, user.last_name);
                const avatarColor = getAvatarColor(user.id);
                
                $('#userAvatar')
                    .text(initials)
                    .css('background-color', avatarColor);
                
                // Show modal
                $('#viewUserModal').modal('show');
            });
        }

        // Handle Edit User callback
        function handleEditUser(userData) {
            // Fetch full user details
            fetchUserData(userData.id, function(user) {
                populateEditForm(user);
                $('#editUserModal').modal('show');
            });
        }

        // Handle Delete User callback
        function handleDeleteUser(userData) {
            $('#deleteUserId').val(userData.id);
            $('#deleteConfirmModal').modal('show');
        }

        // Fetch user data by ID
        function fetchUserData(userId, callback) {
            $.ajax({
                url: `/admin/users/get/${userId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        callback(response.data);
                    } else {
                        userToastManager.showErrorToast('Error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || { message: 'An error occurred while fetching user data' };
                    userToastManager.showErrorToast('Error', response.message);
                }
            });
        }

        // Populate edit form with user data
        function populateEditForm(user) {
            $('#editUserId').val(user.id);
            $('#editFirstName').val(user.first_name);
            $('#editLastName').val(user.last_name);
            $('#editEmail').val(user.email);
            $('#editPassword').val(''); // Clear password field
            $('#editRole').val(user.role);
            $('#editIsActive').prop('checked', user.is_active);
        }

        // Get initials from name
        function getInitials(firstName, lastName) {
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        }

        // Generate a consistent color based on user ID
        function getAvatarColor(userId) {
            const colors = [
                '#007bff', '#28a745', '#dc3545', '#fd7e14', '#6f42c1',
                '#e83e8c', '#17a2b8', '#20c997', '#6610f2', '#ffc107'
            ];
            
            // Simple hash function to get a consistent color
            const index = parseInt(userId) % colors.length;
            return colors[Math.abs(index)];
        }

        // Format date to readable string
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
    });
</script>

<?php
// Close the output buffer and include footer
$content = ob_get_clean();

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>