<?php

namespace App\Controllers;

class WarehouseController extends BaseController
{
    protected $warehouseModel;
    protected $inventoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->warehouseModel = $this->loadModel('WarehouseModel');
        $this->inventoryModel = $this->loadModel('InventoryModel');
    }

    /**
     * Get all warehouses
     */
    public function getAllWarehouses()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $warehouses = $this->warehouseModel->getAllWarehouses();
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get warehouses with inventory stats
     */
    public function getWarehousesWithInventory()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $warehouses = $this->warehouseModel->getWarehousesWithInventory();
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get warehouse by ID
     */
    public function getWarehouse($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $warehouse = $this->warehouseModel->getWarehouseById($id);
            
            if (!$warehouse) {
                $this->jsonError('Warehouse not found', 404);
            }
            
            $this->jsonSuccess($warehouse);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Create a new warehouse
     */
    public function createWarehouse()
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $data = $this->getJsonInput();
            
            // Validate required fields
            if (empty($data['WHOUSE_NAME']) || empty($data['WHOUSE_LOCATION'])) {
                $this->jsonError('Missing required fields');
            }
            
            $warehouseId = $this->warehouseModel->createWarehouse($data);
            
            if ($warehouseId) {
                $this->jsonSuccess(['id' => $warehouseId], 'Warehouse created successfully');
            } else {
                $this->jsonError('Failed to create warehouse');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Update a warehouse
     */
    public function updateWarehouse($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $data = $this->getJsonInput();
            
            // Check if warehouse exists
            $existingWarehouse = $this->warehouseModel->getWarehouseById($id);
            if (!$existingWarehouse) {
                $this->jsonError('Warehouse not found', 404);
            }
            
            $result = $this->warehouseModel->updateWarehouse($id, $data);
            
            if ($result) {
                $this->jsonSuccess([], 'Warehouse updated successfully');
            } else {
                $this->jsonError('Failed to update warehouse');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Delete a warehouse
     */
    public function deleteWarehouse($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            // Check if warehouse exists
            $existingWarehouse = $this->warehouseModel->getWarehouseById($id);
            if (!$existingWarehouse) {
                $this->jsonError('Warehouse not found', 404);
            }
            
            $result = $this->warehouseModel->deleteWarehouse($id);
            
            if ($result) {
                $this->jsonSuccess([], 'Warehouse deleted successfully');
            } else {
                $this->jsonError('Failed to delete warehouse');
            }
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get warehouse utilization
     */
    public function getWarehouseUtilization($id)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $utilization = $this->warehouseModel->getWarehouseUtilization($id);
            
            if (!$utilization) {
                $this->jsonError('Warehouse not found', 404);
            }
            
            $this->jsonSuccess($utilization);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get warehouses with available space for a given quantity
     */
    public function getWarehousesWithAvailableSpace()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $quantity = $this->request('quantity', 1);
            $warehouses = $this->warehouseModel->getWarehousesWithAvailableSpace($quantity);
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Get product distribution across warehouses
     */
    public function getProductDistribution($productId)
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $distribution = $this->warehouseModel->getProductDistribution($productId);
            $this->jsonSuccess($distribution);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * Search warehouses by name or location
     */
    public function searchWarehouses()
    {
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
        }

        try {
            $term = $this->request('term', '');
            $warehouses = $this->warehouseModel->searchWarehouses($term);
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
        }
    }
}
