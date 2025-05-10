<?php

namespace App\Models;
use App\Models\BaseModel;

/**
 * User Role Model
 * 
 * This model represents user roles in the system.
 */
class UserRoleModel extends BaseModel
{
    protected $table = 'user_role';
    protected $primaryKey = 'ur_id';

    /**
     * Get all roles
     * 
     * @return array Array of role records
     */
    public function getAllRoles()
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            ORDER BY ur_name
        ";
        
        return $this->query($sql);
    }

    /**
     * Get role by ID
     * 
     * @param int $id Role ID
     * @return array|null Role record or null if not found
     */
    public function getRoleById($id)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->queryOne($sql, ['id' => $id]);
    }

    /**
     * Get role by name
     * 
     * @param string $name Role name
     * @return array|null Role record or null if not found
     */
    public function getRoleByName($name)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ur_name = :name
        ";
        
        return $this->queryOne($sql, ['name' => $name]);
    }

    /**
     * Create a new role
     * 
     * @param array $data Role data
     * @return int|false ID of created role or false on failure
     */
    public function createRole(array $data)
    {
        // Format the columns and values for the SQL query
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        // Add timestamps
        $now = date('Y-m-d H:i:s');
        $data['ur_created_at'] = $now;
        $data['ur_updated_at'] = $now;
        
        $sql = "
            INSERT INTO {$this->table} ($columns)
            VALUES ($placeholders)
            RETURNING {$this->primaryKey}
        ";
        
        try {
            $this->execute($sql, $data);
            return $this->lastInsertId();
        } catch (\Exception $e) {
            error_log("Error creating role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing role
     * 
     * @param int $id Role ID
     * @param array $data Role data
     * @return bool Success status
     */
    public function updateRole($id, array $data)
    {
        // Add updated_at timestamp
        $data['ur_updated_at'] = date('Y-m-d H:i:s');
        
        // Build the SET part of the query
        $setClauses = [];
        foreach ($data as $key => $value) {
            if ($key !== 'ur_id' && $key !== 'ur_created_at') {
                $setClauses[] = "$key = :$key";
            }
        }
        
        $setClause = implode(', ', $setClauses);
        
        $sql = "
            UPDATE {$this->table}
            SET $setClause
            WHERE {$this->primaryKey} = :id
        ";
        
        // Add the ID to the parameter list
        $data['id'] = $id;
        
        try {
            return $this->execute($sql, $data);
        } catch (\Exception $e) {
            error_log("Error updating role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a role
     * 
     * @param int $id Role ID
     * @return bool Success status
     */
    public function deleteRole($id)
    {
        $sql = "
            DELETE FROM {$this->table}
            WHERE {$this->primaryKey} = :id
        ";
        
        try {
            return $this->execute($sql, ['id' => $id]);
        } catch (\Exception $e) {
            error_log("Error deleting role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if role name exists
     * 
     * @param string $name Role name
     * @return bool True if exists, false otherwise
     */
    public function roleNameExists($name)
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE ur_name = :name
        ";
        
        $result = $this->queryScalar($sql, ['name' => $name], 0);
        return $result > 0;
    }

    /**
     * Get default role ID (usually for new users)
     * 
     * @return int|null Default role ID or null if not found
     */
    public function getDefaultRoleId()
    {
        // Assuming 'customer' is the default role
        $role = $this->getRoleByName('customer');
        return $role ? $role[$this->primaryKey] : null;
    }
}