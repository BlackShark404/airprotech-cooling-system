<?php

namespace App\Models;

class BookAssignmentModel extends BaseModel
{
    protected $table = 'booking_assignment';
    
    /**
     * Get assignment information for a specific booking
     * 
     * @param int $bookingId The booking ID
     * @return array|null The active assignment or null if not found
     */
    public function getBookingAssignment($bookingId)
    {
        $sql = "SELECT ba.*,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                ua.ua_phone_number
                FROM {$this->table} ba
                JOIN technician t ON ba.ba_technician_id = t.te_account_id
                JOIN user_account ua ON t.te_account_id = ua.ua_id
                WHERE ba.ba_booking_id = :bookingId
                AND ba.ba_status != 'unassigned'
                AND ba.ba_completed_at IS NULL
                ORDER BY ba.ba_assigned_at DESC
                LIMIT 1";
                
        return $this->queryOne($sql, ['bookingId' => $bookingId]);
    }
    
    /**
     * Get all assignment history for a booking
     * 
     * @param int $bookingId The booking ID
     * @return array Assignment history
     */
    public function getBookingAssignmentHistory($bookingId)
    {
        $sql = "SELECT ba.*,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                ua.ua_phone_number
                FROM {$this->table} ba
                JOIN technician t ON ba.ba_technician_id = t.te_account_id
                JOIN user_account ua ON t.te_account_id = ua.ua_id
                WHERE ba.ba_booking_id = :bookingId
                ORDER BY ba.ba_assigned_at DESC";
                
        return $this->query($sql, ['bookingId' => $bookingId]);
    }
    
    /**
     * Get all current assignments for a technician
     * 
     * @param int $technicianId The technician ID
     * @return array Current active assignments
     */
    public function getTechnicianAssignments($technicianId)
    {
        $sql = "SELECT ba.*,
                sb.sb_requested_date,
                sb.sb_requested_time,
                sb.sb_status,
                sb.sb_priority,
                sb.sb_address,
                ua.ua_first_name as customer_first_name,
                ua.ua_last_name as customer_last_name,
                st.st_name as service_type_name
                FROM {$this->table} ba
                JOIN service_booking sb ON ba.ba_booking_id = sb.sb_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                WHERE ba.ba_technician_id = :technicianId
                AND ba.ba_status != 'unassigned'
                AND ba.ba_completed_at IS NULL
                AND sb.sb_deleted_at IS NULL
                AND sb.sb_status NOT IN ('completed', 'cancelled')
                ORDER BY sb.sb_requested_date ASC, sb.sb_requested_time ASC";
                
        return $this->query($sql, ['technicianId' => $technicianId]);
    }
    
    /**
     * Assign a booking to a technician
     * 
     * @param int $bookingId The booking ID
     * @param int $technicianId The technician ID
     * @param string $notes Assignment notes
     * @return bool Success status
     */
    public function assignBooking($bookingId, $technicianId, $notes = '')
    {
        // First, change status of any existing assignments to 'unassigned'
        $this->deactivateExistingAssignments($bookingId);
        
        // Then create a new assignment
        $data = [
            'ba_booking_id' => $bookingId,
            'ba_technician_id' => $technicianId,
            'ba_notes' => $notes,
            'ba_assigned_at' => date('Y-m-d H:i:s'),
            'ba_status' => 'assigned'
        ];
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']}) 
                VALUES ({$formatted['placeholders']})";
                
        return $this->execute($sql, $formatted['filteredData']) > 0;
    }
    
    /**
     * Deactivate any existing active assignments for a booking
     * 
     * @param int $bookingId The booking ID
     * @return bool Success status
     */
    private function deactivateExistingAssignments($bookingId)
    {
        $sql = "UPDATE {$this->table} 
                SET ba_status = 'unassigned' 
                WHERE ba_booking_id = :bookingId
                AND ba_status != 'unassigned'
                AND ba_completed_at IS NULL";
                
        return $this->execute($sql, ['bookingId' => $bookingId]) >= 0;
    }
    
    /**
     * Mark assignment as in-progress
     * 
     * @param int $assignmentId The assignment ID
     * @return bool Success status
     */
    public function startAssignment($assignmentId)
    {
        $sql = "UPDATE {$this->table} 
                SET ba_status = 'in-progress'
                WHERE ba_id = :assignmentId
                AND ba_status = 'assigned'";
                
        return $this->execute($sql, ['assignmentId' => $assignmentId]) > 0;
    }
    
    /**
     * Mark assignment as completed
     * 
     * @param int $assignmentId The assignment ID
     * @return bool Success status
     */
    public function completeAssignment($assignmentId)
    {
        $sql = "UPDATE {$this->table} 
                SET ba_status = 'completed',
                ba_completed_at = NOW() 
                WHERE ba_id = :assignmentId
                AND ba_status IN ('assigned', 'in-progress')";
                
        return $this->execute($sql, ['assignmentId' => $assignmentId]) > 0;
    }
    
    /**
     * Remove a technician from a booking assignment
     * 
     * @param int $bookingId The booking ID
     * @return bool Success status
     */
    public function unassignBooking($bookingId)
    {
        // Just deactivate any existing assignments
        return $this->deactivateExistingAssignments($bookingId);
    }
    
    /**
     * Delete an assignment (set as unassigned)
     * 
     * @param int $assignmentId The assignment ID
     * @return bool Success status
     */
    public function deleteAssignment($assignmentId)
    {
        $sql = "UPDATE {$this->table} 
                SET ba_status = 'unassigned' 
                WHERE ba_id = :assignmentId";
                
        return $this->execute($sql, ['assignmentId' => $assignmentId]) > 0;
    }
}