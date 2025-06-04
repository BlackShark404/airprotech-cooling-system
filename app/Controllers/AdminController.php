<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AdminModel; // Assuming you have an AdminModel for admin-specific operations
use App\Models\ServiceRequestModel;
use App\Models\ProductModel;
use App\Models\ReportsModel;

class AdminController extends BaseController {
    protected $userModel;
    protected $adminModel;
    protected $serviceModel;
    protected $productModel;
    protected $reportsModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->adminModel = new AdminModel(); // Initialize AdminModel
        $this->serviceModel = new ServiceRequestModel();
        $this->productModel = new ProductModel();
        $this->reportsModel = new ReportsModel();

        // Ensure user is authenticated and is an admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Redirect to login or show an error page
            // Ensure that redirect actually stops script execution if needed by adding exit or return after it.
            if (isset($_SESSION['user_id']) && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')) {
                // Logged in but not admin (or role not set), show access denied or redirect to a safe page
                // For now, redirecting to home. Implement a proper access denied page later.
                $this->redirect('/'); 
                exit; // Important to stop further execution
            } else if (!isset($_SESSION['user_id'])){
                // Not logged in, redirect to login page
                $this->redirect('/login');
                exit; // Important to stop further execution
            }
            // If user_id is set, but user_role is not 'admin' and not caught by above, it's a state to review.
            // For now, the outer if condition handles this by proceeding to redirect if not admin.
            // If code reaches here, it implies !isset($_SESSION['user_id']) which is handled by the else if.
            // Or, it implies $_SESSION['user_id'] is set, but $_SESSION['user_role'] is not set or not 'admin'.
            // The nested if conditions should cover these, but as a fallback for the outer if:
            if(!isset($_SESSION['user_id'])) $this->redirect('/login');
            else $this->redirect('/'); // Default redirect if conditions are tricky, implies non-admin or issue
            exit;
        }
    }

    public function renderServiceRequest() {
        $this->render('admin/service-request');
    }

    public function renderProductBookings() {
        $this->render('admin/product-booking');
    }

    public function renderInventory() {
        $this->render('admin/inventory');
    }

    public function renderAdminProfile() {
        // Auth check is already in constructor, but double check session for safety
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login'); 
            return;
        }
        
        $adminId = $_SESSION['user_id'];
        
        // Get complete admin data (joins USER_ACCOUNT and ADMIN tables)
        $adminUser = $this->userModel->findUserWithRoleDetails($adminId, 'admin');
        
        if ($adminUser) {
            // Update session with latest data from DB, useful if changed elsewhere or for consistency
            $_SESSION['email'] = $adminUser['ua_email'];
            $_SESSION['address'] = $adminUser['ua_address'];
            $_SESSION['first_name'] = $adminUser['ua_first_name'];
            $_SESSION['last_name'] = $adminUser['ua_last_name'];
            $_SESSION['full_name'] = trim($adminUser['ua_first_name'] . ' ' . $adminUser['ua_last_name']);
            $_SESSION['profile_url'] = $adminUser['ua_profile_url'];
            $_SESSION['office_number'] = $adminUser['ad_office_no'] ?? null; // Ensure it handles null
        } else {
            // Admin user not found with role details, critical error, perhaps log and redirect
            // This might happen if DB is inconsistent or user_id in session is stale and not an admin anymore
            unset($_SESSION['user_id']); // Clear potentially problematic session
            unset($_SESSION['user_role']);
            // log_error("Admin user details not found for ID: " . $adminId);
            $this->redirect('/login');
            return;
        }
        
        // Get system statistics
        $statistics = $this->adminModel->getSystemStatistics();

        $viewData = [
            'user' => $adminUser, // Pass the admin data to the view
            'statistics' => $statistics // Pass system statistics to the view
        ];
        
        $this->render("admin/admin-profile", $viewData);
    }

    public function updateAdminProfile() {
        if (!$this->isAjax()) {
            return $this->jsonError('Invalid request method', 405);
        }
        
        // Auth check already in constructor
        $adminId = $_SESSION['user_id'];
        $data = $this->getJsonInput();
        
        $userAllowedFields = ['first_name', 'last_name', 'phone_number', 'address'];
        $userUpdateData = array_intersect_key($data, array_flip($userAllowedFields));
        
        $userMappedData = [];
        if (isset($userUpdateData['first_name'])) $userMappedData['ua_first_name'] = trim($userUpdateData['first_name']);
        if (isset($userUpdateData['last_name'])) $userMappedData['ua_last_name'] = trim($userUpdateData['last_name']);
        if (isset($userUpdateData['phone_number'])) $userMappedData['ua_phone_number'] = $userUpdateData['phone_number'];
        if (isset($userUpdateData['address'])) $userMappedData['ua_address'] = $userUpdateData['address'];

        $adminUpdateData = [];
        if (isset($data['office_number'])) $adminUpdateData['ad_office_no'] = $data['office_number'];
        
        if (empty($userMappedData) && empty($adminUpdateData)) {
            return $this->jsonError('No valid data provided for update', 400);
        }
        
        try {
            $this->pdo->beginTransaction(); // Start transaction using $this->pdo

            $userResult = true; 
            if (!empty($userMappedData)) {
                $userResult = $this->userModel->updateUser($adminId, $userMappedData);
            }

            $adminResult = true; 
            if (!empty($adminUpdateData)) {
                $adminExists = $this->adminModel->findByAccountId($adminId);
                if ($adminExists) {
                    $adminResult = $this->adminModel->updateByAccountId($adminId, $adminUpdateData);
                } else {
                    // This case should ideally not happen if user is admin due to DB trigger
                    // but as a fallback, create if not exists
                    $adminDataWithId = array_merge($adminUpdateData, ['ad_account_id' => $adminId]);
                    $adminResult = $this->adminModel->createAdmin($adminDataWithId); 
                }
            }
            
            if ($userResult && $adminResult) {
                $this->pdo->commit(); // Commit transaction using $this->pdo

                // Update session data
                if (isset($userMappedData['ua_first_name'])) $_SESSION['first_name'] = $userMappedData['ua_first_name'];
                if (isset($userMappedData['ua_last_name'])) $_SESSION['last_name'] = $userMappedData['ua_last_name'];
                if (isset($userMappedData['ua_first_name']) || isset($userMappedData['ua_last_name'])) {
                    $_SESSION['full_name'] = trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''));
                }
                if (isset($userMappedData['ua_phone_number'])) $_SESSION['phone_number'] = $userMappedData['ua_phone_number'];
                if (isset($userMappedData['ua_address'])) $_SESSION['address'] = $userMappedData['ua_address'];
                if (isset($adminUpdateData['ad_office_no'])) $_SESSION['office_number'] = $adminUpdateData['ad_office_no'];
                
                return $this->jsonSuccess([], 'Profile updated successfully');
            } else {
                $this->pdo->rollBack(); // Rollback transaction using $this->pdo
                return $this->jsonError('Failed to update profile', 500);
            }
        } catch (\Exception $e) {
            $this->pdo->rollBack(); // Rollback transaction on error using $this->pdo
            // Log the error: error_log($e->getMessage());
            return $this->jsonError('An error occurred: ' . $e->getMessage(), 500);
        }
    }
    
    public function updateAdminPassword() {
        if (!$this->isAjax()) {
            return $this->jsonError('Invalid request method', 405);
        }
        
        // Auth check in constructor
        $adminId = $_SESSION['user_id'];
        $data = $this->getJsonInput();
        
        if (!isset($data['current_password']) || !isset($data['new_password']) || !isset($data['confirm_password'])) {
            return $this->jsonError('All password fields are required', 400);
        }
        
        if ($data['new_password'] !== $data['confirm_password']) {
            return $this->jsonError('New password and confirmation do not match', 400);
        }

        if (strlen($data['new_password']) < 8) { // Example: Basic password strength check
            return $this->jsonError('New password must be at least 8 characters long.', 400);
        }
        
        $adminUser = $this->userModel->findById($adminId);
        if (!$adminUser) {
            // Should not happen if session is valid and user is admin
            return $this->jsonError('Admin user not found', 404);
        }
        
        if (!$this->userModel->verifyPassword($data['current_password'], $adminUser['ua_hashed_password'])) {
            return $this->jsonError('Current password is incorrect', 400);
        }
        
        $hashedPassword = $this->userModel->hashPassword($data['new_password']);
        
        try {
            $result = $this->userModel->updateUser($adminId, ['ua_hashed_password' => $hashedPassword]);
            
            if ($result) {
                return $this->jsonSuccess([], 'Password updated successfully');
            } else {
                return $this->jsonError('Failed to update password', 500);
            }
        } catch (\Exception $e) {
            // Log the error: error_log($e->getMessage());
            return $this->jsonError('An error occurred: ' . $e->getMessage(), 500);
        }
    }
    
    public function uploadAdminProfileImage() {
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return $this->jsonError('No image file uploaded or upload error', 400);
        }
        
        // Auth check in constructor
        $adminId = $_SESSION['user_id'];
        $file = $_FILES['profile_image'];
        
        // Validation for file type and size (copied from UserController, can be moved to a helper/BaseController)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            return $this->jsonError('Invalid file type. Only JPG, PNG, WEBP, and GIF are allowed', 400);
        }
        
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            return $this->jsonError('File size exceeds the maximum limit of 2MB', 400);
        }
        
        $adminUser = $this->userModel->findById($adminId);
        $oldProfileUrl = $adminUser['ua_profile_url'] ?? null;
        
        $uploadSubDir = 'profile_images';
        $uploadsDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/' . $uploadSubDir;
        
        if (!file_exists($uploadsDir)) {
            if (!mkdir($uploadsDir, 0775, true)) { 
                // error_log("Failed to create upload directory: {$uploadsDir}");
                return $this->jsonError('Failed to create upload directory. Check server permissions.', 500);
            }
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeExtension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? $extension : 'jpg'; // Default to jpg if ext is unusual
        $filename = 'admin_profile_' . $adminId . '_' . time() . '.' . $safeExtension; 
        $targetPath = $uploadsDir . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            // error_log("Failed to move uploaded file to: {$targetPath}");
            return $this->jsonError('Failed to save the uploaded file. Check server permissions or path.', 500);
        }
        
        $profileUrl = '/uploads/' . $uploadSubDir . '/' . $filename;
        try {
            $result = $this->userModel->updateUser($adminId, ['ua_profile_url' => $profileUrl]);
            
            if ($result) {
                $_SESSION['profile_url'] = $profileUrl;
                
                // Delete old profile image if it exists, is not the default, and is in the uploads folder
                if ($oldProfileUrl && $oldProfileUrl !== '/assets/images/default-profile.jpg' && strpos($oldProfileUrl, '/uploads/' . $uploadSubDir . '/') === 0) {
                    $oldFilePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $oldProfileUrl;
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                
                return $this->jsonSuccess(['profile_url' => $profileUrl], 'Profile image updated successfully');
            } else {
                @unlink($targetPath); // Clean up uploaded file if DB update fails
                return $this->jsonError('Failed to update profile image in database', 500);
            }
        } catch (\Exception $e) {
            @unlink($targetPath); // Clean up on exception
            // Log the error: error_log("DB error updating profile image: " . $e->getMessage());
            return $this->jsonError('An error occurred during database update: ' . $e->getMessage(), 500);
        }
    }

    public function renderReports() {
        // Get current year for default filter
        $year = date('Y');
        
        // Get service request statistics
        $serviceRequestsByStatus = $this->reportsModel->getServiceRequestsByStatus();
        $serviceRequestsByType = $this->reportsModel->getServiceRequestsByType();
        $serviceRequestsByMonth = $this->reportsModel->getServiceRequestsByMonth($year);
        
        // Get product booking statistics
        $productBookingsByStatus = $this->reportsModel->getProductBookingsByStatus();
        $productBookingsByMonth = $this->reportsModel->getProductBookingsByMonth($year);
        $topSellingProducts = $this->reportsModel->getTopSellingProducts(5);
        
        // Get technician performance data
        $technicianPerformance = $this->reportsModel->getTechnicianPerformance();
        
        // Get revenue data
        $revenueByMonth = $this->reportsModel->getRevenueByMonth($year);
        
        // Pass data to the view
        $this->render('admin/reports', [
            'serviceRequestsByStatus' => $serviceRequestsByStatus,
            'serviceRequestsByType' => $serviceRequestsByType,
            'serviceRequestsByMonth' => $serviceRequestsByMonth,
            'productBookingsByStatus' => $productBookingsByStatus,
            'productBookingsByMonth' => $productBookingsByMonth,
            'topSellingProducts' => $topSellingProducts,
            'technicianPerformance' => $technicianPerformance,
            'revenueByMonth' => $revenueByMonth,
            'currentYear' => $year
        ]);
    }

    public function getReportsByYear($year) {
        // Validate year
        if (!is_numeric($year) || $year < 2000 || $year > date('Y') + 1) {
            return $this->jsonError('Invalid year', 400);
        }
        
        // Get data for the specified year
        $serviceRequestsByMonth = $this->reportsModel->getServiceRequestsByMonth($year);
        $productBookingsByMonth = $this->reportsModel->getProductBookingsByMonth($year);
        $revenueByMonth = $this->reportsModel->getRevenueByMonth($year);
        
        // Format the data for charts
        $monthlyServiceData = array_fill(1, 12, 0);
        if (!empty($serviceRequestsByMonth)) {
            foreach ($serviceRequestsByMonth as $monthly) {
                $monthlyServiceData[(int)$monthly['month']] = (int)$monthly['count'];
            }
        }
        
        $monthlyProductData = array_fill(1, 12, 0);
        if (!empty($productBookingsByMonth)) {
            foreach ($productBookingsByMonth as $monthly) {
                $monthlyProductData[(int)$monthly['month']] = (int)$monthly['count'];
            }
        }
        
        $monthlyRevenueData = array_fill(1, 12, 0);
        if (!empty($revenueByMonth)) {
            foreach ($revenueByMonth as $monthly) {
                $monthlyRevenueData[(int)$monthly['month']] = (float)$monthly['total_revenue'];
            }
        }
        
        // Return the formatted data
        header('Content-Type: application/json');
        return $this->jsonSuccess([
            'serviceRequestsByMonth' => array_values($monthlyServiceData),
            'productBookingsByMonth' => array_values($monthlyProductData),
            'revenueByMonth' => array_values($monthlyRevenueData),
            'year' => $year
        ]);
    }

    public function renderAddProduct() {
        $this->render('admin/add-product');
    }

    public function renderUserManagement() {
        $this->render('admin/user-management');
    }
    
    public function renderProductManagement() {
        $this->render('admin/product-management');
    }
    
    public function renderInventoryManagement() {
        $this->render('admin/inventory-management');
    }
    
    public function renderWarehouseManagement() {
        $this->render('admin/warehouse-management');
    }
    
    public function renderProductOrders() {
        $this->render('admin/product-orders');
    }

    public function renderTechnician() {
        
        $this->render('admin/technician');
    }
    
    /**
     * API endpoint to get all technicians
     */
    public function getTechnicians()
    {
        $userModel = $this->loadModel('UserModel');
        $technicians = $userModel->getTechnicians();
        
        $this->jsonSuccess($technicians);
    }
    
    /**
     * API endpoint to get a specific technician's assignments
     */
    public function getTechnicianAssignments($id)
    {
        $bookingAssignmentModel = $this->loadModel('BookingAssignmentModel');
        $productAssignmentModel = $this->loadModel('ProductAssignmentModel');
        
        // Check if type parameter is provided
        $type = $_GET['type'] ?? null;
        
        if ($type === 'service') {
            // Return only service assignments
            $serviceAssignments = $bookingAssignmentModel->getAssignmentsForTechnician($id);
            $this->jsonSuccess(['data' => $serviceAssignments]);
        } else if ($type === 'product') {
            // Return only product assignments
            $productAssignments = $productAssignmentModel->getAssignmentsByTechnician($id);
            $this->jsonSuccess(['data' => $productAssignments]);
        } else {
            // Return both types of assignments
            $serviceAssignments = $bookingAssignmentModel->getAssignmentsForTechnician($id);
            $productAssignments = $productAssignmentModel->getAssignmentsByTechnician($id);
            
            $this->jsonSuccess([
                'serviceAssignments' => $serviceAssignments,
                'productAssignments' => $productAssignments
            ]);
        }
    }
    
    /**
     * API endpoint to assign a service request to a technician
     */
    public function assignServiceRequest()
    {
        if (!$this->isPost()) {
            $this->jsonError('Invalid request method');
        }
        
        $input = $this->getJsonInput();
        
        $bookingId = $input['booking_id'] ?? null;
        $technicianId = $input['technician_id'] ?? null;
        
        if (!$bookingId || !$technicianId) {
            $this->jsonError('Missing required parameters');
        }
        
        $bookingAssignmentModel = $this->loadModel('BookingAssignmentModel');
        
        $data = [
            'ba_booking_id' => $bookingId,
            'ba_technician_id' => $technicianId,
            'ba_status' => 'assigned'
        ];
        
        $result = $bookingAssignmentModel->createAssignment($data);
        
        if ($result) {
            $this->jsonSuccess(['message' => 'Service request assigned successfully']);
        } else {
            $this->jsonError('Failed to assign service request');
        }
    }
    
    /**
     * API endpoint to assign a product booking to a technician
     */
    public function assignProductBooking()
    {
        if (!$this->isPost()) {
            $this->jsonError('Invalid request method');
        }
        
        $input = $this->getJsonInput();
        
        $bookingId = $input['booking_id'] ?? null;
        $technicianId = $input['technician_id'] ?? null;
        
        if (!$bookingId || !$technicianId) {
            $this->jsonError('Missing required parameters');
        }
        
        $productAssignmentModel = $this->loadModel('ProductAssignmentModel');
        
        $data = [
            'pa_order_id' => $bookingId,
            'pa_technician_id' => $technicianId,
            'pa_status' => 'assigned'
        ];
        
        $result = $productAssignmentModel->createAssignment($data);
        
        if ($result) {
            $this->jsonSuccess(['message' => 'Product booking assigned successfully']);
        } else {
            $this->jsonError('Failed to assign product booking');
        }
    }
    
    /**
     * API endpoint to get a specific technician's details
     */
    public function getTechnician($id)
    {
        $userModel = $this->loadModel('UserModel');
        $technician = $userModel->getTechnicianDetails($id);
        
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
        }
        
        $this->jsonSuccess($technician);
    }
    
    /**
     * API endpoint to update a technician's details
     */
    public function updateTechnician($id)
    {
        if (!$this->isPost()) {
            $this->jsonError('Invalid request method');
        }
        
        $technicianModel = $this->loadModel('TechnicianModel');
        
        // Get post data
        $isAvailable = isset($_POST['te_is_available']) ? (int)$_POST['te_is_available'] : null;
        
        if ($isAvailable === null) {
            $this->jsonError('Missing required parameters');
        }
        
        $data = [
            'te_is_available' => $isAvailable
        ];
        
        $result = $technicianModel->update($data, 'te_account_id = :id', ['id' => $id]);
        
        if ($result) {
            $this->jsonSuccess(['message' => 'Technician updated successfully']);
        } else {
            $this->jsonError('Failed to update technician');
        }
    }
}