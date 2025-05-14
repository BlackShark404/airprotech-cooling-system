<?php

namespace App\Models;

use Exception;

class InventoryModel extends Model
{
    protected $table = 'INVENTORY';
    
    /**
     * Create a new inventory record
     * 
     * @param array $data Inventory data
     * @return int|bool ID of the created inventory record or false on failure
     */
    public function createInventory($data)
    {
        $data['INVE_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['INVE_UPDATED_AT'] = date('Y-m-d H:i:s');
        $data['LAST_UPDATED'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                VALUES ({$formatted['placeholders']})
                RETURNING INVE_ID";
        
        $id = $this->queryScalar($sql, $formatted['filteredData']);
        return $id ? (int)$id : false;
    }
    
    /**
     * Update an inventory record by ID
     * 
     * @param int $id Inventory ID
     * @param array $data Inventory data to update
     * @return bool True on success, false on failure
     */
    public function updateInventory($id, $data)
    {
        $data['INVE_UPDATED_AT'] = date('Y-m-d H:i:s');
        $data['LAST_UPDATED'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table}
                SET {$formatted['updateClause']}
                WHERE INVE_ID = :id";
        
        $params = array_merge($formatted['filteredData'], ['id' => $id]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Soft delete an inventory record by ID
     * 
     * @param int $id Inventory ID
     * @return bool True on success, false on failure
     */
    public function deleteInventory($id)
    {
        $sql = "UPDATE {$this->table}
                SET INVE_DELETED_AT = NOW()
                WHERE INVE_ID = :id";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Get all active inventory records
     * 
     * @return array List of active inventory records
     */
    public function getAllInventory()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE INVE_DELETED_AT IS NULL
                ORDER BY INVE_ID DESC";
        
        return $this->query($sql);
    }
    
    /**
     * Get an inventory record by ID
     * 
     * @param int $id Inventory ID
     * @return array|null Inventory data or null if not found
     */
    public function getInventoryById($id)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE INVE_ID = :id
                AND INVE_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get inventory records by product ID
     * 
     * @param int $productId Product ID
     * @return array List of inventory records for the product
     */
    public function getInventoryByProductId($productId)
    {
        $sql = "SELECT i.*, w.WHOUSE_NAME, w.WHOUSE_LOCATION
                FROM {$this->table} i
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
                WHERE i.PROD_ID = :productId
                AND i.INVE_DELETED_AT IS NULL
                AND w.WHOUSE_DELETED_AT IS NULL
                ORDER BY i.INVE_TYPE, w.WHOUSE_NAME";
        
        return $this->query($sql, ['productId' => $productId]);
    }
    
    /**
     * Get inventory records by warehouse ID
     * 
     * @param int $warehouseId Warehouse ID
     * @return array List of inventory records for the warehouse
     */
    public function getInventoryByWarehouseId($warehouseId)
    {
        $sql = "SELECT i.*, p.PROD_NAME, p.PROD_IMAGE
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
                WHERE i.WHOUSE_ID = :warehouseId
                AND i.INVE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, i.INVE_TYPE";
        
        return $this->query($sql, ['warehouseId' => $warehouseId]);
    }
    
    /**
     * Get inventory records by type
     * 
     * @param string $type Inventory type
     * @return array List of inventory records of the specified type
     */
    public function getInventoryByType($type)
    {
        $sql = "SELECT i.*, p.PROD_NAME, w.WHOUSE_NAME
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
                WHERE i.INVE_TYPE = :type
                AND i.INVE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                AND w.WHOUSE_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, w.WHOUSE_NAME";
        
        return $this->query($sql, ['type' => $type]);
    }
    
    /**
     * Update product quantity in inventory
     * 
     * @param int $productId Product ID
     * @param int $warehouseId Warehouse ID
     * @param string $type Inventory type
     * @param int $quantity Quantity change (positive to add, negative to reduce)
     * @return bool True on success, false on failure
     */
    public function updateProductQuantity($productId, $warehouseId, $type, $quantity)
    {
        // Begin transaction
        $this->beginTransaction();
        
        try {
            // Check if inventory record exists
            $sql = "SELECT INVE_ID, QUANTITY FROM {$this->table}
                    WHERE PROD_ID = :productId
                    AND WHOUSE_ID = :warehouseId
                    AND INVE_TYPE = :type
                    AND INVE_DELETED_AT IS NULL
                    FOR UPDATE";
            
            $inventory = $this->queryOne($sql, [
                'productId' => $productId,
                'warehouseId' => $warehouseId,
                'type' => $type
            ]);
            
            $now = date('Y-m-d H:i:s');
            
            if ($inventory) {
                // Update existing record
                $newQuantity = $inventory['QUANTITY'] + $quantity;
                
                // Ensure quantity doesn't go below zero
                if ($newQuantity < 0) {
                    $newQuantity = 0;
                }
                
                $updateSql = "UPDATE {$this->table}
                              SET QUANTITY = :quantity,
                                  LAST_UPDATED = :lastUpdated,
                                  INVE_UPDATED_AT = :updatedAt
                              WHERE INVE_ID = :id";
                
                $this->execute($updateSql, [
                    'quantity' => $newQuantity,
                    'lastUpdated' => $now,
                    'updatedAt' => $now,
                    'id' => $inventory['INVE_ID']
                ]);
            } else {
                // Create new record if quantity is positive
                if ($quantity > 0) {
                    $insertSql = "INSERT INTO {$this->table} (
                                    PROD_ID, WHOUSE_ID, INVE_TYPE, QUANTITY,
                                    LAST_UPDATED, INVE_CREATED_AT, INVE_UPDATED_AT
                                  ) VALUES (
                                    :productId, :warehouseId, :type, :quantity,
                                    :lastUpdated, :createdAt, :updatedAt
                                  )";
                    
                    $this->execute($insertSql, [
                        'productId' => $productId,
                        'warehouseId' => $warehouseId,
                        'type' => $type,
                        'quantity' => $quantity,
                        'lastUpdated' => $now,
                        'createdAt' => $now,
                        'updatedAt' => $now
                    ]);
                }
            }
            
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Get low stock inventory
     * 
     * @return array List of inventory records with stock below warehouse threshold
     */
    public function getLowStockInventory()
    {
        $sql = "SELECT i.*, p.PROD_NAME, p.PROD_IMAGE, w.WHOUSE_NAME, w.WHOUSE_RESTOCK_THRESHOLD
                FROM {$this->table} i
                JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
                JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
                WHERE i.QUANTITY < w.WHOUSE_RESTOCK_THRESHOLD
                AND i.INVE_TYPE = 'Regular'
                AND i.INVE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                AND w.WHOUSE_DELETED_AT IS NULL
                ORDER BY (i.QUANTITY * 1.0 / w.WHOUSE_RESTOCK_THRESHOLD) ASC";
        
        return $this->query($sql);
    }
    
    /**
     * Get total stock for a product across all warehouses
     * 
     * @param int $productId Product ID
     * @return int Total stock quantity
     */
    public function getTotalProductStock($productId)
    {
        $sql = "SELECT COALESCE(SUM(QUANTITY), 0) as total_stock
                FROM {$this->table}
                WHERE PROD_ID = :productId
                AND INVE_TYPE = 'Regular'
                AND INVE_DELETED_AT IS NULL";
        
        return (int)$this->queryScalar($sql, ['productId' => $productId], 0);
    }
    
    /**
     * Get inventory summary with product and warehouse details
     * 
     * @return array Summary of inventory with related details
     */
    public function getInventorySummary()
    {
        $sql = "SELECT 
                    p.PROD_ID, p.PROD_NAME, p.PROD_IMAGE,
                    SUM(CASE WHEN i.INVE_TYPE = 'Regular' THEN i.QUANTITY ELSE 0 END) as regular_stock,
                    SUM(CASE WHEN i.INVE_TYPE = 'Display' THEN i.QUANTITY ELSE 0 END) as display_stock,
                    SUM(CASE WHEN i.INVE_TYPE = 'Reserve' THEN i.QUANTITY ELSE 0 END) as reserve_stock,
                    SUM(CASE WHEN i.INVE_TYPE = 'Damaged' THEN i.QUANTITY ELSE 0 END) as damaged_stock,
                    SUM(CASE WHEN i.INVE_TYPE = 'Returned' THEN i.QUANTITY ELSE 0 END) as returned_stock,
                    SUM(CASE WHEN i.INVE_TYPE = 'Quarantine' THEN i.QUANTITY ELSE 0 END) as quarantine_stock,
                    SUM(i.QUANTITY) as total_stock
                FROM PRODUCT p
                LEFT JOIN {$this->table} i ON p.PROD_ID = i.PROD_ID AND i.INVE_DELETED_AT IS NULL
                WHERE p.PROD_DELETED_AT IS NULL
                GROUP BY p.PROD_ID, p.PROD_NAME, p.PROD_IMAGE
                ORDER BY p.PROD_NAME";
        return $this->query($sql);
    }
}
