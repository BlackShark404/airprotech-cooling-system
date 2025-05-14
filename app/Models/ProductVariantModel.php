<?php

namespace App\Models;

class ProductVariantModel extends Model
{
    protected $table = 'PRODUCT_VARIANT';
    
    /**
     * Create a new product variant
     * 
     * @param array $data Variant data
     * @return int|bool ID of the created variant or false on failure
     */
    public function createVariant($data)
    {
        $data['VAR_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['VAR_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                VALUES ({$formatted['placeholders']})
                RETURNING VAR_ID";
        
        $id = $this->queryScalar($sql, $formatted['filteredData']);
        return $id ? (int)$id : false;
    }
    
    /**
     * Update a product variant by ID
     * 
     * @param int $id Variant ID
     * @param array $data Variant data to update
     * @return bool True on success, false on failure
     */
    public function updateVariant($id, $data)
    {
        $data['VAR_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table}
                SET {$formatted['updateClause']}
                WHERE VAR_ID = :id";
        
        $params = array_merge($formatted['filteredData'], ['id' => $id]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Soft delete a product variant by ID
     * 
     * @param int $id Variant ID
     * @return bool True on success, false on failure
     */
    public function deleteVariant($id)
    {
        $sql = "UPDATE {$this->table}
                SET VAR_DELETED_AT = NOW()
                WHERE VAR_ID = :id";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Get all active variants
     * 
     * @return array List of active variants
     */
    public function getAllVariants()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE VAR_DELETED_AT IS NULL
                ORDER BY VAR_ID DESC";
        
        return $this->query($sql);
    }
    
    /**
     * Get a variant by ID
     * 
     * @param int $id Variant ID
     * @return array|null Variant data or null if not found
     */
    public function getVariantById($id)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE VAR_ID = :id
                AND VAR_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get variants by product ID
     * 
     * @param int $productId Product ID
     * @return array List of variants for the product
     */
    public function getVariantsByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE PROD_ID = :productId
                AND VAR_DELETED_AT IS NULL
                ORDER BY VAR_CAPACITY ASC";
        
        return $this->query($sql, ['productId' => $productId]);
    }
    
    /**
     * Get variants with product details
     * 
     * @return array Variants with associated product details
     */
    public function getVariantsWithProductDetails()
    {
        $sql = "SELECT v.*, p.PROD_NAME, p.PROD_IMAGE, p.PROD_DESCRIPTION, p.PROD_AVAILABILITY_STATUS
                FROM {$this->table} v
                JOIN PRODUCT p ON v.PROD_ID = p.PROD_ID
                WHERE v.VAR_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, v.VAR_CAPACITY";
        
        return $this->query($sql);
    }
    
    /**
     * Get variants with inventory information
     * 
     * @return array Variants with their inventory levels
     */
    public function getVariantsWithInventory()
    {
        $sql = "SELECT v.*, p.PROD_NAME, p.PROD_IMAGE, 
                       SUM(i.QUANTITY) as stock_level
                FROM {$this->table} v
                JOIN PRODUCT p ON v.PROD_ID = p.PROD_ID
                LEFT JOIN INVENTORY i ON p.PROD_ID = i.PROD_ID AND i.INVE_DELETED_AT IS NULL
                WHERE v.VAR_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                GROUP BY v.VAR_ID, p.PROD_ID
                ORDER BY p.PROD_NAME, v.VAR_CAPACITY";
        
        return $this->query($sql);
    }
    
    /**
     * Get variants in a specific price range
     * 
     * @param float $minPrice Minimum price
     * @param float $maxPrice Maximum price
     * @return array Variants in the specified price range
     */
    public function getVariantsByPriceRange($minPrice, $maxPrice)
    {
        $sql = "SELECT v.*, p.PROD_NAME, p.PROD_IMAGE
                FROM {$this->table} v
                JOIN PRODUCT p ON v.PROD_ID = p.PROD_ID
                WHERE v.VAR_SRP_PRICE BETWEEN :minPrice AND :maxPrice
                AND v.VAR_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY v.VAR_SRP_PRICE ASC";
        
        return $this->query($sql, [
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice
        ]);
    }
    
    /**
     * Get variants by capacity
     * 
     * @param string $capacity Capacity value (e.g., '1.5HP', '2.0HP')
     * @return array Variants with the specified capacity
     */
    public function getVariantsByCapacity($capacity)
    {
        $sql = "SELECT v.*, p.PROD_NAME, p.PROD_IMAGE
                FROM {$this->table} v
                JOIN PRODUCT p ON v.PROD_ID = p.PROD_ID
                WHERE v.VAR_CAPACITY = :capacity
                AND v.VAR_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY v.VAR_SRP_PRICE ASC";
        
        return $this->query($sql, ['capacity' => $capacity]);
    }
    
    /**
     * Get distinct capacity values
     * 
     * @return array List of distinct capacity values
     */
    public function getDistinctCapacities()
    {
        $sql = "SELECT DISTINCT VAR_CAPACITY
                FROM {$this->table}
                WHERE VAR_DELETED_AT IS NULL
                ORDER BY VAR_CAPACITY";
        
        return $this->query($sql);
    }
}
