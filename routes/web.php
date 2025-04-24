<?php

// Define public routes
$publicRoutes = [
    '/',
    '/login',
    '/register',
    '/contact-us',
    '/user-data',
    '/paginate-test',
    '/datable-test'
];

// Define the access control map for routes
$accessMap = [
    // Admin-only routes
    '/admin/dashboard' => ['admin'],
    '/admin/users' => ['admin'],
    '/admin/books' => ['admin'],
    '/admin/reading' => ['admin'],
    '/admin/purchases' => ['admin'],
    '/admin/logs' => ['admin'],
    '/admin/user-management' => ['admin'],
    '/admin/book-management' => ['admin'],
    '/admin/reading-sessions' => ['admin'],
    '/admin/activity-logs' => ['admin'],
    '/admin/admin-profile' => ['admin'],
    '/admin/users/create' => ['admin'],
    '/admin/users/data' => ['admin'],
    '/admin/users/update' => ['admin'],
    '/admin/users/delete' => ['admin'],

     // User-only routes
    '/user/dashboard' => ['user'],
    '/user/browse-books' => ['user'],
    '/user/reading-sessions' => ['user'],
    '/user/wishlist' => ['user'],
    '/user/purchases' => ['user'],
    '/user/user-profile' => ['user'],
    '/user/user-profile/delete-account' => ['user'],
    '/user/user-profile/change-password' => ['user'],
    '/user/user-profile/update-profile-info' => ['user'],

    // Shared (admin and user)
    '/logout' => ['admin', 'user'],

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
$router->map('GET', '/auth/login', 'App\Controllers\AuthController#renderLogin', 'render_login');
$router->map('POST', '/auth/login', 'App\Controllers\AuthController#login', 'login_post');
$router->map('GET', '/auth/register', 'App\Controllers\AuthController#renderRegister', 'render_register');
$router->map('POST', '/auth/register', 'App\Controllers\AuthController#register', 'register_post');
$router->map('GET', '/auth/reset-password', 'App\Controllers\AuthController#renderResetPassword', 'reset_password');
$router->map('GET', '/auth/logout', 'App\Controllers\AuthController#logout', 'logout');

// User routes
$router->map('GET', '/user/dashboard', 'App\Controllers\UserController#renderUserDashboard', 'render_user-dashboard');

// Admin routes
$router->map('GET', '/admin/dashboard', 'App\Controllers\AdminController#renderAdminDashboard', 'render_admin-dashboard');

// Test routes
$router->map('GET', '/test', 'App\Controllers\TestController#renderTest', 'test');