<?php

namespace App\Controllers;

/**
 * UsersDataTableController - Example implementation of DataTableController for Users table
 */
class UsersDataTableController extends DataTableController
{
    public function __construct()
    {
        parent::__construct('User');
        
        // Set searchable columns if not defined in the model
        $this->setSearchableColumns([
            'users.username', 
            'users.email', 
            'users.first_name', 
            'users.last_name'
        ]);
        
        // Set column mapping for DataTables to DB columns
        $this->setColumnMap([
            'username' => 'users.username',
            'email' => 'users.email',
            'name' => 'CONCAT(users.first_name, " ", users.last_name)',
            'role' => 'roles.name',
            'status' => 'users.status'
        ]);
        
        // Add fixed filters if needed (e.g., exclude deleted users)
        $this->setFixedFilters([
            'users.status' => ['operator' => '!=', 'value' => 'banned']
        ]);
        
        // Add column formatters
        $this->addColumnFormatter('status', function($value, $row) {
            $statusColors = [
                'active' => 'success',
                'inactive' => 'secondary',
                'pending' => 'warning'
            ];
            $color = $statusColors[$value] ?? 'secondary';
            return "<span class='badge bg-$color'>$value</span>";
        });
    }
    
    /**
     * Handle DataTables request for the users table
     */
    public function index()
    {
        if ($this->isAjax()) {
            // Process DataTables AJAX request
            $this->handleRequest();
        } else {
            // Render the view with DataTable configuration
            $tableConfig = [
                'ajaxUrl' => '/users/data',
                'columns' => [
                    ['data' => 'username', 'title' => 'Username'],
                    ['data' => 'email', 'title' => 'Email'],
                    ['data' => 'name', 'title' => 'Full Name'],
                    ['data' => 'role', 'title' => 'Role'],
                    ['data' => 'status', 'title' => 'Status'],
                    [
                        'data' => null,
                        'title' => 'Actions',
                        'orderable' => false,
                        'searchable' => false,
                        'defaultContent' => '<div class="btn-group">' .
                            '<button class="btn btn-sm btn-primary edit-btn"><i class="fas fa-edit"></i></button>' .
                            '<button class="btn btn-sm btn-danger delete-btn"><i class="fas fa-trash"></i></button>' .
                            '</div>'
                    ]
                ],
                'dom' => 'Bfrtip',
                'buttons' => ['copy', 'excel', 'pdf', 'csv'],
                'pageLength' => 25
            ];
            
            $this->renderDataTable('users/index', $tableConfig, [
                'pageTitle' => 'User Management'
            ]);
        }
    }
    
    /**
     * Create a new user
     */
    public function create()
    {
        if ($this->isPost()) {
            $userData = $this->request();
            // You might want to validate data here
            
            $this->processOperation('create', $userData);
        }
    }
    
    /**
     * Update an existing user
     */
    public function update($id)
    {
        if ($this->isPost()) {
            $userData = $this->request();
            // You might want to validate data here
            
            $this->processOperation('update', $userData, $id);
        }
    }
    
    /**
     * Delete a user
     */
    public function delete($id)
    {
        if ($this->isPost()) {
            $this->processOperation('delete', [], $id);
        }
    }
}