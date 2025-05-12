<?php

namespace App\Controllers;

class ServiceRequestController extends BaseController
{
    private $serviceModel;
    private $serviceTypeModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->serviceModel = $this->loadModel('ServiceRequestModel');
        $this->serviceTypeModel = $this->loadModel('ServiceRequestTypeModel');
    }
    
    /**
     * Display services page
     */
    public function index()
    {
        // Get all active service types
        $serviceTypes = $this->serviceTypeModel->getActiveServiceTypes();
        
        // Get user data from session
        $userData = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? null,
            'user_email' => $_SESSION['user_email'] ?? null,
            'user_role' => $_SESSION['user_role'] ?? null,
        ];
        
        // Render the services view
        $this->render('services', [
            'serviceTypes' => $serviceTypes,
            'userData' => $userData
        ]);
    }
    
    /**
     * Handle service booking form submission
     */
    public function bookService()
    {
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        $requiredFields = [
            'serviceSelect',
            'preferredDate',
            'preferredTime',
            'serviceDescription',
            'fullName',
            'emailAddress',
            'phoneNumber',
            'address'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                $this->jsonError("The {$field} field is required", 400);
                return;
            }
        }
        
        // Validate service type exists
        $serviceType = $this->serviceTypeModel->getServiceTypeByCode($input['serviceSelect']);
        if (!$serviceType) {
            $this->jsonError("Invalid service type selected", 400);
            return;
        }
        
        // Prepare booking data
        $bookingData = [
            'sb_customer_id' => $_SESSION['user_id'],
            'sb_service_type_id' => $serviceType['st_id'],
            'sb_requested_date' => $input['preferredDate'],
            'sb_requested_time' => $input['preferredTime'],
            'sb_address' => $input['address'],
            'sb_description' => $input['serviceDescription'],
            'sb_status' => 'pending'
        ];
        
        // Create the booking
        $success = $this->serviceModel->createBooking($bookingData);
        
        if ($success) {
            $this->jsonSuccess(
                ['status' => 'pending', 'redirect_url' => '/user/my-orders'],
                'Your service request has been submitted successfully. We will contact you soon.'
            );
        } else {
            $this->jsonError("Failed to submit service request. Please try again later.", 500);
        }
    }
    
    /**
     * Display user's service bookings
     */
    public function myBookings()
    {
        // Check if user is logged in
        $userId = $_SESSION['user_id'] ?? null;
        
        // Get user's bookings
        $bookings = $this->serviceModel->getCustomerBookings($userId);
        
        // Render the bookings view
        $this->render('user/bookings', [
            'bookings' => $bookings
        ]);
    }
    
    /**
     * Cancel a booking
     */
    public function cancelBooking($id = null)
    {
        // Check if user is logged in
        $userId = $_SESSION['user_id'] ?? null;
 
        // Validate booking ID
        if (!$id) {
            if ($this->isAjax()) {
                $this->jsonError("Booking ID is required", 400);
            } else {
                $this->redirect('/user/bookings');
            }
            return;
        }
        
        // Get the booking
        $booking = $this->serviceModel->find($id);
        
        // Check if booking exists and belongs to the user
        if (!$booking || $booking['sb_customer_id'] != $userId) {
            if ($this->isAjax()) {
                $this->jsonError("Booking not found or access denied", 404);
            } else {
                $this->redirect('/user/bookings');
            }
            return;
        }
        
        // Cancel the booking
        $success = $this->serviceModel->cancelBooking($id);
        
        if ($this->isAjax()) {
            if ($success) {
                $this->jsonSuccess(
                    ['status' => 'cancelled'],
                    'Your booking has been cancelled successfully'
                );
            } else {
                $this->jsonError("Failed to cancel booking. Please try again later.", 500);
            }
        } else {
            if ($success) {
                // Set flash message
                $_SESSION['flash_message'] = 'Your booking has been cancelled successfully';
                $_SESSION['flash_type'] = 'success';
            } else {
                // Set flash message
                $_SESSION['flash_message'] = 'Failed to cancel booking. Please try again later.';
                $_SESSION['flash_type'] = 'danger';
            }
            
            // Redirect back to bookings page
            $this->redirect('/user/bookings');
        }
    }
    
    /**
     * Admin: Update booking status
     */
    public function updateBookingStatus()
    {
        // Check if user is admin
        if (!$this->checkPermission('admin')) {
            if ($this->isAjax()) {
                $this->jsonError("Access denied", 403);
            } else {
                $this->redirect('/dashboard');
            }
            return;
        }
        
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/bookings');
            return;
        }
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        if (empty($input['bookingId']) || empty($input['status'])) {
            $this->jsonError("Booking ID and status are required", 400);
            return;
        }
        
        // Validate status
        $validStatuses = ['pending', 'confirmed', 'in-progress', 'completed', 'cancelled'];
        if (!in_array($input['status'], $validStatuses)) {
            $this->jsonError("Invalid status", 400);
            return;
        }
        
        // Update booking status
        $success = $this->serviceModel->updateBookingStatus($input['bookingId'], $input['status']);
        
        if ($success) {
            $this->jsonSuccess(
                ['status' => $input['status']],
                'Booking status updated successfully'
            );
        } else {
            $this->jsonError("Failed to update booking status. Please try again later.", 500);
        }
    }
    
    /**
     * Admin: Manage service types
     */
    public function manageServiceTypes()
    {
        // Check if user is admin
        if (!$this->checkPermission('admin')) {
            $this->redirect('/dashboard');
            return;
        }
        
        // Get all service types
        $serviceTypes = $this->serviceTypeModel->all();
        
        // Render the service types management view
        $this->render('admin/service-types', [
            'serviceTypes' => $serviceTypes
        ]);
    }
    
    /**
     * Admin: Add or update service type
     */
    public function saveServiceType()
    {
        // Check if user is admin
        if (!$this->checkPermission('admin')) {
            if ($this->isAjax()) {
                $this->jsonError("Access denied", 403);
            } else {
                $this->redirect('/dashboard');
            }
            return;
        }
        
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-types');
            return;
        }
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        if (empty($input['code']) || empty($input['name'])) {
            $this->jsonError("Service type code and name are required", 400);
            return;
        }
        
        // Prepare service type data
        $serviceTypeData = [
            'ST_CODE' => $input['code'],
            'ST_NAME' => $input['name'],
            'ST_DESCRIPTION' => $input['description'] ?? '',
            'ST_IS_ACTIVE' => isset($input['isActive']) ? (bool) $input['isActive'] : true
        ];
        
        // Check if updating or creating
        if (!empty($input['id'])) {
            // Update existing service type
            $success = $this->serviceTypeModel->updateServiceType($input['id'], $serviceTypeData);
            $message = 'Service type updated successfully';
        } else {
            // Check if service type with this code already exists
            $existingType = $this->serviceTypeModel->getServiceTypeByCode($input['code']);
            if ($existingType) {
                $this->jsonError("A service type with this code already exists", 400);
                return;
            }
            
            // Create new service type
            $success = $this->serviceTypeModel->createServiceType($serviceTypeData);
            $message = 'Service type created successfully';
        }
        
        if ($success) {
            $this->jsonSuccess(
                ['status' => 'saved'],
                $message
            );
        } else {
            $this->jsonError("Failed to save service type. Please try again later.", 500);
        }
    }
    
    /**
     * Admin: Toggle service type active status
     */
    public function toggleServiceTypeStatus()
    {
        // Check if user is admin
        if (!$this->checkPermission('admin')) {
            if ($this->isAjax()) {
                $this->jsonError("Access denied", 403);
            } else {
                $this->redirect('/dashboard');
            }
            return;
        }
        
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-types');
            return;
        }
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        if (empty($input['id']) || !isset($input['isActive'])) {
            $this->jsonError("Service type ID and active status are required", 400);
            return;
        }
        
        // Toggle service type status
        $success = $this->serviceTypeModel->toggleServiceTypeStatus($input['id'], (bool) $input['isActive']);
        
        if ($success) {
            $this->jsonSuccess(
                ['status' => $input['isActive'] ? 'active' : 'inactive'],
                'Service type status updated successfully'
            );
        } else {
            $this->jsonError("Failed to update service type status. Please try again later.", 500);
        }
    }
}