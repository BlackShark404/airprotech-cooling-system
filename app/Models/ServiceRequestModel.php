<?php

namespace App\Models;

class ServiceRequestModel extends BaseModel
{
    protected $table = 'service_booking';
    
    /**
     * Get all service bookings with details (with optional filtering)
     * 
     * @param array $filters Optional filters (status, type, priority, technician, start/end date)
     * @return array Array of bookings with details
     */
    public function getAllBookingsWithDetails($filters = [])
    {
        $conditions = [];
        $params = [];
        
        // Base SQL query
        $sql = "SELECT 
                sb.*,
                st.st_name as service_name,
                st.st_code as service_code,
                ua.ua_first_name as customer_first_name,
                ua.ua_last_name as customer_last_name,
                ua.ua_email as customer_email,
                ua.ua_phone_number as customer_phone,
                CONCAT(tua.ua_first_name, ' ', tua.ua_last_name) as technician_name,
                ba.ba_technician_id as technician_id
                FROM {$this->table} sb
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                LEFT JOIN (
                    SELECT ba.ba_booking_id, ba.ba_technician_id
                    FROM booking_assignment ba
                    WHERE ba.ba_status != 'unassigned'
                    AND ba.ba_completed_at IS NULL
                ) ba ON sb.sb_id = ba.ba_booking_id
                LEFT JOIN technician t ON ba.ba_technician_id = t.te_account_id
                LEFT JOIN user_account tua ON t.te_account_id = tua.ua_id
                WHERE sb.sb_deleted_at IS NULL";
        
        // Add filters if provided
        if (!empty($filters['status'])) {
            $conditions[] = "sb.sb_status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['serviceType'])) {
            $conditions[] = "st.st_code = :serviceType";
            $params['serviceType'] = $filters['serviceType'];
        }
        
        if (!empty($filters['priority'])) {
            $conditions[] = "sb.sb_priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        
        if (isset($filters['technician'])) {
            if ($filters['technician'] === 'unassigned') {
                $conditions[] = "ba.ba_technician_id IS NULL";
            } elseif (!empty($filters['technician'])) {
                $conditions[] = "ba.ba_technician_id = :technicianId";
                $params['technicianId'] = $filters['technician'];
            }
        }
        
        if (!empty($filters['startDate'])) {
            $conditions[] = "sb.sb_requested_date >= :startDate";
            $params['startDate'] = $filters['startDate'];
        }
        
        if (!empty($filters['endDate'])) {
            $conditions[] = "sb.sb_requested_date <= :endDate";
            $params['endDate'] = $filters['endDate'];
        }
        
        // Add WHERE conditions
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }
        
        // Add order by
        $sql .= " ORDER BY sb.sb_requested_date DESC, sb.sb_requested_time DESC";
        
        return $this->query($sql, $params);
    }
    
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
     * Get service booking with customer, service type, and technician details
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
                    ua.ua_phone_number as customer_phone,
                    tua.ua_first_name as technician_first_name,
                    tua.ua_last_name as technician_last_name,
                    ba.ba_technician_id as technician_id,
                    CONCAT(tua.ua_first_name, ' ', tua.ua_last_name) as technician_name
                FROM service_booking sb
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                LEFT JOIN (
                    SELECT ba.ba_booking_id, ba.ba_technician_id
                    FROM booking_assignment ba
                    WHERE ba.ba_status != 'unassigned'
                    AND ba.ba_completed_at IS NULL
                ) ba ON sb.sb_id = ba.ba_booking_id
                LEFT JOIN technician t ON ba.ba_technician_id = t.te_account_id
                LEFT JOIN user_account tua ON t.te_account_id = tua.ua_id
                WHERE sb.sb_id = :bookingId
                AND sb.sb_deleted_at IS NULL";
                
        return $this->queryOne($sql, ['bookingId' => $bookingId]);
    }
    
    /**
     * Create a new service booking
     * 
     * @param array $data Booking data
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return bool Success status
     */
    public function createBooking($data, $exclude = [], $expressions = [])
    {
        // Use the enhanced formatInsertData method
        $formatted = $this->formatInsertData($data, $exclude, $expressions);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']}) 
                VALUES ({$formatted['placeholders']})";
                
        // Use the filtered data
        return $this->execute($sql, $formatted['filteredData']) > 0;
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
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table} 
                SET {$formatted['updateClause']}
                WHERE sb_id = :bookingId
                AND sb_deleted_at IS NULL";
                
        $params = array_merge($formatted['filteredData'], ['bookingId' => $bookingId]);
        
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
     * @param string $priority New priority value
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
    
    /**
     * Get service booking counts by status
     * 
     * @return array Associative array of status counts
     */
    public function getBookingCountsByStatus()
    {
        $sql = "SELECT sb_status, COUNT(*) as count
                FROM {$this->table}
                WHERE sb_deleted_at IS NULL
                GROUP BY sb_status";
                
        $results = $this->query($sql);
        $counts = [
            'pending' => 0,
            'in-progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'total' => 0
        ];
        
        foreach ($results as $row) {
            $counts[$row['sb_status']] = (int) $row['count'];
            $counts['total'] += (int) $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Get bookings scheduled for today
     * 
     * @return array Array of today's bookings
     */
    public function getTodayBookings()
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                    sb.*, 
                    st.st_name as service_name,
                    ua.ua_first_name as customer_first_name,
                    ua.ua_last_name as customer_last_name,
                    CONCAT(tua.ua_first_name, ' ', tua.ua_last_name) as technician_name
                FROM {$this->table} sb
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                LEFT JOIN booking_assignment ba ON sb.sb_id = ba.ba_booking_id AND ba.ba_status != 'unassigned'
                LEFT JOIN technician t ON ba.ba_technician_id = t.te_account_id
                LEFT JOIN user_account tua ON t.te_account_id = tua.ua_id
                WHERE sb.sb_requested_date = :today
                AND sb.sb_deleted_at IS NULL
                AND sb.sb_status != 'cancelled'
                ORDER BY sb.sb_requested_time ASC";
                
        return $this->query($sql, ['today' => $today]);
    }
    
    /**
     * Get upcoming bookings (scheduled for the future)
     * 
     * @param int $limit Maximum number of bookings to retrieve
     * @return array Array of upcoming bookings
     */
    public function getUpcomingBookings($limit = 5)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                    sb.*, 
                    st.st_name as service_name,
                    ua.ua_first_name as customer_first_name,
                    ua.ua_last_name as customer_last_name,
                    CONCAT(tua.ua_first_name, ' ', tua.ua_last_name) as technician_name
                FROM {$this->table} sb
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                LEFT JOIN booking_assignment ba ON sb.sb_id = ba.ba_booking_id AND ba.ba_status != 'unassigned'
                LEFT JOIN technician t ON ba.ba_technician_id = t.te_account_id
                LEFT JOIN user_account tua ON t.te_account_id = tua.ua_id
                WHERE sb.sb_requested_date > :today
                AND sb.sb_deleted_at IS NULL
                AND sb.sb_status != 'cancelled'
                ORDER BY sb.sb_requested_date ASC, sb.sb_requested_time ASC
                LIMIT :limit";
                
        return $this->query($sql, [
            'today' => $today,
            'limit' => $limit
        ]);
    }
}