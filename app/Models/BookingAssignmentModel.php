<?php

namespace App\Models;

class BookingAssignmentModel extends Model
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
     * Get all assignments for a specific booking
     * 
     * @param int $bookingId The booking ID
     * @return array Array of assignment records
     */
    public function getAssignmentsForBooking($bookingId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE ba_booking_id = :bookingId 
                AND ba_status IN ('assigned', 'in-progress', 'completed')
                ORDER BY ba_assigned_at DESC";
        return $this->query($sql, ['bookingId' => $bookingId]);
    }

    /**
     * Get all assignments for a specific technician
     * 
     * @param int $technicianId The technician ID
     * @param string|null $status Filter by assignment status
     * @return array Array of assignment records
     */
    public function getAssignmentsForTechnician($technicianId, $status = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE ba_technician_id = :technicianId";
        $params = ['technicianId' => $technicianId];
        
        if ($status !== null) {
            $sql .= " AND ba_status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY ba_assigned_at DESC";
        
        return $this->query($sql, $params);
    }

    /**
     * Add a new assignment
     * 
     * @param array $data Assignment data
     * @return int|false The ID of the new assignment or false on failure
     */
    public function addAssignment($data)
    {
        // Ensure required fields are present
        if (empty($data['ba_booking_id']) || empty($data['ba_technician_id'])) {
            error_log("Missing required fields for booking assignment");
            return false;
        }
        
        try {
            // Set default values if not provided
            if (!isset($data['ba_status'])) {
                $data['ba_status'] = 'assigned';
            }
            
            if (!isset($data['ba_assigned_at'])) {
                $data['ba_assigned_at'] = date('Y-m-d H:i:s');
            }
            
            // Check if assignment already exists
            $existing = $this->queryOne(
                "SELECT * FROM {$this->table} WHERE ba_booking_id = :bookingId AND ba_technician_id = :technicianId",
                [
                    'bookingId' => $data['ba_booking_id'],
                    'technicianId' => $data['ba_technician_id']
                ]
            );
            
            if ($existing) {
                // Debug log
                error_log("Assignment already exists for booking {$data['ba_booking_id']} and technician {$data['ba_technician_id']}");
                
                // If assignment exists but was cancelled, reactivate it
                if ($existing['ba_status'] === 'cancelled') {
                    error_log("Reactivating cancelled assignment");
                    return $this->updateAssignment($existing[$this->primaryKey], [
                        'ba_status' => 'assigned',
                        'ba_assigned_at' => date('Y-m-d H:i:s')
                    ]);
                }
                
                // Assignment already exists and is active
                return $existing[$this->primaryKey];
            }
            
            // Format insert data
            $formattedInsert = $this->formatInsertData($data);
            
            // Debug log
            error_log("Inserting new assignment: " . json_encode($formattedInsert));
            
            $sql = "INSERT INTO {$this->table} ({$formattedInsert['columns']}) 
                    VALUES ({$formattedInsert['placeholders']})";
            
            $result = $this->execute($sql, $formattedInsert['filteredData']);
            
            if ($result <= 0) {
                error_log("Failed to insert assignment record");
                return false;
            }
            
            // Get the sequence name for PostgreSQL
            $sequenceName = "{$this->table}_{$this->primaryKey}_seq";
            
            // Debug log
            error_log("Using sequence name: " . $sequenceName);
            
            $insertId = $this->lastInsertId($sequenceName);
            error_log("Inserted assignment with ID: " . $insertId);
            
            return $insertId;
        } catch (\Exception $e) {
            error_log("Error inserting booking assignment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an assignment
     * 
     * @param int $assignmentId The assignment ID
     * @param array $data The data to update
     * @return bool Success status
     */
    public function updateAssignment($assignmentId, $data)
    {
        if (empty($data)) {
            return true; // No data to update, considered successful
        }
        
        $formattedUpdate = $this->formatUpdateData($data);
        
        if (empty($formattedUpdate['updateClause'])) {
            return true; // No effective update to make
        }
        
        $sql = "UPDATE {$this->table} 
                SET {$formattedUpdate['updateClause']}
                WHERE {$this->primaryKey} = :_primaryKeyValueBinding";
                
        $params = $formattedUpdate['filteredData'];
        $params['_primaryKeyValueBinding'] = $assignmentId;
        
        return $this->execute($sql, $params) > 0;
    }

    /**
     * Update assignment status
     * 
     * @param int $assignmentId The assignment ID
     * @param string $status The new status
     * @return bool Success status
     */
    public function updateAssignmentStatus($assignmentId, $status)
    {
        $allowedStatuses = ['assigned', 'in-progress', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        
        $data = ['ba_status' => $status];
        
        // Set completed_at timestamp if status is completed
        if ($status === 'completed' && empty($data['ba_completed_at'])) {
            $data['ba_completed_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->updateAssignment($assignmentId, $data);
    }

    /**
     * Remove an assignment between a booking and technician
     * 
     * @param int $bookingId The booking ID
     * @param int $technicianId The technician ID
     * @return bool Success status
     */
    public function removeAssignment($bookingId, $technicianId)
    {
        $sql = "UPDATE {$this->table} 
                SET ba_status = 'cancelled'
                WHERE ba_booking_id = :bookingId 
                AND ba_technician_id = :technicianId
                AND ba_status IN ('assigned', 'in-progress')";
                
        $params = [
            'bookingId' => $bookingId,
            'technicianId' => $technicianId
        ];
        
        return $this->execute($sql, $params) > 0;
    }

    /**
     * Check if a technician is assigned to a booking
     * 
     * @param int $bookingId The booking ID
     * @param int $technicianId The technician ID
     * @return bool True if assigned, false otherwise
     */
    public function isAssigned($bookingId, $technicianId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE ba_booking_id = :bookingId 
                AND ba_technician_id = :technicianId
                AND ba_status IN ('assigned', 'in-progress')";
                
        $params = [
            'bookingId' => $bookingId,
            'technicianId' => $technicianId
        ];
        
        return $this->queryScalar($sql, $params) > 0;
    }
}