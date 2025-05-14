<?php

namespace App\Models;

class InventoryModel extends Model
{
    // Table names
    protected $table = 'INVENTORY';
    protected $primaryKey = 'INVE_ID';
    
    // These fields can be filled
    protected $fillable = [
        'PROD_ID',
        'WHOUSE_ID',
        'INVE_TYPE',
        'QUANTITY',
        'LAST_UPDATED'
    ];
    
    // Date fields
    protected $timestamps = true;
    protected $createdAtColumn = 'last_updated';
    protected $updatedAtColumn = 'last_updated';
    
    // Get all inventory with product and warehouse info
    public function getAllInventory()
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                p.PROD_NAME,
                p.PROD_IMAGE,
                p.PROD_AVAILABILITY_STATUS,
                w.WHOUSE_NAME,
                pv.VAR_CAPACITY
            FROM {$this->table} i
            JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
            JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
            LEFT JOIN PRODUCT_VARIANT pv ON i.PROD_ID = pv.PROD_ID
            WHERE
                i.INVE_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL AND
                w.WHOUSE_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            ORDER BY p.PROD_NAME, pv.VAR_CAPACITY
        ";
        
        return $this->query($sql);
    }
    
    // Get inventory by product ID
    public function getInventoryByProduct($productId)
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                w.WHOUSE_NAME,
                pv.VAR_CAPACITY
            FROM {$this->table} i
            JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
            LEFT JOIN PRODUCT_VARIANT pv ON i.PROD_ID = pv.PROD_ID
            WHERE 
                i.PROD_ID = :prod_id AND
                i.INVE_DELETED_AT IS NULL AND
                w.WHOUSE_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            ORDER BY w.WHOUSE_NAME
        ";
        
        return $this->query($sql, ['prod_id' => $productId]);
    }
    
    // Get inventory by warehouse ID
    public function getInventoryByWarehouse($warehouseId)
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                p.PROD_NAME,
                p.PROD_IMAGE,
                pv.VAR_CAPACITY
            FROM {$this->table} i
            JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
            LEFT JOIN PRODUCT_VARIANT pv ON i.PROD_ID = pv.PROD_ID
            WHERE 
                i.WHOUSE_ID = :whouse_id AND
                i.INVE_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            ORDER BY p.PROD_NAME
        ";
        
        return $this->query($sql, ['whouse_id' => $warehouseId]);
    }
    
    // Get inventory by type (Regular, Display, etc.)
    public function getInventoryByType($type)
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                p.PROD_NAME,
                p.PROD_IMAGE,
                w.WHOUSE_NAME,
                pv.VAR_CAPACITY
            FROM {$this->table} i
            JOIN PRODUCT p ON i.PROD_ID = p.PROD_ID
            JOIN WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
            LEFT JOIN PRODUCT_VARIANT pv ON i.PROD_ID = pv.PROD_ID
            WHERE 
                i.INVE_TYPE = :type AND
                i.INVE_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL AND
                w.WHOUSE_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            ORDER BY p.PROD_NAME
        ";
        
        return $this->query($sql, ['type' => $type]);
    }
    
    // Get product list with variant count
    public function getProductsWithVariants()
    {
        $sql = "
            SELECT 
                p.PROD_ID,
                p.PROD_NAME,
                p.PROD_IMAGE,
                p.PROD_AVAILABILITY_STATUS,
                p.PROD_DESCRIPTION,
                COUNT(DISTINCT pv.VAR_ID) as variant_count,
                COALESCE(SUM(i.QUANTITY), 0) as total_stock
            FROM PRODUCT p
            LEFT JOIN PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID
            LEFT JOIN {$this->table} i ON p.PROD_ID = i.PROD_ID
            WHERE
                p.PROD_DELETED_AT IS NULL AND
                (pv.VAR_DELETED_AT IS NULL OR pv.VAR_DELETED_AT IS NULL) AND
                (i.INVE_DELETED_AT IS NULL OR i.INVE_DELETED_AT IS NULL)
            GROUP BY p.PROD_ID, p.PROD_NAME, p.PROD_IMAGE, p.PROD_AVAILABILITY_STATUS, p.PROD_DESCRIPTION
            ORDER BY p.PROD_NAME
        ";
        
        return $this->query($sql);
    }
    
    // Add inventory quantity
    public function addStock($productId, $warehouseId, $type, $quantity)
    {
        $this->beginTransaction();
        
        try {
            // Check if inventory record exists
            $inventory = $this->queryOne("
                SELECT * FROM {$this->table} 
                WHERE PROD_ID = :prod_id 
                AND WHOUSE_ID = :whouse_id 
                AND INVE_TYPE = :type
                AND INVE_DELETED_AT IS NULL",
                [
                    'prod_id' => $productId,
                    'whouse_id' => $warehouseId,
                    'type' => $type
                ]
            );
            
            $now = date('Y-m-d H:i:s');
            
            if ($inventory) {
                // Update existing inventory
                $newQuantity = $inventory['QUANTITY'] + $quantity;
                $this->execute(
                    "UPDATE {$this->table} 
                    SET QUANTITY = :quantity, LAST_UPDATED = :now, INVE_UPDATED_AT = :now
                    WHERE INVE_ID = :inve_id",
                    [
                        'quantity' => $newQuantity, 
                        'now' => $now,
                        'inve_id' => $inventory['INVE_ID']
                    ]
                );
            } else {
                // Create new inventory record
                $data = [
                    'PROD_ID' => $productId,
                    'WHOUSE_ID' => $warehouseId,
                    'INVE_TYPE' => $type,
                    'QUANTITY' => $quantity,
                    'LAST_UPDATED' => $now,
                    'INVE_CREATED_AT' => $now,
                    'INVE_UPDATED_AT' => $now
                ];
                
                $formatData = $this->formatInsertData($data);
                $sql = "INSERT INTO {$this->table} ({$formatData['columns']}) VALUES ({$formatData['placeholders']})";
                $this->execute($sql, $formatData['filteredData']);
            }
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    // Move stock between warehouses
    public function moveStock($productId, $fromWarehouseId, $toWarehouseId, $type, $quantity)
    {
        // Begin transaction for data integrity
        $this->beginTransaction();
        
        try {
            $now = date('Y-m-d H:i:s');
            
            // Check if source has enough stock
            $sourceStock = $this->queryOne("
                SELECT * FROM {$this->table}
                WHERE PROD_ID = :prod_id
                AND WHOUSE_ID = :whouse_id
                AND INVE_TYPE = :type
                AND INVE_DELETED_AT IS NULL",
                [
                    'prod_id' => $productId,
                    'whouse_id' => $fromWarehouseId,
                    'type' => $type
                ]
            );
            
            if (!$sourceStock || $sourceStock['QUANTITY'] < $quantity) {
                // Not enough stock
                $this->rollback();
                return false;
            }
            
            // Reduce from source warehouse
            $this->execute(
                "UPDATE {$this->table} 
                SET QUANTITY = :quantity, LAST_UPDATED = :now, INVE_UPDATED_AT = :now
                WHERE INVE_ID = :inve_id",
                [
                    'quantity' => $sourceStock['QUANTITY'] - $quantity, 
                    'now' => $now,
                    'inve_id' => $sourceStock['INVE_ID']
                ]
            );
            
            // Add to destination warehouse
            $destStock = $this->queryOne("
                SELECT * FROM {$this->table}
                WHERE PROD_ID = :prod_id
                AND WHOUSE_ID = :whouse_id
                AND INVE_TYPE = :type
                AND INVE_DELETED_AT IS NULL",
                [
                    'prod_id' => $productId,
                    'whouse_id' => $toWarehouseId,
                    'type' => $type
                ]
            );
            
            if ($destStock) {
                // Update existing destination inventory
                $this->execute(
                    "UPDATE {$this->table} 
                    SET QUANTITY = :quantity, LAST_UPDATED = :now, INVE_UPDATED_AT = :now
                    WHERE INVE_ID = :inve_id",
                    [
                        'quantity' => $destStock['QUANTITY'] + $quantity, 
                        'now' => $now,
                        'inve_id' => $destStock['INVE_ID']
                    ]
                );
            } else {
                // Create new destination inventory record
                $data = [
                    'PROD_ID' => $productId,
                    'WHOUSE_ID' => $toWarehouseId,
                    'INVE_TYPE' => $type,
                    'QUANTITY' => $quantity,
                    'LAST_UPDATED' => $now,
                    'INVE_CREATED_AT' => $now,
                    'INVE_UPDATED_AT' => $now
                ];
                
                $formatData = $this->formatInsertData($data);
                $sql = "INSERT INTO {$this->table} ({$formatData['columns']}) VALUES ({$formatData['placeholders']})";
                $this->execute($sql, $formatData['filteredData']);
            }
            
            // Commit transaction
            $this->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->rollback();
            return false;
        }
    }
    
    // Get low stock products
    public function getLowStockProducts()
    {
        return $this->query("
            SELECT 
                p.PROD_ID, 
                p.PROD_NAME, 
                p.PROD_IMAGE, 
                pv.VAR_CAPACITY, 
                w.WHOUSE_NAME,
                i.QUANTITY,
                w.WHOUSE_RESTOCK_THRESHOLD
            FROM 
                {$this->table} i
            JOIN 
                PRODUCT p ON i.PROD_ID = p.PROD_ID
            JOIN 
                WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
            JOIN 
                PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID
            WHERE 
                i.QUANTITY <= w.WHOUSE_RESTOCK_THRESHOLD AND
                i.INVE_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL AND
                w.WHOUSE_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            ORDER BY 
                i.QUANTITY ASC
        ");
    }
    
    // Get inventory summary for all products
    public function getInventorySummary()
    {
        return $this->query("
            SELECT 
                p.PROD_ID,
                p.PROD_NAME,
                p.PROD_IMAGE,
                p.PROD_AVAILABILITY_STATUS,
                pv.VAR_ID,
                pv.VAR_CAPACITY,
                SUM(i.QUANTITY) as total_quantity
            FROM 
                PRODUCT p
            JOIN 
                PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID
            LEFT JOIN 
                {$this->table} i ON p.PROD_ID = i.PROD_ID
            WHERE
                p.PROD_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL AND
                (i.INVE_DELETED_AT IS NULL OR i.INVE_DELETED_AT IS NULL)
            GROUP BY 
                p.PROD_ID, p.PROD_NAME, p.PROD_IMAGE, p.PROD_AVAILABILITY_STATUS, pv.VAR_ID, pv.VAR_CAPACITY
            ORDER BY 
                p.PROD_NAME, pv.VAR_CAPACITY
        ");
    }
    
    // Get inventory by product variant
    public function getInventoryByVariant($variantId)
    {
        return $this->query("
            SELECT 
                i.*,
                p.PROD_NAME,
                p.PROD_IMAGE,
                w.WHOUSE_NAME,
                pv.VAR_CAPACITY
            FROM 
                {$this->table} i
            JOIN 
                PRODUCT p ON i.PROD_ID = p.PROD_ID
            JOIN 
                WAREHOUSE w ON i.WHOUSE_ID = w.WHOUSE_ID
            JOIN 
                PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID
            WHERE 
                pv.VAR_ID = :variant_id AND
                i.INVE_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL AND
                w.WHOUSE_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            ORDER BY 
                w.WHOUSE_NAME
        ", ['variant_id' => $variantId]);
    }
    
    // Get warehouse list
    public function getWarehouses()
    {
        return $this->query("
            SELECT * FROM WAREHOUSE 
            WHERE WHOUSE_DELETED_AT IS NULL 
            ORDER BY WHOUSE_NAME
        ");
    }
}