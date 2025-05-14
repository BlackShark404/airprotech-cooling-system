<?php

namespace App\Models;

class ProductVariantModel extends Model
{
    // Table name
    protected $table = 'PRODUCT_VARIANT';
    protected $primaryKey = 'VAR_ID';
    
    // Fillable fields
    protected $fillable = [
        'PROD_ID',
        'VAR_CAPACITY',
        'VAR_SRP_PRICE',
        'VAR_PRICE_FREE_INSTALL',
        'VAR_PRICE_WITH_INSTALL',
        'VAR_POWER_CONSUMPTION'
    ];
    
    // Get all variants
    public function getAllVariants()
    {
        return $this->query("SELECT * FROM {$this->table} WHERE VAR_DELETED_AT IS NULL ORDER BY VAR_CAPACITY");
    }
    
    // Find variant by ID
    public function findById($id)
    {
        return $this->queryOne("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id AND VAR_DELETED_AT IS NULL", ['id' => $id]);
    }
    
    // Get variants by product ID
    public function getVariantsByProduct($productId)
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE PROD_ID = :prod_id AND VAR_DELETED_AT IS NULL ORDER BY VAR_CAPACITY",
            ['prod_id' => $productId]
        );
    }
    
    // Create a new variant
    public function createVariant($data)
    {
        // Add timestamps
        $data['VAR_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['VAR_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        // Format the insert data
        $formatData = $this->formatInsertData($data);
        
        // Insert variant
        $sql = "INSERT INTO {$this->table} ({$formatData['columns']}) VALUES ({$formatData['placeholders']})";
        $this->execute($sql, $formatData['filteredData']);
        
        return $this->lastInsertId();
    }
    
    // Update a variant
    public function updateVariant($variantId, $data)
    {
        // Add updated timestamp
        $data['VAR_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        // Format the update data
        $formatData = $this->formatUpdateData($data);
        
        // Update variant
        $sql = "UPDATE {$this->table} SET {$formatData['updateClause']} WHERE {$this->primaryKey} = :id";
        $params = array_merge($formatData['filteredData'], ['id' => $variantId]);
        
        return $this->execute($sql, $params);
    }
    
    // Delete a variant (soft delete)
    public function deleteVariant($variantId)
    {
        return $this->execute(
            "UPDATE {$this->table} SET VAR_DELETED_AT = :now WHERE {$this->primaryKey} = :id",
            ['now' => date('Y-m-d H:i:s'), 'id' => $variantId]
        );
    }
    
    // Get variants with product info
    public function getVariantsWithProductInfo()
    {
        return $this->query("
            SELECT 
                pv.*, 
                p.PROD_NAME, 
                p.PROD_IMAGE, 
                p.PROD_AVAILABILITY_STATUS
            FROM 
                {$this->table} pv
            JOIN 
                PRODUCT p ON pv.PROD_ID = p.PROD_ID
            WHERE 
                pv.VAR_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL
            ORDER BY 
                p.PROD_NAME, pv.VAR_CAPACITY
        ");
    }
    
    // Get variants with inventory count
    public function getVariantsWithInventoryCount()
    {
        return $this->query("
            SELECT 
                pv.*,
                p.PROD_NAME,
                p.PROD_IMAGE,
                p.PROD_AVAILABILITY_STATUS,
                COALESCE(SUM(i.QUANTITY), 0) as total_quantity
            FROM 
                {$this->table} pv
            JOIN 
                PRODUCT p ON pv.PROD_ID = p.PROD_ID
            LEFT JOIN 
                INVENTORY i ON pv.PROD_ID = i.PROD_ID
            WHERE
                pv.VAR_DELETED_AT IS NULL AND
                p.PROD_DELETED_AT IS NULL AND
                (i.INVE_DELETED_AT IS NULL OR i.INVE_DELETED_AT IS NULL)
            GROUP BY 
                pv.VAR_ID, p.PROD_NAME, p.PROD_IMAGE, p.PROD_AVAILABILITY_STATUS
            ORDER BY 
                p.PROD_NAME, pv.VAR_CAPACITY
        ");
    }
}