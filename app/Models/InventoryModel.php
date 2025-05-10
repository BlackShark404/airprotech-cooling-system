<?php

namespace App\Models;

class InventoryModel extends BaseModel
{
    // Table names
    protected $table = 'inventory';
    protected $primaryKey = 'inve_id';
    
    // These fields can be filled
    protected $fillable = [
        'prod_id',
        'whouse_id',
        'inve_type',
        'quantity',
        'last_updated'
    ];
    
    // Date fields
    protected $timestamps = true;
    protected $createdAtColumn = 'last_updated';
    protected $updatedAtColumn = 'last_updated';
    
    // Get all inventory with product and warehouse info - FIXED
    public function getAllInventory()
    {
        // Using raw query to avoid issues with query builder
        $sql = "
            SELECT DISTINCT
                i.*,
                p.prod_name,
                p.prod_image,
                p.prod_availability_status,
                w.whouse_name,
                pv.var_capacity
            FROM {$this->table} i
            JOIN product p ON i.prod_id = p.prod_id
            JOIN warehouse w ON i.whouse_id = w.whouse_id
            LEFT JOIN product_variant pv ON i.prod_id = pv.prod_id
            ORDER BY p.prod_name, pv.var_capacity
        ";
        
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get inventory by product ID - FIXED
    public function getInventoryByProduct($productId)
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                w.whouse_name,
                pv.var_capacity
            FROM {$this->table} i
            JOIN warehouse w ON i.whouse_id = w.whouse_id
            LEFT JOIN product_variant pv ON i.prod_id = pv.prod_id
            WHERE i.prod_id = :prod_id
            ORDER BY w.whouse_name
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prod_id' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get inventory by warehouse ID - FIXED
    public function getInventoryByWarehouse($warehouseId)
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                p.prod_name,
                p.prod_image,
                pv.var_capacity
            FROM {$this->table} i
            JOIN product p ON i.prod_id = p.prod_id
            LEFT JOIN product_variant pv ON i.prod_id = pv.prod_id
            WHERE i.whouse_id = :whouse_id
            ORDER BY p.prod_name
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['whouse_id' => $warehouseId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get inventory by type (Regular, Display, etc.) - FIXED
    public function getInventoryByType($type)
    {
        $sql = "
            SELECT DISTINCT
                i.*,
                p.prod_name,
                p.prod_image,
                w.whouse_name,
                pv.var_capacity
            FROM {$this->table} i
            JOIN product p ON i.prod_id = p.prod_id
            JOIN warehouse w ON i.whouse_id = w.whouse_id
            LEFT JOIN product_variant pv ON i.prod_id = pv.prod_id
            WHERE i.inve_type = :type
            ORDER BY p.prod_name
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get product list with variant count - FIXED
    public function getProductsWithVariants()
    {
        $sql = "
            SELECT 
                p.prod_id,
                p.prod_name,
                p.prod_image,
                p.prod_availability_status,
                p.prod_description,
                COUNT(DISTINCT pv.var_id) as variant_count,
                COALESCE(SUM(i.quantity), 0) as total_stock
            FROM product p
            LEFT JOIN product_variant pv ON p.prod_id = pv.prod_id
            LEFT JOIN {$this->table} i ON p.prod_id = i.prod_id
            GROUP BY p.prod_id, p.prod_name, p.prod_image, p.prod_availability_status, p.prod_description
            ORDER BY p.prod_name
        ";
        
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Rest of the methods remain the same
    
    // Add inventory quantity - keeping the same
    public function addStock($productId, $warehouseId, $type, $quantity)
    {
        // Check if inventory record exists
        $inventory = $this->select('*')
                          ->where('prod_id = :prod_id')
                          ->where('whouse_id = :whouse_id')
                          ->where('inve_type = :type')
                          ->bind([
                              'prod_id' => $productId,
                              'whouse_id' => $warehouseId,
                              'type' => $type
                          ])
                          ->first();
        
        if ($inventory) {
            // Update existing inventory
            $newQuantity = $inventory['quantity'] + $quantity;
            return $this->update(
                ['quantity' => $newQuantity, 'last_updated' => date('Y-m-d H:i:s')],
                'inve_id = :inve_id',
                ['inve_id' => $inventory['inve_id']]
            );
        } else {
            // Create new inventory record
            return $this->insert([
                'prod_id' => $productId,
                'whouse_id' => $warehouseId,
                'inve_type' => $type,
                'quantity' => $quantity,
                'last_updated' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // Move stock between warehouses - keeping the same
    public function moveStock($productId, $fromWarehouseId, $toWarehouseId, $type, $quantity)
    {
        // Begin transaction for data integrity
        $this->db->beginTransaction();
        
        try {
            // Check if source has enough stock
            $sourceStock = $this->select('*')
                                ->where('prod_id = :prod_id')
                                ->where('whouse_id = :whouse_id')
                                ->where('inve_type = :type')
                                ->bind([
                                    'prod_id' => $productId,
                                    'whouse_id' => $fromWarehouseId,
                                    'type' => $type
                                ])
                                ->first();
            
            if (!$sourceStock || $sourceStock['quantity'] < $quantity) {
                // Not enough stock
                $this->db->rollBack();
                return false;
            }
            
            // Reduce from source warehouse
            $this->update(
                ['quantity' => $sourceStock['quantity'] - $quantity, 'last_updated' => date('Y-m-d H:i:s')],
                'inve_id = :inve_id',
                ['inve_id' => $sourceStock['inve_id']]
            );
            
            // Add to destination warehouse
            $destStock = $this->select('*')
                              ->where('prod_id = :prod_id')
                              ->where('whouse_id = :whouse_id')
                              ->where('inve_type = :type')
                              ->bind([
                                  'prod_id' => $productId,
                                  'whouse_id' => $toWarehouseId,
                                  'type' => $type
                              ])
                              ->first();
            
            if ($destStock) {
                // Update existing destination inventory
                $this->update(
                    ['quantity' => $destStock['quantity'] + $quantity, 'last_updated' => date('Y-m-d H:i:s')],
                    'inve_id = :inve_id',
                    ['inve_id' => $destStock['inve_id']]
                );
            } else {
                // Create new destination inventory record
                $this->insert([
                    'prod_id' => $productId,
                    'whouse_id' => $toWarehouseId,
                    'inve_type' => $type,
                    'quantity' => $quantity,
                    'last_updated' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->db->rollBack();
            return false;
        }
    }
    
    // Keep other methods as they are - they already use raw queries
    
    // Get low stock products
    public function getLowStockProducts()
    {
        return $this->db->query("
            SELECT 
                p.prod_id, 
                p.prod_name, 
                p.prod_image, 
                pv.var_capacity, 
                w.whouse_name,
                i.quantity,
                w.whouse_restock_threshold
            FROM 
                inventory i
            JOIN 
                product p ON i.prod_id = p.prod_id
            JOIN 
                warehouse w ON i.whouse_id = w.whouse_id
            JOIN 
                product_variant pv ON p.prod_id = pv.prod_id
            WHERE 
                i.quantity <= w.whouse_restock_threshold
            ORDER BY 
                i.quantity ASC
        ")->fetchAll();
    }
    
    // Get inventory summary for all products
    public function getInventorySummary()
    {
        return $this->db->query("
            SELECT 
                p.prod_id,
                p.prod_name,
                p.prod_image,
                p.prod_availability_status,
                pv.var_id,
                pv.var_capacity,
                SUM(i.quantity) as total_quantity
            FROM 
                product p
            JOIN 
                product_variant pv ON p.prod_id = pv.prod_id
            LEFT JOIN 
                inventory i ON p.prod_id = i.prod_id
            GROUP BY 
                p.prod_id, p.prod_name, p.prod_image, p.prod_availability_status, pv.var_id, pv.var_capacity
            ORDER BY 
                p.prod_name, pv.var_capacity
        ")->fetchAll();
    }
    
    // Get inventory by product variant
    public function getInventoryByVariant($variantId)
    {
        return $this->db->query("
            SELECT 
                i.*,
                p.prod_name,
                p.prod_image,
                w.whouse_name,
                pv.var_capacity
            FROM 
                inventory i
            JOIN 
                product p ON i.prod_id = p.prod_id
            JOIN 
                warehouse w ON i.whouse_id = w.whouse_id
            JOIN 
                product_variant pv ON p.prod_id = pv.prod_id
            WHERE 
                pv.var_id = :variant_id
            ORDER BY 
                w.whouse_name
        ", ['variant_id' => $variantId])->fetchAll();
    }
    
    // Get warehouse list
    public function getWarehouses()
    {
        return $this->db->query("
            SELECT * FROM warehouse ORDER BY whouse_name
        ")->fetchAll();
    }
}