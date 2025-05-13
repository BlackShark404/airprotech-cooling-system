<?php

namespace App\Models;

class BookingAssignmentModel extends BaseModel
{
    protected $table = 'booking_assignment';
    protected $primaryKey = 'ba_id';

    protected $fillable = [
        'ba_booking_id',
        'ba_technician_id',
        'ba_assigned_at',
        'ba_status',
        'ba_notes',
        'ba_completed_at'
    ];

    protected $timestamps = true;
    protected $createdAtColumn = 'ba_assigned_at';
    protected $updatedAtColumn = null; // No updated_at column for this table

    /**
     * Get assignments for a specific booking
     * 
     * @param int $bookingId The booking ID
     * @return array Array of assignments
     */
    public function getBookingAssignments($bookingId)
    {
        return $this->where("ba_booking_id = :bookingId")
                    ->orderBy("ba_assigned_at DESC")
                    ->bind(['bookingId' => $bookingId])
                    ->get();
    }

    /**
     * Get current assignments for a technician
     * 
     * @param int $technicianId The technician ID
     * @return array Array of assignments
     */
    public function getTechnicianAssignments($technicianId)
    {
        return $this->where("ba_technician_id = :technicianId")
                    ->where("ba_status != 'completed'")
                    ->orderBy("ba_assigned_at DESC")
                    ->bind(['technicianId' => $technicianId])
                    ->get();
    }

    /**
     * Assign a technician to a booking
     * 
     * @param int $bookingId The booking ID
     * @param int $technicianId The technician ID
     * @param string $notes Assignment notes (optional)
     * @return bool Success status
     */
    public function assignTechnician($bookingId, $technicianId, $notes = '')
    {
        // Check if assignment already exists
        $existingAssignment = $this->where("ba_booking_id = :bookingId AND ba_technician_id = :technicianId")
                                ->bind([
                                    'bookingId' => $bookingId,
                                    'technicianId' => $technicianId
                                ])
                                ->first();
        
        if ($existingAssignment) {
            // Update existing assignment
            return $this->update(
                [
                    'ba_status' => 'assigned',
                    'ba_notes' => $notes
                ],
                "ba_id = :assignmentId",
                ['assignmentId' => $existingAssignment['ba_id']]
            );
        } else {
            // Create new assignment
            return $this->insert([
                'ba_booking_id' => $bookingId,
                'ba_technician_id' => $technicianId,
                'ba_status' => 'assigned',
                'ba_notes' => $notes
            ]);
        }
    }

    /**
     * Update assignment status
     * 
     * @param int $assignmentId The assignment ID
     * @param string $status New status value
     * @param string $notes Additional notes (optional)
     * @return bool Success status
     */
    public function updateAssignmentStatus($assignmentId, $status, $notes = null)
    {
        $data = ['ba_status' => $status];
        
        // If status is completed, set completion timestamp
        if ($status === 'completed') {
            $data['ba_completed_at'] = date('Y-m-d H:i:s');
        }
        
        // Add notes if provided
        if ($notes !== null) {
            $data['ba_notes'] = $notes;
        }
        
        return $this->update(
            $data,
            "ba_id = :assignmentId",
            ['assignmentId' => $assignmentId]
        );
    }

    /**
     * Get assignment with booking and technician details
     * 
     * @param int $assignmentId The assignment ID
     * @return array|null The assignment with details or null if not found
     */
    public function getAssignmentWithDetails($assignmentId)
    {
        $this->select('
            ba.*,
            sb.sb_preferred_date,
            sb.sb_preferred_time,
            sb.sb_address,
            sb.sb_description,
            sb.sb_status as booking_status,
            sb.sb_priority as booking_priority,
            st.st_name as service_name,
            ua.ua_first_name as customer_first_name,
            ua.ua_last_name as customer_last_name,
            ua.ua_phone_number as customer_phone,
            t.te_first_name as technician_first_name,
            t.te_last_name as technician_last_name,
            t.te_phone_number as technician_phone
        ')
        ->where("ba.ba_id = :assignmentId")
        ->join('service_booking sb', 'ba.ba_booking_id', 'sb.sb_id')
        ->join('service_type st', 'sb.sb_service_type_id', 'st.st_id')
        ->join('user_account ua', 'sb.sb_customer_id', 'ua.ua_id')
        ->join('technician t', 'ba.ba_technician_id', 't.te_account_id');

        // Change table alias in the select
        $originalTable = $this->table;
        $this->table = 'booking_assignment ba';
        
        $result = $this->bind(['assignmentId' => $assignmentId])->first();
        
        // Restore original table name
        $this->table = $originalTable;
        
        return $result;
    }

    /**
     * Get assignments for multiple bookings
     * 
     * @param array $bookingIds Array of booking IDs
     * @return array Associative array with booking IDs as keys and arrays of assignments as values
     */
    public function getAssignmentsForBookings(array $bookingIds)
    {
        if (empty($bookingIds)) {
            return [];
        }

        $this->select('
            ba.*,
            t.te_first_name,
            t.te_last_name,
            t.te_phone_number
        ')
        ->whereIn('ba.ba_booking_id', $bookingIds)
        ->join('technician t', 'ba.ba_technician_id', 't.te_account_id')
        ->orderBy('ba.ba_assigned_at DESC');

        // Change table alias in the select
        $originalTable = $this->table;
        $this->table = 'booking_assignment ba';
        
        $assignments = $this->get();
        
        // Restore original table name
        $this->table = $originalTable;
        
        // Group assignments by booking ID
        $result = [];
        foreach ($bookingIds as $bookingId) {
            $result[$bookingId] = [];
        }
        
        foreach ($assignments as $assignment) {
            $bookingId = $assignment['ba_booking_id'];
            $result[$bookingId][] = $assignment;
        }
        
        return $result;
    }

    /**
     * Delete an assignment
     * 
     * @param int $assignmentId The assignment ID
     * @return bool Success status
     */
    public function deleteAssignment($assignmentId)
    {
        return $this->delete(
            "ba_id = :assignmentId",
            ['assignmentId' => $assignmentId]
        );
    }
}