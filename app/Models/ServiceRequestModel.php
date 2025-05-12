<?php

namespace App\Models;

// Assuming Model.php is in App\Models or autoloaded correctly.
// If Model.php is in the same directory and namespace, this is fine.
// The Model.php provided in the prompt is assumed to be used as the base class.

class ServiceRequestModel extends Model // Extends Model directly
{
    protected $table = 'service_booking';
    protected $primaryKey = 'sb_id';

    // The $fillable property is intentionally omitted as per the prompt's instruction:
    // "don't include the fillable".
    // This means that data passed to create/update methods is assumed to be
    // pre-filtered or that the responsibility of filtering columns lies outside these methods,
    // unlike a typical ORM that might use $fillable for mass-assignment protection.

    // These properties are used by the methods in this class to manually
    // implement soft deletes and timestamps, as Model.php doesn't auto-handle them.
    protected $useSoftDeletes = true;
    protected $deletedAtColumn = 'sb_deleted_at';

    protected $timestamps = true;
    protected $createdAtColumn = 'sb_created_at';
    protected $updatedAtColumn = 'sb_updated_at';

    // Constructor is inherited from Model.php, which initializes $this->pdo.
    // No need to redefine it here unless ServiceRequestModel has specific construction needs.

    /**
     * Get service bookings for a specific customer
     * 
     * @param int $customerId The customer ID
     * @return array Array of bookings
     */
    public function getCustomerBookings($customerId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE sb_customer_id = :customerId";
        
        if ($this->useSoftDeletes) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }
        
        $sql .= " ORDER BY sb_requested_date DESC, sb_requested_time DESC";
        
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
        // The table alias 'sb' is used directly in the SQL.
        $sql = "SELECT 
                    sb.*, 
                    st.st_name as service_name, 
                    st.st_description as service_description,
                    ua.ua_first_name as customer_first_name,
                    ua.ua_last_name as customer_last_name,
                    ua.ua_email as customer_email,
                    ua.ua_phone_number as customer_phone
                FROM {$this->table} sb
                JOIN service_type st ON sb.sb_service_type_id = st.st_id
                JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
                WHERE sb.{$this->primaryKey} = :bookingId";

        if ($this->useSoftDeletes) {
            // Ensure alias 'sb' is used if the deletedAtColumn could be ambiguous
            $sql .= " AND sb.{$this->deletedAtColumn} IS NULL"; 
        }
        
        return $this->queryOne($sql, ['bookingId' => $bookingId]);
    }
    
    /**
     * Create a new service booking
     * 
     * @param array $data Booking data. Assumed to be safe and pre-filtered if necessary,
     *                    as $fillable mechanism is not used here.
     * @return string|false The last inserted ID on success, or false on failure (if lastInsertId returns false).
     */
    public function createBooking($data)
    {
        if ($this->timestamps) {
            $currentTime = date('Y-m-d H:i:s');
            if (!isset($data[$this->createdAtColumn])) {
                $data[$this->createdAtColumn] = $currentTime;
            }
            if (!isset($data[$this->updatedAtColumn])) {
                $data[$this->updatedAtColumn] = $currentTime;
            }
        }
        
        $formattedInsert = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formattedInsert['columns']}) 
                VALUES ({$formattedInsert['placeholders']})";
        
        // $formattedInsert['filteredData'] contains key-value pairs for binding.
        // Model::execute() will handle adding ':' to keys if needed.
        $this->execute($sql, $formattedInsert['filteredData']);
        return $this->lastInsertId(); // Returns the ID of the inserted row.
    }
    
    /**
     * Update a service booking
     * 
     * @param int $bookingId The booking ID
     * @param array $data Updated booking data. Assumed to be safe and pre-filtered.
     * @return bool True if update affected one or more rows, false otherwise.
     */
    public function updateBooking($bookingId, $data)
    {
        if (empty($data)) {
            return true; // No data to update, can be considered a "successful" no-op. Or return false.
                         // Original BaseModel behavior might clarify this. Returning true for consistency if 0 rows affected.
        }

        if ($this->timestamps && !isset($data[$this->updatedAtColumn])) {
            $data[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }
        
        // formatUpdateData prepares the SET clause and the data for it.
        // $data should not contain the primary key for the SET part.
        $formattedUpdate = $this->formatUpdateData($data);
        
        if (empty($formattedUpdate['updateClause'])) {
            // This can happen if $data was empty or all items were excluded (if using exclude in formatUpdateData).
            return true; // No effective update to make.
        }

        $sql = "UPDATE {$this->table} 
                SET {$formattedUpdate['updateClause']}
                WHERE {$this->primaryKey} = :_primaryKeyValueBinding"; // Unique placeholder for WHERE clause
        
        if ($this->useSoftDeletes) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }

        // Combine parameters for SET clause and WHERE clause.
        $params = $formattedUpdate['filteredData'];
        $params['_primaryKeyValueBinding'] = $bookingId; 
        
        return $this->execute($sql, $params) > 0; // execute() returns affected row count.
    }
    
    /**
     * Update the status of a service booking
     * 
     * @param int $bookingId The booking ID
     * @param string $status New status value
     * @return bool True if update affected one or more rows, false otherwise.
     */
    public function updateBookingStatus($bookingId, $status)
    {
        $dataToUpdate = ['sb_status' => $status];
        // The updateBooking method (or similar logic) handles timestamps.
        // For directness and clarity, we repeat the core logic here:
        if ($this->timestamps) {
            $dataToUpdate[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }
        
        $formattedUpdate = $this->formatUpdateData($dataToUpdate);

        // This check is mostly a safeguard; sb_status and potentially sb_updated_at will be present.
        if (empty($formattedUpdate['updateClause'])) {
            return true; 
        }
        
        $sql = "UPDATE {$this->table} 
                SET {$formattedUpdate['updateClause']}
                WHERE {$this->primaryKey} = :_primaryKeyValueBinding";
        
        if ($this->useSoftDeletes) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }

        $params = $formattedUpdate['filteredData'];
        $params['_primaryKeyValueBinding'] = $bookingId;
        
        return $this->execute($sql, $params) > 0;
    }

    /**
     * Update the priority of a service booking
     * 
     * @param int $bookingId The booking ID
     * @param string $priority New priority value
     * @return bool True if update affected one or more rows, false otherwise.
     */
    public function updateBookingPriority($bookingId, $priority)
    {
        $dataToUpdate = ['sb_priority' => $priority];
        if ($this->timestamps) {
            $dataToUpdate[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }
        
        $formattedUpdate = $this->formatUpdateData($dataToUpdate);

        if (empty($formattedUpdate['updateClause'])) {
            return true; 
        }
        
        $sql = "UPDATE {$this->table} 
                SET {$formattedUpdate['updateClause']}
                WHERE {$this->primaryKey} = :_primaryKeyValueBinding";
        
        if ($this->useSoftDeletes) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }

        $params = $formattedUpdate['filteredData'];
        $params['_primaryKeyValueBinding'] = $bookingId;
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Cancel a service booking by setting its status to 'cancelled'
     * 
     * @param int $bookingId The booking ID
     * @return bool True if status update was successful (affected rows > 0), false otherwise.
     */
    public function cancelBooking($bookingId)
    {
        // This method reuses updateBookingStatus, which is already converted.
        return $this->updateBookingStatus($bookingId, 'cancelled');
    }
    
    /**
     * Delete a service booking.
     * Performs a soft delete if $useSoftDeletes is true, otherwise a hard delete.
     * 
     * @param int $bookingId The booking ID
     * @return bool True if delete/update affected one or more rows, false otherwise.
     */
    public function deleteBooking($bookingId)
    {
        if ($this->useSoftDeletes) {
            // Soft delete: update the deleted_at column.
            $dataToUpdate = [$this->deletedAtColumn => date('Y-m-d H:i:s')];
            
            // Optionally, update the 'updated_at' column during a soft delete.
            if ($this->timestamps) {
                $dataToUpdate[$this->updatedAtColumn] = date('Y-m-d H:i:s');
            }

            $formattedUpdate = $this->formatUpdateData($dataToUpdate);
            
            // Should not be empty given the data being set.
            if (empty($formattedUpdate['updateClause'])) {
                return false; // Or handle as an error/log.
            }

            $sql = "UPDATE {$this->table} 
                    SET {$formattedUpdate['updateClause']}
                    WHERE {$this->primaryKey} = :_primaryKeyValueBinding";
            // Note: For soft delete, we generally do NOT add "AND {$this->deletedAtColumn} IS NULL"
            // to the WHERE clause, as we want to be able to "delete" it even if already soft-deleted (making the operation idempotent)
            // or if it's currently active.

            $params = $formattedUpdate['filteredData'];
            $params['_primaryKeyValueBinding'] = $bookingId;
            
            return $this->execute($sql, $params) > 0;

        } else {
            // Hard delete: permanently remove the record.
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :_primaryKeyValueBinding";
            $params = ['_primaryKeyValueBinding' => $bookingId];
            return $this->execute($sql, $params) > 0;
        }
    }
}