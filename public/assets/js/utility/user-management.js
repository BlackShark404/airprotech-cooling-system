/**
 * User Management JavaScript
 * Handles user management functionality with DataTablesManager
 */
$(document).ready(function() {
  // Initialize toast manager for notifications
  const toastManager = {
      showSuccess: function(title, message) {
          toastr.success(message, title);
      },
      showError: function(title, message) {
          toastr.error(message, title);
      },
      showWarning: function(title, message) {
          toastr.warning(message, title);
      },
      showInfo: function(title, message) {
          toastr.info(message, title);
      }
  };

  // Initialize DataTablesManager
  const userTableManager = new DataTablesManager('usersTable', {
      ajaxUrl: '/admin/users/data',
      columns: [
          { data: 'id', title: 'ID' },
          { data: 'name', title: 'Name' },
          { data: 'email', title: 'Email' },
          { 
              data: 'role', 
              title: 'Role',
              badge: {
                  valueMap: {
                      'admin': { type: 'danger', display: 'Admin' },
                      'technician': { type: 'warning', display: 'Technician' },
                      'customer': { type: 'info', display: 'Customer' }
                  }
              }
          },
          { 
              data: 'status', 
              title: 'Status',
              badge: {
                  valueMap: {
                      'Active': { type: 'success', display: 'Active' },
                      'Inactive': { type: 'secondary', display: 'Inactive' }
                  }
              }
          },
          { data: 'last_login', title: 'Last Login' }
      ],
      viewRowCallback: viewUser,
      editRowCallback: editUser,
      deleteRowCallback: confirmDeleteUser
  });

  // Apply filters button click handler
  $('#applyFilters').on('click', function() {
      applyFilters();
  });

  // Reset filters button click handler
  $('#resetFilters').on('click', function() {
      $('#roleFilter').val('');
      $('#statusFilter').val('');
      applyFilters();
  });

  // Save user button click handler
  $('#saveUserBtn').on('click', function() {
      saveUser();
  });

  // Apply filters function
  function applyFilters() {
      const roleFilter = $('#roleFilter').val();
      const statusFilter = $('#statusFilter').val();
      
      // Reload table with filters
      userTableManager.dataTable.ajax.reload(null, false);
      
      // Pass filters to the server via AJAX
      $.fn.dataTable.ext.ajax.data = function(data) {
          data.role = roleFilter;
          data.status = statusFilter;
          return data;
      };
  }

  // View user function
  function viewUser(userData) {
      // Create view user modal if it doesn't exist
      if ($('#viewUserModal').length === 0) {
          const modalHtml = `
              <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                              <div class="row mb-3">
                                  <div class="col-md-6">
                                      <p class="mb-1 text-muted">User ID</p>
                                      <p class="fw-bold" id="viewUserId"></p>
                                  </div>
                                  <div class="col-md-6">
                                      <p class="mb-1 text-muted">Status</p>
                                      <p id="viewUserStatus"></p>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <div class="col-md-6">
                                      <p class="mb-1 text-muted">First Name</p>
                                      <p class="fw-bold" id="viewUserFirstName"></p>
                                  </div>
                                  <div class="col-md-6">
                                      <p class="mb-1 text-muted">Last Name</p>
                                      <p class="fw-bold" id="viewUserLastName"></p>
                                  </div>
                              </div>
                              <div class="mb-3">
                                  <p class="mb-1 text-muted">Email</p>
                                  <p class="fw-bold" id="viewUserEmail"></p>
                              </div>
                              <div class="mb-3">
                                  <p class="mb-1 text-muted">Phone</p>
                                  <p class="fw-bold" id="viewUserPhone"></p>
                              </div>
                              <div class="mb-3">
                                  <p class="mb-1 text-muted">Address</p>
                                  <p class="fw-bold" id="viewUserAddress"></p>
                              </div>
                              <div class="mb-3">
                                  <p class="mb-1 text-muted">Role</p>
                                  <p class="fw-bold" id="viewUserRole"></p>
                              </div>
                              <div class="row mb-3">
                                  <div class="col-md-6">
                                      <p class="mb-1 text-muted">Last Login</p>
                                      <p class="fw-bold" id="viewUserLastLogin"></p>
                                  </div>
                                  <div class="col-md-6">
                                      <p class="mb-1 text-muted">Created At</p>
                                      <p class="fw-bold" id="viewUserCreatedAt"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="button" class="btn btn-primary" id="editUserFromViewBtn">Edit User</button>
                          </div>
                      </div>
                  </div>
              </div>
          `;
          
          $('body').append(modalHtml);
          
          // Edit user from view modal button click handler
          $('#editUserFromViewBtn').on('click', function() {
              $('#viewUserModal').modal('hide');
              editUser(userData);
          });
      }
      
      // Get full user details from server
      $.ajax({
          url: `/admin/users/get/${userData.id}`,
          type: 'GET',
          dataType: 'json',
          success: function(response) {
              const user = response.data;
              
              // Populate user details in the modal
              $('#viewUserId').text(user.id);
              $('#viewUserFirstName').text(user.first_name);
              $('#viewUserLastName').text(user.last_name);
              $('#viewUserEmail').text(user.email);
              $('#viewUserPhone').text(user.phone || 'Not specified');
              $('#viewUserAddress').text(user.address || 'Not specified');
              $('#viewUserRole').text(user.role);
              $('#viewUserStatus').html(user.status === 'Active' 
                  ? '<span class="badge bg-success">Active</span>' 
                  : '<span class="badge bg-secondary">Inactive</span>');
              $('#viewUserLastLogin').text(user.last_login);
              $('#viewUserCreatedAt').text(user.created_at);
              
              // Store user data for edit button
              $('#editUserFromViewBtn').data('user', user);
              
              // Show the modal
              $('#viewUserModal').modal('show');
          },
          error: function(xhr) {
              const response = xhr.responseJSON || {};
              toastManager.showError('Error', response.message || 'Failed to load user details');
          }
      });
  }

  // Edit user function
  function editUser(userData) {
      // Create edit user modal if it doesn't exist
      if ($('#editUserModal').length === 0) {
          const modalHtml = `
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
                                      <label for="editPhone" class="form-label">Phone</label>
                                      <input type="text" class="form-control" id="editPhone" name="phone">
                                  </div>
                                  <div class="mb-3">
                                      <label for="editAddress" class="form-label">Address</label>
                                      <textarea class="form-control" id="editAddress" name="address" rows="2"></textarea>
                                  </div>
                                  <div class="mb-3">
                                      <label for="editPassword" class="form-label">Password</label>
                                      <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current password">
                                      <small class="form-text text-muted">Leave blank to keep the current password.</small>
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
                              <button type="button" class="btn btn-primary" id="updateUserBtn">Update User</button>
                          </div>
                      </div>
                  </div>
              </div>
          `;
          
          $('body').append(modalHtml);
          
          // Update user button click handler
          $('#updateUserBtn').on('click', function() {
              updateUser();
          });
      }
      
      // Get full user details from server
      $.ajax({
          url: `/admin/users/get/${userData.id}`,
          type: 'GET',
          dataType: 'json',
          success: function(response) {
              const user = response.data;
              
              // Populate user details in the form
              $('#editUserId').val(user.id);
              $('#editFirstName').val(user.first_name);
              $('#editLastName').val(user.last_name);
              $('#editEmail').val(user.email);
              $('#editPhone').val(user.phone || '');
              $('#editAddress').val(user.address || '');
              $('#editPassword').val(''); // Clear password field
              $('#editRole').val(user.role);
              $('#editIsActive').prop('checked', user.is_active);
              
              // Show the modal
              $('#editUserModal').modal('show');
          },
          error: function(xhr) {
              const response = xhr.responseJSON || {};
              toastManager.showError('Error', response.message || 'Failed to load user details');
          }
      });
  }

  // Update user function
  function updateUser() {
      // Get form data
      const userId = $('#editUserId').val();
      const formData = {
          first_name: $('#editFirstName').val(),
          last_name: $('#editLastName').val(),
          email: $('#editEmail').val(),
          phone: $('#editPhone').val(),
          address: $('#editAddress').val(),
          password: $('#editPassword').val(),
          role: $('#editRole').val(),
          is_active: $('#editIsActive').is(':checked')
      };
      
      // Remove empty password field
      if (!formData.password) {
          delete formData.password;
      }
      
      // Validate form data
      if (!formData.first_name || !formData.last_name || !formData.email || !formData.role) {
          toastManager.showError('Validation Error', 'Please fill in all required fields');
          return;
      }
      
      // Update user
      $.ajax({
          url: `/admin/users/update/${userId}`,
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(formData),
          dataType: 'json',
          success: function(response) {
              // Close modal
              $('#editUserModal').modal('hide');
              
              // Show success message
              toastManager.showSuccess('Success', 'User updated successfully');
              
              // Refresh the table
              userTableManager.refresh();
          },
          error: function(xhr) {
              const response = xhr.responseJSON || {};
              
              if (response.data && response.data.errors) {
                  // Show validation errors
                  let errorMessage = '<ul class="mb-0">';
                  for (const field in response.data.errors) {
                      errorMessage += `<li>${response.data.errors[field]}</li>`;
                  }
                  errorMessage += '</ul>';
                  
                  toastManager.showError('Validation Error', errorMessage);
              } else {
                  toastManager.showError('Error', response.message || 'Failed to update user');
              }
          }
      });
  }

  // Save new user function
  function saveUser() {
      // Get form data
      const formData = {
          first_name: $('#firstName').val(),
          last_name: $('#lastName').val(),
          email: $('#email').val(),
          password: $('#password').val(),
          role: $('#role').val(),
          is_active: $('#isActive').is(':checked')
      };
      
      // Validate form data
      if (!formData.first_name || !formData.last_name || !formData.email || !formData.password || !formData.role) {
          toastManager.showError('Validation Error', 'Please fill in all required fields');
          return;
      }
      
      // Create user
      $.ajax({
          url: '/admin/users/create',
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(formData),
          dataType: 'json',
          success: function(response) {
              // Clear form
              $('#addUserForm')[0].reset();
              
              // Close modal
              $('#addUserModal').modal('hide');
              
              // Show success message
              toastManager.showSuccess('Success', 'User created successfully');
              
              // Refresh the table
              userTableManager.refresh();
          },
          error: function(xhr) {
              const response = xhr.responseJSON || {};
              
              if (response.data && response.data.errors) {
                  // Show validation errors
                  let errorMessage = '<ul class="mb-0">';
                  for (const field in response.data.errors) {
                      errorMessage += `<li>${response.data.errors[field]}</li>`;
                  }
                  errorMessage += '</ul>';
                  
                  toastManager.showError('Validation Error', errorMessage);
              } else {
                  toastManager.showError('Error', response.message || 'Failed to create user');
              }
          }
      });
  }

  // Confirm delete user function
  function confirmDeleteUser(userData) {
      // Create a confirmation dialog
      if (confirm(`Are you sure you want to delete user "${userData.name}" (ID: ${userData.id})?`)) {
          deleteUser(userData.id);
      }
  }

  // Delete user function
  function deleteUser(userId) {
      $.ajax({
          url: `/admin/users/delete/${userId}`,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
              // Show success message
              toastManager.showSuccess('Success', 'User deleted successfully');
              
              // Refresh the table
              userTableManager.refresh();
          },
          error: function(xhr) {
              const response = xhr.responseJSON || {};
              toastManager.showError('Error', response.message || 'Failed to delete user');
          }
      });

    }