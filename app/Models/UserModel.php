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

    /**
     * Get users filtered by role and/or status
     * 
     * @param string $role Role name
     * @param string $status Status ('active' or 'inactive')
     * @return array Filtered user records
     */
    public function getFilteredUsers($role = '', $status = '')
    {
        $sql = "
            SELECT user_account.*, user_role.ur_name AS role_name
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_account.ua_deleted_at IS NULL
        ";
        
        $params = [];
        
        // Apply role filter
        if (!empty($role)) {
            $sql .= " AND user_role.ur_name = :role";
            $params['role'] = $role;
        }
        
        // Apply status filter
        if (!empty($status)) {
            $isActive = ($status === 'active') ? 1 : 0;
            $sql .= " AND user_account.ua_is_active = :is_active";
            $params['is_active'] = $isActive;
        }
        
        $sql .= " ORDER BY user_account.ua_last_name, user_account.ua_first_name";
        
        return $this->query($sql, $params);
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

    public function search($searchTerm)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE (ua_first_name ILIKE :search_term 
                OR ua_last_name ILIKE :search_term 
                OR ua_email ILIKE :search_term)
            AND ua_deleted_at IS NULL
            ORDER BY ua_last_name, ua_first_name
        ";
        
        return $this->query($sql, ['search_term' => "%$searchTerm%"]);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function createUser(array $data)
    {
        // Format the columns and values for the SQL query
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        // Add timestamps
        $now = date('Y-m-d H:i:s');
        $data['ua_created_at'] = $now;
        $data['ua_updated_at'] = $now;
        
        $columns .= ", ua_created_at, ua_updated_at";
        $placeholders .= ", :ua_created_at, :ua_updated_at";
        
        $sql = "
            INSERT INTO {$this->table} ($columns)
            VALUES ($placeholders)
            RETURNING {$this->primaryKey}
        ";
        
        $this->execute($sql, $data);
        
        return $this->lastInsertId();
    }

    public function updateUser($id, array $data)
    {
        // Add timestamp for update
        $data['ua_updated_at'] = date('Y-m-d H:i:s');
        
        // Format the update sections
        $updateParts = [];
        foreach (array_keys($data) as $column) {
            $updateParts[] = "$column = :$column";
        }
        $updateClause = implode(', ', $updateParts);
        
        $sql = "
            UPDATE {$this->table}
            SET $updateClause
            WHERE {$this->primaryKey} = :id
        ";
        
        // Add the ID to the parameter list
        $data['id'] = $id;
        
        return $this->execute($sql, $data);
    }

    /**
     * Delete a user by ID
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
            'ua_deleted_at' => date('Y-m-d H:i:s'),
            'id' => $id
        ];
        
        $sql = "
            UPDATE {$this->table} 
            SET ua_deleted_at = :ua_deleted_at
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->execute($sql, $data);
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
        $sql = "
            UPDATE {$this->table}
            SET ua_last_login = :now
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->execute($sql, [
            'now' => date('Y-m-d H:i:s'),
            'id' => $userId
        ]);
    }

    public function generateRememberToken($userId, $days = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$days days"));

        $sql = "
            UPDATE {$this->table}
            SET ua_remember_token = :token,
                ua_remember_token_expires_at = :expires_at
            WHERE {$this->primaryKey} = :id
        ";
        
        $this->execute($sql, [
            'token' => $token,
            'expires_at' => $expiresAt,
            'id' => $userId
        ]);

        return $token;
    }

    public function clearRememberToken($userId)
    {
        $sql = "
            UPDATE {$this->table}
            SET ua_remember_token = NULL,
                ua_remember_token_expires_at = NULL
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->execute($sql, ['id' => $userId]);
    }

    public function getFullName($user)
    {
        return $user['ua_first_name'] . ' ' . $user['ua_last_name'];
    }

    public function activateUser($userId)
    {
        $sql = "
            UPDATE {$this->table}
            SET ua_is_active = :is_active
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->execute($sql, [
            'is_active' => true,
            'id' => $userId
        ]);
    }

    public function deactivateUser($userId)
    {
        $sql = "
            UPDATE {$this->table}
            SET ua_is_active = :is_active
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->execute($sql, [
            'is_active' => false,
            'id' => $userId
        ]);
    }

    public function getActiveOnly()
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ua_is_active = :is_active
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
        $sql = "
            SELECT user_account.*
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_role.ur_name = :user_role
        ";
        
        return $this->query($sql, ['user_role' => 'admin']);
    }

    public function getRegularUsers()
    {
        $sql = "
            SELECT user_account.*
            FROM {$this->table}
            JOIN user_role ON user_account.ua_role_id = user_role.ur_id
            WHERE user_role.ur_name = :user_role
        ";
        
        return $this->query($sql, ['user_role' => 'customer']);
    }

    public function changeRole($userId, $roleId)
    {
        $sql = "
            UPDATE {$this->table}
            SET ua_role_id = :role_id
            WHERE {$this->primaryKey} = :id
        ";
        
        return $this->execute($sql, [
            'role_id' => $roleId,
            'id' => $userId
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
}