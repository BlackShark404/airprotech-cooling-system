<?php

namespace App\Controllers;

use App\Models\ServiceRequestModel;
use App\Models\BookingAssignmentModel;
use App\Models\UserModel;

class TechnicianController extends BaseController
{
    private $serviceRequestModel;
    private $bookingAssignmentModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        // Ensure technician is logged in and has the correct role for all methods in this controller
        if (!$this->checkPermission('technician')) {
            // If it's an API request, return JSON error, otherwise redirect
            if ($this->isAjax()) {
                $this->jsonError('Access denied. You must be a logged-in technician.', 403);
                exit;
            } else {
                // Redirect to login or a generic access denied page
                $_SESSION['flash_message'] = 'Access denied. You must be a logged-in technician.';
                $_SESSION['flash_type'] = 'danger';
                $this->redirect('/auth/login'); // Assuming you have a login route
                exit;
            }
        }

        $this->serviceRequestModel = new ServiceRequestModel();
        $this->bookingAssignmentModel = new BookingAssignmentModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display the technician dashboard.
     */
    public function dashboard()
    {
        // Technician's user ID will be in $_SESSION['user_id']
        $technicianId = $_SESSION['user_id'] ?? null;
        $technicianInfo = $this->userModel->findById($technicianId);

        $this->render('technician/dashboard', [
            'title' => 'Technician Dashboard',
            'technicianName' => $technicianInfo ? $technicianInfo['ua_first_name'] . ' ' . $technicianInfo['ua_last_name'] : 'Technician'
        ]);
    }

    /**
     * API endpoint to get service requests assigned to the logged-in technician.
     */
    public function getAssignedServiceRequests()
    {
        $technicianId = $_SESSION['user_id'];
        $assignments = $this->bookingAssignmentModel->getAssignmentsForTechnician($technicianId, ['assigned', 'in-progress']);

        $detailedAssignments = [];
        if ($assignments) {
            foreach ($assignments as $assignment) {
                $requestDetails = $this->serviceRequestModel->getBookingWithDetails($assignment['ba_booking_id']);
                if ($requestDetails) {
                    // Combine assignment info (like notes specific to this technician) with request details
                    $detailedAssignment = $requestDetails; // Start with all booking details
                    $detailedAssignment['ba_id'] = $assignment['ba_id'];
                    $detailedAssignment['technician_notes'] = $assignment['ba_notes'];
                    $detailedAssignment['assignment_status'] = $assignment['ba_status']; // Technician's specific assignment status
                    // Ensure main booking status is also present
                    $detailedAssignment['sb_status'] = $requestDetails['sb_status'];
                    
                    // Explicitly add fields expected by a potential DataTable
                    $detailedAssignment['sb_id'] = $requestDetails['sb_id'];
                    $detailedAssignment['customer_name'] = $requestDetails['customer_first_name'] . ' ' . $requestDetails['customer_last_name'];
                    $detailedAssignment['service_name'] = $requestDetails['service_name'];
                    $detailedAssignment['sb_address'] = $requestDetails['sb_address'];
                    $detailedAssignment['sb_preferred_date'] = $requestDetails['sb_preferred_date'];
                    $detailedAssignment['sb_preferred_time'] = $requestDetails['sb_preferred_time'];


                    $detailedAssignments[] = $detailedAssignment;
                }
            }
        }
        $this->jsonSuccess($detailedAssignments);
    }

    /**
     * API endpoint for a technician to update the status of their assigned service request.
     * This can also update the technician's notes for the assignment.
     */
    public function updateServiceAssignment()
    {
        $technicianId = $_SESSION['user_id'];
        $input = $this->getJsonInput();

        $bookingId = $input['bookingId'] ?? null;
        $assignmentId = $input['assignmentId'] ?? null; // Expecting ba_id
        $newStatus = $input['status'] ?? null;
        $notes = $input['notes'] ?? null; // Technician's notes for this assignment

        if (!$bookingId || !$assignmentId || !$newStatus) {
            $this->jsonError('Booking ID, Assignment ID, and new status are required.', 400);
            return;
        }

        // Verify the assignment belongs to this technician and booking
        $assignment = $this->bookingAssignmentModel->find($assignmentId); // Assuming find() gets by primary key ba_id

        if (!$assignment || $assignment['ba_technician_id'] != $technicianId || $assignment['ba_booking_id'] != $bookingId) {
            $this->jsonError('Invalid assignment or permission denied.', 403);
            return;
        }
        
        // Define valid statuses a technician can set
        $validTechnicianStatuses = ['in-progress', 'completed', 'on-hold', 'needs-parts']; // Example statuses
        if (!in_array($newStatus, $validTechnicianStatuses)) {
            $this->jsonError('Invalid status provided for assignment.', 400);
            return;
        }

        $this->pdo->beginTransaction();
        try {
            $assignmentUpdateData = [
                'ba_status' => $newStatus,
                'ba_notes' => $notes
            ];
            if ($newStatus === 'completed' && empty($assignment['ba_completed_at'])) {
                $assignmentUpdateData['ba_completed_at'] = date('Y-m-d H:i:s');
            } elseif ($newStatus === 'in-progress' && empty($assignment['ba_started_at'])) {
                 // Assuming ba_started_at exists. If not, this needs to be added to DB and model.
                 // For now, we'll just update status and notes.
                 // $assignmentUpdateData['ba_started_at'] = date('Y-m-d H:i:s');
            }

            $this->bookingAssignmentModel->updateAssignment($assignmentId, $assignmentUpdateData);

            // Optionally, update the main service booking status (SB_STATUS) if needed
            // This logic depends on your workflow. For example, if one technician completes their part,
            // does it mark the whole booking as 'in-progress' or 'completed'?
            // For now, we only update the assignment status.
            // If all assignments for a booking are 'completed', then update main booking to 'completed'.
            
            // Example: If this assignment's new status is 'completed', check other assignments.
            if ($newStatus === 'completed') {
                $allAssignmentsForBooking = $this->bookingAssignmentModel->getAssignmentsForBooking($bookingId);
                $allComplete = true;
                foreach ($allAssignmentsForBooking as $assgn) {
                    if ($assgn['ba_id'] == $assignmentId) { // Current assignment being updated
                        if ($newStatus !== 'completed') {
                            $allComplete = false;
                            break;
                        }
                    } elseif ($assgn['ba_status'] !== 'completed') {
                        $allComplete = false;
                        break;
                    }
                }
                if ($allComplete) {
                    $this->serviceRequestModel->updateBookingStatus($bookingId, 'completed');
                } elseif ($this->serviceRequestModel->getBookingById($bookingId)['sb_status'] === 'pending' || $this->serviceRequestModel->getBookingById($bookingId)['sb_status'] === 'confirmed') {
                     // If any technician starts work, or marks as in-progress, update main booking to 'in-progress'
                    $this->serviceRequestModel->updateBookingStatus($bookingId, 'in-progress');
                }

            } elseif ($newStatus === 'in-progress') {
                 // If main booking is 'pending' or 'confirmed', update to 'in-progress'
                $currentBookingStatus = $this->serviceRequestModel->getBookingById($bookingId)['sb_status'];
                if ($currentBookingStatus === 'pending' || $currentBookingStatus === 'confirmed') {
                    $this->serviceRequestModel->updateBookingStatus($bookingId, 'in-progress');
                }
            }


            $this->pdo->commit();
            $this->jsonSuccess(['status' => 'updated', 'new_assignment_status' => $newStatus], 'Service assignment updated successfully.');

        } catch (\Exception $e) {
            $this->pdo->rollback();
            $this->jsonError('Failed to update service assignment: ' . $e->getMessage(), 500);
        }
    }
} 