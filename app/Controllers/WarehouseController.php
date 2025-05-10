<?php

namespace App\Controllers;

class WarehouseController extends BaseController
{
    protected $warehouseModel;
    protected $inventoryModel;
    
    public function __construct() 
    {
        parent::__construct();
        $this->warehouseModel = $this->loadModel('WarehouseModel');
        $this->inventoryModel = $this->loadModel('InventoryModel');
        
        // Enable debug logging
        error_log("WarehouseController initialized");
    }
    
    /**
     * Get all warehouses
     */
    public function getAllWarehouses()
    {
        error_log("getAllWarehouses called");
        
        try {
            $warehouses = $this->warehouseModel->getAllWarehouses();
            error_log("Warehouses retrieved: " . json_encode($warehouses));
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            error_log("Error fetching warehouses: " . $e->getMessage());
            $this->jsonError('Error fetching warehouses: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get warehouse by ID
     */
    public function getWarehouse($warehouseId)
    {
        error_log("getWarehouse called for ID: " . $warehouseId);
        
        try {
            $warehouse = $this->warehouseModel->findById($warehouseId);
            
            if (!$warehouse) {
                error_log("Warehouse not found for ID: " . $warehouseId);
                $this->jsonError('Warehouse not found', 404);
                return;
            }
            
            error_log("Warehouse found: " . json_encode($warehouse));
            $this->jsonSuccess($warehouse);
        } catch (\Exception $e) {
            error_log("Error fetching warehouse: " . $e->getMessage());
            $this->jsonError('Error fetching warehouse: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Create a new warehouse
     */
    public function createWarehouse()
    {
        error_log("createWarehouse called");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
        error_log("POST data: " . json_encode($_POST));
        error_log("Raw input: " . file_get_contents('php://input'));
        
        // Handle both AJAX and form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if this is a form submission or JSON
            if (isset($_POST['whouse_name']) && isset($_POST['whouse_location'])) {
                // Form submission
                $data = [
                    'whouse_name' => $_POST['whouse_name'],
                    'whouse_location' => $_POST['whouse_location'],
                    'whouse_storage_capacity' => $_POST['whouse_storage_capacity'] ?? null,
                    'whouse_restock_threshold' => $_POST['whouse_restock_threshold'] ?? null
                ];
            } else {
                // JSON submission
                $data = $this->getJsonInput();
            }
            
            error_log("Processed data for warehouse creation: " . json_encode($data));
            
            // Validate required fields
            if (empty($data['whouse_name']) || empty($data['whouse_location'])) {
                error_log("Missing required fields for warehouse creation");
                $this->jsonError('Warehouse name and location are required', 400);
                return;
            }
            
            try {
                // Create warehouse
                $result = $this->warehouseModel->createWarehouse([
                    'whouse_name' => $data['whouse_name'],
                    'whouse_location' => $data['whouse_location'],
                    'whouse_storage_capacity' => $data['whouse_storage_capacity'] ?? null,
                    'whouse_restock_threshold' => $data['whouse_restock_threshold'] ?? null
                ]);
                
                if ($result) {
                    // Get the newly created warehouse with its ID
                    $warehouseId = $this->db->lastInsertId();
                    $warehouse = $this->warehouseModel->findById($warehouseId);
                    error_log("Warehouse created successfully with ID: " . $warehouseId);
                    
                    // Handle redirect for form submission
                    if ($this->isAjax()) {
                        $this->jsonSuccess($warehouse, 'Warehouse created successfully');
                    } else {
                        // Redirect to inventory page
                        $this->redirect('/admin/inventory?success=warehouse_created');
                    }
                } else {
                    error_log("Failed to create warehouse");
                    $this->jsonError('Failed to create warehouse', 500);
                }
            } catch (\Exception $e) {
                error_log("Error creating warehouse: " . $e->getMessage());
                $this->jsonError('Error creating warehouse: ' . $e->getMessage(), 500);
            }
        } else {
            error_log("Invalid request method for warehouse creation");
            $this->jsonError('Invalid request method', 400);
        }
    }
    
    /**
     * Update an existing warehouse
     */
    public function updateWarehouse($warehouseId)
    {
        error_log("updateWarehouse called for ID: " . $warehouseId);
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        
        // Handle both AJAX and form submissions for update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Check if this is a form submission or JSON
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['whouse_name'])) {
                // Form submission
                $data = [
                    'whouse_name' => $_POST['whouse_name'],
                    'whouse_location' => $_POST['whouse_location'],
                    'whouse_storage_capacity' => $_POST['whouse_storage_capacity'] ?? null,
                    'whouse_restock_threshold' => $_POST['whouse_restock_threshold'] ?? null
                ];
            } else {
                // JSON submission
                $data = $this->getJsonInput();
            }
            
            error_log("Processed data for warehouse update: " . json_encode($data));
            
            // Check if warehouse exists
            $warehouse = $this->warehouseModel->findById($warehouseId);
            if (!$warehouse) {
                error_log("Warehouse not found for update: " . $warehouseId);
                $this->jsonError('Warehouse not found', 404);
                return;
            }
            
            try {
                // Update warehouse
                $result = $this->warehouseModel->updateWarehouse($warehouseId, [
                    'whouse_name' => $data['whouse_name'] ?? $warehouse['whouse_name'],
                    'whouse_location' => $data['whouse_location'] ?? $warehouse['whouse_location'],
                    'whouse_storage_capacity' => $data['whouse_storage_capacity'] ?? $warehouse['whouse_storage_capacity'],
                    'whouse_restock_threshold' => $data['whouse_restock_threshold'] ?? $warehouse['whouse_restock_threshold']
                ]);
                
                if ($result) {
                    // Get the updated warehouse
                    $updatedWarehouse = $this->warehouseModel->findById($warehouseId);
                    error_log("Warehouse updated successfully: " . $warehouseId);
                    
                    // Handle redirect for form submission
                    if ($this->isAjax()) {
                        $this->jsonSuccess($updatedWarehouse, 'Warehouse updated successfully');
                    } else {
                        // Redirect to inventory page
                        $this->redirect('/admin/inventory?success=warehouse_updated');
                    }
                } else {
                    error_log("Failed to update warehouse: " . $warehouseId);
                    $this->jsonError('Failed to update warehouse', 500);
                }
            } catch (\Exception $e) {
                error_log("Error updating warehouse: " . $e->getMessage());
                $this->jsonError('Error updating warehouse: ' . $e->getMessage(), 500);
            }
        } else {
            error_log("Invalid request method for warehouse update");
            $this->jsonError('Invalid request method', 400);
        }
    }
    
    /**
     * Delete a warehouse
     */
    public function deleteWarehouse($warehouseId)
    {
        error_log("deleteWarehouse called for ID: " . $warehouseId);
        
        // Check if warehouse exists
        $warehouse = $this->warehouseModel->findById($warehouseId);
        if (!$warehouse) {
            error_log("Warehouse not found for deletion: " . $warehouseId);
            $this->jsonError('Warehouse not found', 404);
            return;
        }
        
        try {
            // Check if warehouse has inventory items
            $inventory = $this->inventoryModel->getInventoryByWarehouse($warehouseId);
            if (!empty($inventory)) {
                error_log("Cannot delete warehouse with inventory: " . $warehouseId);
                $this->jsonError('Cannot delete warehouse with existing inventory. Please move or remove inventory first.', 400);
                return;
            }
            
            // Delete warehouse
            $result = $this->warehouseModel->deleteWarehouse($warehouseId);
            
            if ($result) {
                error_log("Warehouse deleted successfully: " . $warehouseId);
                $this->jsonSuccess([], 'Warehouse deleted successfully');
            } else {
                error_log("Failed to delete warehouse: " . $warehouseId);
                $this->jsonError('Failed to delete warehouse', 500);
            }
        } catch (\Exception $e) {
            error_log("Error deleting warehouse: " . $e->getMessage());
            $this->jsonError('Error deleting warehouse: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get warehouse inventory
     */
    public function getWarehouseInventory($warehouseId)
    {
        error_log("getWarehouseInventory called for ID: " . $warehouseId);
        
        try {
            $inventory = $this->inventoryModel->getInventoryByWarehouse($warehouseId);
            error_log("Warehouse inventory retrieved: " . count($inventory) . " items");
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            error_log("Error fetching warehouse inventory: " . $e->getMessage());
            $this->jsonError('Error fetching warehouse inventory: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get warehouses with inventory summary
     */
    public function getWarehousesWithSummary()
    {
        error_log("getWarehousesWithSummary called");
        
        try {
            $warehouses = $this->warehouseModel->getWarehousesWithInventorySummary();
            error_log("Warehouses with summary retrieved: " . count($warehouses) . " items");
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            error_log("Error fetching warehouses summary: " . $e->getMessage());
            $this->jsonError('Error fetching warehouses summary: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get warehouses with low stock items
     */
    public function getWarehousesWithLowStock()
    {
        error_log("getWarehousesWithLowStock called");
        
        try {
            $warehouses = $this->warehouseModel->getWarehousesWithLowStock();
            error_log("Warehouses with low stock retrieved: " . count($warehouses) . " items");
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            error_log("Error fetching warehouses with low stock: " . $e->getMessage());
            $this->jsonError('Error fetching warehouses with low stock: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Render the warehouse management page (form)
     */
    public function renderWarehouseManagement()
    {
        try {
            $warehouses = $this->warehouseModel->getAllWarehouses();
            
            // Get warehouse by ID if provided
            $warehouseId = $_GET['whouse_id'] ?? null;
            $warehouse = null;
            
            if ($warehouseId) {
                $warehouse = $this->warehouseModel->findById($warehouseId);
            }
            
            $this->render('admin/warehouse/manage', [
                'warehouses' => $warehouses,
                'warehouse' => $warehouse
            ]);
        } catch (\Exception $e) {
            $this->renderError('Error loading warehouse management: ' . $e->getMessage(), 500);
        }
    }
}