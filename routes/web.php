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
    // '/admin/dashboard' => ['admin'],
    // '/admin/users' => ['admin'],
    // '/admin/books' => ['admin'],
    // '/admin/reading' => ['admin'],
    // '/admin/purchases' => ['admin'],
    // '/admin/logs' => ['admin'],
    // '/admin/user-management' => ['admin'],
    // '/admin/book-management' => ['admin'],
    // '/admin/reading-sessions' => ['admin'],
    // '/admin/activity-logs' => ['admin'],
    // '/admin/admin-profile' => ['admin'],
    // '/admin/users/create' => ['admin'],
    // '/admin/users/data' => ['admin'],
    // '/admin/users/update' => ['admin'],
    // '/admin/users/delete' => ['admin'],

    //  // User-only routes
    // '/user/dashboard' => ['customer'],
    // '/user/browse-books' => ['customer'],
    // '/user/reading-sessions' => ['customer'],
    // '/user/wishlist' => ['customer'],
    // '/user/purchases' => ['customer'],
    // '/user/user-profile' => ['customer'],
    // '/user/user-profile/delete-account' => ['customer'],
    // '/user/user-profile/change-password' => ['customer'],
    // '/user/user-profile/update-profile-info' => ['customer'],

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
$router->map('POST', '/user/service/request', 'App\Controllers\ServiceController#bookService', 'render_user-book_services');

// Admin routes
$router->map('GET', '/technician/dashboard', 'App\Controllers\TechnicianController#renderTechnicianDashboard', 'render_technician-dashboard');

// Admin routes
$router->map('GET', '/admin/dashboard', 'App\Controllers\AdminController#renderAdminDashboard', 'render_admin-dashboard');
$router->map('GET', '/admin/service-requests', 'App\Controllers\AdminController#renderServiceRequest', 'render-service_request');
$router->map('GET', '/admin/technicians', 'App\Controllers\AdminController#renderTechnician', 'render-technician');
$router->map('GET', '/admin/inventory', 'App\Controllers\AdminController#renderInventory', 'render-inventory');
$router->map('GET', '/admin/add-product', 'App\Controllers\AdminController#renderAddProduct', 'render-add-product');
$router->map('GET', '/admin/reports', 'App\Controllers\AdminController#renderReports', 'render-reports');
$router->map('GET', '/admin/admin-profile', 'App\Controllers\AdminController#renderProfile', 'render-admin-profile');



// Book management routes
$router->map('GET', '/admin/book-management', 'App\Controllers\BookController#renderBookManagement', 'admin_books');
$router->map('POST', '/admin/books/data', 'App\Controllers\BookController#getBooksData', 'admin_books_data');



// --- Add these lines for Product Management ---

// Product Management Routes (Assuming Admin Access)
$router->map('GET', '/admin/product-management', 'App\Controllers\ProductController#renderProductManagement', 'admin_product_management');
$router->map('POST', '/admin/products/data', 'App\Controllers\ProductController#getData', 'admin_products_data'); // DataTables data endpoint
$router->map('POST', '/admin/products/create', 'App\Controllers\ProductController#create', 'admin_products_create'); // Create product endpoint
$router->map('GET', '/admin/products/[i:id]', 'App\Controllers\ProductController#get', 'admin_products_get');       // Get single product for view/edit
$router->map('POST', '/admin/products/update/[i:id]', 'App\Controllers\ProductController#update', 'admin_products_update'); // Update product endpoint
$router->map('POST', '/admin/products/delete/[i:id]', 'App\Controllers\ProductController#delete', 'admin_products_delete'); // Delete product endpoint

// --- End of Product Management Routes ---


// Test routes
$router->map('GET', '/test', 'App\Controllers\TestController#renderTest', 'test');