<?php

namespace App\Models;

class WarehouseModel extends Model
{
    // Table name
    protected $table = 'WAREHOUSE';
    protected $primaryKey = 'WHOUSE_ID';
    
    // Fillable fields
    protected $fillable = [
        'WHOUSE_NAME',
        'WHOUSE_LOCATION',
        'WHOUSE_STORAGE_CAPACITY',
        'WHOUSE_RESTOCK_THRESHOLD'
    ];
    
    /**
     * Get all warehouses
     */
    public function getAllWarehouses()
    {
        error_log("WarehouseModel::getAllWarehouses called");
        try {
            $warehouses = $this->query(
                "SELECT * FROM {$this->table} WHERE WHOUSE_DELETED_AT IS NULL ORDER BY WHOUSE_NAME"
            );
            error_log("Retrieved " . count($warehouses) . " warehouses");
            return $warehouses;
        } catch (\Exception $e) {
            error_log("Error in getAllWarehouses: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Find warehouse by ID
     */
    public function findById($id)
    {
        error_log("WarehouseModel::findById called with ID: " . $id);
        try {
            $warehouse = $this->queryOne(
                "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id AND WHOUSE_DELETED_AT IS NULL", 
                ['id' => $id]
            );
            if ($warehouse) {
                error_log("Warehouse found: " . json_encode($warehouse));
            } else {
                error_log("No warehouse found with ID: " . $id);
            }
            return $warehouse;
        } catch (\Exception $e) {
            error_log("Error in findById: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create a new warehouse
     */
    public function createWarehouse($data)
    {
        error_log("WarehouseModel::createWarehouse called with data: " . json_encode($data));
        
        // Normalize data - accept both uppercase and lowercase field names
        $normalizedData = [];
        $fieldMap = [
            'whouse_name' => 'WHOUSE_NAME',
            'whouse_location' => 'WHOUSE_LOCATION',
            'whouse_storage_capacity' => 'WHOUSE_STORAGE_CAPACITY',
            'whouse_restock_threshold' => 'WHOUSE_RESTOCK_THRESHOLD'
        ];
        
        // Process input data to support both naming conventions
        foreach ($fieldMap as $lowercase => $uppercase) {
            if (isset($data[$uppercase])) {
                $normalizedData[$uppercase] = $data[$uppercase];
            } else if (isset($data[$lowercase])) {
                $normalizedData[$uppercase] = $data[$lowercase];
            }
        }
        
        error_log("Normalized data for warehouse creation: " . json_encode($normalizedData));
        
        // Validate data
        if (empty($normalizedData['WHOUSE_NAME'])) {
            error_log("Warehouse name is required");
            throw new \Exception("Warehouse name is required");
        }
        
        if (empty($normalizedData['WHOUSE_LOCATION'])) {
            error_log("Warehouse location is required");
            throw new \Exception("Warehouse location is required");
        }
        
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Add timestamps
            $now = date('Y-m-d H:i:s');
            $normalizedData['WHOUSE_CREATED_AT'] = $now;
            $normalizedData['WHOUSE_UPDATED_AT'] = $now;
            
            // Format the insert data
            $formatData = $this->formatInsertData($normalizedData);
            
            // Insert warehouse
            $sql = "INSERT INTO {$this->table} ({$formatData['columns']}) VALUES ({$formatData['placeholders']})";
            $this->execute($sql, $formatData['filteredData']);
            
            $warehouseId = $this->lastInsertId();
            
            // Commit transaction
            $this->commit();
            
            error_log("Warehouse created successfully with ID: " . $warehouseId);
            return $warehouseId;
        } catch (\Exception $e) {
            // Rollback transaction on error
            if ($this->inTransaction()) {
                $this->rollback();
            }
            error_log("Error in createWarehouse: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update a warehouse
     */
    public function updateWarehouse($warehouseId, $data)
    {
        error_log("WarehouseModel::updateWarehouse called for ID: " . $warehouseId . " with data: " . json_encode($data));
        
        // Validate warehouse exists
        $warehouse = $this->findById($warehouseId);
        if (!$warehouse) {
            error_log("Warehouse not found for update: " . $warehouseId);
            throw new \Exception("Warehouse not found");
        }
        
        // Normalize data - accept both uppercase and lowercase field names
        $normalizedData = [];
        $fieldMap = [
            'whouse_name' => 'WHOUSE_NAME',
            'whouse_location' => 'WHOUSE_LOCATION',
            'whouse_storage_capacity' => 'WHOUSE_STORAGE_CAPACITY',
            'whouse_restock_threshold' => 'WHOUSE_RESTOCK_THRESHOLD'
        ];
        
        // Process input data to support both naming conventions
        foreach ($fieldMap as $lowercase => $uppercase) {
            if (isset($data[$uppercase])) {
                $normalizedData[$uppercase] = $data[$uppercase];
            } else if (isset($data[$lowercase])) {
                $normalizedData[$uppercase] = $data[$lowercase];
            }
        }
        
        error_log("Normalized data for warehouse update: " . json_encode($normalizedData));
        
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Add updated timestamp
            $normalizedData['WHOUSE_UPDATED_AT'] = date('Y-m-d H:i:s');
            
            // Format the update data
            $formatData = $this->formatUpdateData($normalizedData);
            
            // Update warehouse
            $sql = "UPDATE {$this->table} SET {$formatData['updateClause']} WHERE {$this->primaryKey} = :id";
            $params = array_merge($formatData['filteredData'], ['id' => $warehouseId]);
            $this->execute($sql, $params);
            
            // Commit transaction
            $this->commit();
            
            error_log("Warehouse updated successfully: " . $warehouseId);
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            if ($this->inTransaction()) {
                $this->rollback();
            }
            error_log("Error in updateWarehouse: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete a warehouse (if no inventory is associated)
     */
    public function deleteWarehouse($warehouseId)
    {
        error_log("WarehouseModel::deleteWarehouse called for ID: " . $warehouseId);
        
        // Check if warehouse exists
        $warehouse = $this->findById($warehouseId);
        if (!$warehouse) {
            error_log("Warehouse not found for deletion: " . $warehouseId);
            throw new \Exception("Warehouse not found");
        }
        
        // First check if warehouse has inventory
        $count = $this->queryScalar(
            "SELECT COUNT(*) FROM INVENTORY WHERE WHOUSE_ID = :whouse_id AND INVE_DELETED_AT IS NULL",
            ['whouse_id' => $warehouseId],
            0
        );
        
        if ($count > 0) {
            error_log("Cannot delete warehouse with inventory: " . $warehouseId . " (has " . $count . " items)");
            return false; // Cannot delete warehouse with inventory
        }
        
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Soft delete warehouse
            $this->execute(
                "UPDATE {$this->table} SET WHOUSE_DELETED_AT = :now WHERE {$this->primaryKey} = :id",
                ['now' => date('Y-m-d H:i:s'), 'id' => $warehouseId]
            );
            
            // Commit transaction
            $this->commit();
            
            error_log("Warehouse deleted successfully: " . $warehouseId);
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            if ($this->inTransaction()) {
                $this->rollback();
            }
            error_log("Error in deleteWarehouse: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get warehouses with inventory summary
     */
    public function getWarehousesWithInventorySummary()
    {
        error_log("WarehouseModel::getWarehousesWithInventorySummary called");
        
        try {
            // Using raw query for complex join and calculations
            $query = "
                SELECT 
                    w.*,
                    COUNT(DISTINCT i.PROD_ID) as product_count,
                    COALESCE(SUM(i.QUANTITY), 0) as total_quantity,
                    CASE 
                        WHEN w.WHOUSE_STORAGE_CAPACITY > 0 THEN 
                            (COALESCE(SUM(i.QUANTITY), 0) / w.WHOUSE_STORAGE_CAPACITY) * 100 
                        ELSE 0 
                    END as capacity_used_percent
                FROM 
                    {$this->table} w
                LEFT JOIN 
                    INVENTORY i ON w.WHOUSE_ID = i.WHOUSE_ID AND i.INVE_DELETED_AT IS NULL
                WHERE
                    w.WHOUSE_DELETED_AT IS NULL
                GROUP BY 
                    w.WHOUSE_ID
                ORDER BY 
                    w.WHOUSE_NAME
            ";
            
            $result = $this->query($query);
            error_log("Retrieved warehouse summary with " . count($result) . " warehouses");
            return $result;
        } catch (\Exception $e) {
            error_log("Error in getWarehousesWithInventorySummary: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get warehouse stock by product
     */
    public function getWarehouseStockByProduct($warehouseId)
    {
        error_log("WarehouseModel::getWarehouseStockByProduct called for warehouse ID: " . $warehouseId);
        
        try {
            // Using raw query for complex join
            $query = "
                SELECT 
                    p.PROD_ID,
                    p.PROD_NAME,
                    p.PROD_IMAGE,
                    pv.VAR_ID,
                    pv.VAR_CAPACITY,
                    i.INVE_TYPE,
                    i.QUANTITY,
                    i.LAST_UPDATED
                FROM 
                    INVENTORY i
                JOIN 
                    PRODUCT p ON i.PROD_ID = p.PROD_ID
                JOIN 
                    PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID
                WHERE 
                    i.WHOUSE_ID = :whouse_id AND
                    i.INVE_DELETED_AT IS NULL AND
                    p.PROD_DELETED_AT IS NULL AND
                    pv.VAR_DELETED_AT IS NULL
                ORDER BY 
                    p.PROD_NAME, pv.VAR_CAPACITY
            ";
            
            $result = $this->query($query, ['whouse_id' => $warehouseId]);
            
            error_log("Retrieved " . count($result) . " product inventory items for warehouse: " . $warehouseId);
            return $result;
        } catch (\Exception $e) {
            error_log("Error in getWarehouseStockByProduct: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get warehouses with low stock items
     */
    public function getWarehousesWithLowStock()
    {
        error_log("WarehouseModel::getWarehousesWithLowStock called");
        
        try {
            // Using raw query for complex join
            $query = "
                SELECT 
                    w.WHOUSE_ID,
                    w.WHOUSE_NAME,
                    COUNT(DISTINCT i.PROD_ID) as low_stock_product_count
                FROM 
                    {$this->table} w
                JOIN 
                    INVENTORY i ON w.WHOUSE_ID = i.WHOUSE_ID
                WHERE 
                    i.QUANTITY <= w.WHOUSE_RESTOCK_THRESHOLD AND
                    i.INVE_DELETED_AT IS NULL AND
                    w.WHOUSE_DELETED_AT IS NULL
                GROUP BY 
                    w.WHOUSE_ID, w.WHOUSE_NAME
                ORDER BY 
                    low_stock_product_count DESC
            ";
            
            $result = $this->query($query);
            error_log("Retrieved " . count($result) . " warehouses with low stock");
            return $result;
        } catch (\Exception $e) {
            error_log("Error in getWarehousesWithLowStock: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check if a warehouse exists by name
     */
    public function warehouseExistsByName($name, $excludeId = null)
    {
        error_log("WarehouseModel::warehouseExistsByName called for name: " . $name);
        
        try {
            $query = "SELECT COUNT(*) FROM {$this->table} WHERE WHOUSE_NAME = :name AND WHOUSE_DELETED_AT IS NULL";
            $params = ['name' => $name];
            
            if ($excludeId) {
                $query .= " AND {$this->primaryKey} != :id";
                $params['id'] = $excludeId;
            }
            
            $count = $this->queryScalar($query, $params, 0);
            
            error_log("Warehouse name check result: " . ($count > 0 ? 'exists' : 'does not exist'));
            return $count > 0;
        } catch (\Exception $e) {
            error_log("Error in warehouseExistsByName: " . $e->getMessage());
            throw $e;
        }
    }
}