<?php

namespace App\Models;

class WarehouseModel extends Model
{
    protected $table = 'WAREHOUSE';
    
    /**
     * Create a new warehouse
     * 
     * @param array $data Warehouse data
     * @return int|bool ID of the created warehouse or false on failure
     */
    public function createWarehouse($data)
    {
        $data['WHOUSE_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['WHOUSE_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                VALUES ({$formatted['placeholders']})
                RETURNING WHOUSE_ID";
        
        $id = $this->queryScalar($sql, $formatted['filteredData']);
        return $id ? (int)$id : false;
    }
    
    /**
     * Update a warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @param array $data Warehouse data to update
     * @return bool True on success, false on failure
     */
    public function updateWarehouse($id, $data)
    {
        $data['WHOUSE_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table}
                SET {$formatted['updateClause']}
                WHERE WHOUSE_ID = :id";
        
        $params = array_merge($formatted['filteredData'], ['id' => $id]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Soft delete a warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @return bool True on success, false on failure
     */
    public function deleteWarehouse($id)
    {
        $sql = "UPDATE {$this->table}
                SET WHOUSE_DELETED_AT = NOW()
                WHERE WHOUSE_ID = :id";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Get all active warehouses
     * 
     * @return array List of active warehouses
     */
    public function getAllWarehouses()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE WHOUSE_DELETED_AT IS NULL
                ORDER BY WHOUSE_NAME";
        
        return $this->query($sql);
    }
    
    /**
     * Get a warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @return array|null Warehouse data or null if not found
     */
    public function getWarehouseById($id)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE WHOUSE_ID = :id
                AND WHOUSE_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get warehouses with their inventory counts
     * 
     * @return array Warehouses with inventory statistics
     */
    public function getWarehousesWithInventory()
    {
        $sql = "SELECT w.*,
                COUNT(DISTINCT i.PROD_ID) as product_count,
                COALESCE(SUM(i.QUANTITY), 0) as total_items,
                COALESCE(SUM(CASE WHEN i.QUANTITY < w.WHOUSE_RESTOCK_THRESHOLD THEN 1 ELSE 0 END), 0) as low_stock_count
                FROM {$this->table} w
                LEFT JOIN INVENTORY i ON w.WHOUSE_ID = i.WHOUSE_ID 
                    AND i.INVE_DELETED_AT IS NULL
                    AND i.INVE_TYPE = 'Regular'
                WHERE w.WHOUSE_DELETED_AT IS NULL
                GROUP BY w.WHOUSE_ID
                ORDER BY w.WHOUSE_NAME";
        
        return $this->query($sql);
    }
    
    /**
     * Get warehouse storage utilization
     * 
     * @param int $id Warehouse ID
     * @return array|null Warehouse with storage utilization data
     */
    public function getWarehouseUtilization($id)
    {
        $sql = "SELECT w.*,
                COALESCE(SUM(i.QUANTITY), 0) as total_items,
                CASE 
                    WHEN w.WHOUSE_STORAGE_CAPACITY > 0 
                    THEN ROUND((COALESCE(SUM(i.QUANTITY), 0) * 100.0 / w.WHOUSE_STORAGE_CAPACITY), 2)
                    ELSE 0 
                END as utilization_percentage
                FROM {$this->table} w
                LEFT JOIN INVENTORY i ON w.WHOUSE_ID = i.WHOUSE_ID AND i.INVE_DELETED_AT IS NULL
                WHERE w.WHOUSE_ID = :id
                AND w.WHOUSE_DELETED_AT IS NULL
                GROUP BY w.WHOUSE_ID";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get warehouses with available space for a product
     * 
     * @param int $quantity Quantity needed to store
     * @return array Warehouses with available space
     */
    public function getWarehousesWithAvailableSpace($quantity)
    {
        $sql = "SELECT w.*,
                COALESCE(SUM(i.QUANTITY), 0) as current_stock,
                (w.WHOUSE_STORAGE_CAPACITY - COALESCE(SUM(i.QUANTITY), 0)) as available_capacity
                FROM {$this->table} w
                LEFT JOIN INVENTORY i ON w.WHOUSE_ID = i.WHOUSE_ID AND i.INVE_DELETED_AT IS NULL
                WHERE w.WHOUSE_DELETED_AT IS NULL
                GROUP BY w.WHOUSE_ID
                HAVING (w.WHOUSE_STORAGE_CAPACITY - COALESCE(SUM(i.QUANTITY), 0)) >= :quantity
                OR w.WHOUSE_STORAGE_CAPACITY = 0
                ORDER BY 
                    CASE WHEN w.WHOUSE_STORAGE_CAPACITY = 0 THEN 1 ELSE 0 END,
                    available_capacity DESC";
        
        return $this->query($sql, ['quantity' => $quantity]);
    }
    
    /**
     * Get product distribution across warehouses
     * 
     * @param int $productId Product ID
     * @return array Distribution of the product across warehouses
     */
    public function getProductDistribution($productId)
    {
        $sql = "SELECT w.WHOUSE_ID, w.WHOUSE_NAME, w.WHOUSE_LOCATION,
                COALESCE(i.QUANTITY, 0) as quantity,
                i.INVE_TYPE
                FROM {$this->table} w
                LEFT JOIN INVENTORY i ON w.WHOUSE_ID = i.WHOUSE_ID 
                    AND i.PROD_ID = :productId
                    AND i.INVE_DELETED_AT IS NULL
                WHERE w.WHOUSE_DELETED_AT IS NULL
                ORDER BY w.WHOUSE_NAME, i.INVE_TYPE";
        
        return $this->query($sql, ['productId' => $productId]);
    }
    
    /**
     * Search warehouses by name or location
     * 
     * @param string $term Search term
     * @return array Matching warehouses
     */
    public function searchWarehouses($term)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE (WHOUSE_NAME ILIKE :term OR WHOUSE_LOCATION ILIKE :term)
                AND WHOUSE_DELETED_AT IS NULL
                ORDER BY WHOUSE_NAME";
        
        return $this->query($sql, ['term' => "%{$term}%"]);
    }
}
