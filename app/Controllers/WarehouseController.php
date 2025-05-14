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
        
        // Handle both AJAX and form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the raw input
            $input = file_get_contents('php://input');
            error_log("Raw input: " . $input);
            
            try {
                // Check if this is a form submission or JSON
                if (isset($_POST['whouse_name']) && isset($_POST['whouse_location'])) {
                    // Form submission
                    $data = [
                        'WHOUSE_NAME' => $_POST['whouse_name'],
                        'WHOUSE_LOCATION' => $_POST['whouse_location'],
                        'WHOUSE_STORAGE_CAPACITY' => $_POST['whouse_storage_capacity'] ?? null,
                        'WHOUSE_RESTOCK_THRESHOLD' => $_POST['whouse_restock_threshold'] ?? null
                    ];
                } else {
                    // JSON submission - parse and validate
                    $data = json_decode($input, true);
                    
                    // Check if JSON data is valid
                    if (!$data || !is_array($data)) {
                        error_log("Invalid JSON data: " . $input);
                        $this->jsonError('Invalid JSON data', 400);
                        return;
                    }
                }
                
                error_log("Processed data for warehouse creation: " . json_encode($data));
                
                // Validate required fields - check both uppercase and lowercase fields
                $warehouseName = $data['WHOUSE_NAME'] ?? $data['whouse_name'] ?? null;
                $warehouseLocation = $data['WHOUSE_LOCATION'] ?? $data['whouse_location'] ?? null;
                
                if (empty($warehouseName) || empty($warehouseLocation)) {
                    error_log("Missing required fields for warehouse creation. Name: " . ($warehouseName ?? 'null') . ", Location: " . ($warehouseLocation ?? 'null'));
                    $this->jsonError('Warehouse name and location are required', 400);
                    return;
                }
                
                // Create warehouse - use the values we extracted
                $result = $this->warehouseModel->createWarehouse([
                    'whouse_name' => $warehouseName,
                    'whouse_location' => $warehouseLocation,
                    'whouse_storage_capacity' => $data['WHOUSE_STORAGE_CAPACITY'] ?? $data['whouse_storage_capacity'] ?? null,
                    'whouse_restock_threshold' => $data['WHOUSE_RESTOCK_THRESHOLD'] ?? $data['whouse_restock_threshold'] ?? null
                ]);
                
                if ($result) {
                    error_log("Warehouse created successfully with ID: " . $result);
                    $this->jsonSuccess(['id' => $result, 'message' => 'Warehouse created successfully']);
                } else {
                    error_log("Failed to create warehouse");
                    $this->jsonError('Failed to create warehouse', 500);
                }
            } catch (\Exception $e) {
                error_log("Exception in createWarehouse: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
                $this->jsonError('Error creating warehouse: ' . $e->getMessage(), 500);
            }
        } else {
            error_log("Invalid request method for warehouse creation: " . $_SERVER['REQUEST_METHOD']);
            $this->jsonError('Invalid request method', 405);
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
            // Get the raw input
            $input = file_get_contents('php://input');
            error_log("Raw input: " . $input);
            
            try {
                // Check if this is a form submission or JSON
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['whouse_name'])) {
                    // Form submission
                    $data = [
                        'WHOUSE_NAME' => $_POST['whouse_name'],
                        'WHOUSE_LOCATION' => $_POST['whouse_location'],
                        'WHOUSE_STORAGE_CAPACITY' => $_POST['whouse_storage_capacity'] ?? null,
                        'WHOUSE_RESTOCK_THRESHOLD' => $_POST['whouse_restock_threshold'] ?? null
                    ];
                } else {
                    // JSON submission - parse and validate
                    $data = json_decode($input, true);
                    
                    // Check if JSON data is valid
                    if (!$data || !is_array($data)) {
                        error_log("Invalid JSON data: " . $input);
                        $this->jsonError('Invalid JSON data', 400);
                        return;
                    }
                }
                
                error_log("Processed data for warehouse update: " . json_encode($data));
                
                // Check if warehouse exists
                $warehouse = $this->warehouseModel->findById($warehouseId);
                if (!$warehouse) {
                    error_log("Warehouse not found for update: " . $warehouseId);
                    $this->jsonError('Warehouse not found', 404);
                    return;
                }
                
                // Extract fields with flexibility for case
                $warehouseName = $data['WHOUSE_NAME'] ?? $data['whouse_name'] ?? $warehouse['whouse_name'];
                $warehouseLocation = $data['WHOUSE_LOCATION'] ?? $data['whouse_location'] ?? $warehouse['whouse_location'];
                $storageCapacity = $data['WHOUSE_STORAGE_CAPACITY'] ?? $data['whouse_storage_capacity'] ?? $warehouse['whouse_storage_capacity'];
                $restockThreshold = $data['WHOUSE_RESTOCK_THRESHOLD'] ?? $data['whouse_restock_threshold'] ?? $warehouse['whouse_restock_threshold'];
                
                // Validate required fields
                if (empty($warehouseName) || empty($warehouseLocation)) {
                    error_log("Missing required fields for warehouse update. Name: " . ($warehouseName ?? 'null') . ", Location: " . ($warehouseLocation ?? 'null'));
                    $this->jsonError('Warehouse name and location are required', 400);
                    return;
                }
                
                // Update warehouse
                $result = $this->warehouseModel->updateWarehouse($warehouseId, [
                    'whouse_name' => $warehouseName,
                    'whouse_location' => $warehouseLocation,
                    'whouse_storage_capacity' => $storageCapacity,
                    'whouse_restock_threshold' => $restockThreshold
                ]);
                
                if ($result) {
                    error_log("Warehouse updated successfully: " . $warehouseId);
                    $this->jsonSuccess(['id' => $warehouseId, 'message' => 'Warehouse updated successfully']);
                } else {
                    error_log("Failed to update warehouse: " . $warehouseId);
                    $this->jsonError('Failed to update warehouse', 500);
                }
            } catch (\Exception $e) {
                error_log("Exception in updateWarehouse: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
                $this->jsonError('Error updating warehouse: ' . $e->getMessage(), 500);
            }
        } else {
            error_log("Invalid request method for warehouse update: " . $_SERVER['REQUEST_METHOD']);
            $this->jsonError('Invalid request method', 405);
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