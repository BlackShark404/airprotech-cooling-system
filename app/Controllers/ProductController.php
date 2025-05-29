<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductFeatureModel;
use App\Models\ProductSpecModel;
use App\Models\ProductVariantModel;
use App\Models\InventoryModel;

class ProductController extends BaseController
{
    private $productModel;
    private $productFeatureModel;
    private $productSpecModel;
    private $productVariantModel;
    private $inventoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->productFeatureModel = new ProductFeatureModel();
        $this->productSpecModel = new ProductSpecModel();
        $this->productVariantModel = new ProductVariantModel();
        $this->inventoryModel = new InventoryModel();
    }

    public function renderProductManagement()
    {
        $this->render('admin/product-management');
    }

    public function getAllProducts()
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $products = $this->productModel->getAllProducts();
        $this->jsonSuccess($products);
    }

    public function getProduct($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $product = $this->productModel->getProductWithDetails($id);
        
        if (!$product) {
            $this->jsonError('Product not found', 404);
            return;
        }

        // Get inventory information
        $product['inventory'] = $this->inventoryModel->getProductInventory($id);
        
        $this->jsonSuccess($product);
    }

    public function createProduct()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        $data = $this->getJsonInput();
        
        // Validate required fields
        if (empty($data['product']['PROD_NAME']) || empty($data['product']['PROD_AVAILABILITY_STATUS'])) {
            $this->jsonError('Missing required product fields', 400);
            return;
        }
        
        // Start transaction
        $this->productModel->beginTransaction();
        
        try {
            // Create product
            $productId = $this->productModel->createProduct($data['product']);
            
            if (!$productId) {
                throw new \Exception("Failed to create product");
            }
            
            // Create features if provided
            if (!empty($data['features']) && is_array($data['features'])) {
                foreach ($data['features'] as $feature) {
                    $feature['PROD_ID'] = $productId;
                    $this->productFeatureModel->createFeature($feature);
                }
            }
            
            // Create specs if provided
            if (!empty($data['specs']) && is_array($data['specs'])) {
                foreach ($data['specs'] as $spec) {
                    $spec['PROD_ID'] = $productId;
                    $this->productSpecModel->createSpec($spec);
                }
            }
            
            // Create variants if provided
            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variant) {
                    $variant['PROD_ID'] = $productId;
                    $this->productVariantModel->createVariant($variant);
                }
            }
            
            // Add inventory if provided
            if (!empty($data['inventory']) && is_array($data['inventory'])) {
                foreach ($data['inventory'] as $inventory) {
                    $inventory['PROD_ID'] = $productId;
                    $this->inventoryModel->createInventory($inventory);
                }
            }
            
            // Commit the transaction
            $this->productModel->commit();
            
            $this->jsonSuccess(['product_id' => $productId], 'Product created successfully');
            
        } catch (\Exception $e) {
            $this->productModel->rollback();
            error_log("Error creating product: " . $e->getMessage());
            $this->jsonError('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    public function updateProduct($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        $data = $this->getJsonInput();
        
        // Check if product exists
        $existingProduct = $this->productModel->getProductById($id);
        if (!$existingProduct) {
            $this->jsonError('Product not found', 404);
            return;
        }
        
        // Start transaction
        $this->productModel->beginTransaction();
        
        try {
            // Update product
            if (!empty($data['product'])) {
                $this->productModel->updateProduct($id, $data['product']);
            }
            
            // Update features
            if (isset($data['features'])) {
                // Delete existing features and add new ones
                $this->productFeatureModel->deleteFeaturesByProductId($id);
                
                if (!empty($data['features']) && is_array($data['features'])) {
                    foreach ($data['features'] as $feature) {
                        $feature['PROD_ID'] = $id;
                        $this->productFeatureModel->createFeature($feature);
                    }
                }
            }
            
            // Update specs
            if (isset($data['specs'])) {
                // Delete existing specs and add new ones
                $this->productSpecModel->deleteSpecsByProductId($id);
                
                if (!empty($data['specs']) && is_array($data['specs'])) {
                    foreach ($data['specs'] as $spec) {
                        $spec['PROD_ID'] = $id;
                        $this->productSpecModel->createSpec($spec);
                    }
                }
            }
            
            // Update variants
            if (isset($data['variants'])) {
                // Delete existing variants and add new ones
                $this->productVariantModel->deleteVariantsByProductId($id);
                
                if (!empty($data['variants']) && is_array($data['variants'])) {
                    foreach ($data['variants'] as $variant) {
                        $variant['PROD_ID'] = $id;
                        $this->productVariantModel->createVariant($variant);
                    }
                }
            }
            
            // Commit the transaction
            $this->productModel->commit();
            
            $this->jsonSuccess(['product_id' => $id], 'Product updated successfully');
            
        } catch (\Exception $e) {
            $this->productModel->rollback();
            error_log("Error updating product: " . $e->getMessage());
            $this->jsonError('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    public function deleteProduct($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        // Check if product exists
        $existingProduct = $this->productModel->getProductById($id);
        if (!$existingProduct) {
            $this->jsonError('Product not found', 404);
            return;
        }
        
        // Delete the product (soft delete)
        $result = $this->productModel->deleteProduct($id);
        
        if ($result) {
            $this->jsonSuccess(null, 'Product deleted successfully');
        } else {
            $this->jsonError('Failed to delete product', 500);
        }
    }

    public function getProductFeatures($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $features = $this->productFeatureModel->getFeaturesByProductId($id);
        $this->jsonSuccess($features);
    }

    public function getProductSpecs($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $specs = $this->productSpecModel->getSpecsByProductId($id);
        $this->jsonSuccess($specs);
    }

    public function getProductVariants($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        $variants = $this->productVariantModel->getVariantsByProductId($id);
        $this->jsonSuccess($variants);
    }

    public function getProductSummary()
    {
        if (!$this->isAjax()) {
            $this->renderError('Bad Request', 400);
            return;
        }

        // Get all products
        $products = $this->productModel->getAllProducts();
        
        // Calculate summary statistics
        $totalProducts = count($products);
        $availableProducts = 0;
        $outOfStock = 0;
        $totalVariants = 0;
        
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
                $variants = $this->productVariantModel->getVariantsByProductId($product['PROD_ID']);
                $totalVariants += count($variants);
            }
        }
        
        $summary = [
            'total_products' => $totalProducts,
            'available_products' => $availableProducts,
            'out_of_stock' => $outOfStock,
            'total_variants' => $totalVariants
        ];
        
        $this->jsonSuccess($summary);
    }
} 