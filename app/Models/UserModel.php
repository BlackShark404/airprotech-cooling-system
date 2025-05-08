<?php

namespace App\Models;

use App\Models\BaseModel;
/**
 * User Model
 * 
 * This model represents a user in the system and demonstrates how to
 * extend the BaseModel class with specific functionality.
 */
class UserModel extends BaseModel
{
    protected $table = 'user_account';
    protected $primaryKey = 'ua_id';

    /**
     * Get all users with role information
     * 
     * @return array Array of user records with role information
     */
    public function getAllUsers()
    {
        $sql = "
            SELECT user_account.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.ua_deleted_at IS NULL
            ORDER BY user_account.ua_last_name, user_account.ua_first_name
        ";
        
        return $this->query($sql);
    }

    public function createUser(array $data, array $exclude = [], array $expressions = [])
    {
        // Add timestamps
        $now = date('Y-m-d H:i:s');
        if (!isset($data['ua_created_at'])) {
            $data['ua_created_at'] = $now;
        }
        if (!isset($data['ua_updated_at'])) {
            $data['ua_updated_at'] = $now;
        }
        
        // Rename password field to match database schema
        if (isset($data['ua_password'])) {
            $data['ua_hashed_password'] = $this->hashPassword($data['ua_password']);
            unset($data['ua_password']);
        }
        
        // Format the columns and values for the SQL query using BaseModel helper
        $formattedData = $this->formatInsertData($data, $exclude, $expressions);
        
        $sql = "
            INSERT INTO {$this->table} ({$formattedData['columns']})
            VALUES ({$formattedData['placeholders']})
            RETURNING {$this->primaryKey}
        ";
        
        try {
            $this->execute($sql, $formattedData['filteredData']);
            return $this->lastInsertId();
        } catch (\Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, array $data, array $exclude = [], array $expressions = [])
    {
        // Rename password field to match database schema
        if (isset($data['ua_password'])) {
            $data['ua_hashed_password'] = $this->hashPassword($data['ua_password']);
            unset($data['ua_password']);
        }
        
        // Add updated_at timestamp
        if (!isset($data['ua_updated_at'])) {
            $data['ua_updated_at'] = date('Y-m-d H:i:s');
        }
        
        // Use BaseModel helper to format the update data
        $formattedData = $this->formatUpdateData($data, $exclude, $expressions);
        
        $sql = "
            UPDATE {$this->table}
            SET {$formattedData['updateClause']}
            WHERE {$this->primaryKey} = :id
        ";
        
        // Add the ID to the parameter list
        $params = $formattedData['filteredData'];
        $params['id'] = $id;
        
        try {
            return $this->execute($sql, $params);
        } catch (\Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function findById($id) {
        $sql = "
            SELECT user_account.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.{$this->primaryKey} = :id
        ";
        
        return $this->queryOne($sql, ['id' => $id]);
    }

    public function findByEmail($email)
    {
        $sql = "
            SELECT user_account.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.ua_email = :email
        ";
        
        return $this->queryOne($sql, ['email' => $email]);
    }

    public function getByRole($roleName)
    {
        $sql = "
            SELECT user_account.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_role.ur_name = :role_name
            AND user_account.ua_deleted_at IS NULL
        ";
        
        return $this->query($sql, ['role_name' => $roleName]);
    }

    public function getNewest()
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            ORDER BY ua_created_at DESC
        ";
        
        return $this->query($sql);
    }

    /**
     * Delete a user by ID
     * 
     * @param int $id User ID
     * @param bool $permanent Whether to permanently delete
     * @return bool Success status
     */
    public function deleteUser($id, $permanent = false)
    {
        if ($permanent) {
            // Permanently delete the user from the database
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            return $this->execute($sql, ['id' => $id]);
        }
        
        // Use soft delete by setting the deleted_at timestamp
        $data = [
            'ua_deleted_at' => date('Y-m-d H:i:s')
        ];
        
        // Use the updateUser method which now supports formatUpdateData
        return $this->updateUser($id, $data);
    }

    public function emailExists($email)
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE ua_email = :email
        ";
        
        $result = $this->queryScalar($sql, ['email' => $email], 0);
        return $result > 0;
    }

    public function getActiveUsers($days = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ua_is_active = :is_active
            AND ua_last_login >= :cutoff
            AND ua_deleted_at IS NULL
            ORDER BY ua_last_login DESC
        ";
        
        return $this->query($sql, [
            'is_active' => true,
            'cutoff' => $cutoff
        ]);
    }

    public function findByRememberToken($token)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ua_remember_token = :token
            AND ua_remember_token_expires_at > NOW()
        ";
        
        return $this->queryOne($sql, ['token' => $token]);
    }

    public function updateLastLogin($userId)
    {
        return $this->updateUser($userId, [
            'ua_last_login' => date('Y-m-d H:i:s')
        ]);
    }

    public function generateRememberToken($userId, $days = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$days days"));

        $this->updateUser($userId, [
            'ua_remember_token' => $token,
            'ua_remember_token_expires_at' => $expiresAt
        ]);

        return $token;
    }

    public function clearRememberToken($userId)
    {
        return $this->updateUser($userId, [
            'ua_remember_token' => null,
            'ua_remember_token_expires_at' => null
        ]);
    }

    public function getFullName($user)
    {
        return $user['ua_first_name'] . ' ' . $user['ua_last_name'];
    }

    public function activateUser($userId)
    {
        return $this->updateUser($userId, [
            'ua_is_active' => true
        ]);
    }

    public function deactivateUser($userId)
    {
        return $this->updateUser($userId, [
            'ua_is_active' => false
        ]);
    }

    public function getActiveOnly()
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ua_is_active = :is_active
            AND ua_deleted_at IS NULL
        ";
        
        return $this->query($sql, ['is_active' => true]);
    }

    public function getInactiveUsers($days = 90)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE (ua_last_login IS NULL OR ua_last_login < :cutoff)
            AND ua_deleted_at IS NULL
            ORDER BY ua_last_login ASC NULLS FIRST
        ";
        
        return $this->query($sql, ['cutoff' => $cutoff]);
    }

    public function getAdmins()
    {
        return $this->getByRole('admin');
    }

    public function getRegularUsers()
    {
        return $this->getByRole('customer');
    }

    public function getTechnicians()
    {
        return $this->getByRole('technician');
    }

    public function changeRole($userId, $roleId)
    {
        return $this->updateUser($userId, [
            'ua_role_id' => $roleId
        ]);
    }
    
    /**
     * Cleanup expired remember tokens
     * @return bool Success status
     */
    public function cleanupExpiredTokens()
    {
        $sql = "
            UPDATE {$this->table}
            SET ua_remember_token = NULL,
                ua_remember_token_expires_at = NULL
            WHERE ua_remember_token IS NOT NULL 
            AND ua_remember_token_expires_at < NOW()
        ";
        
        return $this->execute($sql) > 0;
    }
    
    /**
     * Create a new customer user with appropriate role
     *
     * @param array $userData Base user data
     * @param array $customerData Customer-specific data
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return int|bool New user ID or false on failure
     */
    public function createCustomer(array $userData, array $customerData = [], array $exclude = [], array $expressions = []) 
    {
        // Ensure customer role
        if (!isset($userData['ua_role_id'])) {
            $userData['ua_role_id'] = $this->getRoleIdByName('customer');
        }
        
        $this->beginTransaction();
        
        try {
            // Create base user account
            $userId = $this->createUser($userData, $exclude, $expressions);
            
            if (!$userId) {
                $this->rollback();
                return false;
            }
            
            // Create customer record
            $customerData['cu_account_id'] = $userId;
            
            $formattedData = $this->formatInsertData($customerData);
            
            $sql = "
                INSERT INTO customer ({$formattedData['columns']})
                VALUES ({$formattedData['placeholders']})
            ";
            
            $this->execute($sql, $formattedData['filteredData']);
            
            $this->commit();
            return $userId;
        } catch (\Exception $e) {
            $this->rollback();
            error_log("Error creating customer: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new technician user with appropriate role
     *
     * @param array $userData Base user data
     * @param array $technicianData Technician-specific data
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return int|bool New user ID or false on failure
     */
    public function createTechnician(array $userData, array $technicianData = [], array $exclude = [], array $expressions = []) 
    {
        // Ensure technician role
        if (!isset($userData['ua_role_id'])) {
            $userData['ua_role_id'] = $this->getRoleIdByName('technician');
        }
        
        $this->beginTransaction();
        
        try {
            // Create base user account
            $userId = $this->createUser($userData, $exclude, $expressions);
            
            if (!$userId) {
                $this->rollback();
                return false;
            }
            
            // Create technician record
            $technicianData['te_account_id'] = $userId;
            
            $formattedData = $this->formatInsertData($technicianData);
            
            $sql = "
                INSERT INTO technician ({$formattedData['columns']})
                VALUES ({$formattedData['placeholders']})
            ";
            
            $this->execute($sql, $formattedData['filteredData']);
            
            $this->commit();
            return $userId;
        } catch (\Exception $e) {
            $this->rollback();
            error_log("Error creating technician: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new admin user with appropriate role
     *
     * @param array $userData Base user data
     * @param array $adminData Admin-specific data
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return int|bool New user ID or false on failure
     */
    public function createAdmin(array $userData, array $adminData = [], array $exclude = [], array $expressions = []) 
    {
        // Ensure admin role
        if (!isset($userData['ua_role_id'])) {
            $userData['ua_role_id'] = $this->getRoleIdByName('admin');
        }
        
        $this->beginTransaction();
        
        try {
            // Create base user account
            $userId = $this->createUser($userData, $exclude, $expressions);
            
            if (!$userId) {
                $this->rollback();
                return false;
            }
            
            // Create admin record
            $adminData['ad_account_id'] = $userId;
            
            $formattedData = $this->formatInsertData($adminData);
            
            $sql = "
                INSERT INTO admin ({$formattedData['columns']})
                VALUES ({$formattedData['placeholders']})
            ";
            
            $this->execute($sql, $formattedData['filteredData']);
            
            $this->commit();
            return $userId;
        } catch (\Exception $e) {
            $this->rollback();
            error_log("Error creating admin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get role ID by role name
     *
     * @param string $roleName Role name to lookup
     * @return int|null Role ID or null if not found
     */
    public function getRoleIdByName($roleName) 
    {
        $sql = "SELECT ur_id FROM user_role WHERE ur_name = :role_name";
        return $this->queryScalar($sql, ['role_name' => $roleName]);
    }
    
    /**
     * Get customer details including user account information
     *
     * @param int $userId User ID
     * @return array|null Customer data or null if not found
     */
    public function getCustomerDetails($userId) 
    {
        $sql = "
            SELECT user_account.*, customer.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN customer ON user_account.ua_id = customer.cu_account_id
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.ua_id = :id
        ";
        
        return $this->queryOne($sql, ['id' => $userId]);
    }
    
    /**
     * Get technician details including user account information
     *
     * @param int $userId User ID
     * @return array|null Technician data or null if not found
     */
    public function getTechnicianDetails($userId) 
    {
        $sql = "
            SELECT user_account.*, technician.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN technician ON user_account.ua_id = technician.te_account_id
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.ua_id = :id
        ";
        
        return $this->queryOne($sql, ['id' => $userId]);
    }
    
    /**
     * Get admin details including user account information
     *
     * @param int $userId User ID
     * @return array|null Admin data or null if not found
     */
    public function getAdminDetails($userId) 
    {
        $sql = "
            SELECT user_account.*, admin.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN admin ON user_account.ua_id = admin.ad_account_id
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.ua_id = :id
        ";
        
        return $this->queryOne($sql, ['id' => $userId]);
    }
    
    /**
     * Update customer-specific details
     *
     * @param int $userId User ID
     * @param array $customerData Customer data to update
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return bool Success status
     */
    public function updateCustomerDetails($userId, array $customerData, array $exclude = [], array $expressions = []) 
    {
        $formattedData = $this->formatUpdateData($customerData, $exclude, $expressions);
        
        $sql = "
            UPDATE customer
            SET {$formattedData['updateClause']}
            WHERE cu_account_id = :id
        ";
        
        $params = $formattedData['filteredData'];
        $params['id'] = $userId;
        
        try {
            return $this->execute($sql, $params) > 0;
        } catch (\Exception $e) {
            error_log("Error updating customer details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update technician-specific details
     *
     * @param int $userId User ID
     * @param array $technicianData Technician data to update
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return bool Success status
     */
    public function updateTechnicianDetails($userId, array $technicianData, array $exclude = [], array $expressions = []) 
    {
        $formattedData = $this->formatUpdateData($technicianData, $exclude, $expressions);
        
        $sql = "
            UPDATE technician
            SET {$formattedData['updateClause']}
            WHERE te_account_id = :id
        ";
        
        $params = $formattedData['filteredData'];
        $params['id'] = $userId;
        
        try {
            return $this->execute($sql, $params) > 0;
        } catch (\Exception $e) {
            error_log("Error updating technician details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update admin-specific details
     *
     * @param int $userId User ID
     * @param array $adminData Admin data to update
     * @param array $exclude Fields to exclude
     * @param array $expressions Custom SQL expressions
     * @return bool Success status
     */
    public function updateAdminDetails($userId, array $adminData, array $exclude = [], array $expressions = []) 
    {
        $formattedData = $this->formatUpdateData($adminData, $exclude, $expressions);
        
        $sql = "
            UPDATE admin
            SET {$formattedData['updateClause']}
            WHERE ad_account_id = :id
        ";
        
        $params = $formattedData['filteredData'];
        $params['id'] = $userId;
        
        try {
            return $this->execute($sql, $params) > 0;
        } catch (\Exception $e) {
            error_log("Error updating admin details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get available technicians
     *
     * @return array Available technicians
     */
    public function getAvailableTechnicians() 
    {
        $sql = "
            SELECT user_account.*, technician.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN technician ON user_account.ua_id = technician.te_account_id
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE technician.te_is_available = true
            AND user_account.ua_is_active = true
            AND user_account.ua_deleted_at IS NULL
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Update technician availability
     *
     * @param int $userId Technician user ID
     * @param bool $isAvailable Availability status
     * @return bool Success status
     */
    public function updateTechnicianAvailability($userId, $isAvailable) 
    {
        return $this->updateTechnicianDetails($userId, [
            'te_is_available' => (bool) $isAvailable
        ]);
    }
}