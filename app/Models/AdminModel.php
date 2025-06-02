<?php

namespace App\Models;

class AdminModel extends Model
{
    protected $table = 'admin'; // Database table name
    protected $primaryKey = 'ad_account_id'; // Primary key for the admin table

    // Specify fillable fields for mass assignment, if any, e.g., 'ad_office_no'
    protected $fillable = [
        'ad_office_no',
    ];

    // Timestamps
    protected $createdAtColumn = 'ad_created_at';
    protected $updatedAtColumn = 'ad_updated_at';
    // No soft deletes for this table as per schema
    // protected $deletedAtColumn = 'ad_deleted_at';

    protected $timestamps = true; // Enable automatic timestamp management for created_at and updated_at
    protected $useSoftDeletes = false; // Admin table does not have a deleted_at column in the provided schema

    /**
     * Update admin record by account ID.
     *
     * @param int $accountId The user account ID (ua_id which is ad_account_id in admin table).
     * @param array $data Data to update.
     * @return bool True on success, false on failure.
     */
    public function updateByAccountId($accountId, array $data)
    {
        if (empty($data)) {
            return false;
        }

        $expressions = [];
        if ($this->timestamps && $this->updatedAtColumn) {
            $expressions[$this->updatedAtColumn] = 'NOW()'; // Use NOW() for PostgreSQL
        }

        // Ensure only fillable fields are updated
        $fillableData = array_intersect_key($data, array_flip($this->fillable));
        if (empty($fillableData)) {
            // If only timestamps are being updated (e.g. by $expressions), still proceed if $expressions is not empty
            if(empty($expressions)) return false; 
        }

        $updateDetails = $this->formatUpdateData($fillableData, [], $expressions);
        
        $sql = "UPDATE {$this->table} 
                SET {$updateDetails['updateClause']} 
                WHERE {$this->primaryKey} = :account_id";
        
        $params = array_merge($updateDetails['filteredData'], ['account_id' => $accountId]);
        
        return $this->execute($sql, $params);
    }

    /**
     * Find an admin record by account ID.
     *
     * @param int $accountId
     * @return array|false
     */
    public function findByAccountId($accountId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :account_id";
        return $this->queryOne($sql, ['account_id' => $accountId]);
    }
    
    /**
     * Create a new admin record.
     * This is typically handled by a trigger in the DB (create_role_specific_record)
     * but can be useful for direct creation or testing.
     *
     * @param array $data
     * @return mixed The last insert ID or false on failure.
     */
    public function createAdmin(array $data)
    {
        if (empty($data[$this->primaryKey])) {
            // ad_account_id is required and usually comes from user_account table
            return false; 
        }

        $expressions = [];
        if ($this->timestamps) {
            if($this->createdAtColumn) $expressions[$this->createdAtColumn] = 'NOW()';
            if($this->updatedAtColumn) $expressions[$this->updatedAtColumn] = 'NOW()';
        }

        $insertData = $this->formatInsertData($data, [], $expressions);
        $sql = "INSERT INTO {$this->table} ({$insertData['columns']}) 
                VALUES ({$insertData['placeholders']})";

        if ($this->execute($sql, $insertData['filteredData'])) {
            // Since primary key is not auto-incrementing but a FK
            // we return the ad_account_id passed in data.
            return $data[$this->primaryKey];
        }
        return false;
    }
} 