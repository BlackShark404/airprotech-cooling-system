<?php

namespace App\Models;

class ServiceRequestModel extends BaseModel
{
    protected $table = 'service_booking';
    protected $primaryKey = 'sb_id';

    protected $fillable = [
        'sb_customer_id',
        'sb_service_type_id',
        'sb_requested_date',
        'sb_requested_time',
        'sb_address',
        'sb_description',
        'sb_status',
        'sb_priority'
    ];

    protected $useSoftDeletes = true;
    protected $deletedAtColumn = 'sb_deleted_at';

    protected $timestamps = true;
    protected $createdAtColumn = 'sb_created_at';
    protected $updatedAtColumn = 'sb_updated_at';

    
    /**
     * Get service bookings for a specific customer
     * 
     * @param int $customerId The customer ID
     * @return array Array of bookings
     */
    public function getCustomerBookings($customerId)
    {
        return $this->where("sb_customer_id = :customerId")
                    ->orderBy("sb_requested_date DESC, sb_requested_time DESC")
                    ->bind(['customerId' => $customerId])
                    ->get();

    }
    
    /**
     * Get service booking with customer and service type details
     * 
     * @param int $bookingId The booking ID
     * @return array|null The booking with additional details or null if not found
     */
    public function getBookingWithDetails($bookingId)
    {
        $this->select('
            sb.*, 
            st.st_name as service_name, 
            st.st_description as service_description,
            ua.ua_first_name as customer_first_name,
            ua.ua_last_name as customer_last_name,
            ua.ua_email as customer_email,
            ua.ua_phone_number as customer_phone
        ')
        ->where("sb.sb_id = :bookingId")
        ->join('service_type st', 'sb.sb_service_type_id', 'st.st_id')
        ->join('user_account ua', 'sb.sb_customer_id', 'ua.ua_id');

        // Change table alias in the select
        $originalTable = $this->table;
        $this->table = 'service_booking sb';

        
        $result = $this->bind(['bookingId' => $bookingId])->first();
        
        // Restore original table name
        $this->table = $originalTable;
        
        return $result;
    }
    
    /**
     * Create a new service booking
     * 
     * @param array $data Booking data
     * @return bool Success status
     */
    public function createBooking($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Update a service booking
     * 
     * @param int $bookingId The booking ID
     * @param array $data Updated booking data
     * @return bool Success status
     */
    public function updateBooking($bookingId, $data)
    {
        return $this->update(
            $data,
            "sb_id = :bookingId",
            ['bookingId' => $bookingId]
        );

    }
    
    /**
     * Update the status of a service booking
     * 
     * @param int $bookingId The booking ID
     * @param string $status New status value
     * @return bool Success status
     */
    public function updateBookingStatus($bookingId, $status)
    {
        return $this->update(
            ['sb_status' => $status],
            "sb_id = :bookingId",
            ['bookingId' => $bookingId]
        );  
    }

    public function updateBookingPriority($bookingId, $priority)
    {
        return $this->update(
            ['sb_priority' => $priority],
            "sb_id = :bookingId",
            ['bookingId' => $bookingId]
        );  
    }
    
    /**
     * Cancel a service booking
     * 
     * @param int $bookingId The booking ID
     * @return bool Success status
     */
    public function cancelBooking($bookingId)
    {
        return $this->updateBookingStatus($bookingId, 'cancelled');
    }
    
    /**
     * Delete a service booking (soft delete)
     * 
     * @param int $bookingId The booking ID
     * @return bool Success status
     */
    public function deleteBooking($bookingId)
    {
        return $this->delete(
            "sb_id = :bookingId",
            ['bookingId' => $bookingId]
        );
    }
}