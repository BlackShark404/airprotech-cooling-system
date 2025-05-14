<?php

namespace App\Models;

class ProductModel extends Model
{
    protected $table = 'PRODUCT';
    
    /**
     * Create a new product
     * 
     * @param array $data Product data
     * @return int|bool ID of the created product or false on failure
     */
    public function createProduct($data)
    {
        $data['PROD_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['PROD_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                VALUES ({$formatted['placeholders']})
                RETURNING PROD_ID";
        
        $id = $this->queryScalar($sql, $formatted['filteredData']);
        return $id ? (int)$id : false;
    }
    
    /**
     * Update a product by ID
     * 
     * @param int $id Product ID
     * @param array $data Product data to update
     * @return bool True on success, false on failure
     */
    public function updateProduct($id, $data)
    {
        $data['PROD_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table}
                SET {$formatted['updateClause']}
                WHERE PROD_ID = :id";
        
        $params = array_merge($formatted['filteredData'], ['id' => $id]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Soft delete a product by ID
     * 
     * @param int $id Product ID
     * @return bool True on success, false on failure
     */
    public function deleteProduct($id)
    {
        $sql = "UPDATE {$this->table}
                SET PROD_DELETED_AT = NOW()
                WHERE PROD_ID = :id";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Get all active products
     * 
     * @return array List of active products
     */
    public function getAllProducts()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE PROD_DELETED_AT IS NULL
                ORDER BY PROD_ID DESC";
        
        return $this->query($sql);
    }
    
    /**
     * Get a product by ID
     * 
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public function getProductById($id)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE PROD_ID = :id
                AND PROD_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get products with their features and specifications
     * 
     * @return array Products with related features and specifications
     */
    public function getProductsWithDetails()
    {
        $sql = "SELECT p.*, 
                    ARRAY_AGG(DISTINCT f.FEATURE_NAME) FILTER (WHERE f.FEATURE_ID IS NOT NULL) AS features,
                    JSON_OBJECT_AGG(s.SPEC_NAME, s.SPEC_VALUE) FILTER (WHERE s.SPEC_ID IS NOT NULL) AS specifications
                FROM {$this->table} p
                LEFT JOIN PRODUCT_FEATURE f ON p.PROD_ID = f.PROD_ID AND f.FEATURE_DELETED_AT IS NULL
                LEFT JOIN PRODUCT_SPEC s ON p.PROD_ID = s.PROD_ID AND s.SPEC_DELETED_AT IS NULL
                WHERE p.PROD_DELETED_AT IS NULL
                GROUP BY p.PROD_ID
                ORDER BY p.PROD_ID DESC";
        
        return $this->query($sql);
    }
    
    /**
     * Get a product with its features and specifications
     * 
     * @param int $id Product ID
     * @return array|null Product with related data or null if not found
     */
    public function getProductWithDetails($id)
    {
        $sql = "SELECT p.*, 
                    ARRAY_AGG(DISTINCT f.FEATURE_NAME) FILTER (WHERE f.FEATURE_ID IS NOT NULL) AS features,
                    JSON_OBJECT_AGG(s.SPEC_NAME, s.SPEC_VALUE) FILTER (WHERE s.SPEC_ID IS NOT NULL) AS specifications
                FROM {$this->table} p
                LEFT JOIN PRODUCT_FEATURE f ON p.PROD_ID = f.PROD_ID AND f.FEATURE_DELETED_AT IS NULL
                LEFT JOIN PRODUCT_SPEC s ON p.PROD_ID = s.PROD_ID AND s.SPEC_DELETED_AT IS NULL
                WHERE p.PROD_ID = :id
                AND p.PROD_DELETED_AT IS NULL
                GROUP BY p.PROD_ID";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Search products by name or description
     * 
     * @param string $term Search term
     * @return array Matching products
     */
    public function searchProducts($term)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE (PROD_NAME ILIKE :term OR PROD_DESCRIPTION ILIKE :term)
                AND PROD_DELETED_AT IS NULL
                ORDER BY PROD_ID DESC";
        
        return $this->query($sql, ['term' => "%{$term}%"]);
    }
    
    /**
     * Get available products (with stock > 0)
     * 
     * @return array Available products
     */
    public function getAvailableProducts()
    {
        $sql = "SELECT p.*, SUM(i.QUANTITY) as total_stock
                FROM {$this->table} p
                JOIN INVENTORY i ON p.PROD_ID = i.PROD_ID AND i.INVE_DELETED_AT IS NULL
                WHERE p.PROD_DELETED_AT IS NULL
                AND p.PROD_AVAILABILITY_STATUS = 'Available'
                GROUP BY p.PROD_ID
                HAVING SUM(i.QUANTITY) > 0
                ORDER BY p.PROD_ID DESC";
        
        return $this->query($sql);
    }
    
    /**
     * Get products by availability status
     * 
     * @param string $status Availability status
     * @return array Products with specified status
     */
    public function getProductsByStatus($status)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE PROD_AVAILABILITY_STATUS = :status
                AND PROD_DELETED_AT IS NULL
                ORDER BY PROD_ID DESC";
        
        return $this->query($sql, ['status' => $status]);
    }
}
