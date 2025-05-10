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

    protected $fillable = [
        'ua_profile_url',
        'ua_first_name',
        'ua_last_name',
        'ua_email',
        'ua_hashed_password',
        'ua_phone_number',
        'ua_role_id',
        'ua_is_active',
        'ua_remember_token',
        'ua_remember_token_expires_at',
        'ua_last_login'
    ];

    protected $createdAtColumn = 'ua_created_at';
    protected $updatedAtColumn = 'ua_updated_at';
    protected $deletedAtColumn = 'ua_deleted_at';

    protected $timestamps = true;
    protected $useSoftDeletes = true;


    public function getAllUsers()
    {
        return $this->select('user_account.*, user_role.ur_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->whereSoftDeleted('user_account')
                    ->orderBy('user_account.ua_last_name, user_account.ua_first_name')
                    ->get();
    }


    public function getFilteredUsers($role = '', $status = '')
    {
        $query = $this->select('user_account.*, user_role.ur_name AS role_name')
                       ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                       ->whereSoftDeleted('user_account');
    
        if (!empty($role)) {
            $query->where('user_role.ur_name = :role')
                  ->bind(['role' => $role]);
        }
        
        
        if (!empty($status)) {
            $isActive = ($status === 'active') ? 1 : 0;
            $query->where('user_account.ua_is_active = :is_active')
                  ->bind(['is_active' => $isActive]);
        }
        
        return $query->orderBy('user_account.ua_last_name, user_account.ua_first_name')
                     ->get();
    }

    public function findById($id) {
        return $this->select('user_account.*, user_role.ur_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_account.ua_id = :id')
                    ->bind(['id' => $id])
                    ->first();
    }

    public function findByEmail($email)
    {
        return $this->select('user_account.*, user_role.ur_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_account.ua_email = :email')
                    ->bind(['email' => $email])
                    ->first();
    }

    public function getByRole($roleName)
    {
        return $this->select('user_account.*, user_role.ur_name AS role_name')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_role.ur_name = :role_name')
                    ->bind(['role_name' => $roleName])
                    ->whereSoftDeleted('user_account')
                    ->get();
    }

    public function getNewest()
    {
        return $this->orderBy('ua_created_at DESC')
                    ->get();
    }

    public function search($searchTerm)
    {
        return $this->where("ua_first_name ILIKE :search_term OR ua_last_name ILIKE :search_term OR ua_email ILIKE :search_term")
                    ->bind(['search_term' => "%$searchTerm%"])
                    ->whereSoftDeleted()
                    ->orderBy('ua_last_name, ua_first_name')
                    ->get();
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
        // No need to manually set timestamps as BaseModel.insert() handles this
        return $this->insert($data);
    }

    public function updateUser($id, array $data)
    {
        // No need to manually set updated_at as BaseModel.update() handles this
        return $this->update($data, "{$this->primaryKey} = :id", ['id' => $id]);
    }


    public function deleteUser($id, $permanent = false)
    {
        if ($permanent && $this->useSoftDeletes) {
            // Permanently delete the user from the database
            return $this->execute(
                "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
                ['id' => $id]
            );
        }
        
        // Use the standard delete method which respects soft deletes
        return $this->delete("{$this->primaryKey} = :id", ['id' => $id]);
    }

    public function emailExists($email)
    {
        return $this->exists('ua_email = :email', ['email' => $email]);
    }

    public function getActiveUsers($days = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));
        return $this->where('ua_is_active = :is_active')
                    ->where('ua_last_login >= :cutoff')
                    ->bind([
                        'is_active' => true,
                        'cutoff' => $cutoff
                    ])
                    ->whereSoftDeleted()
                    ->orderBy('ua_last_login DESC')
                    ->get();
    }

    public function findByRememberToken($token)
    {
        // Add check for token expiration
        return $this->where('ua_remember_token = :token')
                    ->where('ua_remember_token_expires_at > NOW()')
                    ->bind(['token' => $token])
                    ->first();
    }

    public function updateLastLogin($userId)
    {
        return $this->update(
            [
                'ua_last_login' => date('Y-m-d H:i:s')
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function generateRememberToken($userId, $days = 30)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$days days"));

        $this->update(
            [
                'ua_remember_token' => $token,
                'ua_remember_token_expires_at' => $expiresAt
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );

        return $token;
    }

    public function clearRememberToken($userId)
    {
        return $this->update(
            [
                'ua_remember_token' => null,
                'ua_remember_token_expires_at' => null
            ],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function getFullName($user)
    {
        return $user['ua_first_name'] . ' ' . $user['ua_last_name'];
    }

    public function activateUser($userId)
    {
        return $this->update(
            ['ua_is_active' => true],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function deactivateUser($userId)
    {
        return $this->update(
            ['ua_is_active' => false],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }

    public function getActiveOnly()
    {
        return $this->where('ua_is_active = :is_active')
                    ->bind(['is_active' => true])
                    ->get();
    }

    public function getInactiveUsers($days = 90)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

        return $this->where('(ua_last_login IS NULL OR ua_last_login < :cutoff)')
                    ->bind(['cutoff' => $cutoff])
                    ->whereSoftDeleted()
                    ->orderBy('ua_last_login ASC NULLS FIRST')
                    ->get();
    }

    public function getAdmins()
    {
        return $this->select('user_account.*')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_role.ur_name = :user_role')
                    ->bind(['user_role' => 'admin'])
                    ->get();
    }

    public function getRegularUsers()
    {
        return $this->select('user_account.*')
                    ->join('user_role', 'user_account.ua_role_id', 'user_role.ur_id')
                    ->where('user_role.ur_name = :user_role')
                    ->bind(['user_role' => 'customer'])
                    ->get();
    }

    public function changeRole($userId, $roleId)
    {
        return $this->update(
            ['ua_role_id' => $roleId],
            "{$this->primaryKey} = :id",
            ['id' => $userId]
        );
    }
    

    public function cleanupExpiredTokens()
    {
        return $this->update(
            [
                'ua_remember_token' => null,
                'ua_remember_token_expires_at' => null
            ],
            "ua_remember_token IS NOT NULL AND ua_remember_token_expires_at < NOW()",
            []
        );
    }


    public function getUserStats($userId)
    {
        // Sample implementation - in a real system, you would query related tables
        return [
            'services' => rand(0, 10),
            'active_services' => rand(0, 5),
            'logins' => rand(1, 50),
            'last_activity' => date('Y-m-d H:i:s', rand(
                strtotime('-30 days'), 
                strtotime('now')
            ))
        ];
    }

    /**
     * Bulk activate users
     * 
     * @param array $userIds Array of user IDs
     * @return int Number of updated users
     */
    public function bulkActivate(array $userIds)
    {
        if (empty($userIds)) {
            return 0;
        }

        $placeholders = [];
        $params = [];
        
        foreach ($userIds as $index => $id) {
            $placeholders[] = ":id{$index}";
            $params["id{$index}"] = $id;
        }
        
        $sql = "UPDATE {$this->table} SET ua_is_active = TRUE, {$this->updatedAtColumn} = NOW() 
                WHERE {$this->primaryKey} IN (" . implode(',', $placeholders) . ")";
        
        $this->execute($sql, $params);
        return count($userIds);
    }
    
    /**
     * Bulk deactivate users
     * 
     * @param array $userIds Array of user IDs
     * @return int Number of updated users
     */
    public function bulkDeactivate(array $userIds)
    {
        if (empty($userIds)) {
            return 0;
        }

        $placeholders = [];
        $params = [];
        
        foreach ($userIds as $index => $id) {
            $placeholders[] = ":id{$index}";
            $params["id{$index}"] = $id;
        }
        
        $sql = "UPDATE {$this->table} SET ua_is_active = FALSE, {$this->updatedAtColumn} = NOW() 
                WHERE {$this->primaryKey} IN (" . implode(',', $placeholders) . ")";
        
        $this->execute($sql, $params);
        return count($userIds);
    }
    
    /**
     * Bulk delete users
     * 
     * @param array $userIds Array of user IDs
     * @param bool $permanent Whether to permanently delete or soft delete
     * @return int Number of deleted users
     */
    public function bulkDelete(array $userIds, $permanent = false)
    {
        if (empty($userIds)) {
            return 0;
        }

        $placeholders = [];
        $params = [];
        
        foreach ($userIds as $index => $id) {
            $placeholders[] = ":id{$index}";
            $params["id{$index}"] = $id;
        }
        
        if ($permanent) {
            // Permanent delete
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} IN (" . implode(',', $placeholders) . ")";
        } else {
            // Soft delete
            $sql = "UPDATE {$this->table} SET {$this->deletedAtColumn} = NOW(), {$this->updatedAtColumn} = NOW() 
                    WHERE {$this->primaryKey} IN (" . implode(',', $placeholders) . ")";
        }
        
        $this->execute($sql, $params);
        return count($userIds);
    }
}