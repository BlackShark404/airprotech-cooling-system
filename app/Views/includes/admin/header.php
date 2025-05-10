<?php
function base_url($uri = '', $protocol = true) {
    // Get the protocol
    $base_protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    
    // Get the server name and any potential subfolder the application is in
    $base_domain = $_SERVER['HTTP_HOST'];
    
    // Application subfolder - adjust this if your application is in a subfolder
    $base_folder = dirname($_SERVER['SCRIPT_NAME']);
    $base_folder = ($base_folder === '/' || $base_folder === '\\') ? '' : $base_folder;
    
    // Combine to create base URL
    $base_url = $protocol ? $base_protocol . $base_domain . $base_folder : $base_domain . $base_folder;
    
    // Clean up base URL (ensure single trailing slash)
    $base_url = rtrim($base_url, '/') . '/';
    
    // Add URI if provided
    if ($uri) {
        // Remove leading slashes from URI
        $uri = ltrim($uri, '/');
        $base_url .= $uri;
    }
    
    return $base_url;
}
?>

<!-- Navbar -->
<nav class="navbar navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/assets/images/logo/Air-TechLogo.png" alt="AirProtect logo" height="36" width="36">
            AirPotect
        </a>
        <div class="d-flex">
            <div class="me-3">
                <i class="bi bi-bell text-white"></i>
            </div>
            <div class="dropdown profile-dropdown">
                <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $_SESSION['profile_url'] ?>" alt="User Profile" width="30" height="30">
                    <span class="d-none d-sm-inline">Admin User</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item href="<?= base_url('/admin/admin-profile') ?>"><i class="bi bi-person"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Tabs with horizontal scroll for mobile -->
<div class="container-fluid">
    <div class="nav-scroll">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'service_requests' ? 'active' : '' ?>" href="<?= base_url('/admin/service-requests') ?>">Service Requests</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'inventory' ? 'active' : '' ?>" href="<?= base_url('/admin/inventory') ?>">Inventory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'reports' ? 'active' : '' ?>" href="<?= base_url('/admin/reports') ?>">Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'user_management' ? 'active' : '' ?>" href="<?= base_url('/admin/user-management') ?>">User Management</a>
            </li>
        </ul>
    </div>
</div>