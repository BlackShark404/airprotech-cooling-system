<?php

namespace App\Controllers;

use App\Models\BookAssignmentModel;
use App\Models\ServiceRequestModel;
use App\Models\ServiceRequestTypeModel;
use App\Models\TechnicianModel;

class ServiceRequestController extends BaseController
{
    private $serviceModel;
    private $serviceTypeModel;
    private $technicianModel;
    private $assignmentModel;
    
    public function __construct()
    {
        parent::__construct();
        // Direct instantiation instead of using loadModel method
        $this->serviceModel = new ServiceRequestModel();
        $this->serviceTypeModel = new ServiceRequestTypeModel();
        $this->technicianModel = new TechnicianModel();
        $this->assignmentModel = new BookAssignmentModel();
    }
    
    /**
     * Display admin service requests management page
     */
    public function adminServiceRequests()
    {
        // Render the service requests management view
        $this->render('admin/service-request');
    }
    
    /**
     * Get service requests data for DataTables
     */
    public function getServiceRequestsData()
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Get filter parameters from the request
        $filters = $this->getJsonInput();
        
        // Get service bookings with details and filters
        $bookings = $this->serviceModel->getAllBookingsWithDetails($filters);
        
        // Prepare the response for DataTables
        $response = [
            'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            'recordsTotal' => count($bookings),
            'recordsFiltered' => count($bookings),
            'data' => $bookings
        ];
        
        $this->json($response);
    }
    
    /**
     * Get active service types for dropdowns
     */
    public function getActiveServiceTypes()
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Get all active service types
        $serviceTypes = $this->serviceTypeModel->getActiveServiceTypes();
        
        $this->jsonSuccess($serviceTypes);
    }
    
    /**
     * Get active technicians for dropdowns
     */
    public function getActiveTechnicians()
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Get all active technicians
        $technicians = $this->technicianModel->getActiveTechnicians();
        
        $this->jsonSuccess($technicians);
    }
    
    /**
     * Get service request details by ID
     * 
     * @param int $id Service request ID
     */
    public function getServiceRequest($id = null)
    {
      
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Validate ID
        if (!$id) {
            $this->jsonError('Invalid service request ID', 400);
            return;
        }
        
        // Get the service booking with details
        $booking = $this->serviceModel->getBookingWithDetails($id);
        
        if (!$booking) {
            $this->jsonError('Service request not found', 404);
            return;
        }
        
        // Get assignment details if assigned
        if (!empty($booking['technician_id'])) {
            $assignment = $this->assignmentModel->getBookingAssignment($id);
            $booking['assignment_notes'] = $assignment['ba_notes'] ?? '';
            $booking['assignment_date'] = $assignment['ba_assigned_at'] ?? '';
        }
        
        $this->jsonSuccess($booking);
    }
    
    /**
     * Update service request
     */
    public function updateServiceRequest()
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        if (empty($input['bookingId'])) {
            $this->jsonError('Service request ID is required', 400);
            return;
        }
        
        // Prepare booking data
        $bookingData = [];
        
        // Update status if provided
        if (isset($input['status'])) {
            $validStatuses = ['pending', 'confirmed', 'in-progress', 'completed', 'cancelled'];
            if (!in_array($input['status'], $validStatuses)) {
                $this->jsonError('Invalid status value', 400);
                return;
            }
            $bookingData['sb_status'] = $input['status'];
        }
        
        // Update priority if provided
        if (isset($input['priority'])) {
            $validPriorities = ['normal', 'moderate', 'urgent'];
            if (!in_array($input['priority'], $validPriorities)) {
                $this->jsonError('Invalid priority value', 400);
                return;
            }
            $bookingData['sb_priority'] = $input['priority'];
        }
        
        // Update dates if provided
        if (isset($input['requestedDate'])) {
            $bookingData['sb_requested_date'] = $input['requestedDate'];
        }
        
        if (isset($input['requestedTime'])) {
            $bookingData['sb_requested_time'] = $input['requestedTime'];
        }
        
        // Only proceed if there's data to update
        if (empty($bookingData)) {
            $this->jsonError('No data provided for update', 400);
            return;
        }
        
        // Update the booking
        $success = $this->serviceModel->updateBooking($input['bookingId'], $bookingData);
        
        if ($success) {
            $this->jsonSuccess(
                ['status' => $bookingData['sb_status'] ?? null],
                'Service request updated successfully'
            );
        } else {
            $this->jsonError('Failed to update service request. Please try again later.', 500);
        }
    }
    
    /**
     * Assign technician to service request
     */
    public function assignTechnician()
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        if (empty($input['bookingId']) || empty($input['technicianId'])) {
            $this->jsonError('Booking ID and technician ID are required', 400);
            return;
        }
        
        // Get the booking to confirm it exists
        $booking = $this->serviceModel->getBookingWithDetails($input['bookingId']);
        if (!$booking) {
            $this->jsonError('Service request not found', 404);
            return;
        }
        
        // Get the technician to confirm they exist
        $technician = $this->technicianModel->getTechnicianById($input['technicianId']);
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
            return;
        }
        
        // Create the assignment
        $notes = $input['notes'] ?? '';
        $success = $this->assignmentModel->assignBooking($input['bookingId'], $input['technicianId'], $notes);
        
        // If we're assigning a technician, update the status to confirmed if it's pending
        if ($success && $booking['sb_status'] === 'pending') {
            $this->serviceModel->updateBookingStatus($input['bookingId'], 'confirmed');
        }
        
        if ($success) {
            $this->jsonSuccess(
                [
                    'technicianName' => $technician['ua_first_name'] . ' ' . $technician['ua_last_name'],
                    'technicianId' => $technician['te_account_id']
                ],
                'Technician assigned successfully'
            );
        } else {
            $this->jsonError('Failed to assign technician. Please try again later.', 500);
        }
    }
    
    /**
     * Unassign technician from service request
     */
    public function unassignTechnician()
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Get JSON input from request body
        $input = $this->getJsonInput();
        
        // Validate required fields
        if (empty($input['bookingId'])) {
            $this->jsonError('Booking ID is required', 400);
            return;
        }
        
        // Unassign the booking
        $success = $this->assignmentModel->unassignBooking($input['bookingId']);
        
        if ($success) {
            $this->jsonSuccess(
                ['unassigned' => true],
                'Technician unassigned successfully'
            );
        } else {
            $this->jsonError('Failed to unassign technician. Please try again later.', 500);
        }
    }
    
    /**
     * Delete service request
     * 
     * @param int $id Service request ID
     */
    public function deleteServiceRequest($id = null)
    {
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->redirect('/admin/service-requests');
            return;
        }
        
        // Validate ID
        if (!$id) {
            $this->jsonError('Invalid service request ID', 400);
            return;
        }
        
        // Get the booking to confirm it exists
        $booking = $this->serviceModel->getBookingWithDetails($id);
        if (!$booking) {
            $this->jsonError('Service request not found', 404);
            return;
        }
        
        // Delete the booking
        $success = $this->serviceModel->deleteBooking($id);
        
        if ($success) {
            $this->jsonSuccess(
                ['deleted' => true],
                'Service request deleted successfully'
            );
        } else {
            $this->jsonError('Failed to delete service request. Please try again later.', 500);
        }
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
            'sb_status' => 'pending',
            'sb_priority' => 'moderate' // Default priority
        ];

        $exclude = [
            'sb_id',      // Exclude the primary key if it's auto-generated
        ];
        
        // Create the booking
        $success = $this->serviceModel->createBooking($bookingData, $exclude);
        
        if ($success) {
            $this->jsonSuccess(
                ['status' => 'pending', 'redirect_url' => '/user/bookings'],
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
        
        if (!$userId) {
            $this->redirect('/login');
            return;
        }
        
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
        
        if (!$userId) {
            if ($this->isAjax()) {
                $this->jsonError("You must be logged in to cancel a booking", 401);
            } else {
                $this->redirect('/login');
            }
            return;
        }
 
        // Get the booking
        $booking = $this->serviceModel->getBookingWithDetails($id);
        
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
}