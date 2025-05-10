<?php

namespace App\Models;

class WarehouseModel extends BaseModel
{
    // Table name
    protected $table = 'warehouse';
    protected $primaryKey = 'whouse_id';
    
    // Fillable fields
    protected $fillable = [
        'whouse_name',
        'whouse_location',
        'whouse_storage_capacity',
        'whouse_restock_threshold'
    ];
    
    /**
     * Get all warehouses
     */
    public function getAllWarehouses()
    {
        error_log("WarehouseModel::getAllWarehouses called");
        try {
            $warehouses = $this->orderBy('whouse_name')->get();
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
            $warehouse = $this->find($id);
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
        
        // Validate data
        if (empty($data['whouse_name'])) {
            error_log("Warehouse name is required");
            throw new \Exception("Warehouse name is required");
        }
        
        if (empty($data['whouse_location'])) {
            error_log("Warehouse location is required");
            throw new \Exception("Warehouse location is required");
        }
        
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Insert warehouse
            $result = $this->insert($data);
            
            // Commit transaction
            $this->commit();
            
            error_log("Warehouse created successfully");
            return $result;
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
        
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Update warehouse
            $result = $this->update(
                $data,
                "{$this->primaryKey} = :id",
                ['id' => $warehouseId]
            );
            
            // Commit transaction
            $this->commit();
            
            error_log("Warehouse updated successfully: " . $warehouseId);
            return $result;
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
        $hasInventory = $this->db->prepare(
            "SELECT COUNT(*) FROM inventory WHERE whouse_id = :whouse_id"
        );
        $hasInventory->execute(['whouse_id' => $warehouseId]);
        $count = $hasInventory->fetchColumn();
        
        if ($count > 0) {
            error_log("Cannot delete warehouse with inventory: " . $warehouseId . " (has " . $count . " items)");
            return false; // Cannot delete warehouse with inventory
        }
        
        try {
            // Begin transaction
            $this->beginTransaction();
            
            // Delete warehouse
            $result = $this->delete(
                "{$this->primaryKey} = :id",
                ['id' => $warehouseId]
            );
            
            // Commit transaction
            $this->commit();
            
            error_log("Warehouse deleted successfully: " . $warehouseId);
            return $result;
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
                    COUNT(DISTINCT i.prod_id) as product_count,
                    COALESCE(SUM(i.quantity), 0) as total_quantity,
                    CASE 
                        WHEN w.whouse_storage_capacity > 0 THEN 
                            (COALESCE(SUM(i.quantity), 0) / w.whouse_storage_capacity) * 100 
                        ELSE 0 
                    END as capacity_used_percent
                FROM 
                    {$this->table} w
                LEFT JOIN 
                    inventory i ON w.whouse_id = i.whouse_id
                GROUP BY 
                    w.whouse_id
                ORDER BY 
                    w.whouse_name
            ";
            
            $result = $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
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
                    p.prod_id,
                    p.prod_name,
                    p.prod_image,
                    pv.var_id,
                    pv.var_capacity,
                    i.inve_type,
                    i.quantity,
                    i.last_updated
                FROM 
                    inventory i
                JOIN 
                    product p ON i.prod_id = p.prod_id
                JOIN 
                    product_variant pv ON p.prod_id = pv.prod_id
                WHERE 
                    i.whouse_id = :whouse_id
                ORDER BY 
                    p.prod_name, pv.var_capacity
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['whouse_id' => $warehouseId]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
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
                    w.whouse_id,
                    w.whouse_name,
                    COUNT(DISTINCT i.prod_id) as low_stock_product_count
                FROM 
                    {$this->table} w
                JOIN 
                    inventory i ON w.whouse_id = i.whouse_id
                WHERE 
                    i.quantity <= w.whouse_restock_threshold
                GROUP BY 
                    w.whouse_id, w.whouse_name
                ORDER BY 
                    low_stock_product_count DESC
            ";
            
            $result = $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
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
            $query = "SELECT COUNT(*) FROM {$this->table} WHERE whouse_name = :name";
            $params = ['name' => $name];
            
            if ($excludeId) {
                $query .= " AND {$this->primaryKey} != :id";
                $params['id'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            
            error_log("Warehouse name check result: " . ($count > 0 ? 'exists' : 'does not exist'));
            return $count > 0;
        } catch (\Exception $e) {
            error_log("Error in warehouseExistsByName: " . $e->getMessage());
            throw $e;
        }
    }
}