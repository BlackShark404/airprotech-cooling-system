<?php
$title = 'My Profile - AirProtect';
$activeTab = '';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .profile-section {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }
    
    .profile-header {
        background-color: #007bff;
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 24px;
        position: relative;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid white;
        object-fit: cover;
        margin-bottom: 16px;
    }
    
    .profile-details {
        padding: 24px;
    }
    
    .profile-label {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 4px;
    }
    
    .profile-value {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 16px;
    }
    
    .edit-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .edit-icon:hover {
        background-color: rgba(255, 255, 255, 0.3);
    }
    
    .profile-tab-content {
        display: none;
    }
    
    .profile-tab-content.active {
        display: block;
    }
    
    .profile-nav {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 24px;
    }
    
    .profile-nav-link {
        padding: 12px 16px;
        color: #343a40;
        font-weight: 500;
        border-bottom: 2px solid transparent;
        display: inline-block;
        text-decoration: none;
        margin-right: 16px;
    }
    
    .profile-nav-link.active {
        color: #007bff;
        border-bottom-color: #007bff;
    }
    
    .profile-nav-link:hover {
        color: #007bff;
    }
    
    .btn-save {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn-cancel {
        background-color: #f8f9fa;
        color: #343a40;
        border: 1px solid #dee2e6;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 500;
        margin-right: 8px;
    }
    
    .form-label {
        font-weight: 500;
    }
    
    .activity-list {
        list-style: none;
        padding: 0;
    }
    
    .activity-item {
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
    }
    
    .activity-time {
        font-size: 12px;
        color: #6c757d;
    }
    
    /* Responsive styles */
    @media (max-width: 767.98px) {
        .profile-header {
            text-align: center;
        }
        
        .edit-icon {
            top: 16px;
            right: 16px;
        }
        
        .profile-nav {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 8px;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
        }
    }
</style>
HTML;

// Start output buffering for content
ob_start();

?>

<div class="container-fluid py-4">
    <!-- Back button -->
    <div class="mb-3">
        <a href="<?= base_url('/admin/dashboard') ?>" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <!-- Profile Header -->
    <div class="profile-section">
        <div class="profile-header">
            <div class="edit-icon" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil"></i>
            </div>
            <div class="text-center text-md-start">
                <img src="<?= $userData['profile_url'] ?>" alt="Profile Photo" class="profile-avatar">
                <h2 class="h3 mb-1"><?= $userData['first_name'] . ' ' . $userData['last_name'] ?></h2>
                <p class="mb-0"><?= $userData['role'] ?></p>
            </div>
        </div>
        
        <!-- Profile Navigation -->
        <div class="profile-nav px-3">
            <a href="#personal-info" class="profile-nav-link active" data-tab="personal-info">Personal Information</a>
            <a href="#activity" class="profile-nav-link" data-tab="activity">Activity</a>
            <a href="#security" class="profile-nav-link" data-tab="security">Security</a>
        </div>
        
        <!-- Profile Content -->
        <div class="profile-details">
            <!-- Personal Information Tab -->
            <div id="personal-info" class="profile-tab-content active">
                <div class="row">
                    <div class="col-md-6">
                        <div class="profile-label">First Name</div>
                        <div class="profile-value"><?= $userData['first_name'] ?></div>
                        
                        <div class="profile-label">Email Address</div>
                        <div class="profile-value"><?= $userData['email'] ?></div>
                        
                        <div class="profile-label">Address</div>
                        <div class="profile-value"><?= $userData['address'] ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-label">Last Name</div>
                        <div class="profile-value"><?= $userData['last_name'] ?></div>
                        
                        <div class="profile-label">Phone Number</div>
                        <div class="profile-value"><?= $userData['phone'] ?></div>
                        
                        <div class="profile-label">Account Created</div>
                        <div class="profile-value"><?= $userData['created_at'] ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Activity Tab -->
            <div id="activity" class="profile-tab-content">
                <h5 class="mb-4">Recent Activity</h5>
                <ul class="activity-list">
                    <li class="activity-item d-flex align-items-start">
                        <div class="activity-icon activity-icon-green bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Profile Updated</strong>
                                <span class="activity-time">Today at 09:34 AM</span>
                            </div>
                            <p class="mb-0 text-muted">You updated your profile information</p>
                        </div>
                    </li>
                    <li class="activity-item d-flex align-items-start">
                        <div class="activity-icon activity-icon-green bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Service Request Approved</strong>
                                <span class="activity-time">Yesterday at 2:45 PM</span>
                            </div>
                            <p class="mb-0 text-muted">You approved service request SR005</p>
                        </div>
                    </li>
                    <li class="activity-item d-flex align-items-start">
                        <div class="activity-icon activity-icon-orange bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Technician Assigned</strong>
                                <span class="activity-time">May 5, 2025 at 11:20 AM</span>
                            </div>
                            <p class="mb-0 text-muted">You assigned Mike Wilson to service request SR004</p>
                        </div>
                    </li>
                    <li class="activity-item d-flex align-items-start">
                        <div class="activity-icon activity-icon-blue bg-info bg-opacity-10 text-info">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Login</strong>
                                <span class="activity-time"><?= $userData['last_login'] ?></span>
                            </div>
                            <p class="mb-0 text-muted">You logged in to your account</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Security Tab -->
            <div id="security" class="profile-tab-content">
                <h5 class="mb-4">Security Settings</h5>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Password</h6>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            Change Password
                        </button>
                    </div>
                    <p class="text-muted mb-0">Last updated 2 months ago</p>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Two-Factor Authentication</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="twoFactorToggle">
                        </div>
                    </div>
                    <p class="text-muted mb-0">Add an extra layer of security to your account</p>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Login Sessions</h6>
                        <button class="btn btn-sm btn-outline-danger">
                            Sign Out All Devices
                        </button>
                    </div>
                    <p class="text-muted mb-2">Currently signed in on 1 device</p>
                    
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-laptop fs-3 text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Chrome on Windows</h6>
                                        <span class="badge bg-success">Current</span>
                                    </div>
                                    <p class="text-muted mb-0 small">IP: 192.168.1.100 · Last active: Just now</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm" action="/update-profile" method="post">
                    <div class="text-center mb-4">
                        <img src="<?= $userData['profile_url'] ?>" alt="Profile Photo" class="profile-avatar">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary">Change Photo</button>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?= $userData['first_name'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?= $userData['last_name'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $userData['email'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= $userData['phone'] ?>">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?= $userData['address'] ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editProfileForm" class="btn btn-save">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" action="/change-password" method="post">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="changePasswordForm" class="btn btn-save">Update Password</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional scripts specific to this page
$additionalScripts = <<<HTML
<script>
    // Tab navigation functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.profile-nav-link');
        const tabContents = document.querySelectorAll('.profile-tab-content');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabLinks.forEach(tab => tab.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to current tab
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Form validation for password change
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New password and confirmation do not match!');
                }
            });
        }
        
        // Two-factor toggle
        const twoFactorToggle = document.getElementById('twoFactorToggle');
        if (twoFactorToggle) {
            twoFactorToggle.addEventListener('change', function() {
                if (this.checked) {
                    // In a real implementation, this would show a 2FA setup process
                    alert('Two-factor authentication setup would be shown here.');
                }
            });
        }
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>