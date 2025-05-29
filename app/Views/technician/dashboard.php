<?php
$title = $title ?? 'Technician Dashboard - AirProtech';
$activeTab = 'dashboard'; // For potential sidebar navigation highlighting

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    body {
        background-color: #f4f7f6; /* Light gray background for the page */
    }
    .dashboard-header {
        background-color: #007bff; /* Blue header */
        color: white;
        padding: 20px 15px;
        border-radius: 0 0 8px 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .dashboard-header h1 {
        margin: 0;
        font-size: 1.8rem;
    }
    .dashboard-header p {
        margin: 5px 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .service-request-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        transition: all 0.3s ease-in-out;
    }
    .service-request-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
        padding: 0.75rem 1.25rem;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .card-header .badge {
        font-size: 0.8em;
    }
    .card-body {
        padding: 1.25rem;
    }
    .card-body p {
        margin-bottom: 0.5rem;
    }
    .card-body strong {
        color: #343a40;
    }
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 0.75rem 1.25rem;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .notes-textarea {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 0.5rem;
        width: 100%;
        min-height: 80px;
        margin-top: 5px;
        font-size: 0.9rem;
    }
    .status-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 0.5rem 1rem;
        width: auto;
        min-width: 150px;
        margin-right: 10px;
    }
    .badge-assigned {
        background-color: #17a2b8; /* Info */
        color: white;
    }
    .badge-in-progress {
        background-color: #007bff; /* Primary */
        color: white;
    }
    .badge-completed {
        background-color: #28a745; /* Success */
        color: white;
    }
    .badge-on-hold {
        background-color: #ffc107; /* Warning */
        color: #212529;
    }
    .badge-needs-parts {
        background-color: #fd7e14; /* Orange */
        color: white;
    }
    .badge-cancelled {
        background-color: #dc3545; /* Danger */
        color: white;
    }
    .badge-pending {
        background-color: #6c757d; /* Secondary */
        color: white;
    }
    .badge-confirmed {
        background-color: #17a2b8; /* Info */
        color: white;
    }
    #noAssignmentsMessage {
        text-align: center;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .action-buttons .btn {
        margin-top: 10px;
    }
</style>
HTML;

ob_start();
?>

<div class="container-fluid py-3">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($technicianName ?? 'Technician'); ?>!</h1>
        <p>Here are your currently assigned service requests.</p>
    </div>

    <div id="serviceRequestsContainer" class="row">
        <!-- Service requests will be loaded here by JavaScript -->
    </div>
    <div id="noAssignmentsMessage" class="d-none">
        <p class="lead">You have no service requests assigned to you at the moment.</p>
    </div>

</div>

<!-- Include jQuery first -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- Toast Notifications (assuming you have this utility) -->
<!-- <script src="/assets/js/utility/toast-notifications.js"></script> -->

<script>
$(document).ready(function() {
    const technicianId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
    const toastManager = new ToastNotifications(); 

    function getStatusBadgeClass(status) {
        const statusLower = status ? status.toLowerCase().replace(/\s+/g, '-') : 'default';
        return `badge-${statusLower}`;
    }

    function loadAssignedServiceRequests() {
        $.ajax({
            url: '/api/technician/service-requests',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const container = $('#serviceRequestsContainer');
                const noAssignmentsMessage = $('#noAssignmentsMessage');
                container.empty(); // Clear previous entries

                if (response.success && response.data && response.data.length > 0) {
                    noAssignmentsMessage.addClass('d-none');
                    response.data.forEach(function(request) {
                        const cardHtml = `
                            <div class="col-md-6 col-lg-4">
                                <div class="service-request-card" data-booking-id="${request.sb_id}" data-assignment-id="${request.ba_id}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span>Request ID: ${request.sb_id}</span>
                                        <span class="badge ${getStatusBadgeClass(request.assignment_status)}">${request.assignment_status ? request.assignment_status.replace('-', ' ').toUpperCase() : 'N/A'}</span>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Customer:</strong> ${request.customer_name || 'N/A'}</p>
                                        <p><strong>Service:</strong> ${request.service_name || 'N/A'}</p>
                                        <p><strong>Address:</strong> ${request.sb_address || 'N/A'}</p>
                                        <p><strong>Preferred Date:</strong> ${request.sb_preferred_date || 'N/A'}</p>
                                        <p><strong>Preferred Time:</strong> ${request.sb_preferred_time || 'N/A'}</p>
                                        <p><strong>Overall Status:</strong> <span class="badge ${getStatusBadgeClass(request.sb_status)}">${request.sb_status ? request.sb_status.toUpperCase() : 'N/A'}</span></p>
                                        <hr>
                                        <p><strong>Your Notes:</strong></p>
                                        <textarea class="form-control notes-textarea technician-notes" placeholder="Add your notes here...">${request.technician_notes || ''}</textarea>
                                    </div>
                                    <div class="card-footer action-buttons">
                                        <label for="status-select-${request.ba_id}" class="form-label me-2">Update Status:</label>
                                        <select class="form-select status-select assignment-status-select" id="status-select-${request.ba_id}">
                                            <option value="assigned" ${request.assignment_status === 'assigned' ? 'selected' : ''}>Assigned</option>
                                            <option value="in-progress" ${request.assignment_status === 'in-progress' ? 'selected' : ''}>In Progress</option>
                                            <option value="on-hold" ${request.assignment_status === 'on-hold' ? 'selected' : ''}>On Hold</option>
                                            <option value="needs-parts" ${request.assignment_status === 'needs-parts' ? 'selected' : ''}>Needs Parts</option>
                                            <option value="completed" ${request.assignment_status === 'completed' ? 'selected' : ''}>Completed</option>
                                        </select>
                                        <button class="btn btn-primary btn-sm update-assignment-btn">Update Assignment</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(cardHtml);
                    });
                } else {
                    noAssignmentsMessage.removeClass('d-none');
                    if (!response.success) {
                        toastManager.showErrorToast('Error', response.message || 'Failed to load assigned service requests.');
                    }
                }
            },
            error: function(xhr) {
                toastManager.showErrorToast('Error', 'An error occurred while fetching your assignments.');
                noAssignmentsMessage.removeClass('d-none');
            }
        });
    }

    // Handle assignment update
    $('#serviceRequestsContainer').on('click', '.update-assignment-btn', function() {
        const card = $(this).closest('.service-request-card');
        const bookingId = card.data('booking-id');
        const assignmentId = card.data('assignment-id');
        const newStatus = card.find('.assignment-status-select').val();
        const notes = card.find('.technician-notes').val();

        if (!newStatus) {
            toastManager.showWarningToast('Warning', 'Please select a status to update.');
            return;
        }

        $.ajax({
            url: '/api/technician/service-assignment/update',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                bookingId: bookingId,
                assignmentId: assignmentId,
                status: newStatus,
                notes: notes
            }),
            success: function(response) {
                if (response.success) {
                    toastManager.showSuccessToast('Success', response.message || 'Assignment updated successfully.');
                    loadAssignedServiceRequests(); // Refresh the list
                } else {
                    toastManager.showErrorToast('Error', response.message || 'Failed to update assignment.');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred while updating the assignment.';
                toastManager.showErrorToast('Error', errorMsg);
            }
        });
    });

    // Initial load
    if (technicianId) {
      loadAssignedServiceRequests();
    } else {
        toastManager.showErrorToast('Error', 'Technician ID not found. Cannot load assignments.');
        $('#noAssignmentsMessage').removeClass('d-none').find('p').text('Could not verify technician identity.');
    }
});
</script>

<?php
$content = ob_get_clean();

// Include the base template (assuming you have a base template for technicians or can use the admin one)
// You might need to create a specific base for technicians if the layout differs significantly
$baseTemplate = __DIR__ . '/../includes/technician/base.php'; // UPDATED to technician base
if (file_exists($baseTemplate)) {
    include $baseTemplate;
} else {
    // Fallback if the base template is not found
    echo "<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$title}</title>
    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\">
    {$additionalStyles}
</head>
<body>
    <div class=\"container\">
        {$content}
    </div>
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
</body>
</html>";
}
?> 