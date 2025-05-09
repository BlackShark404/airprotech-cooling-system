<?php

// Define public routes
$publicRoutes = [
    '/',
    '/login',
    '/register',
    '/contact-us',
    '/user-data',
    '/paginate-test',
];

// Define the access control map for routes
$accessMap = [
    // Admin-only routes
    '/admin/dashboard' => ['admin'],
    '/admin/user-management' => ['admin'],
    '/admin/users/data' => ['admin'],
    '/admin/users/create' => ['admin'],
    '/admin/users/update' => ['admin'],
    '/admin/users/delete' => ['admin'],
    '/admin/users/activate' => ['admin'],
    '/admin/users/deactivate' => ['admin'],
    '/admin/users/get' => ['admin'],
    '/admin/profile' => ['admin'],
    
    // Service requests management (admin-only)
    '/admin/service-requests' => ['admin'],
    '/admin/service-requests/data' => ['admin'],
    '/admin/service-requests/get' => ['admin'],
    '/admin/service-requests/update' => ['admin'],
    '/admin/service-requests/delete' => ['admin'],
    '/admin/service-requests/assign' => ['admin'],
    '/admin/service-requests/unassign' => ['admin'],
    '/admin/service-types/get-active' => ['admin'],
    '/admin/technicians/get-active' => ['admin'],

    // Shared (admin and user)
    '/logout' => ['customer', 'technician', 'admin'],
];

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#index', 'home');

$router->map('GET', '/services', 'App\Controllers\HomeController#service', 'service');
$router->map('GET', '/products', 'App\Controllers\HomeController#products', 'product');
$router->map('GET', '/about', 'App\Controllers\HomeController#about', 'about');
$router->map('GET', '/contact-us', 'App\Controllers\HomeController#contactUs', 'contact');
$router->map('POST', '/contact-us', 'App\Controllers\HomeController#contactUs', 'contact_post');
$router->map('GET', '/privacy-policy', 'App\Controllers\HomeController#privacy', 'privacy-policy');
$router->map('GET', '/terms-of-service', 'App\Controllers\HomeController#terms', 'terms-of-service');


// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#renderLogin', 'render_login');
$router->map('POST', '/login', 'App\Controllers\AuthController#loginAccount', 'login_post');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render_register');
$router->map('POST', '/register', 'App\Controllers\AuthController#registerAccount', 'register_post');
$router->map('GET', '/reset-password', 'App\Controllers\AuthController#renderResetPassword', 'reset_password');
$router->map('GET', '/logout', 'App\Controllers\AuthController#logout', 'logout');

// User routes
$router->map('GET', '/user/dashboard', 'App\Controllers\UserController#renderUserDashboard', 'render_user-dashboard');
$router->map('GET', '/user/services', 'App\Controllers\UserController#renderUserServices', 'render_user-products');
$router->map('GET', '/user/products', 'App\Controllers\UserController#renderUserProducts', 'render_user-services');
$router->map('POST', '/user/service/request', 'App\Controllers\ServiceRequestController#bookService', 'render_user-book_services');
$router->map('GET', '/user/bookings', 'App\Controllers\ServiceRequestController#myBookings', 'user_bookings');
$router->map('POST', '/user/bookings/cancel/[i:id]', 'App\Controllers\ServiceRequestController#cancelBooking', 'user_cancel_booking');

// Admin routes
$router->map('GET', '/admin/dashboard', 'App\Controllers\AdminController#renderAdminDashboard', 'render_admin-dashboard');
$router->map('GET', '/admin/technicians', 'App\Controllers\AdminController#renderTechnician', 'render-technician');
$router->map('GET', '/admin/inventory', 'App\Controllers\AdminController#renderInventory', 'render-inventory');
$router->map('GET', '/admin/add-product', 'App\Controllers\AdminController#renderAddProduct', 'render-add-product');
$router->map('GET', '/admin/reports', 'App\Controllers\AdminController#renderReports', 'render-reports');
$router->map('GET', '/admin/admin-profile', 'App\Controllers\AdminController#renderProfile', 'render-admin-profile');
$router->map('GET', '/admin/profile', 'App\Controllers\AdminController#renderAdminProfile', 'render-admin_profile');

// User Management Routes - Adding these new routes
$router->map('GET', '/admin/user-management', 'App\Controllers\UserManagementController#index', 'admin_user_management');
$router->map('POST', '/admin/users/data', 'App\Controllers\UserManagementController#getUsersData', 'admin_users_data');
$router->map('GET', '/admin/users/get/[i:id]', 'App\Controllers\UserManagementController#getUser', 'admin_user_get');
$router->map('POST', '/admin/users/create', 'App\Controllers\UserManagementController#createUser', 'admin_user_create');
$router->map('POST', '/admin/users/update/[i:id]', 'App\Controllers\UserManagementController#updateUser', 'admin_user_update');
$router->map('POST', '/admin/users/delete/[i:id]', 'App\Controllers\UserManagementController#deleteUser', 'admin_user_delete');
$router->map('POST', '/admin/users/activate/[i:id]', 'App\Controllers\UserManagementController#activateUser', 'admin_user_activate');
$router->map('POST', '/admin/users/deactivate/[i:id]', 'App\Controllers\UserManagementController#deactivateUser', 'admin_user_deactivate');
$router->map('GET', '/admin/users/roles', 'App\Controllers\UserManagementController#getRoles', 'admin_user_roles');

// Service Request Management Routes
$router->map('GET', '/admin/service-requests', 'App\Controllers\ServiceRequestController#adminServiceRequests', 'admin_service_requests');
$router->map('POST', '/admin/service-requests/data', 'App\Controllers\ServiceRequestController#getServiceRequestsData', 'admin_service_requests_data');
$router->map('GET', '/admin/service-requests/get/[i:id]', 'App\Controllers\ServiceRequestController#getServiceRequest', 'admin_service_request_get');
$router->map('POST', '/admin/service-requests/update', 'App\Controllers\ServiceRequestController#updateServiceRequest', 'admin_service_request_update');
$router->map('POST', '/admin/service-requests/delete/[i:id]', 'App\Controllers\ServiceRequestController#deleteServiceRequest', 'admin_service_request_delete');
$router->map('POST', '/admin/service-requests/assign', 'App\Controllers\ServiceRequestController#assignTechnician', 'admin_service_request_assign');
$router->map('POST', '/admin/service-requests/unassign', 'App\Controllers\ServiceRequestController#unassignTechnician', 'admin_service_request_unassign');
$router->map('GET', '/admin/service-types/get-active', 'App\Controllers\ServiceRequestController#getActiveServiceTypes', 'admin_service_types_get_active');
$router->map('GET', '/admin/technicians/get-active', 'App\Controllers\ServiceRequestController#getActiveTechnicians', 'admin_technicians_get_active');
