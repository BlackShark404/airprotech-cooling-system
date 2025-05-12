<?php

namespace App\Models;

use Exception;
use PDO;

class ServiceRequestModel extends Model
{
    protected $table = 'SERVICE_BOOKING';
    
    /**
     * Create a new service request
     * 
     * @param array $data Service request data
     * @return int|bool The ID of the new request or false on failure
     */
    public function create(array $data)
    {
        try {
            // Start a transaction
            $this->beginTransaction();
            
            // Format data for insertion
            $formattedData = $this->formatInsertData(
                $data,
                ['SB_ID', 'SB_CREATED_AT', 'SB_UPDATED_AT', 'SB_DELETED_AT'], // Exclude these fields
                [] // No custom expressions
            );
            
            // Build the SQL query
            $sql = "INSERT INTO {$this->table} ({$formattedData['columns']})
                    VALUES ({$formattedData['placeholders']})
                    RETURNING SB_ID";
            
            // Execute the query and get the ID
            $id = $this->queryScalar($sql, $formattedData['filteredData']);
            
            // Commit the transaction
            $this->commit();
            
            // Update customer statistics
            $this->updateCustomerStatistics($data['SB_CUSTOMER_ID']);
            
            return $id;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->rollback();
            error_log("Error creating service request: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update an existing service request
     * 
     * @param int $id Service request ID
     * @param array $data Updated service request data
     * @return bool Success status
     */
    public function update(int $id, array $data)
    {
        try {
            // Start a transaction
            $this->beginTransaction();
            
            // Add updated timestamp
            $data['SB_UPDATED_AT'] = date('Y-m-d H:i:s');
            
            // Format data for update
            $formattedData = $this->formatUpdateData(
                $data,
                ['SB_ID', 'SB_CREATED_AT', 'SB_DELETED_AT'], // Exclude these fields
                [] // No custom expressions
            );
            
            // Build the SQL query
            $sql = "UPDATE {$this->table}
                    SET {$formattedData['updateClause']}
                    WHERE SB_ID = :id";
            
            // Add ID to parameters
            $params = array_merge($formattedData['filteredData'], ['id' => $id]);
            
            // Execute the query
            $affectedRows = $this->execute($sql, $params);
            
            // Commit the transaction
            $this->commit();
            
            // If we have customer ID in data or need to update stats based on status change
            if (isset($data['SB_CUSTOMER_ID'])) {
                $this->updateCustomerStatistics($data['SB_CUSTOMER_ID']);
            } else {
                // Get customer ID from the booking
                $customerId = $this->getCustomerIdByBookingId($id);
                if ($customerId) {
                    $this->updateCustomerStatistics($customerId);
                }
            }
            
            return $affectedRows > 0;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->rollback();
            error_log("Error updating service request ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Soft delete a service request
     * 
     * @param int $id Service request ID
     * @return bool Success status
     */
    public function delete(int $id)
    {
        try {
            // Get customer ID before deleting
            $customerId = $this->getCustomerIdByBookingId($id);
            
            // Start a transaction
            $this->beginTransaction();
            
            // Build the SQL query for soft delete
            $sql = "UPDATE {$this->table}
                    SET SB_DELETED_AT = NOW(),
                        SB_STATUS = 'cancelled'
                    WHERE SB_ID = :id AND SB_DELETED_AT IS NULL";
            
            // Execute the query
            $affectedRows = $this->execute($sql, ['id' => $id]);
            
            // Commit the transaction
            $this->commit();
            
            // Update customer statistics
            if ($customerId) {
                $this->updateCustomerStatistics($customerId);
            }
            
            return $affectedRows > 0;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->rollback();
            error_log("Error deleting service request ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get a service request by ID
     * 
     * @param int $id Service request ID
     * @return array|null Service request data or null if not found
     */
    public function getById(int $id)
    {
        $sql = "SELECT sb.*, 
                       st.ST_NAME as SERVICE_TYPE_NAME,
                       ua.UA_FIRST_NAME as CUSTOMER_FIRST_NAME,
                       ua.UA_LAST_NAME as CUSTOMER_LAST_NAME,
                       ua.UA_EMAIL as CUSTOMER_EMAIL,
                       ua.UA_PHONE_NUMBER as CUSTOMER_PHONE
                FROM {$this->table} sb
                JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
                JOIN CUSTOMER cu ON sb.SB_CUSTOMER_ID = cu.CU_ACCOUNT_ID
                JOIN USER_ACCOUNT ua ON cu.CU_ACCOUNT_ID = ua.UA_ID
                WHERE sb.SB_ID = :id 
                AND sb.SB_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get service requests by customer ID
     * 
     * @param int $customerId Customer ID
     * @param string|null $status Filter by status
     * @return array Service requests
     */
    public function getByCustomerId(int $customerId, string $status = null)
    {
        $params = ['customerId' => $customerId];
        
        $sql = "SELECT sb.*, 
                       st.ST_NAME as SERVICE_TYPE_NAME
                FROM {$this->table} sb
                JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
                WHERE sb.SB_CUSTOMER_ID = :customerId 
                AND sb.SB_DELETED_AT IS NULL";
        
        if ($status) {
            $sql .= " AND sb.SB_STATUS = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY sb.SB_REQUESTED_DATE DESC, sb.SB_REQUESTED_TIME DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get all service requests with optional filtering
     * 
     * @param array $filters Associative array of filter conditions
     * @param string $sortField Field to sort by
     * @param string $sortOrder Sort order (ASC/DESC)
     * @return array Service requests
     */
    public function getAll(array $filters = [], string $sortField = 'SB_REQUESTED_DATE', string $sortOrder = 'DESC')
    {
        $params = [];
        $whereConditions = ['sb.SB_DELETED_AT IS NULL'];
        
        // Build WHERE clause based on filters
        if (!empty($filters)) {
            if (isset($filters['status']) && $filters['status']) {
                $whereConditions[] = 'sb.SB_STATUS = :status';
                $params['status'] = $filters['status'];
            }
            
            if (isset($filters['priority']) && $filters['priority']) {
                $whereConditions[] = 'sb.SB_PRIORITY = :priority';
                $params['priority'] = $filters['priority'];
            }
            
            if (isset($filters['serviceTypeId']) && $filters['serviceTypeId']) {
                $whereConditions[] = 'sb.SB_SERVICE_TYPE_ID = :serviceTypeId';
                $params['serviceTypeId'] = $filters['serviceTypeId'];
            }
            
            if (isset($filters['date_from']) && $filters['date_from']) {
                $whereConditions[] = 'sb.SB_REQUESTED_DATE >= :dateFrom';
                $params['dateFrom'] = $filters['date_from'];
            }
            
            if (isset($filters['date_to']) && $filters['date_to']) {
                $whereConditions[] = 'sb.SB_REQUESTED_DATE <= :dateTo';
                $params['dateTo'] = $filters['date_to'];
            }
            
            if (isset($filters['search']) && $filters['search']) {
                $whereConditions[] = '(sb.SB_ADDRESS ILIKE :search 
                                    OR sb.SB_DESCRIPTION ILIKE :search 
                                    OR ua.UA_FIRST_NAME ILIKE :search 
                                    OR ua.UA_LAST_NAME ILIKE :search)';
                $params['search'] = '%' . $filters['search'] . '%';
            }
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Validate and sanitize sort field
        $allowedSortFields = [
            'SB_ID', 'SB_REQUESTED_DATE', 'SB_REQUESTED_TIME',
            'SB_STATUS', 'SB_PRIORITY', 'SB_ESTIMATED_COST'
        ];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'SB_REQUESTED_DATE';
        }
        
        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql = "SELECT sb.*, 
                       st.ST_NAME as SERVICE_TYPE_NAME,
                       ua.UA_FIRST_NAME as CUSTOMER_FIRST_NAME,
                       ua.UA_LAST_NAME as CUSTOMER_LAST_NAME,
                       ua.UA_EMAIL as CUSTOMER_EMAIL,
                       ua.UA_PHONE_NUMBER as CUSTOMER_PHONE
                FROM {$this->table} sb
                JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
                JOIN CUSTOMER cu ON sb.SB_CUSTOMER_ID = cu.CU_ACCOUNT_ID
                JOIN USER_ACCOUNT ua ON cu.CU_ACCOUNT_ID = ua.UA_ID
                WHERE {$whereClause}
                ORDER BY sb.{$sortField} {$sortOrder}, sb.SB_ID DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get service requests by technician assignment
     * 
     * @param int $technicianId Technician ID
     * @param string|null $status Filter by assignment status
     * @return array Service requests assigned to the technician
     */
    public function getByTechnicianId(int $technicianId, string $status = null)
    {
        $params = ['technicianId' => $technicianId];
        
        $sql = "SELECT sb.*, 
                       st.ST_NAME as SERVICE_TYPE_NAME,
                       ua.UA_FIRST_NAME as CUSTOMER_FIRST_NAME,
                       ua.UA_LAST_NAME as CUSTOMER_LAST_NAME,
                       ua.UA_EMAIL as CUSTOMER_EMAIL,
                       ua.UA_PHONE_NUMBER as CUSTOMER_PHONE,
                       ba.BA_STATUS as ASSIGNMENT_STATUS,
                       ba.BA_NOTES as ASSIGNMENT_NOTES,
                       ba.BA_ASSIGNED_AT,
                       ba.BA_STARTED_AT,
                       ba.BA_COMPLETED_AT
                FROM BOOKING_ASSIGNMENT ba
                JOIN {$this->table} sb ON ba.BA_BOOKING_ID = sb.SB_ID
                JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
                JOIN CUSTOMER cu ON sb.SB_CUSTOMER_ID = cu.CU_ACCOUNT_ID
                JOIN USER_ACCOUNT ua ON cu.CU_ACCOUNT_ID = ua.UA_ID
                WHERE ba.BA_TECHNICIAN_ID = :technicianId 
                AND sb.SB_DELETED_AT IS NULL";
        
        if ($status) {
            $sql .= " AND ba.BA_STATUS = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY sb.SB_REQUESTED_DATE ASC, sb.SB_REQUESTED_TIME ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get available service types
     * 
     * @param bool $activeOnly Whether to return only active service types
     * @return array Service types
     */
    public function getServiceTypes(bool $activeOnly = true)
    {
        $sql = "SELECT * FROM SERVICE_TYPE";
        
        if ($activeOnly) {
            $sql .= " WHERE ST_IS_ACTIVE = TRUE";
        }
        
        $sql .= " ORDER BY ST_NAME ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Assign a technician to a service booking
     * 
     * @param int $bookingId Service booking ID
     * @param int $technicianId Technician ID
     * @param string $notes Assignment notes
     * @return bool Success status
     */
    public function assignTechnician(int $bookingId, int $technicianId, string $notes = '')
    {
        try {
            // Start a transaction
            $this->beginTransaction();
            
            // Check if the assignment already exists
            $checkSql = "SELECT COUNT(*) FROM BOOKING_ASSIGNMENT 
                         WHERE BA_BOOKING_ID = :bookingId 
                         AND BA_TECHNICIAN_ID = :technicianId";
            
            $exists = (int)$this->queryScalar($checkSql, [
                'bookingId' => $bookingId,
                'technicianId' => $technicianId
            ]);
            
            if ($exists > 0) {
                // Update existing assignment
                $sql = "UPDATE BOOKING_ASSIGNMENT 
                        SET BA_NOTES = :notes, 
                            BA_STATUS = 'assigned', 
                            BA_UPDATED_AT = NOW() 
                        WHERE BA_BOOKING_ID = :bookingId 
                        AND BA_TECHNICIAN_ID = :technicianId";
            } else {
                // Create new assignment
                $sql = "INSERT INTO BOOKING_ASSIGNMENT 
                        (BA_BOOKING_ID, BA_TECHNICIAN_ID, BA_NOTES) 
                        VALUES (:bookingId, :technicianId, :notes)";
            }
            
            // Execute query
            $this->execute($sql, [
                'bookingId' => $bookingId,
                'technicianId' => $technicianId,
                'notes' => $notes
            ]);
            
            // Update booking status to confirmed if currently pending
            $updateSql = "UPDATE {$this->table} 
                          SET SB_STATUS = 'confirmed', 
                              SB_UPDATED_AT = NOW() 
                          WHERE SB_ID = :bookingId 
                          AND SB_STATUS = 'pending'";
            
            $this->execute($updateSql, ['bookingId' => $bookingId]);
            
            // Commit the transaction
            $this->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->rollback();
            error_log("Error assigning technician: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update the status of a service booking assignment
     * 
     * @param int $bookingId Service booking ID
     * @param int $technicianId Technician ID
     * @param string $status New status value
     * @param array $additionalData Additional data to update
     * @return bool Success status
     */
    public function updateAssignmentStatus(int $bookingId, int $technicianId, string $status, array $additionalData = [])
    {
        try {
            // Start a transaction
            $this->beginTransaction();
            
            $params = [
                'bookingId' => $bookingId,
                'technicianId' => $technicianId,
                'status' => $status
            ];
            
            $updateFields = ['BA_STATUS = :status', 'BA_UPDATED_AT = NOW()'];
            
            // Add status-specific timestamp updates
            if ($status === 'in-progress') {
                $updateFields[] = 'BA_STARTED_AT = NOW()';
            } elseif ($status === 'completed') {
                $updateFields[] = 'BA_COMPLETED_AT = NOW()';
            }
            
            // Add any additional fields to update
            foreach ($additionalData as $key => $value) {
                $columnName = 'BA_' . strtoupper($key);
                $paramName = 'param_' . $key;
                $updateFields[] = "{$columnName} = :{$paramName}";
                $params[$paramName] = $value;
            }
            
            // Update the assignment
            $sql = "UPDATE BOOKING_ASSIGNMENT 
                    SET " . implode(', ', $updateFields) . " 
                    WHERE BA_BOOKING_ID = :bookingId 
                    AND BA_TECHNICIAN_ID = :technicianId";
            
            $this->execute($sql, $params);
            
            // Update booking status accordingly
            $bookingStatus = $status;
            if ($status === 'cancelled') {
                $bookingStatus = 'pending'; // Reset to pending if technician cancels
            }
            
            $updateSql = "UPDATE {$this->table} 
                          SET SB_STATUS = :status, 
                              SB_UPDATED_AT = NOW() 
                          WHERE SB_ID = :bookingId";
            
            $this->execute($updateSql, [
                'bookingId' => $bookingId, 
                'status' => $bookingStatus
            ]);
            
            // Get customer ID and update statistics
            $customerId = $this->getCustomerIdByBookingId($bookingId);
            if ($customerId) {
                $this->updateCustomerStatistics($customerId);
            }
            
            // Commit the transaction
            $this->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->rollback();
            error_log("Error updating assignment status: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get dashboard statistics for admins
     * 
     * @return array Statistics data
     */
    public function getDashboardStats()
    {
        $stats = [];
        
        // Total active bookings
        $stats['total_active'] = $this->queryScalar(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE SB_STATUS IN ('pending', 'confirmed', 'in-progress') 
             AND SB_DELETED_AT IS NULL"
        );
        
        // Bookings by status
        $stats['status_counts'] = $this->query(
            "SELECT SB_STATUS, COUNT(*) as count 
             FROM {$this->table} 
             WHERE SB_DELETED_AT IS NULL 
             GROUP BY SB_STATUS 
             ORDER BY COUNT(*) DESC"
        );
        
        // Bookings by service type
        $stats['service_type_counts'] = $this->query(
            "SELECT st.ST_NAME, COUNT(*) as count 
             FROM {$this->table} sb
             JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
             WHERE sb.SB_DELETED_AT IS NULL 
             GROUP BY st.ST_NAME 
             ORDER BY COUNT(*) DESC"
        );
        
        // Upcoming bookings (next 7 days)
        $stats['upcoming_bookings'] = $this->query(
            "SELECT sb.*, st.ST_NAME as SERVICE_TYPE_NAME,
                    ua.UA_FIRST_NAME as CUSTOMER_FIRST_NAME,
                    ua.UA_LAST_NAME as CUSTOMER_LAST_NAME
             FROM {$this->table} sb
             JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
             JOIN CUSTOMER cu ON sb.SB_CUSTOMER_ID = cu.CU_ACCOUNT_ID
             JOIN USER_ACCOUNT ua ON cu.CU_ACCOUNT_ID = ua.UA_ID
             WHERE sb.SB_DELETED_AT IS NULL 
             AND sb.SB_STATUS IN ('pending', 'confirmed') 
             AND sb.SB_REQUESTED_DATE BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL '7 days')
             ORDER BY sb.SB_REQUESTED_DATE ASC, sb.SB_REQUESTED_TIME ASC
             LIMIT 10"
        );
        
        return $stats;
    }
    
    /**
     * Get dashboard statistics for a specific technician
     * 
     * @param int $technicianId Technician ID
     * @return array Statistics data
     */
    public function getTechnicianStats(int $technicianId)
    {
        $stats = [];
        
        // Active assignments
        $stats['active_assignments'] = $this->queryScalar(
            "SELECT COUNT(*) FROM BOOKING_ASSIGNMENT ba
             JOIN {$this->table} sb ON ba.BA_BOOKING_ID = sb.SB_ID
             WHERE ba.BA_TECHNICIAN_ID = :technicianId
             AND ba.BA_STATUS IN ('assigned', 'in-progress')
             AND sb.SB_DELETED_AT IS NULL",
            ['technicianId' => $technicianId]
        );
        
        // Assignments by status
        $stats['status_counts'] = $this->query(
            "SELECT ba.BA_STATUS, COUNT(*) as count 
             FROM BOOKING_ASSIGNMENT ba
             JOIN {$this->table} sb ON ba.BA_BOOKING_ID = sb.SB_ID
             WHERE ba.BA_TECHNICIAN_ID = :technicianId
             AND sb.SB_DELETED_AT IS NULL
             GROUP BY ba.BA_STATUS 
             ORDER BY COUNT(*) DESC",
            ['technicianId' => $technicianId]
        );
        
        // Today's schedule
        $stats['today_schedule'] = $this->query(
            "SELECT sb.*, st.ST_NAME as SERVICE_TYPE_NAME,
                    ua.UA_FIRST_NAME as CUSTOMER_FIRST_NAME,
                    ua.UA_LAST_NAME as CUSTOMER_LAST_NAME,
                    ba.BA_STATUS as ASSIGNMENT_STATUS
             FROM BOOKING_ASSIGNMENT ba
             JOIN {$this->table} sb ON ba.BA_BOOKING_ID = sb.SB_ID
             JOIN SERVICE_TYPE st ON sb.SB_SERVICE_TYPE_ID = st.ST_ID
             JOIN CUSTOMER cu ON sb.SB_CUSTOMER_ID = cu.CU_ACCOUNT_ID
             JOIN USER_ACCOUNT ua ON cu.CU_ACCOUNT_ID = ua.UA_ID
             WHERE ba.BA_TECHNICIAN_ID = :technicianId
             AND sb.SB_DELETED_AT IS NULL
             AND sb.SB_REQUESTED_DATE = CURRENT_DATE
             ORDER BY sb.SB_REQUESTED_TIME ASC",
            ['technicianId' => $technicianId]
        );
        
        return $stats;
    }
    
    /**
     * Get customer ID by booking ID
     * 
     * @param int $bookingId Booking ID
     * @return int|null Customer ID or null if not found
     */
    protected function getCustomerIdByBookingId(int $bookingId)
    {
        return $this->queryScalar(
            "SELECT SB_CUSTOMER_ID FROM {$this->table} WHERE SB_ID = :id",
            ['id' => $bookingId]
        );
    }
    
    /**
     * Update customer statistics
     * 
     * @param int $customerId Customer ID
     * @return bool Success status
     */
    protected function updateCustomerStatistics(int $customerId)
    {              
        try {
            // Calculate and update customer statistics
            $sql = "UPDATE CUSTOMER
                    SET CU_TOTAL_BOOKINGS = (
                        SELECT COUNT(*) FROM {$this->table}
                        WHERE SB_CUSTOMER_ID = :customerId
                    ),
                    CU_ACTIVE_BOOKINGS = (
                        SELECT COUNT(*) FROM {$this->table}
                        WHERE SB_CUSTOMER_ID = :customerId
                        AND SB_STATUS IN ('pending', 'confirmed', 'in-progress')
                        AND SB_DELETED_AT IS NULL
                    ),
                    CU_PENDING_SERVICES = (
                        SELECT COUNT(*) FROM {$this->table}
                        WHERE SB_CUSTOMER_ID = :customerId
                        AND SB_STATUS IN ('pending', 'confirmed')
                        AND SB_DELETED_AT IS NULL
                    ),
                    CU_COMPLETED_SERVICES = (
                        SELECT COUNT(*) FROM {$this->table}
                        WHERE SB_CUSTOMER_ID = :customerId
                        AND SB_STATUS = 'completed'
                        AND SB_DELETED_AT IS NULL
                    ),
                    CU_UPDATED_AT = NOW()
                    WHERE CU_ACCOUNT_ID = :customerId";
            
            $this->execute($sql, ['customerId' => $customerId]);
            return true;
        } catch (Exception $e) {
            error_log("Error updating customer statistics: " . $e->getMessage());
            return false;
        }
    }
}