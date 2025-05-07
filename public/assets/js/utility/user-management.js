document.addEventListener("DOMContentLoaded", function () {
  // Initialize DataTablesManager with enhanced configuration
  const userTableManager = new DataTablesManager("usersTable", {
    ajaxUrl: "/api/users",
    columns: [
      { data: "id", title: "ID" },
      {
        // Combined name column with avatar placeholder
        data: null,
        title: "Name",
        render: function (data, type, row) {
          // For sorting and filtering, use raw data
          if (type === 'sort' || type === 'filter') {
            return row.first_name + ' ' + row.last_name;
          }
          
          return `<div class="d-flex align-items-center">
                    <div class="avatar-placeholder d-flex align-items-center justify-content-center me-2 rounded-circle bg-light text-primary" 
                        style="width: 36px; height: 36px; font-size: 14px;">
                        ${row.first_name.charAt(0)}${row.last_name.charAt(0)}
                    </div>
                    <div>
                        <p class="mb-0 fw-medium">${row.first_name} ${row.last_name}</p>
                    </div>
                </div>`;
        },
      },
      { data: "email", title: "Email" },
      {
        data: "role",
        title: "Role",
        // Using the new badge configuration
        badge: {
          type: 'primary',
          pill: true,
          valueMap: {
            'admin': { type: 'danger', display: 'Admin' },
            'technician': { type: 'primary', display: 'Technician' },
            'customer': { type: 'success', display: 'Customer' }
          }
        }
      },
      {
        data: "status",
        title: "Status",
        // Using the new badge configuration
        badge: {
          type: 'secondary',
          pill: true,
          valueMap: {
            'active': { type: 'success', display: 'Active' },
            'inactive': { type: 'danger', display: 'Inactive' }
          }
        }
      },
      {
        data: "registered",
        title: "Registered",
        render: function (data, type, row) {
          // For sorting and filtering, use raw data
          if (type === 'sort' || type === 'filter') {
            return data;
          }
          return `<span class="text-nowrap"><i class="bi bi-calendar3 me-1 text-muted"></i>${data}</span>`;
        },
      },
      {
        data: "last_login",
        title: "Last Login",
        render: function (data, type, row) {
          // For sorting and filtering, use raw data
          if (type === 'sort' || type === 'filter') {
            return data || '';
          }
          
          if (!data) {
            return '<span class="text-muted">Never</span>';
          }
          return `<span class="text-nowrap"><i class="bi bi-clock me-1 text-muted"></i>${data}</span>`;
        },
      },
    ],
    // Improved toast options
    toastOptions: {
      position: 'bottom-right',
      autoClose: 3000,
      hideProgressBar: false,
      closeOnClick: true,
      pauseOnHover: true,
      draggable: true,
      enableIcons: true
    },
    // View user callback
    viewRowCallback: function (rowData, tableManager) {
      // Set user initials
      const initials = rowData.first_name.charAt(0) + rowData.last_name.charAt(0);
      $("#userInitials").text(initials);

      // Populate the view modal with user data
      $("#viewUserId").text(rowData.id);
      $("#viewUserName").text(rowData.first_name + " " + rowData.last_name);
      $("#viewUserEmail").text(rowData.email);

      // Set role with badge
      let roleBadgeClass = "bg-success";
      if (rowData.role === "admin") {
        roleBadgeClass = "bg-danger";
      } else if (rowData.role === "technician") {
        roleBadgeClass = "bg-primary";
      }

      $("#viewUserRole").html(
        `<span class="badge ${roleBadgeClass} rounded-pill">${
          rowData.role.charAt(0).toUpperCase() + rowData.role.slice(1)
        }</span>`
      );

      // Set status with badge
      const statusBadgeClass = rowData.status === "active" ? "bg-success" : "bg-danger";
      $("#viewUserStatus").html(
        `<span class="badge ${statusBadgeClass} rounded-pill">${
          rowData.status.charAt(0).toUpperCase() + rowData.status.slice(1)
        }</span>`
      );

      $("#viewUserRegistered").text(rowData.registered);
      $("#viewUserLastLogin").text(rowData.last_login || "Never");

      // Set activity statistics
      $("#viewUserLogins").text(rowData.logins || "0");
      $("#viewUserServices").text(rowData.services || "0");
      $("#viewUserActiveServices").text(rowData.active_services || "0");
      $("#viewUserLastActivity").text(rowData.last_activity || "No recent activity");

      // Update progress bars based on data
      const loginPercent = Math.min(100, (rowData.logins || 0) * 3);
      const servicesPercent = Math.min(100, (rowData.services || 0) * 10);
      const activeServicesPercent = Math.min(100, (rowData.active_services || 0) * 20);

      $(".progress-bar").eq(0).css("width", loginPercent + "%");
      $(".progress-bar").eq(1).css("width", servicesPercent + "%");
      $(".progress-bar").eq(2).css("width", activeServicesPercent + "%");

      // Show the modal
      const viewModal = new bootstrap.Modal(document.getElementById("viewUserModal"));
      viewModal.show();

      // Setup edit button in view modal
      $("#viewUserEditBtn").off("click").on("click", function () {
        // Hide view modal
        viewModal.hide();

        // Setup and show edit modal
        setupEditUserModal(rowData, tableManager);
      });
    },

    // Edit user callback
    editRowCallback: function (rowData, tableManager) {
      setupEditUserModal(rowData, tableManager);
    },

    // Delete user callback - uses improved delete confirmation modal
    deleteRowCallback: function (rowData, tableManager) {
      // Set user info in delete confirmation modal
      $("#deleteUserName").text(rowData.first_name + " " + rowData.last_name);

      // Reset checkbox
      $("#confirmDeleteCheck").prop("checked", false);
      $("#confirmDeleteBtn").prop("disabled", true);

      // Show delete confirmation modal
      const deleteModal = new bootstrap.Modal(document.getElementById("deleteConfirmModal"));
      deleteModal.show();

      // Setup confirm delete button
      $("#confirmDeleteBtn").off("click").on("click", function () {
        // Call delete API
        $.ajax({
          url: `/api/users/${rowData.id}`,
          method: "DELETE",
          contentType: "application/json",
          success: function (response) {
            if (response.success) {
              // Delete succeeded - use the deleteRow method of the manager
              tableManager.deleteRow(rowData.id);
              deleteModal.hide();
              
              // User count will be updated automatically in the next step
            } else {
              // Delete failed
              tableManager.showErrorToast("Error", response.message);
            }
          },
          error: function (xhr) {
            const response = xhr.responseJSON || { message: "Server error" };
            tableManager.showErrorToast("Error", response.message);
          },
        });
      });
    },

    // Custom buttons
    customButtons: {
      export: {
        text: '<i class="bi bi-download"></i> Export',
        className: 'btn btn-sm btn-outline-primary',
        action: function (e, dt, node, config) {
          // Show export format dropdown
          // Implementation depends on your UI
          $('#exportDropdown').toggle();
        }
      }
    }
  });

  // Function to update user count
  function updateUserCount() {
    const table = $("#usersTable").DataTable();
    const filteredData = table.rows({ search: "applied" }).data();
    $("#userCount").text(filteredData.length + " Users");
  }

  // Update count when data is loaded or filtered
  $("#usersTable").on('draw.dt', function() {
    updateUserCount();
  });

  // Apply filters functionality
  $("#applyFilters").on("click", function () {
    applyTableFilters();
  });

  // Apply filters to table
  function applyTableFilters() {
    const roleFilter = $("#roleFilter").val();
    const statusFilter = $("#statusFilter").val();
    const searchQuery = $("#searchInput").val();

    // Apply filters using the applyFilters method
    const filters = {};
    if (roleFilter) filters.role = roleFilter;
    if (statusFilter) filters.status = statusFilter;

    userTableManager.applyFilters(filters);

    // Apply search if provided
    const table = $("#usersTable").DataTable();
    table.search(searchQuery).draw();

    // Show info toast with the new toast system
    userTableManager.showInfoToast("Filters Applied", "Table has been filtered");
  }

  // Reset filters
  $("#resetFilters").on("click", function () {
    // Reset filter selects
    $("#roleFilter").val("");
    $("#statusFilter").val("");
    $("#searchInput").val("");

    // Clear filters using the manager
    userTableManager.applyFilters({});

    // Clear search
    const table = $("#usersTable").DataTable();
    table.search("").draw();

    // Show info toast
    userTableManager.showInfoToast("Filters Reset", "All filters have been cleared");
  });

  // Search input keyup event
  $("#searchInput").on("keyup", function (e) {
    if (e.key === "Enter") {
      applyTableFilters();
    }
  });

  // Handle add user form submission
  $("#saveUserBtn").on("click", function () {
    // Validate form
    const firstName = $("#first_name").val();
    const lastName = $("#last_name").val();
    const email = $("#email").val();
    const password = $("#password").val();
    const confirmPassword = $("#confirm_password").val();
    const roleId = parseInt($("#role_id").val());
    const isActive = parseInt($("#is_active").val());

    // Simple validation
    if (!firstName || !lastName || !email || !password || !roleId) {
      userTableManager.showErrorToast("Validation Error", "Please fill all required fields");
      return;
    }

    // Check passwords match
    if (password !== confirmPassword) {
      userTableManager.showErrorToast("Validation Error", "Passwords do not match");
      return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      userTableManager.showErrorToast("Validation Error", "Please enter a valid email address");
      return;
    }

    const formData = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      password: password,
      role_id: roleId,
      is_active: isActive
    };

    // Submit form via AJAX
    $.ajax({
      url: "/api/users",
      method: "POST",
      data: JSON.stringify(formData),
      contentType: "application/json",
      success: function (response) {
        if (response.success) {
          // Close modal
          const addModal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"));
          addModal.hide();

          // Reset form
          $("#addUserForm")[0].reset();

          // Refresh table using the manager's refresh method
          userTableManager.refresh();

          // Show success message using the manager's toast system
          userTableManager.showSuccessToast("User Added", response.message);
        } else {
          userTableManager.showErrorToast("Error", response.message);
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON || { message: "Server error" };
        userTableManager.showErrorToast("Error", response.message);
      },
    });
  });

  // Handle edit user form submission
  $("#updateUserBtn").on("click", function () {
    // Get form data
    const userId = $("#edit_user_id").val();
    const firstName = $("#edit_first_name").val();
    const lastName = $("#edit_last_name").val();
    const email = $("#edit_email").val();
    const password = $("#edit_password").val();
    const roleId = parseInt($("#edit_role_id").val());
    const isActive = parseInt($("#edit_is_active").val());

    // Simple validation
    if (!firstName || !lastName || !email || !roleId === NaN) {
      userTableManager.showErrorToast("Validation Error", "Please fill all required fields");
      return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      userTableManager.showErrorToast("Validation Error", "Please enter a valid email address");
      return;
    }

    // Prepare form data
    const formData = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      role_id: roleId,
      is_active: isActive
    };

    // Add password only if provided
    if (password) {
      formData.password = password;
    }

    // Submit form via AJAX
    $.ajax({
      url: `/api/users/${userId}`,
      method: "PUT",
      data: JSON.stringify(formData),
      contentType: "application/json",
      success: function (response) {
        if (response.success) {
          // Close modal
          const editModal = bootstrap.Modal.getInstance(document.getElementById("editUserModal"));
          editModal.hide();

          // Refresh table using the manager's method
          userTableManager.refresh();

          // Show success message using the manager's toast system
          userTableManager.showSuccessToast("User Updated", response.message);
        } else {
          userTableManager.showErrorToast("Error", response.message);
        }
      },
      error: function (xhr) {
        const response = xhr.responseJSON || { message: "Server error" };
        userTableManager.showErrorToast("Error", response.message);
      },
    });
  });

  // Function to setup edit user modal 
  function setupEditUserModal(rowData, tableManager) {
    // Set form values
    $("#edit_user_id").val(rowData.id);
    $("#edit_first_name").val(rowData.first_name);
    $("#edit_last_name").val(rowData.last_name);
    $("#edit_email").val(rowData.email);

    // Map role to role_id
    let roleId;
    if (rowData.role === "admin") {
      roleId = "3"; // ID for admin in your database
    } else if (rowData.role === "technician") {
      roleId = "2"; // ID for technician in your database
    } else {
      roleId = "1"; // Default to customer
    }

    // Set the dropdown value
    $("#edit_role_id").val(roleId);

    // Set status
    $("#edit_is_active").val(rowData.status === "active" ? "1" : "0");

    // Clear password field (for security)
    $("#edit_password").val("");

    // Show the edit modal
    const editModal = new bootstrap.Modal(document.getElementById("editUserModal"));
    editModal.show();
  }

  // Toggle password visibility
  $("#togglePassword").on("click", function () {
    const passwordField = $("#password");
    const type = passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", type);
    $(this).find("i").toggleClass("bi-eye bi-eye-slash");
  });

  $("#toggleConfirmPassword").on("click", function () {
    const passwordField = $("#confirm_password");
    const type = passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", type);
    $(this).find("i").toggleClass("bi-eye bi-eye-slash");
  });

  $("#toggleEditPassword").on("click", function () {
    const passwordField = $("#edit_password");
    const type = passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", type);
    $(this).find("i").toggleClass("bi-eye bi-eye-slash");
  });

  // Handle confirm delete checkbox
  $("#confirmDeleteCheck").on("change", function () {
    $("#confirmDeleteBtn").prop("disabled", !$(this).is(":checked"));
  });

  // Function to export users
  window.exportUsers = function (format) {
    // Get current filters
    const roleFilter = $("#roleFilter").val();
    const statusFilter = $("#statusFilter").val();

    // Build query with filters
    let queryString = `?format=${format}`;
    if (roleFilter) queryString += `&role=${roleFilter}`;
    if (statusFilter) queryString += `&status=${statusFilter}`;

    // Redirect to export API with format
    window.location.href = `/api/users/export${queryString}`;

    // Show info toast using the manager's toast system
    userTableManager.showInfoToast("Export Started", `Exporting users as ${format.toUpperCase()}`);
  };
  
  // Clean up function to handle page unload
  window.addEventListener('beforeunload', function() {
    // Destroy the DataTablesManager to prevent memory leaks
    if (userTableManager) {
      userTableManager.destroy();
    }
  });
});