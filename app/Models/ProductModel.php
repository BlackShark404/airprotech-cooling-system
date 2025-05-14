<?php

namespace App\Models;

class ProductModel extends Model
{
    // Table name
    protected $table = 'PRODUCT';
    protected $primaryKey = 'PROD_ID';
    
    // Fillable fields
    protected $fillable = [
        'PROD_NAME',
        'PROD_DESCRIPTION',
        'PROD_IMAGE',
        'PROD_AVAILABILITY_STATUS'
    ];
    
    // Get all products
    public function getAllProducts()
    {
        return $this->query("SELECT * FROM {$this->table} WHERE PROD_DELETED_AT IS NULL ORDER BY PROD_NAME");
    }
    
    // Find product by ID
    public function findById($id)
    {
        return $this->queryOne("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id AND PROD_DELETED_AT IS NULL", ['id' => $id]);
    }
    
    // Get products with variants
    public function getProductsWithVariants()
    {
        return $this->query("
            SELECT 
                p.*,
                COUNT(pv.VAR_ID) AS variant_count
            FROM 
                {$this->table} p
            LEFT JOIN 
                PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID
            WHERE 
                p.PROD_DELETED_AT IS NULL AND
                pv.VAR_DELETED_AT IS NULL
            GROUP BY 
                p.PROD_ID
            ORDER BY 
                p.PROD_NAME
        ");
    }
    
    // Get product with full details (variants, features, specs)
    public function getProductWithDetails($productId)
    {
        $product = $this->findById($productId);
        
        if (!$product) {
            return null;
        }
        
        // Get variants
        $variants = $this->query("
            SELECT * FROM PRODUCT_VARIANT 
            WHERE PROD_ID = :prod_id 
            AND VAR_DELETED_AT IS NULL", 
            ['prod_id' => $productId]
        );
        
        // Get features
        $features = $this->query("
            SELECT * FROM PRODUCT_FEATURE 
            WHERE PROD_ID = :prod_id 
            AND FEATURE_DELETED_AT IS NULL", 
            ['prod_id' => $productId]
        );
        
        // Get specs
        $specs = $this->query("
            SELECT * FROM PRODUCT_SPEC 
            WHERE PROD_ID = :prod_id 
            AND SPEC_DELETED_AT IS NULL", 
            ['prod_id' => $productId]
        );
        
        // Build combined result
        $product['variants'] = $variants;
        $product['features'] = $features;
        $product['specs'] = $specs;
        
        return $product;
    }
    
    // Create a new product with variants, features, and specs
    public function createProduct($productData, $variants = [], $features = [], $specs = [])
    {
        $this->beginTransaction();
        
        try {
            // Add timestamps
            $productData['PROD_CREATED_AT'] = date('Y-m-d H:i:s');
            $productData['PROD_UPDATED_AT'] = date('Y-m-d H:i:s');
            
            // Format the insert data
            $formatData = $this->formatInsertData($productData);
            
            // Insert product
            $sql = "INSERT INTO {$this->table} ({$formatData['columns']}) VALUES ({$formatData['placeholders']})";
            $this->execute($sql, $formatData['filteredData']);
            
            $productId = $this->lastInsertId();
            
            // Insert variants
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $variant['PROD_ID'] = $productId;
                    $variant['VAR_CREATED_AT'] = date('Y-m-d H:i:s');
                    $variant['VAR_UPDATED_AT'] = date('Y-m-d H:i:s');
                    
                    $variantFormat = $this->formatInsertData($variant);
                    $sql = "INSERT INTO PRODUCT_VARIANT ({$variantFormat['columns']}) VALUES ({$variantFormat['placeholders']})";
                    $this->execute($sql, $variantFormat['filteredData']);
                }
            }
            
            // Insert features
            if (!empty($features)) {
                foreach ($features as $feature) {
                    $featureData = [
                        'PROD_ID' => $productId,
                        'FEATURE_NAME' => $feature,
                        'FEATURE_CREATED_AT' => date('Y-m-d H:i:s'),
                        'FEATURE_UPDATED_AT' => date('Y-m-d H:i:s')
                    ];
                    
                    $featureFormat = $this->formatInsertData($featureData);
                    $sql = "INSERT INTO PRODUCT_FEATURE ({$featureFormat['columns']}) VALUES ({$featureFormat['placeholders']})";
                    $this->execute($sql, $featureFormat['filteredData']);
                }
            }
            
            // Insert specs
            if (!empty($specs)) {
                foreach ($specs as $spec) {
                    $spec['PROD_ID'] = $productId;
                    $spec['SPEC_CREATED_AT'] = date('Y-m-d H:i:s');
                    $spec['SPEC_UPDATED_AT'] = date('Y-m-d H:i:s');
                    
                    $specFormat = $this->formatInsertData($spec);
                    $sql = "INSERT INTO PRODUCT_SPEC ({$specFormat['columns']}) VALUES ({$specFormat['placeholders']})";
                    $this->execute($sql, $specFormat['filteredData']);
                }
            }
            
            $this->commit();
            return $productId;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    // Update product with variants, features, and specs
    public function updateProduct($productId, $productData, $variants = null, $features = null, $specs = null)
    {
        $this->beginTransaction();
        
        try {
            // Add updated timestamp
            $productData['PROD_UPDATED_AT'] = date('Y-m-d H:i:s');
            
            // Format the update data
            $formatData = $this->formatUpdateData($productData);
            
            // Update product
            $sql = "UPDATE {$this->table} SET {$formatData['updateClause']} WHERE {$this->primaryKey} = :id";
            $params = array_merge($formatData['filteredData'], ['id' => $productId]);
            $this->execute($sql, $params);
            
            // Handle variants
            if ($variants !== null) {
                // Soft delete existing variants
                $this->execute(
                    "UPDATE PRODUCT_VARIANT SET VAR_DELETED_AT = :now WHERE PROD_ID = :prod_id",
                    ['now' => date('Y-m-d H:i:s'), 'prod_id' => $productId]
                );
                
                // Insert new variants
                foreach ($variants as $variant) {
                    $variant['PROD_ID'] = $productId;
                    $variant['VAR_CREATED_AT'] = date('Y-m-d H:i:s');
                    $variant['VAR_UPDATED_AT'] = date('Y-m-d H:i:s');
                    
                    $variantFormat = $this->formatInsertData($variant);
                    $sql = "INSERT INTO PRODUCT_VARIANT ({$variantFormat['columns']}) VALUES ({$variantFormat['placeholders']})";
                    $this->execute($sql, $variantFormat['filteredData']);
                }
            }
            
            // Handle features
            if ($features !== null) {
                // Soft delete existing features
                $this->execute(
                    "UPDATE PRODUCT_FEATURE SET FEATURE_DELETED_AT = :now WHERE PROD_ID = :prod_id",
                    ['now' => date('Y-m-d H:i:s'), 'prod_id' => $productId]
                );
                
                // Insert new features
                foreach ($features as $feature) {
                    $featureData = [
                        'PROD_ID' => $productId,
                        'FEATURE_NAME' => $feature,
                        'FEATURE_CREATED_AT' => date('Y-m-d H:i:s'),
                        'FEATURE_UPDATED_AT' => date('Y-m-d H:i:s')
                    ];
                    
                    $featureFormat = $this->formatInsertData($featureData);
                    $sql = "INSERT INTO PRODUCT_FEATURE ({$featureFormat['columns']}) VALUES ({$featureFormat['placeholders']})";
                    $this->execute($sql, $featureFormat['filteredData']);
                }
            }
            
            // Handle specs
            if ($specs !== null) {
                // Soft delete existing specs
                $this->execute(
                    "UPDATE PRODUCT_SPEC SET SPEC_DELETED_AT = :now WHERE PROD_ID = :prod_id",
                    ['now' => date('Y-m-d H:i:s'), 'prod_id' => $productId]
                );
                
                // Insert new specs
                foreach ($specs as $spec) {
                    $spec['PROD_ID'] = $productId;
                    $spec['SPEC_CREATED_AT'] = date('Y-m-d H:i:s');
                    $spec['SPEC_UPDATED_AT'] = date('Y-m-d H:i:s');
                    
                    $specFormat = $this->formatInsertData($spec);
                    $sql = "INSERT INTO PRODUCT_SPEC ({$specFormat['columns']}) VALUES ({$specFormat['placeholders']})";
                    $this->execute($sql, $specFormat['filteredData']);
                }
            }
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    // Delete product and related data
    public function deleteProduct($productId)
    {
        $this->beginTransaction();
        
        try {
            // Soft delete product (set deletion timestamp)
            $this->execute(
                "UPDATE {$this->table} SET PROD_DELETED_AT = :now WHERE {$this->primaryKey} = :id",
                ['now' => date('Y-m-d H:i:s'), 'id' => $productId]
            );
            
            // Soft delete variants
            $this->execute(
                "UPDATE PRODUCT_VARIANT SET VAR_DELETED_AT = :now WHERE PROD_ID = :prod_id",
                ['now' => date('Y-m-d H:i:s'), 'prod_id' => $productId]
            );
            
            // Soft delete features
            $this->execute(
                "UPDATE PRODUCT_FEATURE SET FEATURE_DELETED_AT = :now WHERE PROD_ID = :prod_id",
                ['now' => date('Y-m-d H:i:s'), 'prod_id' => $productId]
            );
            
            // Soft delete specs
            $this->execute(
                "UPDATE PRODUCT_SPEC SET SPEC_DELETED_AT = :now WHERE PROD_ID = :prod_id",
                ['now' => date('Y-m-d H:i:s'), 'prod_id' => $productId]
            );
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    // Update product availability status
    public function updateAvailabilityStatus($productId, $status)
    {
        return $this->execute(
            "UPDATE {$this->table} SET PROD_AVAILABILITY_STATUS = :status, PROD_UPDATED_AT = :now 
             WHERE {$this->primaryKey} = :id",
            ['status' => $status, 'now' => date('Y-m-d H:i:s'), 'id' => $productId]
        );
    }
    
    // Search products
    public function searchProducts($searchTerm)
    {
        return $this->query("
            SELECT 
                p.*,
                COUNT(pv.VAR_ID) AS variant_count
            FROM 
                {$this->table} p
            LEFT JOIN 
                PRODUCT_VARIANT pv ON p.PROD_ID = pv.PROD_ID AND pv.VAR_DELETED_AT IS NULL
            WHERE 
                (p.PROD_NAME LIKE :search_term OR
                p.PROD_DESCRIPTION LIKE :search_term) AND
                p.PROD_DELETED_AT IS NULL
            GROUP BY 
                p.PROD_ID
            ORDER BY 
                p.PROD_NAME
        ", ['search_term' => "%$searchTerm%"]);
    }
}