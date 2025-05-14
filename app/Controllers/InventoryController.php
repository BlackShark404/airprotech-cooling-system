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
    
    // Display the inventory management page
    public function index()
    {
        // Get stats for dashboard
        $stats = [
            'total_products' => $this->productModel->count(),
            'total_variants' => $this->productVariantModel->count(),
            'total_warehouses' => $this->warehouseModel->count(),
            'low_stock_count' => count($this->inventoryModel->getLowStockProducts())
        ];
        
        $this->render('admin/inventory', [
            'stats' => $stats
        ]);
    }
    
    // API Endpoint: Get all inventory items
    public function getAllInventory()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $inventory = $this->inventoryModel->getAllInventory();
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching inventory data: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get inventory by product ID
    public function getInventoryByProduct($productId)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $inventory = $this->inventoryModel->getInventoryByProduct($productId);
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching inventory data: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get inventory by warehouse ID
    public function getInventoryByWarehouse($warehouseId)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $inventory = $this->inventoryModel->getInventoryByWarehouse($warehouseId);
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching inventory data: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get inventory by type
    public function getInventoryByType($type)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $inventory = $this->inventoryModel->getInventoryByType($type);
            $this->jsonSuccess($inventory);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching inventory data: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Add stock
    public function addStock()
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request method', 400);
        }
        
        // Get form data
        $data = $this->getJsonInput();
        
        // Validate required fields
        $requiredFields = ['prod_id', 'whouse_id', 'inve_type', 'quantity'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->jsonError("Missing required field: $field", 400);
            }
        }
        
        // Validate quantity
        if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            $this->jsonError('Quantity must be a positive number', 400);
        }
        
        try {
            // Add stock
            $result = $this->inventoryModel->addStock(
                $data['prod_id'],
                $data['whouse_id'],
                $data['inve_type'],
                $data['quantity']
            );
            
            if ($result) {
                // Log the stock addition
                $this->logInventoryChange(
                    $data['prod_id'],
                    $data['whouse_id'],
                    $data['inve_type'],
                    'Added',
                    $data['quantity'],
                    $data['reason'] ?? '',
                    $data['notes'] ?? ''
                );
                
                $this->jsonSuccess([], 'Stock added successfully');
            } else {
                $this->jsonError('Failed to add stock', 500);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error adding stock: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Move stock
    public function moveStock()
    {
        // Check if request is AJAX and POST
        if (!$this->isAjax() || !$this->isPost()) {
            $this->jsonError('Invalid request method', 400);
        }
        
        // Get form data
        $data = $this->getJsonInput();
        
        // Validate required fields
        $requiredFields = ['prod_id', 'source_warehouse_id', 'destination_warehouse_id', 'inve_type', 'quantity'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->jsonError("Missing required field: $field", 400);
            }
        }
        
        // Validate quantity
        if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            $this->jsonError('Quantity must be a positive number', 400);
        }
        
        // Check if source and destination warehouses are different
        if ($data['source_warehouse_id'] == $data['destination_warehouse_id']) {
            $this->jsonError('Source and destination warehouses must be different', 400);
        }
        
        try {
            // Move stock
            $result = $this->inventoryModel->moveStock(
                $data['prod_id'],
                $data['source_warehouse_id'],
                $data['destination_warehouse_id'],
                $data['inve_type'],
                $data['quantity']
            );
            
            if ($result) {
                // Log the stock movement
                $this->logInventoryChange(
                    $data['prod_id'],
                    $data['source_warehouse_id'],
                    $data['inve_type'],
                    'Moved Out',
                    -$data['quantity'],
                    'Stock Transfer',
                    $data['notes'] ?? ''
                );
                
                $this->logInventoryChange(
                    $data['prod_id'],
                    $data['destination_warehouse_id'],
                    $data['inve_type'],
                    'Moved In',
                    $data['quantity'],
                    'Stock Transfer',
                    $data['notes'] ?? ''
                );
                
                $this->jsonSuccess([], 'Stock moved successfully');
            } else {
                $this->jsonError('Failed to move stock. Check if source has enough quantity.', 400);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error moving stock: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get low stock products
    public function getLowStockProducts()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $lowStockProducts = $this->inventoryModel->getLowStockProducts();
            $this->jsonSuccess($lowStockProducts);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching low stock products: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get inventory summary
    public function getInventorySummary()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $summary = $this->inventoryModel->getInventorySummary();
            $this->jsonSuccess($summary);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching inventory summary: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get warehouses
    public function getWarehouses()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $warehouses = $this->warehouseModel->getAllWarehouses();
            $this->jsonSuccess($warehouses);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching warehouses: ' . $e->getMessage(), 500);
        }
    }
    
    // API Endpoint: Get products with variants
    public function getProductsWithVariants()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        try {
            $products = $this->productModel->getProductsWithVariants();
            $this->jsonSuccess($products);
        } catch (\Exception $e) {
            $this->jsonError('Error fetching products: ' . $e->getMessage(), 500);
        }
    }
    
    // Helper method: Log inventory changes to inventory_log table
    private function logInventoryChange($productId, $warehouseId, $type, $action, $quantity, $reason, $notes)
    {
        // Check if inventory_log model exists, if not create simple log
        if (class_exists('App\\Models\\InventoryLogModel')) {
            $logModel = $this->loadModel('InventoryLogModel');
            $logModel->insert([
                'prod_id' => $productId,
                'whouse_id' => $warehouseId,
                'inve_type' => $type,
                'log_action' => $action,
                'log_quantity' => $quantity,
                'log_reason' => $reason,
                'log_notes' => $notes,
                'log_user_id' => $_SESSION['user_id'] ?? null,
                'log_timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Fallback logging to system log
            error_log("Inventory Change: Product ID $productId, Warehouse ID $warehouseId, Type $type, Action $action, Quantity $quantity, Reason $reason");
        }
    }
    
    // View: Product Details with inventory
    public function viewProduct($productId)
    {
        try {
            $product = $this->productModel->findById($productId);
            
            if (!$product) {
                $this->renderError('Product not found', 404);
                return;
            }
            
            $variants = $this->productVariantModel->getVariantsByProduct($productId);
            $inventory = $this->inventoryModel->getInventoryByProduct($productId);
            
            if ($this->isAjax()) {
                $this->jsonSuccess([
                    'product' => $product,
                    'variants' => $variants,
                    'inventory' => $inventory
                ]);
            } else {
                $this->render('admin/inventory/product-details', [
                    'product' => $product,
                    'variants' => $variants,
                    'inventory' => $inventory
                ]);
            }
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->jsonError('Error fetching product details: ' . $e->getMessage(), 500);
            } else {
                $this->renderError('Error fetching product details: ' . $e->getMessage(), 500);
            }
        }
    }
    
    // Import inventory from CSV
    public function importInventory()
    {
        // Check if request is POST
        if (!$this->isPost()) {
            $this->renderError('Invalid request method', 400);
            return;
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['inventory_file']) || $_FILES['inventory_file']['error'] !== UPLOAD_ERR_OK) {
            if ($this->isAjax()) {
                $this->jsonError('No file uploaded or upload error', 400);
            } else {
                $this->renderError('No file uploaded or upload error', 400);
            }
            return;
        }
        
        $file = $_FILES['inventory_file'];
        
        // Check file type
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($fileType !== 'csv') {
            if ($this->isAjax()) {
                $this->jsonError('Only CSV files are allowed', 400);
            } else {
                $this->renderError('Only CSV files are allowed', 400);
            }
            return;
        }
        
        try {
            // Process the CSV file
            $handle = fopen($file['tmp_name'], 'r');
            
            // Read header row
            $header = fgetcsv($handle);
            
            // Map expected columns
            $expectedColumns = ['product_id', 'warehouse_id', 'type', 'quantity'];
            $columnMap = [];
            
            foreach ($expectedColumns as $column) {
                $index = array_search($column, $header);
                if ($index === false) {
                    throw new \Exception("Required column '$column' not found in CSV");
                }
                $columnMap[$column] = $index;
            }
            
            // Process data rows
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            $this->db->beginTransaction();
            
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    $productId = $row[$columnMap['product_id']];
                    $warehouseId = $row[$columnMap['warehouse_id']];
                    $type = $row[$columnMap['type']];
                    $quantity = $row[$columnMap['quantity']];
                    
                    // Validate data
                    if (!is_numeric($productId) || !is_numeric($warehouseId) || !is_numeric($quantity)) {
                        throw new \Exception("Invalid data types in row");
                    }
                    
                    // Add inventory
                    $result = $this->inventoryModel->addStock($productId, $warehouseId, $type, $quantity);
                    
                    if ($result) {
                        $successCount++;
                    } else {
                        throw new \Exception("Failed to add inventory");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($successCount + $errorCount) . ": " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            if ($errorCount === 0) {
                $this->db->commit();
                if ($this->isAjax()) {
                    $this->jsonSuccess(['count' => $successCount], "Successfully imported $successCount inventory records");
                } else {
                    $this->redirect('/inventory?success=import&count=' . $successCount);
                }
            } else {
                $this->db->rollBack();
                if ($this->isAjax()) {
                    $this->jsonError("Import completed with errors: $errorCount errors, $successCount successes", 400, ['errors' => $errors]);
                } else {
                    $_SESSION['import_errors'] = $errors;
                    $this->redirect('/inventory?error=import&count=' . $errorCount);
                }
            }
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            if ($this->isAjax()) {
                $this->jsonError('Error importing inventory: ' . $e->getMessage(), 500);
            } else {
                $this->renderError('Error importing inventory: ' . $e->getMessage(), 500);
            }
        }
    }
    
    // Export inventory to CSV
    public function exportInventory()
    {
        try {
            // Get all inventory data
            $inventory = $this->inventoryModel->getAllInventory();
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="inventory_export_' . date('Y-m-d') . '.csv"');
            
            // Open output stream
            $output = fopen('php://output', 'w');
            
            // Add header row
            fputcsv($output, ['Product ID', 'Product Name', 'Variant', 'Warehouse', 'Type', 'Quantity', 'Last Updated']);
            
            // Add data rows
            foreach ($inventory as $item) {
                fputcsv($output, [
                    $item['prod_id'],
                    $item['prod_name'],
                    $item['var_capacity'],
                    $item['whouse_name'],
                    $item['inve_type'],
                    $item['quantity'],
                    $item['last_updated']
                ]);
            }
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->jsonError('Error exporting inventory: ' . $e->getMessage(), 500);
            } else {
                $this->renderError('Error exporting inventory: ' . $e->getMessage(), 500);
            }
        }
    }
    
    /**
     * API Endpoint: Get statistics for dashboard
     */
    public function getStats()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
            return;
        }
        
        try {
            // Use more robust ways to count records instead of relying on a count() method
            $stats = [
                'total_products' => $this->countProducts(),
                'total_variants' => $this->countVariants(),
                'total_warehouses' => $this->countWarehouses(),
                'low_stock_count' => count($this->inventoryModel->getLowStockProducts() ?: [])
            ];
            
            $this->jsonSuccess($stats);
        } catch (\Exception $e) {
            error_log("Error in getStats: " . $e->getMessage());
            $this->jsonError('Error fetching statistics: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Count products
     */
    private function countProducts()
    {
        try {
            $result = $this->productModel->query("SELECT COUNT(*) as count FROM PRODUCT WHERE PROD_DELETED_AT IS NULL");
            return isset($result[0]['count']) ? (int)$result[0]['count'] : 0;
        } catch (\Exception $e) {
            error_log("Error counting products: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Count variants
     */
    private function countVariants()
    {
        try {
            $result = $this->productVariantModel->query("SELECT COUNT(*) as count FROM PRODUCT_VARIANT WHERE VAR_DELETED_AT IS NULL");
            return isset($result[0]['count']) ? (int)$result[0]['count'] : 0;
        } catch (\Exception $e) {
            error_log("Error counting variants: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Count warehouses
     */
    private function countWarehouses()
    {
        try {
            $result = $this->warehouseModel->query("SELECT COUNT(*) as count FROM WAREHOUSE WHERE WHOUSE_DELETED_AT IS NULL");
            return isset($result[0]['count']) ? (int)$result[0]['count'] : 0;
        } catch (\Exception $e) {
            error_log("Error counting warehouses: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Create a new product with variants, features, and specs
     */
    public function createProduct()
    {
        // Check if request is POST
        if (!$this->isPost()) {
            if ($this->isAjax()) {
                $this->jsonError('Invalid request method', 400);
            } else {
                $this->renderError('Invalid request method', 400);
            }
            return;
        }
        
        try {
            // Get product data
            $productData = [
                'prod_name' => $_POST['prod_name'] ?? '',
                'prod_description' => $_POST['prod_description'] ?? '',
                'prod_availability_status' => $_POST['prod_availability_status'] ?? 'Available'
            ];
            
            // Validate required fields
            if (empty($productData['prod_name'])) {
                $this->jsonError('Product name is required', 400);
                return;
            }
            
            // Handle product image upload if present
            if (isset($_FILES['prod_image']) && $_FILES['prod_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate a unique filename
                $fileName = uniqid() . '_' . basename($_FILES['prod_image']['name']);
                $targetFilePath = $uploadDir . $fileName;
                
                // Upload the file
                if (move_uploaded_file($_FILES['prod_image']['tmp_name'], $targetFilePath)) {
                    $productData['prod_image'] = '/uploads/products/' . $fileName;
                }
            }
            
            // Get variants, features, and specs from JSON data
            $variants = json_decode($_POST['variants'] ?? '[]', true);
            $features = json_decode($_POST['features'] ?? '[]', true);
            $specs = json_decode($_POST['specs'] ?? '[]', true);
            
            // Create the product with all related data
            $productId = $this->productModel->createProduct($productData, $variants, $features, $specs);
            
            // Add initial inventory if provided
            if (isset($_POST['inventory']) && !empty($_POST['inventory'])) {
                $inventory = json_decode($_POST['inventory'], true);
                
                foreach ($inventory as $item) {
                    if (
                        isset($item['warehouse_id']) && 
                        isset($item['variant_id']) && 
                        isset($item['type']) && 
                        isset($item['quantity'])
                    ) {
                        $this->inventoryModel->addStock(
                            $productId,
                            $item['warehouse_id'],
                            $item['type'],
                            $item['quantity']
                        );
                    }
                }
            }
            
            $this->jsonSuccess(['product_id' => $productId], 'Product created successfully');
        } catch (\Exception $e) {
            $this->jsonError('Error creating product: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Update an existing product
     */
    public function updateProduct($productId)
    {
        // Check if request is PUT
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            if ($this->isAjax()) {
                $this->jsonError('Invalid request method', 400);
            } else {
                $this->renderError('Invalid request method', 400);
            }
            return;
        }
        
        try {
            // Get PUT data
            $putData = $this->getJsonInput();
            
            // Get product data
            $productData = [
                'prod_name' => $putData['prod_name'] ?? null,
                'prod_description' => $putData['prod_description'] ?? null,
                'prod_availability_status' => $putData['prod_availability_status'] ?? null
            ];
            
            // Remove null values
            $productData = array_filter($productData, function($value) {
                return $value !== null;
            });
            
            // Check if product exists
            $product = $this->productModel->findById($productId);
            if (!$product) {
                $this->jsonError('Product not found', 404);
                return;
            }
            
            // Handle image update if provided (base64)
            if (isset($putData['prod_image']) && !empty($putData['prod_image'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Process base64 image
                $imageData = $putData['prod_image'];
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                    $imageType = $matches[1];
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageData = base64_decode($imageData);
                    
                    $fileName = uniqid() . '.' . $imageType;
                    $targetFilePath = $uploadDir . $fileName;
                    
                    if (file_put_contents($targetFilePath, $imageData)) {
                        $productData['prod_image'] = '/uploads/products/' . $fileName;
                    }
                }
            }
            
            // Get variants, features, and specs
            $variants = $putData['variants'] ?? null;
            $features = $putData['features'] ?? null;
            $specs = $putData['specs'] ?? null;
            
            // Update the product
            $result = $this->productModel->updateProduct($productId, $productData, $variants, $features, $specs);
            
            if ($result) {
                $this->jsonSuccess([], 'Product updated successfully');
            } else {
                $this->jsonError('Failed to update product', 500);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error updating product: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Delete a product
     */
    public function deleteProduct($productId)
    {
        try {
            // Check if product exists
            $product = $this->productModel->findById($productId);
            if (!$product) {
                $this->jsonError('Product not found', 404);
                return;
            }
            
            // Delete the product (this will cascade to variants, features, specs, and inventory)
            $result = $this->productModel->deleteProduct($productId);
            
            if ($result) {
                $this->jsonSuccess([], 'Product deleted successfully');
            } else {
                $this->jsonError('Failed to delete product', 500);
            }
        } catch (\Exception $e) {
            $this->jsonError('Error deleting product: ' . $e->getMessage(), 500);
        }
    }

    
}