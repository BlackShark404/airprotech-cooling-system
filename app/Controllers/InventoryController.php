<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;

class InventoryController extends BaseController
{
    private $inventoryModel;
    private $productModel;
    private $warehouseModel;

    public function __construct()
    {
        parent::__construct();
        $this->inventoryModel = new InventoryModel();
        $this->productModel = new ProductModel();
        $this->warehouseModel = new WarehouseModel();
    }

    public function renderInventoryManagement()
    {
        $this->render('admin/inventory-management');
    }

    public function getAllInventory()
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $inventory = $this->inventoryModel->getAllInventory();
        $this->jsonSuccess($inventory);
    }

    public function getProductInventory($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        // Check if product exists
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->jsonError('Product not found', 404);
            return;
        }

        $inventory = $this->inventoryModel->getProductInventory($id);
        
        // Add product info
        $data = [
            'product' => $product,
            'inventory' => $inventory
        ];
        
        $this->jsonSuccess($data);
    }

    public function getWarehouseInventory($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        // Check if warehouse exists
        $warehouse = $this->warehouseModel->getWarehouseById($id);
        if (!$warehouse) {
            $this->jsonError('Warehouse not found', 404);
            return;
        }

        $inventory = $this->inventoryModel->getWarehouseInventory($id);
        
        // Add warehouse info
        $data = [
            'warehouse' => $warehouse,
            'inventory' => $inventory
        ];
        
        $this->jsonSuccess($data);
    }

    public function getLowStockInventory()
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $lowStock = $this->inventoryModel->getLowStockInventory();
        $this->jsonSuccess($lowStock);
    }

    public function getInventorySummary()
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $summary = $this->inventoryModel->getInventorySummary();
        $this->jsonSuccess($summary);
    }

    public function addStock()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        $data = $this->getJsonInput();
        
        // Validate required fields
        if (!isset($data['product_id']) || !isset($data['warehouse_id']) || !isset($data['quantity'])) {
            $this->jsonError('Missing required fields: product_id, warehouse_id, quantity', 400);
            return;
        }
        
        // Validate quantity is positive
        if ($data['quantity'] <= 0) {
            $this->jsonError('Quantity must be greater than zero', 400);
            return;
        }
        
        // Check if product exists
        $product = $this->productModel->getProductById($data['product_id']);
        if (!$product) {
            $this->jsonError('Product not found', 404);
            return;
        }
        
        // Check if warehouse exists
        $warehouse = $this->warehouseModel->getWarehouseById($data['warehouse_id']);
        if (!$warehouse) {
            $this->jsonError('Warehouse not found', 404);
            return;
        }
        
        // Add stock
        $inventoryType = $data['inventory_type'] ?? 'Regular';
        $result = $this->inventoryModel->addStock(
            $data['product_id'], 
            $data['warehouse_id'], 
            $data['quantity'],
            $inventoryType
        );
        
        if ($result) {
            $this->jsonSuccess([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'added_quantity' => $data['quantity'],
            ], 'Stock added successfully');
        } else {
            $this->jsonError('Failed to add stock', 500);
        }
    }

    public function moveStock()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        $data = $this->getJsonInput();
        
        // Debug log the received data
        error_log("[DEBUG] moveStock data received: " . print_r($data, true));
        
        // Validate required fields
        if (!isset($data['source_inventory_id']) || !isset($data['target_warehouse_id']) || !isset($data['quantity'])) {
            $this->jsonError('Missing required fields: source_inventory_id, target_warehouse_id, quantity', 400);
            return;
        }
        
        // Convert values to proper types to avoid string comparison issues
        $sourceInventoryId = intval($data['source_inventory_id']);
        $targetWarehouseId = intval($data['target_warehouse_id']);
        $quantity = intval($data['quantity']);
        
        // Validate quantity is positive
        if ($quantity <= 0) {
            $this->jsonError('Quantity must be greater than zero', 400);
            return;
        }
        
        // Check if source inventory exists
        $sourceInventory = $this->inventoryModel->getInventoryById($sourceInventoryId);
        error_log("[DEBUG] Source inventory: " . print_r($sourceInventory, true));
        
        if (!$sourceInventory) {
            $this->jsonError('Source inventory not found', 404);
            return;
        }
        
        // Extract quantity, handling both lowercase and uppercase keys
        $availableQuantity = 0;
        if (isset($sourceInventory['QUANTITY'])) {
            $availableQuantity = intval($sourceInventory['QUANTITY']);
        } else if (isset($sourceInventory['quantity'])) {
            $availableQuantity = intval($sourceInventory['quantity']);
        }
        
        // Debug the source inventory quantity check
        error_log("[DEBUG] Source quantity: " . $availableQuantity);
        error_log("[DEBUG] Quantity to move: " . $quantity);
        
        // Check if source has enough quantity
        if ($availableQuantity < $quantity) {
            error_log("[DEBUG] Not enough stock available to move. Available: " . $availableQuantity . ", Requested: " . $quantity);
            $this->jsonError('Not enough stock available to move', 400);
            return;
        }
        
        // Check if target warehouse exists
        $targetWarehouse = $this->warehouseModel->getWarehouseById($targetWarehouseId);
        if (!$targetWarehouse) {
            $this->jsonError('Target warehouse not found', 404);
            return;
        }
        
        // Move stock
        $result = $this->inventoryModel->moveStock(
            $sourceInventoryId,
            $targetWarehouseId,
            $quantity
        );
        
        if ($result) {
            $this->jsonSuccess([
                'source_inventory_id' => $sourceInventoryId,
                'target_warehouse_id' => $targetWarehouseId,
                'moved_quantity' => $quantity,
            ], 'Stock moved successfully');
        } else {
            $this->jsonError('Failed to move stock', 500);
        }
    }

    public function getInventoryStats()
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        // Get summary data
        $summary = $this->inventoryModel->getInventorySummary();
        
        // Get low stock items
        $lowStock = $this->inventoryModel->getLowStockInventory();
        
        // Get warehouse utilization
        $warehouses = $this->warehouseModel->getWarehousesWithInventory();
        
        // Prepare warehouse utilization data for charts
        $warehouseData = [];
        foreach ($warehouses as $warehouse) {
            if (isset($warehouse['WHOUSE_STORAGE_CAPACITY']) && $warehouse['WHOUSE_STORAGE_CAPACITY'] > 0) {
                $utilization = isset($warehouse['TOTAL_INVENTORY']) ? 
                    round(($warehouse['TOTAL_INVENTORY'] * 100.0) / $warehouse['WHOUSE_STORAGE_CAPACITY'], 2) : 
                    0;
                
                $warehouseData[] = [
                    'name' => $warehouse['WHOUSE_NAME'],
                    'total_capacity' => $warehouse['WHOUSE_STORAGE_CAPACITY'],
                    'used_capacity' => $warehouse['TOTAL_INVENTORY'] ?? 0,
                    'utilization_percentage' => $utilization,
                    'available_space' => $warehouse['WHOUSE_STORAGE_CAPACITY'] - ($warehouse['TOTAL_INVENTORY'] ?? 0)
                ];
            }
        }
        
        // Combine all data
        $stats = [
            'summary' => $summary,
            'low_stock_count' => count($lowStock),
            'warehouse_utilization' => $warehouseData
        ];
        
        $this->jsonSuccess($stats);
    }

    public function getInventoryById($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        // First, add debug logging to trace the issue
        error_log("[DEBUG] Getting inventory item with ID: " . $id);
        
        // Update our query to explicitly include the required product fields
        $sql = "SELECT 
                i.*,
                p.PROD_NAME, 
                p.PROD_DESCRIPTION,
                p.PROD_IMAGE, 
                p.PROD_AVAILABILITY_STATUS,
                w.WHOUSE_NAME 
            FROM INVENTORY i
            LEFT JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
            LEFT JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
            WHERE i.INVE_ID = :inventory_id 
            AND i.INVE_DELETED_AT IS NULL";
            
        $inventoryItem = $this->inventoryModel->queryOne($sql, [':inventory_id' => $id]);
        
        if (!$inventoryItem) {
            $this->jsonError('Inventory item not found', 404);
            return;
        }
        
        // Debug the returned data
        error_log("[DEBUG] Raw inventory data: " . print_r($inventoryItem, true));
        
        // Convert uppercase database keys to lowercase for frontend consistency
        $normalized = [];
        foreach ($inventoryItem as $key => $value) {
            // Convert PROD_NAME to prod_name, etc.
            $normalized[strtolower($key)] = $value;
        }
        
        $this->jsonSuccess($normalized);
    }
} 