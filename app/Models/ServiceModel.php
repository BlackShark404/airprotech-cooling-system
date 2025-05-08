<?php

namespace App\Models;

class ServiceModel extends BaseModel
{
    protected $table = 'service_booking';
    
    /**
     * Get service bookings for a specific customer
     * 
     * @param int $customerId The customer ID
     * @return array Array of bookings
     */
    public function getCustomerBookings($customerId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE sb_customer_id = :customerId 
                AND sb_deleted_at IS NULL
                ORDER BY sb_requested_date DESC, sb_requested_time DESC";
                
        return $this->query($sql, ['customerId' => $customerId]);
    }
    
    /**
     * Get service booking with customer and service type details
     * 
     * @param int $bookingId The booking ID
     * @return array|null The booking with additional details or null if not found
     */
    public function getBookingWithDetails($bookingId)
    {
        $sql = "SELECT 
                    sb.*, 
                    st.st_name as service_name, 
                    st.st_description as service_description,
                    ua.ua_first_name as customer_first_name,
                    ua.ua_last_name as customer_last_name,
                    ua.ua_email as customer_email,
                    ua.ua_phone_number as customer_phone
                FROM service_booking sb
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                WHERE sb.sb_id = :bookingId
                AND sb.sb_deleted_at IS NULL";
                
        return $this->queryOne($sql, ['bookingId' => $bookingId]);
    }
    
    /**
     * Create a new service booking
     * 
     * @param array $data Booking data
     * @return bool Success status
     */
    public function createBooking($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) 
                VALUES ($placeholders)";
                
        return $this->execute($sql, $data) > 0;
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
        $updates = [];
        foreach (array_keys($data) as $column) {
            $updates[] = "$column = :$column";
        }
        $setClause = implode(', ', $updates);
        
        $sql = "UPDATE {$this->table} 
                SET $setClause 
                WHERE sb_id = :bookingId
                AND sb_deleted_at IS NULL";
                
        $params = array_merge($data, ['bookingId' => $bookingId]);
        
        return $this->execute($sql, $params) > 0;
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
        $sql = "UPDATE {$this->table} 
                SET sb_status = :status 
                WHERE sb_id = :bookingId
                AND sb_deleted_at IS NULL";
                
        return $this->execute($sql, [
            'status' => $status,
            'bookingId' => $bookingId
        ]) > 0;
    }

    /**
     * Update the priority of a service booking
     * 
     * @param int $bookingId The booking ID
     * @param int $priority New priority value
     * @return bool Success status
     */
    public function updateBookingPriority($bookingId, $priority)
    {
        $sql = "UPDATE {$this->table} 
                SET sb_priority = :priority 
                WHERE sb_id = :bookingId
                AND sb_deleted_at IS NULL";
                
        return $this->execute($sql, [
            'priority' => $priority,
            'bookingId' => $bookingId
        ]) > 0;
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
        $sql = "UPDATE {$this->table} 
                SET sb_deleted_at = NOW() 
                WHERE sb_id = :bookingId
                AND sb_deleted_at IS NULL";
                
        return $this->execute($sql, ['bookingId' => $bookingId]) > 0;
    }
}