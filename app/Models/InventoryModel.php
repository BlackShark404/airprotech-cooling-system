<?php

namespace App\Models;

class InventoryModel extends Model
{
    protected $table = 'INVENTORY';

    public function getAllInventory()
    {
        $sql = "SELECT 
                    i.*,
                    p.PROD_NAME,
                    p.PROD_IMAGE,
                    w.WHOUSE_NAME
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID AND p.PROD_DELETED_AT IS NULL
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID AND w.WHOUSE_DELETED_AT IS NULL
                WHERE i.INVE_DELETED_AT IS NULL
                ORDER BY i.INVE_UPDATED_AT DESC";
        
        return $this->query($sql);
    }

    public function getInventoryById($inventoryId)
    {
        $sql = "SELECT 
                    i.*,
                    p.PROD_NAME,
                    p.PROD_IMAGE,
                    w.WHOUSE_NAME
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
                WHERE i.INVE_ID = :inventory_id AND i.INVE_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, [':inventory_id' => $inventoryId]);
    }

    public function getProductInventory($productId)
    {
        $sql = "SELECT 
                    i.*,
                    w.WHOUSE_NAME,
                    w.WHOUSE_LOCATION
                FROM {$this->table} i
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
                WHERE i.PROD_ID = :product_id AND i.INVE_DELETED_AT IS NULL
                ORDER BY w.WHOUSE_NAME";
        
        return $this->query($sql, [':product_id' => $productId]);
    }

    public function getWarehouseInventory($warehouseId)
    {
        $sql = "SELECT 
                    i.*,
                    p.PROD_NAME,
                    p.PROD_IMAGE,
                    p.PROD_AVAILABILITY_STATUS
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
                WHERE i.WHOUSE_ID = :warehouse_id AND i.INVE_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME";
        
        return $this->query($sql, [':warehouse_id' => $warehouseId]);
    }

    public function getLowStockInventory()
    {
        $sql = "SELECT 
                    i.*,
                    p.PROD_NAME,
                    p.PROD_IMAGE,
                    w.WHOUSE_NAME,
                    w.WHOUSE_RESTOCK_THRESHOLD
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID AND p.PROD_DELETED_AT IS NULL
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID AND w.WHOUSE_DELETED_AT IS NULL
                WHERE i.INVE_DELETED_AT IS NULL
                AND i.QUANTITY <= w.WHOUSE_RESTOCK_THRESHOLD
                AND w.WHOUSE_RESTOCK_THRESHOLD > 0
                ORDER BY i.QUANTITY ASC";
        
        return $this->query($sql);
    }

    public function createInventory($data)
    {
        $sql = "INSERT INTO {$this->table} (PROD_ID, WHOUSE_ID, INVE_TYPE, QUANTITY)
                VALUES (:product_id, :warehouse_id, :inventory_type, :quantity)";
        
        $params = [
            ':product_id' => $data['PROD_ID'],
            ':warehouse_id' => $data['WHOUSE_ID'],
            ':inventory_type' => $data['INVE_TYPE'],
            ':quantity' => $data['QUANTITY']
        ];
        
        $this->execute($sql, $params);
        return $this->lastInsertId('inventory_inve_id_seq');
    }

    public function updateInventoryQuantity($inventoryId, $newQuantity)
    {
        $sql = "UPDATE {$this->table} SET 
                QUANTITY = :quantity,
                INVE_UPDATED_AT = CURRENT_TIMESTAMP
                WHERE INVE_ID = :inventory_id AND INVE_DELETED_AT IS NULL";
        
        return $this->execute($sql, [
            ':quantity' => $newQuantity,
            ':inventory_id' => $inventoryId
        ]);
    }

    public function updateInventory($inventoryId, $data)
    {
        $setClauses = [];
        $params = [':inventory_id' => $inventoryId];

        if (isset($data['WHOUSE_ID'])) {
            $setClauses[] = "WHOUSE_ID = :warehouse_id";
            $params[':warehouse_id'] = $data['WHOUSE_ID'];
        }

        if (isset($data['INVE_TYPE'])) {
            $setClauses[] = "INVE_TYPE = :inventory_type";
            $params[':inventory_type'] = $data['INVE_TYPE'];
        }

        if (isset($data['QUANTITY'])) {
            $setClauses[] = "QUANTITY = :quantity";
            $params[':quantity'] = $data['QUANTITY'];
        }

        // Always update the timestamp
        $setClauses[] = "INVE_UPDATED_AT = CURRENT_TIMESTAMP";
        
        if (empty($setClauses)) {
            return false; // No fields to update
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . 
               " WHERE INVE_ID = :inventory_id AND INVE_DELETED_AT IS NULL";
        
        return $this->execute($sql, $params);
    }

    public function deleteInventory($inventoryId)
    {
        $sql = "UPDATE {$this->table} SET INVE_DELETED_AT = CURRENT_TIMESTAMP 
                WHERE INVE_ID = :inventory_id";
        return $this->execute($sql, [':inventory_id' => $inventoryId]);
    }

    public function getInventorySummary()
    {
        error_log("[InventoryModel] getInventorySummary called.");
        // Get total active warehouses from WarehouseModel
        $warehouseModel = new WarehouseModel(); // Instantiate WarehouseModel
        $totalWarehouses = $warehouseModel->countActiveWarehouses();
        error_log("[InventoryModel] Total warehouses from WarehouseModel: " . $totalWarehouses);

        // Get other inventory-specific stats
        $sql = "SELECT 
                    COUNT(DISTINCT i.PROD_ID) AS TOTAL_PRODUCTS,
                    SUM(i.QUANTITY) AS TOTAL_INVENTORY,
                    COUNT(DISTINCT CASE WHEN i.QUANTITY <= w.WHOUSE_RESTOCK_THRESHOLD AND w.WHOUSE_RESTOCK_THRESHOLD > 0 THEN i.INVE_ID ELSE NULL END) AS LOW_STOCK_ITEMS
                FROM {$this->table} i
                LEFT JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID AND p.PROD_DELETED_AT IS NULL
                LEFT JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID AND w.WHOUSE_DELETED_AT IS NULL
                WHERE i.INVE_DELETED_AT IS NULL";
        
        error_log("[InventoryModel] SQL for inventory stats: " . $sql);
        $inventoryStats = $this->queryOne($sql);
        error_log("[InventoryModel] Raw inventory stats from queryOne: " . print_r($inventoryStats, true));

        // Combine the results, ensuring all are integers and default to 0 if null/not set
        $summaryData = [
            'TOTAL_PRODUCTS' => ($inventoryStats && isset($inventoryStats['TOTAL_PRODUCTS'])) ? (int)$inventoryStats['TOTAL_PRODUCTS'] : 0,
            'TOTAL_WAREHOUSES' => (int)$totalWarehouses, // Already an int from WarehouseModel
            'TOTAL_INVENTORY' => ($inventoryStats && isset($inventoryStats['TOTAL_INVENTORY'])) ? (int)$inventoryStats['TOTAL_INVENTORY'] : 0,
            'LOW_STOCK_ITEMS' => ($inventoryStats && isset($inventoryStats['LOW_STOCK_ITEMS'])) ? (int)$inventoryStats['LOW_STOCK_ITEMS'] : 0
        ];
        error_log("[InventoryModel] Final summary data: " . print_r($summaryData, true));
        return $summaryData;
    }

    public function getInventoryByProductAndWarehouse($productId, $warehouseId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE PROD_ID = :product_id 
                AND WHOUSE_ID = :warehouse_id 
                AND INVE_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, [
            ':product_id' => $productId,
            ':warehouse_id' => $warehouseId
        ]);
    }

    public function addStock($productId, $warehouseId, $quantity, $inventoryType = 'Regular')
    {
        // First check if there's an existing inventory record
        $existingInventory = $this->getInventoryByProductAndWarehouse($productId, $warehouseId);
        
        if ($existingInventory) {
            // Update existing inventory
            $newQuantity = $existingInventory['QUANTITY'] + $quantity;
            return $this->updateInventoryQuantity($existingInventory['INVE_ID'], $newQuantity);
        } else {
            // Create new inventory record
            return $this->createInventory([
                'PROD_ID' => $productId,
                'WHOUSE_ID' => $warehouseId,
                'INVE_TYPE' => $inventoryType,
                'QUANTITY' => $quantity
            ]);
        }
    }

    public function moveStock($sourceInventoryId, $targetWarehouseId, $quantity)
    {
        // Start a transaction
        $this->beginTransaction();
        
        try {
            // Get source inventory record
            $sourceInventory = $this->getInventoryById($sourceInventoryId);
            if (!$sourceInventory || $sourceInventory['QUANTITY'] < $quantity) {
                $this->rollback();
                return false; // Not enough stock to move
            }
            
            // Update source inventory quantity
            $this->updateInventoryQuantity($sourceInventoryId, $sourceInventory['QUANTITY'] - $quantity);
            
            // Check if there's already inventory for this product in the target warehouse
            $targetInventory = $this->getInventoryByProductAndWarehouse(
                $sourceInventory['PROD_ID'], 
                $targetWarehouseId
            );
            
            if ($targetInventory) {
                // Update existing inventory at target
                $this->updateInventoryQuantity(
                    $targetInventory['INVE_ID'], 
                    $targetInventory['QUANTITY'] + $quantity
                );
            } else {
                // Create new inventory at target
                $this->createInventory([
                    'PROD_ID' => $sourceInventory['PROD_ID'],
                    'WHOUSE_ID' => $targetWarehouseId,
                    'INVE_TYPE' => $sourceInventory['INVE_TYPE'],
                    'QUANTITY' => $quantity
                ]);
            }
            
            // Commit the transaction
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error moving stock: " . $e->getMessage());
            return false;
        }
    }
} 