<?php

namespace App\Controllers;

class InventoryController extends BaseController
{
    protected $inventoryModel;
    protected $productModel;
    protected $warehouseModel;
    protected $productVariantModel;

    public function __construct()
    {
        parent::__construct();
        $this->inventoryModel = $this->loadModel('InventoryModel');
        $this->productModel = $this->loadModel('ProductModel');
        $this->warehouseModel = $this->loadModel('WarehouseModel');
        $this->productVariantModel = $this->loadModel('ProductVariantModel');
    }

    /**
     * Get all inventory items
     */
    public function getAllInventory()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $inventory = $this->inventoryModel->getAllInventory();
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get inventory for a specific product
     */
    public function getProductInventory($productId)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $inventory = $this->inventoryModel->getInventoryByProductId($productId);
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get inventory for a specific warehouse
     */
    public function getWarehouseInventory($warehouseId)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $inventory = $this->inventoryModel->getInventoryByWarehouseId($warehouseId);
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get all low stock inventory items
     */
    public function getLowStockInventory()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $inventory = $this->inventoryModel->getLowStockInventory();
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get inventory summary
     */
    public function getInventorySummary()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $summary = $this->inventoryModel->getInventorySummary();
            $this->jsonSuccess($summary);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Add inventory stock
     */
    public function addStock()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $data = $this->getJsonInput();
            
            if (empty($data['prod_id']) || empty($data['whouse_id']) || 
                empty($data['inve_type']) || !isset($data['quantity']) || $data['quantity'] <= 0) {
                $this->jsonError('Missing required fields');
            }

            $productId = $data['prod_id'];
            $warehouseId = $data['whouse_id'];
            $type = $data['inve_type'];
            $quantity = $data['quantity'];

            $result = $this->inventoryModel->updateProductQuantity($productId, $warehouseId, $type, $quantity);
            
            if ($result) {
                $this->jsonSuccess([], 'Stock updated successfully');
            } else {
                $this->jsonError('Failed to update stock');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Move inventory between warehouses
     */
    public function moveStock()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $data = $this->getJsonInput();
            
            if (empty($data['prod_id']) || empty($data['source_whouse_id']) || 
                empty($data['dest_whouse_id']) || empty($data['inve_type']) || 
                !isset($data['quantity']) || $data['quantity'] <= 0) {
                $this->jsonError('Missing required fields');
            }

            $productId = $data['prod_id'];
            $sourceWarehouseId = $data['source_whouse_id'];
            $destWarehouseId = $data['dest_whouse_id'];
            $type = $data['inve_type'];
            $quantity = $data['quantity'];

            // Begin transaction
            $this->inventoryModel->beginTransaction();

            // Reduce stock from source warehouse
            $sourceResult = $this->inventoryModel->updateProductQuantity($productId, $sourceWarehouseId, $type, -$quantity);
            
            // Add stock to destination warehouse
            $destResult = $this->inventoryModel->updateProductQuantity($productId, $destWarehouseId, $type, $quantity);
            
            if ($sourceResult && $destResult) {
                $this->inventoryModel->commit();
                $this->jsonSuccess([], 'Stock moved successfully');
            } else {
                $this->inventoryModel->rollback();
                $this->jsonError('Failed to move stock');
            }
        } catch (\Exception $e) {
            $this->inventoryModel->rollback();
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStats()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            // Get product count
            $products = $this->productModel->getAllProducts();
            $productCount = count($products);

            // Get variant count
            $variants = $this->productVariantModel->getAllVariants();
            $variantCount = count($variants);

            // Get warehouse count
            $warehouses = $this->warehouseModel->getAllWarehouses();
            $warehouseCount = count($warehouses);

            // Get low stock count
            $lowStock = $this->inventoryModel->getLowStockInventory();
            $lowStockCount = count($lowStock);

            $stats = [
                'productCount' => $productCount,
                'variantCount' => $variantCount,
                'warehouseCount' => $warehouseCount,
                'lowStockCount' => $lowStockCount
            ];

            $this->jsonSuccess($stats);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }
}
