<?php 
use Core\Session;
require_once __DIR__ . '/../../../../script/active_page.php';
?>

<!-- Navbar -->
<nav class="navbar navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/assets/images/logo/Air-TechLogo.png" alt="AirProtect logo" height="36" width="36">
            AirPotech
        </a>
        <div class="d-flex">
            <div class="me-3">
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="position-relative">
                        <img src="<?=Session::get('profile_url')?>" alt="Profile Image" class="rounded-circle" width="30" height="30">
                        <span class="position-absolute bottom-0 end-0 translate-middle-y bg-success rounded-circle border border-white" style="width: 8px; height: 8px;"></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
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
                <a class="nav-link <?= $activeTab === 'product_bookings' ? 'active' : '' ?>" href="<?= base_url('/admin/product-bookings') ?>">Product Bookings</a>
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