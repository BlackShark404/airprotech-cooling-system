<?php

namespace App\Models;

class ProductModel extends Model
{
    protected $table = 'PRODUCT'; // Corresponds to the PRODUCT table in the database

    // Basic CRUD methods (can be expanded as needed)

    public function getAllProducts()
    {
        $sql = "SELECT * FROM {$this->table} WHERE PROD_DELETED_AT IS NULL ORDER BY PROD_CREATED_AT DESC";
        return $this->query($sql);
    }

    public function getProductById($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE PROD_ID = :product_id AND PROD_DELETED_AT IS NULL";
        return $this->queryOne($sql, [':product_id' => $productId]);
    }

    public function createProduct($data)
    {
        // Ensure all required fields are present
        $requiredFields = ['PROD_IMAGE', 'PROD_NAME', 'PROD_DESCRIPTION', 'PROD_AVAILABILITY_STATUS'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                // Handle missing field error, perhaps throw an exception or return false
                // For simplicity, returning false here
                error_log("Product creation failed: Missing field {$field}");
                return false;
            }
        }
        
        $sql = "INSERT INTO {$this->table} (PROD_IMAGE, PROD_NAME, PROD_DESCRIPTION, PROD_AVAILABILITY_STATUS, PROD_CREATED_AT, PROD_UPDATED_AT)
                VALUES (:prod_image, :prod_name, :prod_description, :prod_availability_status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $params = [
            ':prod_image' => $data['PROD_IMAGE'],
            ':prod_name' => $data['PROD_NAME'],
            ':prod_description' => $data['PROD_DESCRIPTION'],
            ':prod_availability_status' => $data['PROD_AVAILABILITY_STATUS'],
        ];
        
        $this->execute($sql, $params);
        return $this->lastInsertId('product_prod_id_seq'); // Adjust sequence name if different
    }

    public function updateProduct($productId, $data)
    {
        $setClauses = [];
        $params = [':product_id' => $productId];

        if (isset($data['PROD_IMAGE'])) {
            $setClauses[] = "PROD_IMAGE = :prod_image";
            $params[':prod_image'] = $data['PROD_IMAGE'];
        }
        if (isset($data['PROD_NAME'])) {
            $setClauses[] = "PROD_NAME = :prod_name";
            $params[':prod_name'] = $data['PROD_NAME'];
        }
        if (isset($data['PROD_DESCRIPTION'])) {
            $setClauses[] = "PROD_DESCRIPTION = :prod_description";
            $params[':prod_description'] = $data['PROD_DESCRIPTION'];
        }
        if (isset($data['PROD_AVAILABILITY_STATUS'])) {
            $setClauses[] = "PROD_AVAILABILITY_STATUS = :prod_availability_status";
            $params[':prod_availability_status'] = $data['PROD_AVAILABILITY_STATUS'];
        }

        if (empty($setClauses)) {
            return false; // No fields to update
        }

        $setClauses[] = "PROD_UPDATED_AT = CURRENT_TIMESTAMP";
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE PROD_ID = :product_id AND PROD_DELETED_AT IS NULL";
        
        return $this->execute($sql, $params);
    }

    public function deleteProduct($productId)
    {
        // Soft delete by setting PROD_DELETED_AT
        $sql = "UPDATE {$this->table} SET PROD_DELETED_AT = CURRENT_TIMESTAMP WHERE PROD_ID = :product_id";
        return $this->execute($sql, [':product_id' => $productId]);
    }

    // Methods for Product Features, Specs, and Variants will be in their respective models
    // However, you might want methods here to get a product with all its related data.

    public function getProductWithDetails($productId)
    {
        $product = $this->getProductById($productId);
        if (!$product) {
            return null;
        }

        $productFeatureModel = new ProductFeatureModel();
        $productSpecModel = new ProductSpecModel();
        $productVariantModel = new ProductVariantModel();

        $product['features'] = $productFeatureModel->getFeaturesByProductId($productId);
        $product['specs'] = $productSpecModel->getSpecsByProductId($productId);
        $product['variants'] = $productVariantModel->getVariantsByProductId($productId);

        return $product;
    }
    
    /**
     * Get summary statistics for products
     */
    public function getProductSummary()
    {
        // Get all products
        $products = $this->getAllProducts();
        
        // Calculate summary statistics
        $totalProducts = count($products);
        $availableProducts = 0;
        $outOfStock = 0;
        $totalVariants = 0;
        
        $productVariantModel = new ProductVariantModel();
        
        foreach ($products as $product) {
            if (isset($product['PROD_AVAILABILITY_STATUS'])) {
                if ($product['PROD_AVAILABILITY_STATUS'] === 'Available') {
                    $availableProducts++;
                } elseif ($product['PROD_AVAILABILITY_STATUS'] === 'Out of Stock') {
                    $outOfStock++;
                }
            }
            
            // Get variants count for this product
            if (isset($product['PROD_ID'])) {
                $variants = $productVariantModel->getVariantsByProductId($product['PROD_ID']);
                $totalVariants += count($variants);
            }
        }
        
        return [
            'total_products' => $totalProducts,
            'available_products' => $availableProducts,
            'out_of_stock' => $outOfStock,
            'total_variants' => $totalVariants
        ];
    }
} 