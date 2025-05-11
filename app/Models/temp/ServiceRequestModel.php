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
}