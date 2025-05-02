<?php
// Assuming this file is named profile.php
$title = 'My Profile - AirProtect';
$additionalStyles = '
<style>
    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .profile-header {
        background-color: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #6c757d;
    }
    .profile-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .profile-role {
        color: #6c757d;
        font-size: 16px;
        margin-bottom: 16px;
    }
    .profile-stats {
        display: flex;
        gap: 24px;
        margin-top: 16px;
    }
    .profile-stat {
        text-align: center;
    }
    .profile-stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #007bff;
    }
    .profile-stat-label {
        font-size: 14px;
        color: #6c757d;
    }
    .profile-section {
        background-color: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .profile-section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .profile-info-row {
        display: flex;
        margin-bottom: 16px;
    }
    .profile-info-label {
        width: 150px;
        font-weight: 500;
        color: #6c757d;
    }
    .profile-info-value {
        flex: 1;
    }
    .profile-activity {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .profile-activity:last-child {
        border-bottom: none;
    }
    .activity-date {
        font-size: 14px;
        color: #6c757d;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .btn-save {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 500;
    }
</style>
';

ob_start();
?>

<div class="container profile-container mt-4">
    <div class="profile-header row">
        <div class="col-md-3 text-center mb-3 mb-md-0">
            <div class="profile-avatar mx-auto">
                <i class="bi bi-person"></i>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="profile-name">Admin User</h1>
                    <p class="profile-role">System Administrator</p>
                    <p><i class="bi bi-envelope me-2"></i>admin@airprotect.com</p>
                    <p><i class="bi bi-telephone me-2"></i>(555) 123-4567</p>
                </div>
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </button>
            </div>
            
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="profile-stat-value">145</div>
                    <div class="profile-stat-label">Service Requests</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">27</div>
                    <div class="profile-stat-label">Days Active</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">98%</div>
                    <div class="profile-stat-label">Response Rate</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="profile-section">
        <div class="profile-section-title">
            Personal Information
            <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </button>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-label">Full Name</div>
            <div class="profile-info-value">Admin User</div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-label">Email</div>
            <div class="profile-info-value">admin@airprotect.com</div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-label">Phone</div>
            <div class="profile-info-value">(555) 123-4567</div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-label">Position</div>
            <div class="profile-info-value">System Administrator</div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-label">Department</div>
            <div class="profile-info-value">IT Management</div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-label">Location</div>
            <div class="profile-info-value">Main Office</div>
        </div>
    </div>
    
    <div class="profile-section">
        <div class="profile-section-title">
            Account Settings
        </div>
        <form>
            <div class="form-group">
                <label for="notifications" class="form-label">Email Notifications</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notifyRequests" checked>
                    <label class="form-check-label" for="notifyRequests">New service requests</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notifyInventory" checked>
                    <label class="form-check-label" for="notifyInventory">Low inventory alerts</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="notifySystem">
                    <label class="form-check-label" for="notifySystem">System updates</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="language" class="form-label">Language</label>
                <select class="form-select" id="language">
                    <option selected>English (US)</option>
                    <option>Spanish</option>
                    <option>French</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="timezone" class="form-label">Time Zone</label>
                <select class="form-select" id="timezone">
                    <option selected>Eastern Time (ET)</option>
                    <option>Central Time (CT)</option>
                    <option>Mountain Time (MT)</option>
                    <option>Pacific Time (PT)</option>
                </select>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
    
    <div class="profile-section">
        <div class="profile-section-title">
            Recent Activity
            <a href="#" class="view-all">View All</a>
        </div>
        
        <div class="profile-activity d-flex align-items-center">
            <div class="activity-icon activity-icon-green me-3">
                <i class="bi bi-check"></i>
            </div>
            <div class="flex-grow-1">
                <div>Updated inventory item PRD005 - Split System Classic</div>
                <div class="activity-date">Today, 10:23 AM</div>
            </div>
        </div>
        
        <div class="profile-activity d-flex align-items-center">
            <div class="activity-icon activity-icon-orange me-3">
                <i class="bi bi-plus"></i>
            </div>
            <div class="flex-grow-1">
                <div>Added new product to inventory: Portable Ac Unit</div>
                <div class="activity-date">Yesterday, 3:45 PM</div>
            </div>
        </div>
        
        <div class="profile-activity d-flex align-items-center">
            <div class="activity-icon activity-icon-green me-3">
                <i class="bi bi-check"></i>
            </div>
            <div class="flex-grow-1">
                <div>Completed service request #SR-2304</div>
                <div class="activity-date">Apr 25, 2025, 11:30 AM</div>
            </div>
        </div>
        
        <div class="profile-activity d-flex align-items-center">
            <div class="activity-icon activity-icon-green me-3">
                <i class="bi bi-person"></i>
            </div>
            <div class="flex-grow-1">
                <div>Updated profile information</div>
                <div class="activity-date">Apr 24, 2025, 9:15 AM</div>
            </div>
        </div>
    </div>
    
    <div class="profile-section">
        <div class="profile-section-title">
            Security
        </div>
        <form>
            <div class="form-group">
                <label for="currentPassword" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="currentPassword">
            </div>
            
            <div class="form-group">
                <label for="newPassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword">
            </div>
            
            <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword">
            </div>
            
            <div class="form-group mt-4">
                <label class="form-label">Two-Factor Authentication</label>
                <div class="d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                        <label class="form-check-label" for="twoFactorAuth">Enable two-factor authentication</label>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary ms-3">Set Up</button>
                </div>
                <div class="text-muted small mt-1">Add an extra layer of security to your account</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn-save">Update Password</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../includes/admin/base.php';
?>