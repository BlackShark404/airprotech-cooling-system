<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserManagementController extends BaseController
{
    private $userModel;

    public function __construct() 
    {
        parent::__construct();
        $this->userModel = $this->loadModel('UserModel');
    }

    /**
     * Render the user management page
     */
    public function renderUserManagement()
    {
        $this->render('admin/user-management');
    }

    /**
     * Get all users for DataTables
     */
    public function getData()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            $users = $this->userModel->getAllUsers();
            
            // Format data for DataTables
            $formattedUsers = array_map(function($user) {
                return [
                    'id' => $user['ua_id'],
                    'first_name' => $user['ua_first_name'],
                    'last_name' => $user['ua_last_name'],
                    'name' => $user['ua_first_name'] . ' ' . $user['ua_last_name'],
                    'email' => $user['ua_email'],
                    'role' => $user['role_name'],
                    'status' => $user['ua_is_active'] ? 'Active' : 'Inactive',
                    'last_login' => $user['ua_last_login'] ? date('Y-m-d H:i', strtotime($user['ua_last_login'])) : 'Never',
                ];
            }, $users);
            
            $this->jsonSuccess($formattedUsers);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching users: ' . $e->getMessage());
        }
    }

    /**
     * Get a single user by ID
     * 
     * @param int $id User ID
     */
    public function getUser($id)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            $user = $this->userModel->findById($id);
            
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Format the user data
            $formattedUser = [
                'id' => $user['ua_id'],
                'first_name' => $user['ua_first_name'],
                'last_name' => $user['ua_last_name'],
                'email' => $user['ua_email'],
                'role_id' => $user['ua_role_id'],
                'role' => $user['role_name'],
                'status' => $user['ua_is_active'] ? 'Active' : 'Inactive',
                'is_active' => (bool)$user['ua_is_active'],
                'last_login' => $user['ua_last_login'],
                'created_at' => $user['ua_created_at']
            ];
            
            $this->jsonSuccess($formattedUser);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching user: ' . $e->getMessage());
        }
    }

    /**
     * Create a new user
     */
    public function createUser()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            $data = $this->getJsonInput();
            
            // Validate required fields
            $requiredFields = ['first_name', 'last_name', 'email', 'password', 'role'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->jsonError("Field '{$field}' is required", 400);
                }
            }
            
            // Check if email already exists
            if ($this->userModel->emailExists($data['email'])) {
                $this->jsonError('Email already exists', 400);
            }
            
            // Format data for user model
            $userData = [
                'ua_first_name' => $data['first_name'],
                'ua_last_name' => $data['last_name'],
                'ua_email' => $data['email'],
                'ua_password' => $data['password'],
                'ua_role_id' => $this->userModel->getRoleIdByName($data['role']),
                'ua_is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true
            ];
            
            // Create user based on role
            $userId = null;
            
            switch ($data['role']) {
                case 'admin':
                    $userId = $this->userModel->createAdmin($userData);
                    break;
                case 'technician':
                    $userId = $this->userModel->createTechnician($userData);
                    break;
                case 'customer':
                    $userId = $this->userModel->createCustomer($userData);
                    break;
                default:
                    $userId = $this->userModel->createUser($userData);
            }
            
            if (!$userId) {
                $this->jsonError('Failed to create user', 500);
            }
            
            // Get the newly created user
            $newUser = $this->userModel->findById($userId);
            
            $formattedUser = [
                'id' => $newUser['ua_id'],
                'first_name' => $newUser['ua_first_name'],
                'last_name' => $newUser['ua_last_name'],
                'name' => $newUser['ua_first_name'] . ' ' . $newUser['ua_last_name'],
                'email' => $newUser['ua_email'],
                'role' => $newUser['role_name'],
                'status' => $newUser['ua_is_active'] ? 'Active' : 'Inactive',
                'last_login' => $newUser['ua_last_login'] ? date('Y-m-d H:i', strtotime($newUser['ua_last_login'])) : 'Never',
            ];
            
            $this->jsonSuccess($formattedUser, 'User created successfully');
        } catch (\Exception $e) {
            $this->jsonError('Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing user
     * 
     * @param int $id User ID
     */
    public function updateUser($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            $data = $this->getJsonInput();
            
            // Validate user exists
            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Format data for user model
            $userData = [
                'ua_first_name' => $data['first_name'] ?? $user['ua_first_name'],
                'ua_last_name' => $data['last_name'] ?? $user['ua_last_name'],
                'ua_is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : $user['ua_is_active']
            ];
            
            // Only update email if changed and not already taken
            if (isset($data['email']) && $data['email'] !== $user['ua_email']) {
                if ($this->userModel->emailExists($data['email'])) {
                    $this->jsonError('Email already exists', 400);
                }
                $userData['ua_email'] = $data['email'];
            }
            
            // Only update password if provided
            if (!empty($data['password'])) {
                $userData['ua_password'] = $data['password'];
            }
            
            // Only update role if provided and different
            if (isset($data['role']) && $data['role'] !== $user['role_name']) {
                $roleId = $this->userModel->getRoleIdByName($data['role']);
                if ($roleId) {
                    $userData['ua_role_id'] = $roleId;
                }
            }
            
            // Update user
            $success = $this->userModel->updateUser($id, $userData);
            
            if (!$success) {
                $this->jsonError('Failed to update user', 500);
            }
            
            // Get the updated user
            $updatedUser = $this->userModel->findById($id);
            
            $formattedUser = [
                'id' => $updatedUser['ua_id'],
                'first_name' => $updatedUser['ua_first_name'],
                'last_name' => $updatedUser['ua_last_name'],
                'name' => $updatedUser['ua_first_name'] . ' ' . $updatedUser['ua_last_name'],
                'email' => $updatedUser['ua_email'],
                'role' => $updatedUser['role_name'],
                'status' => $updatedUser['ua_is_active'] ? 'Active' : 'Inactive',
                'last_login' => $updatedUser['ua_last_login'] ? date('Y-m-d H:i', strtotime($updatedUser['ua_last_login'])) : 'Never',
            ];
            
            $this->jsonSuccess($formattedUser, 'User updated successfully');
        } catch (\Exception $e) {
            $this->jsonError('Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Delete a user
     * 
     * @param int $id User ID
     */
    public function deleteUser($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            // Validate user exists
            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Delete user (soft delete)
            $success = $this->userModel->deleteUser($id);
            
            if (!$success) {
                $this->jsonError('Failed to delete user', 500);
            }
            
            $this->jsonSuccess(['id' => $id], 'User deleted successfully');
        } catch (\Exception $e) {
            $this->jsonError('Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Activate a user
     * 
     * @param int $id User ID
     */
    public function activateUser($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            // Validate user exists
            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Activate user
            $success = $this->userModel->activateUser($id);
            
            if (!$success) {
                $this->jsonError('Failed to activate user', 500);
            }
            
            $this->jsonSuccess(['id' => $id], 'User activated successfully');
        } catch (\Exception $e) {
            $this->jsonError('Error activating user: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate a user
     * 
     * @param int $id User ID
     */
    public function deactivateUser($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }

        try {
            // Validate user exists
            $user = $this->userModel->findById($id);
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Deactivate user
            $success = $this->userModel->deactivateUser($id);
            
            if (!$success) {
                $this->jsonError('Failed to deactivate user', 500);
            }
            
            $this->jsonSuccess(['id' => $id], 'User deactivated successfully');
        } catch (\Exception $e) {
            $this->jsonError('Error deactivating user: ' . $e->getMessage());
        }
    }
}