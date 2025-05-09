/**
 * Initialize the User Management page
 * This script should be included at the bottom of the user-management.php file
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configure toastr options
    toastr.options = {
        closeButton: true,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-bottom-right",
        preventDuplicates: false,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };
    
    // Load the User Management script
    const script = document.createElement('script');
    script.src = '/assets/js/utility/user-management.js';
    document.body.appendChild(script);
});