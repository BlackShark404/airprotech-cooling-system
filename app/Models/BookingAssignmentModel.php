<?php

namespace App\Models;

class BookingAssignmentModel extends BaseModel
{
    protected $table = 'booking_assignment';
    protected $primaryKey = 'ba_id';
    protected $useSoftDeletes = false;
    protected $timestamps = true;
    protected $createdAtColumn = 'ba_assigned_at';
    protected $updatedAtColumn = 'ba_updated_at';
    
    protected $fillable = [
        'ba_booking_id',
        'ba_technician_id',
        'ba_status',
        'ba_notes',
        'ba_started_at',
        'ba_completed_at'
    ];

    /**
     * Get all assignments for a specific technician
     * 
     * @param int $technicianId The technician ID
     * @return array The assignments for the technician
     */
    public function getAssignmentsByTechnician($technicianId)
    {
        return $this->select('booking_assignment.*, service_booking.sb_service_type_id, 
                            service_booking.sb_preferred_date, service_booking.sb_preferred_time, 
                            service_booking.sb_address, service_booking.sb_description, 
                            service_booking.sb_status as booking_status, service_booking.sb_priority,
                            service_type.st_name as service_type_name,
                            CONCAT(user_account.ua_first_name, " ", user_account.ua_last_name) as customer_name')
                    ->join('service_booking', 'booking_assignment.ba_booking_id = service_booking.sb_id', 'INNER')
                    ->join('service_type', 'service_booking.sb_service_type_id = service_type.st_id', 'INNER')
                    ->join('customer', 'service_booking.sb_customer_id = customer.cu_account_id', 'INNER')
                    ->join('user_account', 'customer.cu_account_id = user_account.ua_id', 'INNER')
                    ->where('booking_assignment.ba_technician_id = :technician_id')
                    ->bind(['technician_id' => $technicianId])
                    ->orderBy('service_booking.sb_preferred_date DESC, service_booking.sb_preferred_time DESC')
                    ->get();
    }

    /**
     * Get all assignments for a specific service booking
     * 
     * @param int $bookingId The service booking ID
     * @return array The assignments for the service booking
     */
    public function getAssignmentsByBooking($bookingId)
    {
        return $this->select('booking_assignment.*, 
                            CONCAT(user_account.ua_first_name, " ", user_account.ua_last_name) as technician_name')
                    ->join('technician', 'booking_assignment.ba_technician_id = technician.te_account_id', 'INNER')
                    ->join('user_account', 'technician.te_account_id = user_account.ua_id', 'INNER')
                    ->where('booking_assignment.ba_booking_id = :booking_id')
                    ->bind(['booking_id' => $bookingId])
                    ->get();
    }

    /**
     * Create a new assignment
     * 
     * @param array $data The assignment data
     * @return int|bool The new assignment ID or false on failure
     */
    public function createAssignment($data)
    {
        return $this->insert($data);
    }

    /**
     * Update an assignment
     * 
     * @param int $id The assignment ID
     * @param array $data The assignment data
     * @return bool Success or failure
     */
    public function updateAssignment($id, $data)
    {
        return $this->update($data, 'ba_id = :id', ['id' => $id]);
    }

    /**
     * Delete an assignment
     * 
     * @param int $id The assignment ID
     * @return bool Success or failure
     */
    public function deleteAssignment($id)
    {
        return $this->delete('ba_id = :id', ['id' => $id]);
    }
}