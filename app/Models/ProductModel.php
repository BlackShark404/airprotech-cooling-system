<?php

namespace App\Models;

class ProductModel extends BaseModel
{
    // Table name
    protected $table = 'product';
    protected $primaryKey = 'prod_id';
    
    // Fillable fields
    protected $fillable = [
        'prod_name',
        'prod_description',
        'prod_image',
        'prod_availability_status'
    ];
    
    // Get all products
    public function getAllProducts()
    {
        return $this->all();
    }
    
    // Find product by ID
    public function findById($id)
    {
        return $this->find($id);
    }
    
    // Get products with variants
    public function getProductsWithVariants()
    {
        return $this->db->query("
            SELECT 
                p.*,
                COUNT(pv.var_id) AS variant_count
            FROM 
                product p
            LEFT JOIN 
                product_variant pv ON p.prod_id = pv.prod_id
            GROUP BY 
                p.prod_id
            ORDER BY 
                p.prod_name
        ")->fetchAll();
    }
    
    // Get product with full details (variants, features, specs)
    public function getProductWithDetails($productId)
    {
        $product = $this->findById($productId);
        
        if (!$product) {
            return null;
        }
        
        // Get variants
        $variants = $this->db->query("
            SELECT * FROM product_variant WHERE prod_id = :prod_id
        ", ['prod_id' => $productId])->fetchAll();
        
        // Get features
        $features = $this->db->query("
            SELECT * FROM product_feature WHERE prod_id = :prod_id
        ", ['prod_id' => $productId])->fetchAll();
        
        // Get specs
        $specs = $this->db->query("
            SELECT * FROM product_spec WHERE prod_id = :prod_id
        ", ['prod_id' => $productId])->fetchAll();
        
        // Build combined result
        $product['variants'] = $variants;
        $product['features'] = $features;
        $product['specs'] = $specs;
        
        return $product;
    }
    
    // Create a new product with variants, features, and specs
    public function createProduct($productData, $variants = [], $features = [], $specs = [])
    {
        $this->db->beginTransaction();
        
        try {
            // Insert product
            $this->insert($productData);
            $productId = $this->db->lastInsertId();
            
            // Insert variants
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $variant['prod_id'] = $productId;
                    $stmt = $this->db->prepare(
                        "INSERT INTO product_variant (prod_id, var_capacity, var_srp_price, var_price_free_install, var_price_with_install, var_power_consumption) 
                        VALUES (:prod_id, :var_capacity, :var_srp_price, :var_price_free_install, :var_price_with_install, :var_power_consumption)"
                    );
                    $stmt->execute($variant);
                }
            }
            
            // Insert features
            if (!empty($features)) {
                foreach ($features as $feature) {
                    $stmt = $this->db->prepare(
                        "INSERT INTO product_feature (prod_id, feature_name) VALUES (:prod_id, :feature_name)"
                    );
                    $stmt->execute([
                        'prod_id' => $productId, 
                        'feature_name' => $feature
                    ]);
                }
            }
            
            // Insert specs
            if (!empty($specs)) {
                foreach ($specs as $spec) {
                    $spec['prod_id'] = $productId;
                    $stmt = $this->db->prepare(
                        "INSERT INTO product_spec (prod_id, spec_name, spec_value) VALUES (:prod_id, :spec_name, :spec_value)"
                    );
                    $stmt->execute($spec);
                }
            }
            
            $this->db->commit();
            return $productId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Update product with variants, features, and specs
    public function updateProduct($productId, $productData, $variants = null, $features = null, $specs = null)
    {
        $this->db->beginTransaction();
        
        try {
            // Update product
            $this->update($productData, "{$this->primaryKey} = :id", ['id' => $productId]);
            
            // Handle variants
            if ($variants !== null) {
                // Delete existing variants
                $stmt = $this->db->prepare("DELETE FROM product_variant WHERE prod_id = :prod_id");
                $stmt->execute(['prod_id' => $productId]);
                
                // Insert new variants
                foreach ($variants as $variant) {
                    $variant['prod_id'] = $productId;
                    $stmt = $this->db->prepare(
                        "INSERT INTO product_variant (prod_id, var_capacity, var_srp_price, var_price_free_install, var_price_with_install, var_power_consumption) 
                        VALUES (:prod_id, :var_capacity, :var_srp_price, :var_price_free_install, :var_price_with_install, :var_power_consumption)"
                    );
                    $stmt->execute($variant);
                }
            }
            
            // Handle features
            if ($features !== null) {
                // Delete existing features
                $stmt = $this->db->prepare("DELETE FROM product_feature WHERE prod_id = :prod_id");
                $stmt->execute(['prod_id' => $productId]);
                
                // Insert new features
                foreach ($features as $feature) {
                    $stmt = $this->db->prepare(
                        "INSERT INTO product_feature (prod_id, feature_name) VALUES (:prod_id, :feature_name)"
                    );
                    $stmt->execute([
                        'prod_id' => $productId, 
                        'feature_name' => $feature
                    ]);
                }
            }
            
            // Handle specs
            if ($specs !== null) {
                // Delete existing specs
                $stmt = $this->db->prepare("DELETE FROM product_spec WHERE prod_id = :prod_id");
                $stmt->execute(['prod_id' => $productId]);
                
                // Insert new specs
                foreach ($specs as $spec) {
                    $spec['prod_id'] = $productId;
                    $stmt = $this->db->prepare(
                        "INSERT INTO product_spec (prod_id, spec_name, spec_value) VALUES (:prod_id, :spec_name, :spec_value)"
                    );
                    $stmt->execute($spec);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Delete product and related data
// Delete product and related data
    public function deleteProduct($productId)
    {
        $this->db->beginTransaction();
        
        try {
            // Delete from inventory
            $stmt = $this->db->prepare("DELETE FROM inventory WHERE prod_id = :prod_id");
            $stmt->execute(['prod_id' => $productId]);
            
            // Delete variants
            $stmt = $this->db->prepare("DELETE FROM product_variant WHERE prod_id = :prod_id");
            $stmt->execute(['prod_id' => $productId]);
            
            // Delete features
            $stmt = $this->db->prepare("DELETE FROM product_feature WHERE prod_id = :prod_id");
            $stmt->execute(['prod_id' => $productId]);
            
            // Delete specs
            $stmt = $this->db->prepare("DELETE FROM product_spec WHERE prod_id = :prod_id");
            $stmt->execute(['prod_id' => $productId]);
            
            // Delete product using raw DELETE query
            $stmt = $this->db->prepare("DELETE FROM product WHERE prod_id = :prod_id");
            $stmt->execute(['prod_id' => $productId]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Update product availability status
    public function updateAvailabilityStatus($productId, $status)
    {
        return $this->update(
            ['prod_availability_status' => $status],
            "{$this->primaryKey} = :id",
            ['id' => $productId]
        );
    }
    
    // Search products
    public function searchProducts($searchTerm)
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                COUNT(pv.var_id) AS variant_count
            FROM 
                product p
            LEFT JOIN 
                product_variant pv ON p.prod_id = pv.prod_id
            WHERE 
                p.prod_name LIKE :search_term OR
                p.prod_description LIKE :search_term
            GROUP BY 
                p.prod_id
            ORDER BY 
                p.prod_name
        ");
        $stmt->execute(['search_term' => "%$searchTerm%"]);
        return $stmt->fetchAll();
    }
}