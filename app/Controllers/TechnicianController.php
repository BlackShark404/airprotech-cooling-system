<?php

namespace App\Controllers;

use App\Models\TechnicianModel;
use App\Models\UserModel;
use App\Models\ServiceRequestModel;
use App\Models\BookingAssignmentModal;
use App\Models\ServiceRequestTypeModel;

class TechnicianController extends BaseController
{
    private $technicianModel;
    private $userModel;
    private $serviceModel;
    private $bookingAssignmentModel;
    private $serviceTypeModel;

    public function __construct()
    {
        parent::__construct();
        $this->technicianModel = $this->loadModel('TechnicianModel');
        $this->userModel = $this->loadModel('UserModel');
        $this->serviceModel = $this->loadModel('ServiceRequestModel');
        $this->bookingAssignmentModel = $this->loadModel('BookingAssignmentModal');
        $this->serviceTypeModel = $this->loadModel('ServiceRequestTypeModel');
    }

    /**
     * API endpoint to get all technicians
     */
    public function getAllTechnicians()
    {
        $technicians = $this->technicianModel->getAllTechnicians();
        
        // Format data for DataTables
        $formattedData = [];
        foreach ($technicians as $technician) {
            $formattedData[] = [
                'id' => $technician['te_account_id'],
                'first_name' => $technician['ua_first_name'],
                'last_name' => $technician['ua_last_name'],
                'email' => $technician['ua_email'],
                'phone' => $technician['ua_phone_number'],
                'address' => $technician['ua_address'],
                'profile_url' => $technician['ua_profile_url'] ?? '/assets/images/avatars/default.jpg',
                'is_available' => $technician['te_is_available'] ? true : false,
                'is_active' => $technician['ua_is_active'] ? true : false,
                'full_name' => $technician['ua_first_name'] . ' ' . $technician['ua_last_name']
            ];
        }
        
        $this->jsonSuccess($formattedData);
    }

    /**
     * API endpoint to get a single technician by ID
     */
    public function getTechnician($id)
    {
        $technician = $this->technicianModel->getTechnicianByAccountId($id);
        
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
        }
        
        // Get technician stats
        $technicianStats = $this->technicianModel->getTechnicianStats($id);
        
        // Get current assignments
        $currentAssignments = $this->technicianModel->getCurrentAssignments($id);
        
        $formattedData = [
            'id' => $technician['te_account_id'],
            'first_name' => $technician['ua_first_name'],
            'last_name' => $technician['ua_last_name'],
            'email' => $technician['ua_email'],
            'phone' => $technician['ua_phone_number'],
            'address' => $technician['ua_address'],
            'profile_url' => $technician['ua_profile_url'] ?? '/assets/images/avatars/default.jpg',
            'is_available' => $technician['te_is_available'] ? true : false,
            'is_active' => $technician['ua_is_active'] ? true : false,
            'full_name' => $technician['ua_first_name'] . ' ' . $technician['ua_last_name'],
            'stats' => $technicianStats,
            'assignments' => $currentAssignments
        ];
        
        $this->jsonSuccess($formattedData);
    }

    /**
     * API endpoint to create a new technician
     */
    public function createTechnician()
    {
        if (!$this->isPost()) {
            $this->jsonError('Method not allowed', 405);
        }

        $data = $this->getJsonInput();
        
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password', 'phone'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->jsonError("Field '$field' is required");
            }
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($data['email'])) {
            $this->jsonError('Email already exists');
        }
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Create user account
            $userAccountData = [
                'ua_first_name' => $data['first_name'],
                'ua_last_name' => $data['last_name'],
                'ua_email' => $data['email'],
                'ua_hashed_password' => $this->userModel->hashPassword($data['password']),
                'ua_phone_number' => $data['phone'],
                'ua_address' => $data['address'] ?? '',
                'ua_role_id' => 2, // Role ID for technician
                'ua_is_active' => $data['is_active'] ?? true
            ];
            
            if (isset($data['profile_url'])) {
                $userAccountData['ua_profile_url'] = $data['profile_url'];
            }
            
            $userId = $this->userModel->createUser($userAccountData);
            
            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }
            
            // Create technician record
            $technicianCreated = $this->technicianModel->createTechnician(
                $userId,
                $data['is_available'] ?? true
            );
            
            if (!$technicianCreated) {
                throw new \Exception('Failed to create technician record');
            }
            
            // Commit transaction
            $this->db->commit();
            
            // Get the created technician with details
            $technician = $this->technicianModel->getTechnicianByAccountId($userId);
            
            $this->jsonSuccess([
                'id' => $technician['te_account_id'],
                'first_name' => $technician['ua_first_name'],
                'last_name' => $technician['ua_last_name'],
                'email' => $technician['ua_email'],
                'phone' => $technician['ua_phone_number'],
                'address' => $technician['ua_address'],
                'profile_url' => $technician['ua_profile_url'] ?? '/assets/images/avatars/default.jpg',
                'is_available' => $technician['te_is_available'] ? true : false,
                'is_active' => $technician['ua_is_active'] ? true : false,
                'full_name' => $technician['ua_first_name'] . ' ' . $technician['ua_last_name']
            ], 'Technician created successfully');
            
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->rollBack();
            $this->jsonError('Failed to create technician: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to update a technician
     */
    public function updateTechnician($id)
    {

        if (!$this->isPost() && !$this->isPut()) {
            $this->jsonError('Method not allowed', 405);
        }

        // Check if technician exists
        $technician = $this->technicianModel->getTechnicianByAccountId($id);
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
        }

        $data = $this->getJsonInput();
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Update user account
            $userAccountData = [];
            
            if (isset($data['first_name'])) {
                $userAccountData['ua_first_name'] = $data['first_name'];
            }
            
            if (isset($data['last_name'])) {
                $userAccountData['ua_last_name'] = $data['last_name'];
            }
            
            if (isset($data['email']) && $data['email'] != $technician['ua_email']) {
                // Check if new email already exists
                if ($this->userModel->emailExists($data['email'])) {
                    $this->jsonError('Email already exists');
                }
                $userAccountData['ua_email'] = $data['email'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $userAccountData['ua_hashed_password'] = $this->userModel->hashPassword($data['password']);
            }
            
            if (isset($data['phone'])) {
                $userAccountData['ua_phone_number'] = $data['phone'];
            }
            
            if (isset($data['address'])) {
                $userAccountData['ua_address'] = $data['address'];
            }
            
            if (isset($data['profile_url'])) {
                $userAccountData['ua_profile_url'] = $data['profile_url'];
            }
            
            if (isset($data['is_active'])) {
                $userAccountData['ua_is_active'] = $data['is_active'] ? true : false;
            }
            
            // Update user account if we have data to update
            if (!empty($userAccountData)) {
                $userUpdated = $this->userModel->updateUser($id, $userAccountData);
                
                if (!$userUpdated) {
                    throw new \Exception('Failed to update user account');
                }
            }
            
            // Update technician record
            if (isset($data['is_available'])) {
                $technicianUpdated = $this->technicianModel->updateAvailability(
                    $id,
                    $data['is_available'] ? true : false
                );
                
                if (!$technicianUpdated) {
                    throw new \Exception('Failed to update technician availability');
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            // Get the updated technician with details
            $updatedTechnician = $this->technicianModel->getTechnicianByAccountId($id);
            
            $this->jsonSuccess([
                'id' => $updatedTechnician['te_account_id'],
                'first_name' => $updatedTechnician['ua_first_name'],
                'last_name' => $updatedTechnician['ua_last_name'],
                'email' => $updatedTechnician['ua_email'],
                'phone' => $updatedTechnician['ua_phone_number'],
                'address' => $updatedTechnician['ua_address'],
                'profile_url' => $updatedTechnician['ua_profile_url'] ?? '/assets/images/avatars/default.jpg',
                'is_available' => $updatedTechnician['te_is_available'] ? true : false,
                'is_active' => $updatedTechnician['ua_is_active'] ? true : false,
                'full_name' => $updatedTechnician['ua_first_name'] . ' ' . $updatedTechnician['ua_last_name']
            ], 'Technician updated successfully');
            
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->rollBack();
            $this->jsonError('Failed to update technician: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to delete a technician
     */
    public function deleteTechnician($id)
    {
        if (!$this->isPost() && !$this->isDelete()) {
            $this->jsonError('Method not allowed', 405);
        }

        // Check if technician exists
        $technician = $this->technicianModel->getTechnicianByAccountId($id);
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
        }

        // Check if technician has active assignments
        $currentAssignments = $this->technicianModel->getCurrentAssignments($id);
        if (count($currentAssignments) > 0) {
            $this->jsonError('Cannot delete technician with active assignments. Please reassign or complete all assignments first.');
        }

        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Delete technician record
            $technicianDeleted = $this->technicianModel->deleteTechnician($id);
            
            if (!$technicianDeleted) {
                throw new \Exception('Failed to delete technician record');
            }
            
            // Change user role to regular customer or deactivate
            $userUpdated = $this->userModel->updateUser($id, [
                'ua_role_id' => 1, // Change to customer role
                'ua_is_active' => false // Deactivate account
            ]);
            
            if (!$userUpdated) {
                throw new \Exception('Failed to update user account');
            }
            
            // Commit transaction
            $this->db->commit();
            
            $this->jsonSuccess(['id' => $id], 'Technician deleted successfully');
            
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->rollBack();
            $this->jsonError('Failed to delete technician: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to toggle technician availability
     */
    public function toggleAvailability($id)
    {
        if (!$this->isPost()) {
            $this->jsonError('Method not allowed', 405);
        }

        // Check if technician exists
        $technician = $this->technicianModel->getTechnicianByAccountId($id);
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
        }

        // Toggle availability
        $newAvailability = !$technician['te_is_available'];
        $updated = $this->technicianModel->updateAvailability($id, $newAvailability);
        
        if (!$updated) {
            $this->jsonError('Failed to update availability');
        }
        
        $this->jsonSuccess([
            'id' => $id,
            'is_available' => $newAvailability
        ], 'Availability updated successfully');
    }

    /**
     * API endpoint to get pending service bookings for assignment
     */
    public function getPendingBookings()
    {
        // Get pending service bookings
        $pendingBookings = $this->db->query("
            SELECT 
                sb.sb_id,
                sb.sb_requested_date,
                sb.sb_requested_time,
                sb.sb_address,
                sb.sb_description,
                sb.sb_status,
                sb.sb_priority,
                st.st_name as service_type,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_phone_number
            FROM service_booking sb
            JOIN service_type st ON sb.sb_service_type_id = st.st_id
            JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
            WHERE sb.sb_status = 'pending'
            ORDER BY 
                CASE 
                    WHEN sb.sb_priority = 'urgent' THEN 1
                    WHEN sb.sb_priority = 'moderate' THEN 2
                    ELSE 3
                END,
                sb.sb_requested_date,
                sb.sb_requested_time
        ")->fetchAll();
        
        $formattedData = [];
        foreach ($pendingBookings as $booking) {
            $formattedData[] = [
                'id' => $booking['sb_id'],
                'customer_name' => $booking['ua_first_name'] . ' ' . $booking['ua_last_name'],
                'phone' => $booking['ua_phone_number'],
                'service_type' => $booking['service_type'],
                'requested_date' => $booking['sb_requested_date'],
                'requested_time' => $booking['sb_requested_time'],
                'address' => $booking['sb_address'],
                'description' => $booking['sb_description'],
                'status' => $booking['sb_status'],
                'priority' => $booking['sb_priority']
            ];
        }
        
        $this->jsonSuccess($formattedData);
    }

    /**
     * API endpoint to assign a technician to a service booking
     */
    public function assignTechnician()
    {
        if (!$this->isPost()) {
            $this->jsonError('Method not allowed', 405);
        }

        $data = $this->getJsonInput();
        
        // Validate required fields
        if (!isset($data['booking_id']) || !isset($data['technician_id'])) {
            $this->jsonError('Booking ID and Technician ID are required');
        }
        
        $bookingId = $data['booking_id'];
        $technicianId = $data['technician_id'];
        $notes = $data['notes'] ?? '';
        
        // Check if booking exists and is pending
        $booking = $this->serviceModel->getBookingWithDetails($bookingId);
        if (!$booking) {
            $this->jsonError('Booking not found', 404);
        }
        
        if ($booking['sb_status'] !== 'pending') {
            $this->jsonError('Booking is already assigned or completed');
        }
        
        // Check if technician exists and is available
        $technician = $this->technicianModel->getTechnicianByAccountId($technicianId);
        if (!$technician) {
            $this->jsonError('Technician not found', 404);
        }
        
        if (!$technician['te_is_available'] || !$technician['ua_is_active']) {
            $this->jsonError('Technician is not available');
        }
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Create assignment
            $assigned = $this->bookingAssignmentModel->assignTechnician($bookingId, $technicianId, $notes);
            
            if (!$assigned) {
                throw new \Exception('Failed to create assignment');
            }
            
            // Update booking status to confirmed
            $updated = $this->serviceModel->updateBookingStatus($bookingId, 'confirmed');
            
            if (!$updated) {
                throw new \Exception('Failed to update booking status');
            }
            
            // Commit transaction
            $this->db->commit();
            
            $this->jsonSuccess([
                'booking_id' => $bookingId,
                'technician_id' => $technicianId,
                'technician_name' => $technician['ua_first_name'] . ' ' . $technician['ua_last_name'],
                'status' => 'confirmed'
            ], 'Technician assigned successfully');
            
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->rollBack();
            $this->jsonError('Failed to assign technician: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for AJAX methods (DataTables)
     */
    public function api()
    {
        if (!$this->isPost()) {
            $this->jsonError('Method not allowed', 405);
        }

        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'get_technicians':
                $this->getAllTechnicians();
                break;
                
            case 'get_technician':
                $id = $_POST['id'] ?? 0;
                $this->getTechnician($id);
                break;
                
            case 'create_technician':
                $this->createTechnician();
                break;
                
            case 'update_technician':
                $id = $_POST['id'] ?? 0;
                $this->updateTechnician($id);
                break;
                
            case 'delete_technician':
                $id = $_POST['id'] ?? 0;
                $this->deleteTechnician($id);
                break;
                
            case 'toggle_availability':
                $id = $_POST['id'] ?? 0;
                $this->toggleAvailability($id);
                break;
                
            case 'get_pending_bookings':
                $this->getPendingBookings();
                break;
                
            case 'assign_technician':
                $this->assignTechnician();
                break;
                
            default:
                $this->jsonError('Invalid action');
        }
    }

    /**
     * Helper: Check if request is PUT
     */
    private function isPut()
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    /**
     * Helper: Check if request is DELETE
     */
    private function isDelete()
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }
}