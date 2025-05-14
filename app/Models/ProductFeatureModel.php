<?php

namespace App\Models;

class ProductFeatureModel extends Model
{
    protected $table = 'PRODUCT_FEATURE';
    
    /**
     * Create a new product feature
     * 
     * @param array $data Feature data
     * @return int|bool ID of the created feature or false on failure
     */
    public function createFeature($data)
    {
        $data['FEATURE_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['FEATURE_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                VALUES ({$formatted['placeholders']})
                RETURNING FEATURE_ID";
        
        $id = $this->queryScalar($sql, $formatted['filteredData']);
        return $id ? (int)$id : false;
    }
    
    /**
     * Update a product feature by ID
     * 
     * @param int $id Feature ID
     * @param array $data Feature data to update
     * @return bool True on success, false on failure
     */
    public function updateFeature($id, $data)
    {
        $data['FEATURE_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table}
                SET {$formatted['updateClause']}
                WHERE FEATURE_ID = :id";
        
        $params = array_merge($formatted['filteredData'], ['id' => $id]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Soft delete a product feature by ID
     * 
     * @param int $id Feature ID
     * @return bool True on success, false on failure
     */
    public function deleteFeature($id)
    {
        $sql = "UPDATE {$this->table}
                SET FEATURE_DELETED_AT = NOW()
                WHERE FEATURE_ID = :id";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Get all active features
     * 
     * @return array List of active features
     */
    public function getAllFeatures()
    {
        $sql = "SELECT f.*, p.PROD_NAME
                FROM {$this->table} f
                JOIN PRODUCT p ON f.PROD_ID = p.PROD_ID
                WHERE f.FEATURE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, f.FEATURE_ID";
        
        return $this->query($sql);
    }
    
    /**
     * Get a feature by ID
     * 
     * @param int $id Feature ID
     * @return array|null Feature data or null if not found
     */
    public function getFeatureById($id)
    {
        $sql = "SELECT f.*, p.PROD_NAME
                FROM {$this->table} f
                JOIN PRODUCT p ON f.PROD_ID = p.PROD_ID
                WHERE f.FEATURE_ID = :id
                AND f.FEATURE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get features by product ID
     * 
     * @param int $productId Product ID
     * @return array List of features for the product
     */
    public function getFeaturesByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE PROD_ID = :productId
                AND FEATURE_DELETED_AT IS NULL
                ORDER BY FEATURE_ID";
        
        return $this->query($sql, ['productId' => $productId]);
    }
    
    /**
     * Get features by name (partial match)
     * 
     * @param string $featureName Feature name to search for
     * @return array List of matching features
     */
    public function getFeaturesByName($featureName)
    {
        $sql = "SELECT f.*, p.PROD_NAME
                FROM {$this->table} f
                JOIN PRODUCT p ON f.PROD_ID = p.PROD_ID
                WHERE f.FEATURE_NAME ILIKE :featureName
                AND f.FEATURE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, f.FEATURE_NAME";
        
        return $this->query($sql, ['featureName' => "%{$featureName}%"]);
    }
    
    /**
     * Add multiple features to a product
     * 
     * @param int $productId Product ID
     * @param array $features Array of feature names
     * @return bool True on success, false on failure
     */
    public function addFeaturesToProduct($productId, array $features)
    {
        try {
            $this->beginTransaction();
            
            foreach ($features as $featureName) {
                $data = [
                    'PROD_ID' => $productId,
                    'FEATURE_NAME' => $featureName,
                    'FEATURE_CREATED_AT' => date('Y-m-d H:i:s'),
                    'FEATURE_UPDATED_AT' => date('Y-m-d H:i:s')
                ];
                
                $formatted = $this->formatInsertData($data);
                
                $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                        VALUES ({$formatted['placeholders']})";
                
                $this->execute($sql, $formatted['filteredData']);
            }
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Delete all features for a product
     * 
     * @param int $productId Product ID
     * @return bool True on success, false on failure
     */
    public function deleteFeaturesByProductId($productId)
    {
        $sql = "UPDATE {$this->table}
                SET FEATURE_DELETED_AT = NOW()
                WHERE PROD_ID = :productId
                AND FEATURE_DELETED_AT IS NULL";
        
        return $this->execute($sql, ['productId' => $productId]) > 0;
    }
    
    /**
     * Get products with common features
     * 
     * @param string $featureName Feature name to search for
     * @return array Products sharing the specified feature
     */
    public function getProductsByFeature($featureName)
    {
        $sql = "SELECT p.*, COUNT(f.FEATURE_ID) as matching_features
                FROM PRODUCT p
                JOIN {$this->table} f ON p.PROD_ID = f.PROD_ID
                WHERE f.FEATURE_NAME ILIKE :featureName
                AND f.FEATURE_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                GROUP BY p.PROD_ID
                ORDER BY matching_features DESC, p.PROD_NAME";
        
        return $this->query($sql, ['featureName' => "%{$featureName}%"]);
    }
    
    /**
     * Check if a feature exists for a product
     * 
     * @param int $productId Product ID
     * @param string $featureName Feature name to check
     * @return bool True if feature exists, false otherwise
     */
    public function featureExists($productId, $featureName)
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE PROD_ID = :productId
                AND FEATURE_NAME = :featureName
                AND FEATURE_DELETED_AT IS NULL";
        
        $result = $this->queryScalar($sql, [
            'productId' => $productId,
            'featureName' => $featureName
        ], 0);
        
        return (int)$result > 0;
    }
} 