<?php

namespace App\Models;

class ProductSpecModel extends Model
{
    protected $table = 'PRODUCT_SPEC';
    
    /**
     * Create a new product specification
     * 
     * @param array $data Specification data
     * @return int|bool ID of the created specification or false on failure
     */
    public function createSpec($data)
    {
        $data['SPEC_CREATED_AT'] = date('Y-m-d H:i:s');
        $data['SPEC_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']})
                VALUES ({$formatted['placeholders']})
                RETURNING SPEC_ID";
        
        $id = $this->queryScalar($sql, $formatted['filteredData']);
        return $id ? (int)$id : false;
    }
    
    /**
     * Update a product specification by ID
     * 
     * @param int $id Specification ID
     * @param array $data Specification data to update
     * @return bool True on success, false on failure
     */
    public function updateSpec($id, $data)
    {
        $data['SPEC_UPDATED_AT'] = date('Y-m-d H:i:s');
        
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table}
                SET {$formatted['updateClause']}
                WHERE SPEC_ID = :id";
        
        $params = array_merge($formatted['filteredData'], ['id' => $id]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Soft delete a product specification by ID
     * 
     * @param int $id Specification ID
     * @return bool True on success, false on failure
     */
    public function deleteSpec($id)
    {
        $sql = "UPDATE {$this->table}
                SET SPEC_DELETED_AT = NOW()
                WHERE SPEC_ID = :id";
        
        return $this->execute($sql, ['id' => $id]) > 0;
    }
    
    /**
     * Get all active specifications
     * 
     * @return array List of active specifications
     */
    public function getAllSpecs()
    {
        $sql = "SELECT s.*, p.PROD_NAME
                FROM {$this->table} s
                JOIN PRODUCT p ON s.PROD_ID = p.PROD_ID
                WHERE s.SPEC_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, s.SPEC_NAME";
        
        return $this->query($sql);
    }
    
    /**
     * Get a specification by ID
     * 
     * @param int $id Specification ID
     * @return array|null Specification data or null if not found
     */
    public function getSpecById($id)
    {
        $sql = "SELECT s.*, p.PROD_NAME
                FROM {$this->table} s
                JOIN PRODUCT p ON s.PROD_ID = p.PROD_ID
                WHERE s.SPEC_ID = :id
                AND s.SPEC_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Get specifications by product ID
     * 
     * @param int $productId Product ID
     * @return array List of specifications for the product
     */
    public function getSpecsByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE PROD_ID = :productId
                AND SPEC_DELETED_AT IS NULL
                ORDER BY SPEC_NAME";
        
        return $this->query($sql, ['productId' => $productId]);
    }
    
    /**
     * Get specifications by name (partial match)
     * 
     * @param string $specName Specification name to search for
     * @return array List of matching specifications
     */
    public function getSpecsByName($specName)
    {
        $sql = "SELECT s.*, p.PROD_NAME
                FROM {$this->table} s
                JOIN PRODUCT p ON s.PROD_ID = p.PROD_ID
                WHERE s.SPEC_NAME ILIKE :specName
                AND s.SPEC_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, s.SPEC_NAME";
        
        return $this->query($sql, ['specName' => "%{$specName}%"]);
    }
    
    /**
     * Get specifications by value (partial match)
     * 
     * @param string $specValue Specification value to search for
     * @return array List of matching specifications
     */
    public function getSpecsByValue($specValue)
    {
        $sql = "SELECT s.*, p.PROD_NAME
                FROM {$this->table} s
                JOIN PRODUCT p ON s.PROD_ID = p.PROD_ID
                WHERE s.SPEC_VALUE ILIKE :specValue
                AND s.SPEC_DELETED_AT IS NULL
                AND p.PROD_DELETED_AT IS NULL
                ORDER BY p.PROD_NAME, s.SPEC_NAME";
        
        return $this->query($sql, ['specValue' => "%{$specValue}%"]);
    }
    
    /**
     * Add multiple specifications to a product
     * 
     * @param int $productId Product ID
     * @param array $specs Associative array of spec_name => spec_value pairs
     * @return bool True on success, false on failure
     */
    public function addSpecsToProduct($productId, array $specs)
    {
        try {
            $this->beginTransaction();
            
            foreach ($specs as $specName => $specValue) {
                $data = [
                    'PROD_ID' => $productId,
                    'SPEC_NAME' => $specName,
                    'SPEC_VALUE' => $specValue,
                    'SPEC_CREATED_AT' => date('Y-m-d H:i:s'),
                    'SPEC_UPDATED_AT' => date('Y-m-d H:i:s')
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
     * Update or create a specification for a product
     * 
     * @param int $productId Product ID
     * @param string $specName Specification name
     * @param string $specValue Specification value
     * @return bool True on success, false on failure
     */
    public function updateOrCreateSpec($productId, $specName, $specValue)
    {
        try {
            $this->beginTransaction();
            
            // Check if the spec exists
            $sql = "SELECT SPEC_ID FROM {$this->table}
                    WHERE PROD_ID = :productId
                    AND SPEC_NAME = :specName
                    AND SPEC_DELETED_AT IS NULL";
            
            $specId = $this->queryScalar($sql, [
                'productId' => $productId,
                'specName' => $specName
            ]);
            
            if ($specId) {
                // Update existing spec
                $updateSql = "UPDATE {$this->table}
                              SET SPEC_VALUE = :specValue,
                                  SPEC_UPDATED_AT = :updatedAt
                              WHERE SPEC_ID = :specId";
                
                $this->execute($updateSql, [
                    'specValue' => $specValue,
                    'updatedAt' => date('Y-m-d H:i:s'),
                    'specId' => $specId
                ]);
            } else {
                // Create new spec
                $data = [
                    'PROD_ID' => $productId,
                    'SPEC_NAME' => $specName,
                    'SPEC_VALUE' => $specValue,
                    'SPEC_CREATED_AT' => date('Y-m-d H:i:s'),
                    'SPEC_UPDATED_AT' => date('Y-m-d H:i:s')
                ];
                
                $formatted = $this->formatInsertData($data);
                
                $insertSql = "INSERT INTO {$this->table} ({$formatted['columns']})
                              VALUES ({$formatted['placeholders']})";
                
                $this->execute($insertSql, $formatted['filteredData']);
            }
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Delete all specifications for a product
     * 
     * @param int $productId Product ID
     * @return bool True on success, false on failure
     */
    public function deleteSpecsByProductId($productId)
    {
        $sql = "UPDATE {$this->table}
                SET SPEC_DELETED_AT = NOW()
                WHERE PROD_ID = :productId
                AND SPEC_DELETED_AT IS NULL";
        
        return $this->execute($sql, ['productId' => $productId]) > 0;
    }
    
    /**
     * Get products by specification
     * 
     * @param string $specName Specification name
     * @param string $specValue Specification value (optional)
     * @return array Products matching the specification
     */
    public function getProductsBySpec($specName, $specValue = null)
    {
        $params = ['specName' => $specName];
        
        $sql = "SELECT p.*, s.SPEC_VALUE
                FROM PRODUCT p
                JOIN {$this->table} s ON p.PROD_ID = s.PROD_ID
                WHERE s.SPEC_NAME = :specName";
        
        if ($specValue !== null) {
            $sql .= " AND s.SPEC_VALUE = :specValue";
            $params['specValue'] = $specValue;
        }
        
        $sql .= " AND s.SPEC_DELETED_AT IS NULL
                  AND p.PROD_DELETED_AT IS NULL
                  ORDER BY p.PROD_NAME";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get distinct specification names
     * 
     * @return array List of distinct specification names
     */
    public function getDistinctSpecNames()
    {
        $sql = "SELECT DISTINCT SPEC_NAME
                FROM {$this->table}
                WHERE SPEC_DELETED_AT IS NULL
                ORDER BY SPEC_NAME";
        
        return $this->query($sql);
    }
    
    /**
     * Get distinct values for a specification name
     * 
     * @param string $specName Specification name
     * @return array List of distinct values for the specification
     */
    public function getDistinctSpecValues($specName)
    {
        $sql = "SELECT DISTINCT SPEC_VALUE
                FROM {$this->table}
                WHERE SPEC_NAME = :specName
                AND SPEC_DELETED_AT IS NULL
                ORDER BY SPEC_VALUE";
        
        return $this->query($sql, ['specName' => $specName]);
    }
}
