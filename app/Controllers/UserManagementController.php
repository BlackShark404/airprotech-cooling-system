<?php

namespace App\Controllers;

class UserManagementController extends BaseController
{
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = $this->loadModel('UserManagementModel');
    }
    
    /**
     * Render the user management page
     */
    public function index()
    {
        $this->render('admin/user-management');
    }
    
    /**
     * Get users data for DataTables
     */
    public function getUsersData()
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            // Get request parameters from DataTables
            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
            
            // Order column
            $orderColumn = 'id'; // Default order column
            $orderDirection = 'asc'; // Default order direction
            
            if (isset($_POST['order'][0]['column'])) {
                $columnIndex = $_POST['order'][0]['column'];
                
                // Map column index to column name
                $columns = ['id', 'name', 'email', 'role', 'status', 'last_login'];
                
                if (isset($columns[$columnIndex])) {
                    $orderColumn = $columns[$columnIndex];
                    $orderDirection = $_POST['order'][0]['dir'] ?? 'asc';
                }
            }
            
            // Get filters
            $filters = [];
            
            if (isset($_POST['role']) && !empty($_POST['role'])) {
                $filters['role'] = $_POST['role'];
            }
            
            if (isset($_POST['status']) && !empty($_POST['status'])) {
                $filters['status'] = $_POST['status'];
            }
            
            // Get users with pagination and filtering
            $result = $this->userModel->getUsers($start, $length, $search, $filters, $orderColumn, $orderDirection);
            
            // Format dates for display
            foreach ($result['data'] as &$user) {
                if (isset($user['last_login']) && $user['last_login']) {
                    $user['last_login'] = date('M d, Y h:i A', strtotime($user['last_login']));
                } else {
                    $user['last_login'] = 'Never';
                }
            }
            
            // Prepare response for DataTables
            $response = [
                'draw' => $draw,
                'recordsTotal' => $result['recordsTotal'],
                'recordsFiltered' => $result['recordsFiltered'],
                'data' => $result['data']
            ];
            
            $this->json($response);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Get a single user by ID
     */
    public function getUser($id)
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Format dates for display
            if (isset($user['last_login']) && $user['last_login']) {
                $user['last_login'] = date('M d, Y h:i A', strtotime($user['last_login']));
            } else {
                $user['last_login'] = 'Never';
            }
            
            if (isset($user['created_at']) && $user['created_at']) {
                $user['created_at'] = date('M d, Y h:i A', strtotime($user['created_at']));
            }
            
            if (isset($user['updated_at']) && $user['updated_at']) {
                $user['updated_at'] = date('M d, Y h:i A', strtotime($user['updated_at']));
            }
            
            $this->jsonSuccess($user);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Create a new user
     */
    public function createUser()
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            // Get user data from request
            $userData = $this->getJsonInput();
            
            // Validate user data
            $errors = $this->validateUserData($userData);
            
            if (!empty($errors)) {
                $this->jsonError('Validation failed', 422, ['errors' => $errors]);
            }
            
            // Check if email already exists
            if ($this->userModel->emailExists($userData['email'])) {
                $this->jsonError('Email already exists', 422, ['errors' => ['email' => 'This email is already in use']]);
            }
            
            // Create new user
            $newUserId = $this->userModel->createUser($userData);
            
            if (!$newUserId) {
                $this->jsonError('Failed to create user', 500);
            }
            
            // Get created user data
            $newUser = $this->userModel->getUserById($newUserId);
            
            $this->jsonSuccess($newUser, 'User created successfully');
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Update an existing user
     */
    public function updateUser($id)
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            // Check if user exists
            $existingUser = $this->userModel->getUserById($id);
            
            if (!$existingUser) {
                $this->jsonError('User not found', 404);
            }
            
            // Get user data from request
            $userData = $this->getJsonInput();
            
            // Validate user data (partial update)
            $errors = $this->validateUserData($userData, true);
            
            if (!empty($errors)) {
                $this->jsonError('Validation failed', 422, ['errors' => $errors]);
            }
            
            // Check if email already exists (for a different user)
            if (isset($userData['email']) && $userData['email'] !== $existingUser['email']) {
                if ($this->userModel->emailExists($userData['email'], $id)) {
                    $this->jsonError('Email already exists', 422, ['errors' => ['email' => 'This email is already in use']]);
                }
            }
            
            // Update user
            $success = $this->userModel->updateUser($id, $userData);
            
            if (!$success) {
                $this->jsonError('Failed to update user', 500);
            }
            
            // Get updated user data
            $updatedUser = $this->userModel->getUserById($id);
            
            $this->jsonSuccess($updatedUser, 'User updated successfully');
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            // Check if user exists
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Delete user
            $success = $this->userModel->deleteUser($id);
            
            if (!$success) {
                $this->jsonError('Failed to delete user', 500);
            }
            
            $this->jsonSuccess(null, 'User deleted successfully');
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Activate a user
     */
    public function activateUser($id)
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            // Check if user exists
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Activate user
            $success = $this->userModel->activateUser($id);
            
            if (!$success) {
                $this->jsonError('Failed to activate user', 500);
            }
            
            $this->jsonSuccess(null, 'User activated successfully');
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Deactivate a user
     */
    public function deactivateUser($id)
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            // Check if user exists
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                $this->jsonError('User not found', 404);
            }
            
            // Deactivate user
            $success = $this->userModel->deactivateUser($id);
            
            if (!$success) {
                $this->jsonError('Failed to deactivate user', 500);
            }
            
            $this->jsonSuccess(null, 'User deactivated successfully');
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Get all roles
     */
    public function getRoles()
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $roles = $this->userModel->getAllRoles();
            $this->jsonSuccess($roles);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage(), 500);
        }
    }
    
    /**
     * Validate user data
     * 
     * @param array $userData User data
     * @param bool $isUpdate Is this a partial update (some fields may be missing)
     * @return array Validation errors
     */
    private function validateUserData($userData, $isUpdate = false)
    {
        $errors = [];
        
        // First name validation
        if (isset($userData['first_name'])) {
            if (empty($userData['first_name'])) {
                $errors['first_name'] = 'First name is required';
            } elseif (strlen($userData['first_name']) > 255) {
                $errors['first_name'] = 'First name cannot exceed 255 characters';
            }
        } elseif (!$isUpdate) {
            $errors['first_name'] = 'First name is required';
        }
        
        // Last name validation
        if (isset($userData['last_name'])) {
            if (empty($userData['last_name'])) {
                $errors['last_name'] = 'Last name is required';
            } elseif (strlen($userData['last_name']) > 255) {
                $errors['last_name'] = 'Last name cannot exceed 255 characters';
            }
        } elseif (!$isUpdate) {
            $errors['last_name'] = 'Last name is required';
        }
        
        // Email validation
        if (isset($userData['email'])) {
            if (empty($userData['email'])) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            } elseif (strlen($userData['email']) > 255) {
                $errors['email'] = 'Email cannot exceed 255 characters';
            }
        } elseif (!$isUpdate) {
            $errors['email'] = 'Email is required';
        }
        
        // Password validation (required for new users, optional for updates)
        if (isset($userData['password'])) {
            if (!$isUpdate && empty($userData['password'])) {
                $errors['password'] = 'Password is required';
            } elseif (!empty($userData['password']) && strlen($userData['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters long';
            }
        } elseif (!$isUpdate) {
            $errors['password'] = 'Password is required';
        }
        
        // Role validation
        if (isset($userData['role'])) {
            if (empty($userData['role'])) {
                $errors['role'] = 'Role is required';
            }
        } elseif (!$isUpdate) {
            $errors['role'] = 'Role is required';
        }
        
        // Phone validation (optional)
        if (isset($userData['phone']) && !empty($userData['phone'])) {
            if (strlen($userData['phone']) > 20) {
                $errors['phone'] = 'Phone number cannot exceed 20 characters';
            }
        }
        
        // Address validation (optional)
        if (isset($userData['address']) && !empty($userData['address'])) {
            if (strlen($userData['address']) > 1000) {
                $errors['address'] = 'Address cannot exceed 1000 characters';
            }
        }
        
        return $errors;
    }
}