<?php

namespace App\Models;

class UserManagementModel extends Model
{
    protected $table = 'USER_ACCOUNT';
    
    /**
     * Get all users with pagination and filtering
     * 
     * @param int $start Start index for pagination
     * @param int $length Number of records per page
     * @param string $search Search keyword
     * @param array $filters Additional filters (role, status)
     * @param string $orderColumn Column to order by
     * @param string $orderDirection Order direction (asc, desc)
     * @return array Array with 'data', 'recordsTotal', and 'recordsFiltered'
     */
    public function getUsers($start = 0, $length = 10, $search = '', $filters = [], $orderColumn = 'UA_ID', $orderDirection = 'asc')
    {
        // Base query - join with roles table
        $baseQuery = "
            SELECT 
                UA_ID as id,
                UA_FIRST_NAME as first_name,
                UA_LAST_NAME as last_name,
                CONCAT(UA_FIRST_NAME, ' ', UA_LAST_NAME) as name,
                UA_EMAIL as email,
                UR_NAME as role,
                CASE WHEN UA_IS_ACTIVE = TRUE THEN 'Active' ELSE 'Inactive' END as status,
                UA_LAST_LOGIN as last_login
            FROM {$this->table}
            JOIN USER_ROLE ON USER_ROLE.UR_ID = {$this->table}.UA_ROLE_ID
            WHERE UA_DELETED_AT IS NULL
        ";
        
        // Add search condition if search term provided
        $searchCondition = '';
        $searchParams = [];
        
        if (!empty($search)) {
            $searchCondition = " AND (
                UA_FIRST_NAME ILIKE :search OR
                UA_LAST_NAME ILIKE :search OR
                UA_EMAIL ILIKE :search
            )";
            $searchParams['search'] = "%{$search}%";
        }
        
        // Add filter conditions
        $filterCondition = '';
        $filterParams = [];
        
        if (!empty($filters['role'])) {
            $filterCondition .= " AND UR_NAME = :role";
            $filterParams['role'] = $filters['role'];
        }
        
        if (isset($filters['status'])) {
            $isActive = $filters['status'] === 'Active' ? 'TRUE' : 'FALSE';
            $filterCondition .= " AND UA_IS_ACTIVE = {$isActive}";
        }
        
        // Count total records (without filtering)
        $totalQuery = "SELECT COUNT(*) FROM {$this->table} WHERE UA_DELETED_AT IS NULL";
        $recordsTotal = $this->queryScalar($totalQuery);
        
        // Count filtered records
        $filteredQuery = $baseQuery . $searchCondition . $filterCondition;
        $countQuery = "SELECT COUNT(*) FROM ({$filteredQuery}) AS filtered_data";
        $recordsFiltered = $this->queryScalar($countQuery, array_merge($searchParams, $filterParams));
        
        // Add pagination and ordering
        $orderBy = '';
        
        // Map front-end column names to DB column names
        $columnMap = [
            'id' => 'UA_ID',
            'name' => 'UA_FIRST_NAME', // Order by first name
            'email' => 'UA_EMAIL',
            'role' => 'UR_NAME',
            'status' => 'UA_IS_ACTIVE',
            'last_login' => 'UA_LAST_LOGIN'
        ];
        
        if (array_key_exists($orderColumn, $columnMap)) {
            $dbColumn = $columnMap[$orderColumn];
            $orderBy = " ORDER BY {$dbColumn} {$orderDirection}";
        } else {
            $orderBy = " ORDER BY UA_ID ASC"; // Default ordering
        }
        
        $paginationQuery = $baseQuery . $searchCondition . $filterCondition . $orderBy . " LIMIT {$length} OFFSET {$start}";
        $data = $this->query($paginationQuery, array_merge($searchParams, $filterParams));
        
        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getUserById($id)
    {
        $sql = "
            SELECT 
                UA_ID as id,
                UA_FIRST_NAME as first_name,
                UA_LAST_NAME as last_name,
                UA_EMAIL as email,
                UA_PHONE_NUMBER as phone,
                UA_ADDRESS as address,
                UA_ROLE_ID as role_id,
                UR_NAME as role,
                UA_IS_ACTIVE as is_active,
                CASE WHEN UA_IS_ACTIVE = TRUE THEN 'Active' ELSE 'Inactive' END as status,
                UA_LAST_LOGIN as last_login,
                UA_CREATED_AT as created_at,
                UA_UPDATED_AT as updated_at
            FROM {$this->table}
            JOIN USER_ROLE ON USER_ROLE.UR_ID = {$this->table}.UA_ROLE_ID
            WHERE UA_ID = :id AND UA_DELETED_AT IS NULL
        ";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Create a new user
     * 
     * @param array $userData User data
     * @return int|bool New user ID or false on failure
     */
    public function createUser($userData)
    {
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Hash the password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Get role ID from role name
            $roleId = $this->getRoleIdByName($userData['role']);
            
            if (!$roleId) {
                throw new \Exception("Invalid role specified");
            }
            
            // Insert new user
            $sql = "
                INSERT INTO {$this->table} (
                    UA_FIRST_NAME,
                    UA_LAST_NAME,
                    UA_EMAIL,
                    UA_HASHED_PASSWORD,
                    UA_ROLE_ID,
                    UA_IS_ACTIVE
                ) VALUES (
                    :first_name,
                    :last_name,
                    :email,
                    :password,
                    :role_id,
                    :is_active
                ) RETURNING UA_ID
            ";
            
            $params = [
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => $hashedPassword,
                'role_id' => $roleId,
                'is_active' => isset($userData['is_active']) ? $userData['is_active'] : true
            ];
            
            $newUserId = $this->queryScalar($sql, $params);
            
            // Commit transaction
            $this->commit();
            
            return $newUserId;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Update an existing user
     * 
     * @param int $id User ID
     * @param array $userData User data
     * @return bool True on success, false on failure
     */
    public function updateUser($id, $userData)
    {
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Build the update query
            $setClause = [];
            $params = ['id' => $id];
            
            if (isset($userData['first_name'])) {
                $setClause[] = "UA_FIRST_NAME = :first_name";
                $params['first_name'] = $userData['first_name'];
            }
            
            if (isset($userData['last_name'])) {
                $setClause[] = "UA_LAST_NAME = :last_name";
                $params['last_name'] = $userData['last_name'];
            }
            
            if (isset($userData['email'])) {
                $setClause[] = "UA_EMAIL = :email";
                $params['email'] = $userData['email'];
            }
            
            if (isset($userData['phone'])) {
                $setClause[] = "UA_PHONE_NUMBER = :phone";
                $params['phone'] = $userData['phone'];
            }
            
            if (isset($userData['address'])) {
                $setClause[] = "UA_ADDRESS = :address";
                $params['address'] = $userData['address'];
            }
            
            if (isset($userData['password']) && !empty($userData['password'])) {
                $setClause[] = "UA_HASHED_PASSWORD = :password";
                $params['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($userData['role'])) {
                $roleId = $this->getRoleIdByName($userData['role']);
                if ($roleId) {
                    $setClause[] = "UA_ROLE_ID = :role_id";
                    $params['role_id'] = $roleId;
                }
            }
            
            if (isset($userData['is_active'])) {
                $setClause[] = "UA_IS_ACTIVE = :is_active";
                $params['is_active'] = $userData['is_active'];
            }
            
            // Always update UA_UPDATED_AT
            $setClause[] = "UA_UPDATED_AT = CURRENT_TIMESTAMP";
            
            // Construct and execute the query if we have something to update
            if (!empty($setClause)) {
                $sql = "
                    UPDATE {$this->table}
                    SET " . implode(", ", $setClause) . "
                    WHERE UA_ID = :id AND UA_DELETED_AT IS NULL
                ";
                
                $result = $this->execute($sql, $params);
                
                // Commit transaction
                $this->commit();
                
                return $result > 0;
            }
            
            // Nothing to update
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Soft delete a user by setting the deleted_at timestamp
     * 
     * @param int $id User ID
     * @return bool True on success, false on failure
     */
    public function deleteUser($id)
    {
        $sql = "
            UPDATE {$this->table}
            SET UA_DELETED_AT = CURRENT_TIMESTAMP
            WHERE UA_ID = :id AND UA_DELETED_AT IS NULL
        ";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Activate a user
     * 
     * @param int $id User ID
     * @return bool True on success, false on failure
     */
    public function activateUser($id)
    {
        return $this->updateUser($id, ['is_active' => true]);
    }
    
    /**
     * Deactivate a user
     * 
     * @param int $id User ID
     * @return bool True on success, false on failure
     */
    public function deactivateUser($id)
    {
        return $this->updateUser($id, ['is_active' => false]);
    }
    
    /**
     * Get role ID by role name
     * 
     * @param string $roleName Role name
     * @return int|null Role ID or null if not found
     */
    private function getRoleIdByName($roleName)
    {
        $sql = "SELECT UR_ID FROM USER_ROLE WHERE UR_NAME = :role_name";
        return $this->queryScalar($sql, ['role_name' => $roleName]);
    }
    
    /**
     * Get all roles
     * 
     * @return array All roles
     */
    public function getAllRoles()
    {
        $sql = "SELECT UR_ID as id, UR_NAME as name FROM USER_ROLE ORDER BY UR_ID ASC";
        return $this->query($sql);
    }
    
    /**
     * Check if email already exists (for a different user)
     * 
     * @param string $email Email to check
     * @param int $excludeUserId User ID to exclude (for updates)
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email, $excludeUserId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE UA_EMAIL = :email AND UA_DELETED_AT IS NULL";
        $params = ['email' => $email];
        
        if ($excludeUserId) {
            $sql .= " AND UA_ID != :exclude_id";
            $params['exclude_id'] = $excludeUserId;
        }
        
        return $this->queryScalar($sql, $params) > 0;
    }
}