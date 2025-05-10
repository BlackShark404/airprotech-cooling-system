document.addEventListener("DOMContentLoaded", function () {
    // Initialize DataTablesManager with improved configuration
    const userTableManager = new DataTablesManager("usersTable", {
      ajaxUrl: "/api/users",
      responsive: true,
      dom: "Bfrtip", // Properly display buttons
      autoWidth: false, // Disable auto width calculation
      buttons: [
        {
          extend: "copy",
          className: "btn btn-sm btn-light me-1",
        },
        {
          extend: "csv",
          className: "btn btn-sm btn-light me-1",
        },
        {
          extend: "excel",
          className: "btn btn-sm btn-light me-1",
        },
        {
          extend: "pdf",
          className: "btn btn-sm btn-light me-1",
        },
      ],
  
  
      //This is for add  customer
      columns: [
        { data: "id", title: "ID" },
        {
          // Combined name column
          data: null,
          title: "Name",
          render: function (data, type, row) {
            return `<div class="d-flex align-items-center">
                          <div class="avatar-placeholder d-flex align-items-center justify-content-center me-2 rounded-circle bg-light text-primary" 
                              style="width: 36px; height: 36px; font-size: 14px;">
                              ${row.first_name.charAt(0)}${row.last_name.charAt(
              0
            )}
                          </div>
                          <div>
                              <p class="mb-0 fw-medium">${row.first_name} ${
              row.last_name
            }</p>
                          </div>
                      </div>`;
          },
        },
        { data: "email", title: "Email" },
        {
          data: "role",
          title: "Role",
          render: function (data, type, row) {
            let badgeClass = " ";
            if (data === "admin") {
              badgeClass = "badge-admin";
            } else if (data === "technician") {
              badgeClass = "badge-technician";
            } else if (data === "customer") {
              badgeClass = "badge-customer";
            }
  
            return `<span class="badge ${badgeClass}">${
              data.charAt(0).toUpperCase() + data.slice(1)
            }</span>`;
          },
        },
        {
          data: "status",
          title: "Status",
          render: function (data, type, row) {
            const isActive = data === "active";
            const indicatorClass = isActive ? "status-active" : "status-inactive";
            const badgeClass = isActive ? "badge-active" : "badge-inactive";
  
            return `<span class="badge ${badgeClass}">
                          <span class="status-indicator ${indicatorClass}"></span>
                          ${data.charAt(0).toUpperCase() + data.slice(1)}
                      </span>`;
          },
        },
        {
          data: "registered",
          title: "Registered",
          render: function (data, type, row) {
            return `<span class="text-nowrap"><i class="bi bi-calendar3 me-1 text-muted"></i>${data}</span>`;
          },
        },
        {
          data: "last_login",
          title: "Last Login",
          render: function (data, type, row) {
            if (!data) {
              return '<span class="text-muted">Never</span>';
            }
            return `<span class="text-nowrap"><i class="bi bi-clock me-1 text-muted"></i>${data}</span>`;
          },
        },
      ],
      // View user callback
      viewRowCallback: function (rowData, tableManager) {
        // Set user initials
        const initials =
          rowData.first_name.charAt(0) + rowData.last_name.charAt(0);
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
        const statusBadgeClass =
          rowData.status === "active" ? "bg-success" : "bg-danger";
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
        $("#viewUserLastActivity").text(
          rowData.last_activity || "No recent activity"
        );
  
        // Update progress bars based on data
        const loginPercent = Math.min(100, (rowData.logins || 0) * 3);
        const servicesPercent = Math.min(100, (rowData.services || 0) * 10);
        const activeServicesPercent = Math.min(
          100,
          (rowData.active_services || 0) * 20
        );
  
        $(".progress-bar")
          .eq(0)
          .css("width", loginPercent + "%");
        $(".progress-bar")
          .eq(1)
          .css("width", servicesPercent + "%");
        $(".progress-bar")
          .eq(2)
          .css("width", activeServicesPercent + "%");
  
        // Show the modal
        const viewModal = new bootstrap.Modal(
          document.getElementById("viewUserModal")
        );
        viewModal.show();
  
        // Setup edit button in view modal
        $("#viewUserEditBtn")
          .off("click")
          .on("click", function () {
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
  
      // Delete user callback
      deleteRowCallback: function (rowData, tableManager) {
        // Set user info in delete confirmation modal
        $("#deleteUserName").text(rowData.first_name + " " + rowData.last_name);
  
        // Reset checkbox
        $("#confirmDeleteCheck").prop("checked", false);
        $("#confirmDeleteBtn").prop("disabled", true);
  
        // Show delete confirmation modal
        const deleteModal = new bootstrap.Modal(
          document.getElementById("deleteConfirmModal")
        );
        deleteModal.show();
  
        // Setup confirm delete button
        $("#confirmDeleteBtn")
          .off("click")
          .on("click", function () {
            // Call delete API
            $.ajax({
              url: `/api/users/${rowData.id}`,
              method: "DELETE",
              contentType: "application/json",
              success: function (response) {
                if (response.success) {
                  // Delete succeeded
                  tableManager.deleteRow(rowData.id);
                  deleteModal.hide();
  
                  // Show success message
                  tableManager.showSuccessToast("User Deleted", response.message);
  
                  // Update user count
                  updateUserCount();
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
  
      // After data loaded callback to update user count
      afterDataLoadedCallback: function (data) {
        updateUserCount();
      },
    });
  
    // FAILSAFE: Fix duplicate Actions headers after table initialization
    setTimeout(function () {
      // Find all column headers with the text "Actions"
      const actionHeaders = $("#usersTable thead th").filter(function () {
        return $(this).text().trim() === "Actions";
      });
  
      // If there are duplicate Actions headers, remove all but the first one
      if (actionHeaders.length > 1) {
        console.log("Fixing duplicate Actions headers...");
        actionHeaders.not(":first").remove();
  
        // Force DataTables to redraw the table
        try {
          $("#usersTable").DataTable().columns.adjust().draw();
        } catch (e) {
          console.error("Error redrawing table:", e);
        }
      }
    }, 500);
    // Function to update user count
    function updateUserCount() {
      const table = $("#usersTable").DataTable();
      const filteredData = table.rows({ search: "applied" }).data();
      $("#userCount").text(filteredData.length + " Users");
    }
  
    // Apply filters functionality
    $("#applyFilters").on("click", function () {
      applyTableFilters();
    });
  
    // Apply filters to table
    function applyTableFilters() {
      const roleFilter = $("#roleFilter").val();
      const statusFilter = $("#statusFilter").val();
      const searchQuery = $("#searchInput").val();
  
      // Apply filters
      const filters = {};
      if (roleFilter) filters.role = roleFilter;
      if (statusFilter) filters.status = statusFilter;
  
      userTableManager.applyFilters(filters);
  
      // Apply search if provided
      const table = $("#usersTable").DataTable();
      table.search(searchQuery).draw();
  
      // Show info toast
      userTableManager.showInfoToast(
        "Filters Applied",
        "Table has been filtered"
      );
  
      // Update user count
      updateUserCount();
    }
  
    // Reset filters
    $("#resetFilters").on("click", function () {
      // Reset filter selects
      $("#roleFilter").val("");
      $("#statusFilter").val("");
      $("#searchInput").val("");
  
      // Clear filters
      userTableManager.applyFilters({});
  
      // Clear search
      const table = $("#usersTable").DataTable();
      table.search("").draw();
  
      // Show info toast
      userTableManager.showInfoToast(
        "Filters Reset",
        "All filters have been cleared"
      );
  
      // Update user count
      updateUserCount();
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
      const roleId = parseInt($("#role_id").val()); // Ensure roleId is an integer
      const isActive = parseInt($("#is_active").val()); // Ensure isActive is an integer
  
      // Simple validation
      if (!firstName || !lastName || !email || !password || !roleId) {
        userTableManager.showErrorToast(
          "Validation Error",
          "Please fill all required fields"
        );
        return;
      }
  
      // Check passwords match
      if (password !== confirmPassword) {
        userTableManager.showErrorToast(
          "Validation Error",
          "Passwords do not match"
        );
        return;
      }
  
      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        userTableManager.showErrorToast(
          "Validation Error",
          "Please enter a valid email address"
        );
        return;
      }
  
      const formData = {
        first_name: firstName,
        last_name: lastName,
        email: email,
        password: password,
        role_id: roleId, // Now correctly as a number
        is_active: isActive, // Now correctly as a number
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
            const addModal = bootstrap.Modal.getInstance(
              document.getElementById("addUserModal")
            );
            addModal.hide();
  
            // Reset form
            $("#addUserForm")[0].reset();
  
            // Refresh table
            userTableManager.refresh();
  
            // Show success message
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
      const roleId = parseInt($("#edit_role_id").val()); // Ensure roleId is an integer
      const isActive = parseInt($("#edit_is_active").val()); // Ensure isActive is an integer
  
      // Simple validation
      if (!firstName || !lastName || !email || !roleId) {
        userTableManager.showErrorToast(
          "Validation Error",
          "Please fill all required fields"
        );
        return;
      }
  
      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        userTableManager.showErrorToast(
          "Validation Error",
          "Please enter a valid email address"
        );
        return;
      }
  
      // Prepare form data
      const formData = {
        first_name: firstName,
        last_name: lastName,
        email: email,
        role_id: roleId, // Now correctly as a number
        is_active: isActive, // Now correctly as a number
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
            const editModal = bootstrap.Modal.getInstance(
              document.getElementById("editUserModal")
            );
            editModal.hide();
  
            // Refresh table
            userTableManager.refresh();
  
            // Show success message
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
  
      // FIXED: Properly map role_id based on the actual database values
      // Make sure these match your actual database values in USER_ROLE table
      let roleId;
      if (rowData.role === "admin") {
        roleId = "3"; // ID for admin in your database
      } else if (rowData.role === "technician") {
        roleId = "2"; // ID for technician in your database
      } else if (rowData.role === "customer") {
        roleId = "1"; // ID for customer in your database
      } else {
        roleId = "1"; // Default to customer
      }
  
      console.log("Setting role dropdown for: " + rowData.role + " with value: " + roleId);
  
      // Set the dropdown value
      $("#edit_role_id").val(roleId);
  
      // Set status
      $("#edit_is_active").val(rowData.status === "active" ? "1" : "0");
  
      // Clear password field (for security)
      $("#edit_password").val("");
  
      // Show the edit modal
      const editModal = new bootstrap.Modal(
        document.getElementById("editUserModal")
      );
      editModal.show();
  
      // Diagnostic check after modal is shown
      setTimeout(function() {
        console.log("After modal shown, role value is: " + $("#edit_role_id").val());
        console.log("Selected option text: " + $("#edit_role_id option:selected").text());
      }, 100);
    }
  
    // Toggle password visibility
    $("#togglePassword").on("click", function () {
      const passwordField = $("#password");
      const type =
        passwordField.attr("type") === "password" ? "text" : "password";
      passwordField.attr("type", type);
      $(this).find("i").toggleClass("bi-eye bi-eye-slash");
    });
  
    $("#toggleConfirmPassword").on("click", function () {
      const passwordField = $("#confirm_password");
      const type =
        passwordField.attr("type") === "password" ? "text" : "password";
      passwordField.attr("type", type);
      $(this).find("i").toggleClass("bi-eye bi-eye-slash");
    });
  
    $("#toggleEditPassword").on("click", function () {
      const passwordField = $("#edit_password");
      const type =
        passwordField.attr("type") === "password" ? "text" : "password";
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
  
      // Show info toast
      userTableManager.showInfoToast(
        "Export Started",
        `Exporting users as ${format.toUpperCase()}`
      );
    };
  });