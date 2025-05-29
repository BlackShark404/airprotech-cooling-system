<?php

namespace App\Controllers;

class ProductController extends BaseController
{
    protected $productModel;
    protected $productVariantModel;
    protected $productFeatureModel;
    protected $productSpecModel;
    protected $inventoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = $this->loadModel('ProductModel');
        $this->productVariantModel = $this->loadModel('ProductVariantModel');
        $this->productFeatureModel = $this->loadModel('ProductFeatureModel');
        $this->productSpecModel = $this->loadModel('ProductSpecModel');
        $this->inventoryModel = $this->loadModel('InventoryModel');
    }

    /**
     * Get all products
     */
    public function getAllProducts()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $products = $this->productModel->getProductsWithDetails();
            $this->jsonSuccess($products);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get product by ID
     */
    public function getProduct($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $product = $this->productModel->getProductWithDetails($id);
            
            if (!$product) {
                $this->jsonError('Product not found', 404);
            }
            
            // Get variants
            $variants = $this->productVariantModel->getVariantsByProductId($id);
            $product['variants'] = $variants;
            
            // Get total stock
            $product['total_stock'] = $this->inventoryModel->getTotalProductStock($id);
            
            $this->jsonSuccess($product);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Create a new product
     */
    public function createProduct()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        $transactionStarted = false;
        
        try {
            $data = $this->getJsonInput();
            
            // Validate required fields
            if (empty($data['PROD_NAME']) || empty($data['PROD_IMAGE']) || empty($data['PROD_AVAILABILITY_STATUS'])) {
                $this->jsonError('Missing required fields');
            }
            
            // Begin transaction
            $this->productModel->beginTransaction();
            $transactionStarted = true;
            
            // Extract and store special data arrays
            $variants = $data['variants'] ?? [];
            $features = $data['features'] ?? [];
            $specs = $data['specs'] ?? [];
            $inventory = $data['inventory'] ?? [];
            
            // Store warehouse info for inventory
            $warehouseId = $data['WHOUSE_ID'] ?? null;
            $inventoryType = $data['INVE_TYPE'] ?? null;
            
            // Remove non-product fields from data
            unset($data['variants']);
            unset($data['features']);
            unset($data['specs']);
            unset($data['inventory']);
            unset($data['WHOUSE_ID']);  // Remove warehouse ID - not part of product table
            unset($data['INVE_TYPE']);  // Remove inventory type - not part of product table
            
            // Create product
            $productId = $this->productModel->createProduct($data);
            
            if (!$productId) {
                $this->productModel->rollback();
                $this->jsonError('Failed to create product');
            }
            
            // Process variants if provided
            if (!empty($variants) && is_array($variants)) {
                foreach ($variants as $variant) {
                    $variant['PROD_ID'] = $productId;
                    $this->productVariantModel->createVariant($variant);
                }
            }
            
            // Process features if provided
            if (!empty($features) && is_array($features)) {
                $this->productFeatureModel->addFeaturesToProduct($productId, $features);
            }
            
            // Process specifications if provided
            if (!empty($specs) && is_array($specs)) {
                $specData = [];
                foreach ($specs as $spec) {
                    if (!empty($spec['SPEC_NAME']) && !empty($spec['SPEC_VALUE'])) {
                        $specData[$spec['SPEC_NAME']] = $spec['SPEC_VALUE'];
                    }
                }
                if (!empty($specData)) {
                    $this->productSpecModel->addSpecsToProduct($productId, $specData);
                }
            }
            
            // Process initial inventory if provided
            if (!empty($inventory) && is_array($inventory) &&
                !empty($warehouseId) && !empty($inventoryType)) {
                
                foreach ($inventory as $inventoryItem) {
                    if (!empty($inventoryItem['quantity']) && $inventoryItem['quantity'] > 0) {
                        $this->inventoryModel->updateProductQuantity(
                            $productId,
                            $warehouseId,
                            $inventoryType,
                            $inventoryItem['quantity']
                        );
                    }
                }
            }
            
            $this->productModel->commit();
            $transactionStarted = false;
            $this->jsonSuccess(['id' => $productId], 'Product created successfully');
        } catch (\Exception $e) {
            error_log("Error creating product: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Only try to rollback if we successfully started a transaction
            if ($transactionStarted) {
                $this->productModel->rollback();
            }
            
            $this->jsonError('Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Update a product
     */
    public function updateProduct($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        $transactionStarted = false;
        
        try {
            $data = $this->getJsonInput();
            
            // Check if product exists
            $existingProduct = $this->productModel->getProductById($id);
            if (!$existingProduct) {
                $this->jsonError('Product not found', 404);
            }
            
            // Begin transaction
            $this->productModel->beginTransaction();
            $transactionStarted = true;
            
            // Extract and store special data arrays
            $variants = $data['variants'] ?? [];
            $features = isset($data['features']) ? $data['features'] : null;
            $specs = isset($data['specs']) ? $data['specs'] : null;
            $inventory = $data['inventory'] ?? [];
            
            // Store warehouse info for inventory
            $warehouseId = $data['WHOUSE_ID'] ?? null;
            $inventoryType = $data['INVE_TYPE'] ?? null;
            
            // Remove non-product fields from data
            unset($data['variants']);
            unset($data['features']);
            unset($data['specs']);
            unset($data['inventory']);
            unset($data['WHOUSE_ID']);  // Remove warehouse ID - not part of product table
            unset($data['INVE_TYPE']);  // Remove inventory type - not part of product table
            
            // Update product
            $result = $this->productModel->updateProduct($id, $data);
            
            if (!$result) {
                $this->productModel->rollback();
                $transactionStarted = false;
                $this->jsonError('Failed to update product');
            }
            
            // Update variants if provided
            if (!empty($variants) && is_array($variants)) {
                foreach ($variants as $variant) {
                    if (!empty($variant['VAR_ID'])) {
                        // Update existing variant
                        $this->productVariantModel->updateVariant($variant['VAR_ID'], $variant);
                    } else {
                        // Create new variant
                        $variant['PROD_ID'] = $id;
                        $this->productVariantModel->createVariant($variant);
                    }
                }
            }
            
            // Update features if provided
            if ($features !== null) {
                // First, delete existing features
                $this->productFeatureModel->deleteFeaturesByProductId($id);
                
                // Then add new features
                if (!empty($features) && is_array($features)) {
                    $this->productFeatureModel->addFeaturesToProduct($id, $features);
                }
            }
            
            // Update specifications if provided
            if ($specs !== null) {
                // First, delete existing specs
                $this->productSpecModel->deleteSpecsByProductId($id);
                
                // Then add new specs
                if (!empty($specs) && is_array($specs)) {
                    $specData = [];
                    foreach ($specs as $spec) {
                        if (!empty($spec['SPEC_NAME']) && !empty($spec['SPEC_VALUE'])) {
                            $specData[$spec['SPEC_NAME']] = $spec['SPEC_VALUE'];
                        }
                    }
                    if (!empty($specData)) {
                        $this->productSpecModel->addSpecsToProduct($id, $specData);
                    }
                }
            }
            
            // Process inventory if provided
            if (!empty($inventory) && is_array($inventory) &&
                !empty($warehouseId) && !empty($inventoryType)) {
                
                foreach ($inventory as $inventoryItem) {
                    if (!empty($inventoryItem['quantity']) && $inventoryItem['quantity'] > 0) {
                        $this->inventoryModel->updateProductQuantity(
                            $id,
                            $warehouseId,
                            $inventoryType,
                            $inventoryItem['quantity']
                        );
                    }
                }
            }
            
            $this->productModel->commit();
            $transactionStarted = false;
            $this->jsonSuccess([], 'Product updated successfully');
        } catch (\Exception $e) {
            error_log("Error updating product: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Only try to rollback if we successfully started a transaction
            if ($transactionStarted) {
                $this->productModel->rollback();
            }
            
            $this->jsonError('Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        $transactionStarted = false;
        
        try {
            // Check if product exists
            $existingProduct = $this->productModel->getProductById($id);
            if (!$existingProduct) {
                $this->jsonError('Product not found', 404);
            }
            
            // Begin transaction
            $this->productModel->beginTransaction();
            $transactionStarted = true;
            
            $result = $this->productModel->deleteProduct($id);
            
            if ($result) {
                $this->productModel->commit();
                $transactionStarted = false;
                $this->jsonSuccess([], 'Product deleted successfully');
            } else {
                $this->productModel->rollback();
                $transactionStarted = false;
                $this->jsonError('Failed to delete product');
            }
        } catch (\Exception $e) {
            error_log("Error deleting product: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Only try to rollback if we successfully started a transaction
            if ($transactionStarted) {
                $this->productModel->rollback();
            }
            
            $this->jsonError('Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Get product variants by product ID
     */
    public function getProductVariants($productId)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $variants = $this->productVariantModel->getVariantsByProductId($productId);
            $this->jsonSuccess($variants);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get product features by product ID
     */
    public function getProductFeatures($productId)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $features = $this->productFeatureModel->getFeaturesByProductId($productId);
            $this->jsonSuccess($features);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get product specifications by product ID
     */
    public function getProductSpecs($productId)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $specs = $this->productSpecModel->getSpecsByProductId($productId);
            $this->jsonSuccess($specs);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }
}
