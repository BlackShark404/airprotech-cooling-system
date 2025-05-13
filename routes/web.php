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
$router->map('POST', '/user/service/request', 'App\Controllers\ServiceRequestController#bookService', 'create-service request');
$router->map('GET', '/user/bookings', 'App\Controllers\ServiceRequestController#myBookings', 'user_bookings');
$router->map('POST', '/user/bookings/cancel/[i:id]', 'App\Controllers\ServiceRequestController#cancelBooking', 'user_cancel_booking');
$router->map('GET', '/user/my-orders', 'App\Controllers\UserController#renderMyOrders', 'render_my-orders');

// Service Request API endpoints for ServiceRequestsManager.js



// Admin routes
$router->map('GET', '/admin/service-requests', 'App\Controllers\AdminController#renderServiceRequest', 'render-service-request');
$router->map('GET', '/admin/inventory', 'App\Controllers\AdminController#renderInventory', 'render-inventory');
$router->map('GET', '/admin/add-product', 'App\Controllers\AdminController#renderAddProduct', 'render-add-product');
$router->map('GET', '/admin/reports', 'App\Controllers\AdminController#renderReports', 'render-reports');

// Service Request Management Routes
$router->map('GET', '/api/user/service-bookings', 'App\Controllers\ServiceRequestController#getUserServiceBookings', 'user_service_bookings_api');
$router->map('GET', '/api/user/service-bookings/[i:id]', 'App\Controllers\ServiceRequestController#getUserServiceBookingDetails', 'user_service_booking_details_api');


// Inventory Management API Routes
$router->map('GET', '/inventory/getAllInventory', 'App\Controllers\InventoryController#getAllInventory', 'inventory_get_all');
$router->map('GET', '/inventory/getInventoryByProduct/[i:productId]', 'App\Controllers\InventoryController#getInventoryByProduct', 'inventory_by_product');
$router->map('GET', '/inventory/getInventoryByWarehouse/[i:warehouseId]', 'App\Controllers\InventoryController#getInventoryByWarehouse', 'inventory_by_warehouse');
$router->map('GET', '/inventory/getInventoryByType/[*:type]', 'App\Controllers\InventoryController#getInventoryByType', 'inventory_by_type');
$router->map('GET', '/inventory/getLowStockProducts', 'App\Controllers\InventoryController#getLowStockProducts', 'inventory_low_stock');
$router->map('GET', '/inventory/getStats', 'App\Controllers\InventoryController#getStats', 'inventory_stats');
$router->map('GET', '/inventory/getWarehouses', 'App\Controllers\InventoryController#getWarehouses', 'inventory_warehouses');
$router->map('GET', '/inventory/getProductsWithVariants', 'App\Controllers\InventoryController#getProductsWithVariants', 'inventory_products_variants');
$router->map('POST', '/inventory/addStock', 'App\Controllers\InventoryController#addStock', 'inventory_add_stock');
$router->map('POST', '/inventory/moveStock', 'App\Controllers\InventoryController#moveStock', 'inventory_move_stock');
$router->map('GET', '/inventory/viewProduct/[i:productId]', 'App\Controllers\InventoryController#viewProduct', 'inventory_view_product');
$router->map('GET', '/inventory/exportInventory', 'App\Controllers\InventoryController#exportInventory', 'inventory_export');
$router->map('POST', '/inventory/importInventory', 'App\Controllers\InventoryController#importInventory', 'inventory_import');
$router->map('POST', '/inventory/createProduct', 'App\Controllers\InventoryController#createProduct', 'inventory_create_product');
$router->map('PUT', '/inventory/updateProduct/[i:productId]', 'App\Controllers\InventoryController#updateProduct', 'inventory_update_product');
$router->map('POST', '/inventory/deleteProduct/[i:productId]', 'App\Controllers\InventoryController#deleteProduct', 'inventory_delete_product');

// User Management Routes 
$router->map('GET', '/admin/user-management', 'App\Controllers\UserManagementController#index', 'render-user-management');
$router->map('GET', '/api/users', 'App\Controllers\UserManagementController#getUsers', 'api_get_users');
$router->map('GET', '/api/users/data', 'App\Controllers\UserManagementController#getUsersData', 'api_get_users_data');
$router->map('GET', '/api/users/[i:id]', 'App\Controllers\UserManagementController#getUser', 'api_get_user');
$router->map('POST', '/api/users', 'App\Controllers\UserManagementController#createUser', 'api_create_user');
$router->map('PUT', '/api/users/[i:id]', 'App\Controllers\UserManagementController#updateUser', 'api_update_user');
$router->map('DELETE', '/api/users/[i:id]', 'App\Controllers\UserManagementController#deleteUser', 'api_delete_user');
$router->map('POST', '/api/users/reset-password/[i:id]', 'App\Controllers\UserManagementController#resetPassword', 'api_reset_password');
$router->map('GET', '/api/users/export', 'App\Controllers\UserManagementController#exportUsers', 'api_export_users');



