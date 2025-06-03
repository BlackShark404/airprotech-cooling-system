<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductFeatureModel;
use App\Models\ProductSpecModel;
use App\Models\ProductVariantModel;
use App\Models\ProductBookingModel;
use App\Models\InventoryModel;

class ProductController extends BaseController
{
    private $productModel;
    private $productFeatureModel;
    private $productSpecModel;
    private $productVariantModel;
    private $productBookingModel;
    private $inventoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->productFeatureModel = new ProductFeatureModel();
        $this->productSpecModel = new ProductSpecModel();
        $this->productVariantModel = new ProductVariantModel();
        $this->productBookingModel = new ProductBookingModel();
        $this->inventoryModel = new InventoryModel();
    }

    public function renderProductManagement()
    {
        $this->render('admin/product-management');
    }

    public function getAllProducts()
    {
        $products = $this->productModel->getAllProducts();
        $this->jsonSuccess($products);
    }

    public function getProduct($id)
    {
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
        if (!$this->isPost()) {
            $this->renderError('Bad Request: Expected POST', 400);
            return;
        }
        
        $payload = [];
        
        // 1. Get and decode the 'product' JSON string
        $productJson = $this->request('product');
        if (empty($productJson)) {
            $this->jsonError('Missing product data field.', 400);
            return;
        }
        $payload['product'] = json_decode($productJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError('Invalid product JSON data: ' . json_last_error_msg(), 400);
            return;
        }

        // 2. Handle 'product_image' file upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $_FILES['product_image'];
            $imageName = basename($uploadedImage['name']);
            $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($imageExt, $allowedExts)) {
                $this->jsonError('Invalid image file type. Allowed types: ' . implode(', ', $allowedExts), 400);
                return;
            }

            // Define upload path - relative to the public directory
            // Assumes your web root is 'public' and this controller is accessed via index.php in public
            $uploadDir = 'uploads/products/'; 
            $absoluteUploadPath = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $uploadDir;

            if (!is_dir($absoluteUploadPath)) {
                if (!mkdir($absoluteUploadPath, 0777, true)) {
                    error_log("Failed to create directory: " . $absoluteUploadPath);
                    $this->jsonError('Failed to create image upload directory.', 500);
                    return;
                }
            }
            
            // Generate a unique name to prevent overwrites
            $uniqueImageName = uniqid('prod_', true) . '.' . $imageExt;
            $targetPath = $absoluteUploadPath . $uniqueImageName;

            if (move_uploaded_file($uploadedImage['tmp_name'], $targetPath)) {
                $payload['product']['PROD_IMAGE'] = $uploadDir . $uniqueImageName; // Store relative path for DB
            } else {
                error_log("Failed to move uploaded file to: " . $targetPath);
                $this->jsonError('Failed to save product image.', 500);
                return;
            }
        } else {
            // If PROD_IMAGE is already set in the JSON (e.g., as a URL or base64 for update, or if optional)
            // Or if it's truly missing for a new product where it's required by DB.
            // The validation below will catch it if it's still empty and required.
            if (!isset($payload['product']['PROD_IMAGE'])) {
                 $payload['product']['PROD_IMAGE'] = null; 
            }
        }

        // 3. Get and decode 'features', 'specs', 'variants' JSON strings
        $featuresJson = $this->request('features');
        $payload['features'] = $featuresJson ? json_decode($featuresJson, true) : [];
        if ($featuresJson && json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError('Invalid features JSON data: ' . json_last_error_msg(), 400);
            return;
        }

        $specsJson = $this->request('specs');
        $payload['specs'] = $specsJson ? json_decode($specsJson, true) : [];
        if ($specsJson && json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError('Invalid specs JSON data: ' . json_last_error_msg(), 400);
            return;
        }

        $variantsJson = $this->request('variants');
        $payload['variants'] = $variantsJson ? json_decode($variantsJson, true) : [];
        if ($variantsJson && json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError('Invalid variants JSON data: ' . json_last_error_msg(), 400);
            return;
        }
        
        // Validate required product fields (PROD_IMAGE is now populated if uploaded)
        if (empty($payload['product']['PROD_NAME']) || empty($payload['product']['PROD_AVAILABILITY_STATUS']) || empty($payload['product']['PROD_IMAGE'])) {
            $this->jsonError('Missing required product fields: Name, Availability Status, or Image.', 400);
            return;
        }
        
        // Start transaction
        $this->productModel->beginTransaction();
        
        try {
            // Create product
            $productId = $this->productModel->createProduct($payload['product']);
            
            if (!$productId) {
                throw new \Exception("Failed to create product entry in database");
            }
            
            // Create features if provided
            if (!empty($payload['features']) && is_array($payload['features'])) {
                foreach ($payload['features'] as $feature) {
                    $featureData = is_array($feature) ? $feature : ['FEATURE_NAME' => $feature];
                    $featureData['PROD_ID'] = $productId;
                    if (empty($featureData['FEATURE_NAME'])) continue; // Skip empty features
                    $this->productFeatureModel->createFeature($featureData);
                }
            }
            
            // Create specs if provided
            if (!empty($payload['specs']) && is_array($payload['specs'])) {
                foreach ($payload['specs'] as $spec) {
                    $spec['PROD_ID'] = $productId;
                    if (empty($spec['SPEC_NAME']) || !isset($spec['SPEC_VALUE'])) continue; // Skip incomplete specs
                    $this->productSpecModel->createSpec($spec);
                }
            }
            
            // Create variants if provided
            if (!empty($payload['variants']) && is_array($payload['variants'])) {
                foreach ($payload['variants'] as $variant) {
                    $variant['PROD_ID'] = $productId;
                    // Add validation for required variant fields if necessary here
                    if (empty($variant['VAR_CAPACITY']) || empty($variant['VAR_SRP_PRICE'])) {
                         error_log("Skipping variant due to missing capacity or SRP price: " . json_encode($variant));
                         continue; 
                    }
                    $this->productVariantModel->createVariant($variant);
                }
            }
            
            // Add inventory if provided (assuming it comes similarly, or within 'product' data)
            // If 'inventory' is also a separate JSON string:
            // $inventoryJson = $this->request('inventory');
            // $payload['inventory'] = $inventoryJson ? json_decode($inventoryJson, true) : [];
            // if ($inventoryJson && json_last_error() !== JSON_ERROR_NONE) { /* error handling */ }

            if (!empty($payload['inventory']) && is_array($payload['inventory'])) { // Assuming inventory might be part of the 'product' field for now or handled separately
                foreach ($payload['inventory'] as $inventoryItem) {
                    $inventoryItem['PROD_ID'] = $productId;
                    // Add validation for required inventory fields
                    $this->inventoryModel->createInventory($inventoryItem);
                }
            }
            
            // Commit the transaction
            $this->productModel->commit();
            
            $this->jsonSuccess(['product_id' => $productId], 'Product created successfully');
            
        } catch (\Exception $e) {
            $this->productModel->rollback();
            error_log("Error creating product: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            // Provide a more generic error to the client for security
            $this->jsonError('An unexpected error occurred while creating the product. Please check server logs.', 500);
        }
    }

    public function updateProduct($id)
    {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->renderError('Bad Request', 400);
            return;
        }
        
        // Try to get data from JSON input, fallback to form data for multipart/form-data
        $data = $this->getJsonInput();
        if (empty($data) && !empty($_POST['product'])) {
            // Handle multipart/form-data
            $data = [
                'product' => json_decode($_POST['product'], true),
                'features' => !empty($_POST['features']) ? json_decode($_POST['features'], true) : [],
                'specs' => !empty($_POST['specs']) ? json_decode($_POST['specs'], true) : [],
                'variants' => !empty($_POST['variants']) ? json_decode($_POST['variants'], true) : [],
            ];
            
            // Handle image upload if present
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $uploadedImage = $_FILES['product_image'];
                $imageName = basename($uploadedImage['name']);
                $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                
                // Generate a unique name to prevent overwrites
                $uniqueImageName = uniqid('prod_', true) . '.' . $imageExt;
                $uploadDir = 'uploads/products/';
                $absoluteUploadPath = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $uploadDir;
                
                if (!is_dir($absoluteUploadPath)) {
                    if (!mkdir($absoluteUploadPath, 0777, true)) {
                        error_log("Failed to create directory: " . $absoluteUploadPath);
                        $this->jsonError('Failed to create image upload directory.', 500);
                        return;
                    }
                }
                
                $targetPath = $absoluteUploadPath . $uniqueImageName;
                
                if (move_uploaded_file($uploadedImage['tmp_name'], $targetPath)) {
                    $data['product']['prod_image'] = $uploadDir . $uniqueImageName;
                } else {
                    error_log("Failed to move uploaded file to: " . $targetPath);
                    $this->jsonError('Failed to save product image.', 500);
                    return;
                }
            }
        }
        
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
                $productData = $data['product'];
                
                // Normalize product data keys to uppercase
                if (isset($productData['prod_name'])) {
                    $productData['PROD_NAME'] = $productData['prod_name'];
                    unset($productData['prod_name']);
                }
                if (isset($productData['prod_description'])) {
                    $productData['PROD_DESCRIPTION'] = $productData['prod_description'];
                    unset($productData['prod_description']);
                }
                if (isset($productData['prod_availability_status'])) {
                    $productData['PROD_AVAILABILITY_STATUS'] = $productData['prod_availability_status'];
                    unset($productData['prod_availability_status']);
                }
                if (isset($productData['prod_image'])) {
                    $productData['PROD_IMAGE'] = $productData['prod_image'];
                    unset($productData['prod_image']);
                }
                
                $this->productModel->updateProduct($id, $productData);
            }
            
            // Update features
            if (isset($data['features'])) {
                // Delete existing features and add new ones
                $this->productFeatureModel->deleteFeaturesByProductId($id);
                
                if (!empty($data['features']) && is_array($data['features'])) {
                    foreach ($data['features'] as $feature) {
                        // Normalize feature data keys to uppercase
                        $normalizedFeature = [];
                        $normalizedFeature['PROD_ID'] = $id;
                        
                        if (isset($feature['feature_name'])) {
                            $normalizedFeature['FEATURE_NAME'] = $feature['feature_name'];
                        } elseif (isset($feature['FEATURE_NAME'])) {
                            $normalizedFeature['FEATURE_NAME'] = $feature['FEATURE_NAME'];
                        }
                        
                        if (isset($feature['feature_id'])) {
                            $normalizedFeature['FEATURE_ID'] = $feature['feature_id'];
                        } elseif (isset($feature['FEATURE_ID'])) {
                            $normalizedFeature['FEATURE_ID'] = $feature['FEATURE_ID'];
                        }
                        
                        $this->productFeatureModel->createFeature($normalizedFeature);
                    }
                }
            }
            
            // Update specs
            if (isset($data['specs'])) {
                // Delete existing specs and add new ones
                $this->productSpecModel->deleteSpecsByProductId($id);
                
                if (!empty($data['specs']) && is_array($data['specs'])) {
                    foreach ($data['specs'] as $spec) {
                        // Normalize spec data keys to uppercase
                        $normalizedSpec = [];
                        $normalizedSpec['PROD_ID'] = $id;
                        
                        if (isset($spec['spec_name'])) {
                            $normalizedSpec['SPEC_NAME'] = $spec['spec_name'];
                        } elseif (isset($spec['SPEC_NAME'])) {
                            $normalizedSpec['SPEC_NAME'] = $spec['SPEC_NAME'];
                        }
                        
                        if (isset($spec['spec_value'])) {
                            $normalizedSpec['SPEC_VALUE'] = $spec['spec_value'];
                        } elseif (isset($spec['SPEC_VALUE'])) {
                            $normalizedSpec['SPEC_VALUE'] = $spec['SPEC_VALUE'];
                        }
                        
                        if (isset($spec['spec_id'])) {
                            $normalizedSpec['SPEC_ID'] = $spec['spec_id'];
                        } elseif (isset($spec['SPEC_ID'])) {
                            $normalizedSpec['SPEC_ID'] = $spec['SPEC_ID'];
                        }
                        
                        $this->productSpecModel->createSpec($normalizedSpec);
                    }
                }
            }
            
            // Update variants
            if (isset($data['variants'])) {
                // Delete existing variants and add new ones
                $this->productVariantModel->deleteVariantsByProductId($id);
                
                if (!empty($data['variants']) && is_array($data['variants'])) {
                    foreach ($data['variants'] as $variant) {
                        // Normalize variant data keys to uppercase
                        $normalizedVariant = [];
                        $normalizedVariant['PROD_ID'] = $id;
                        
                        // Map lowercase to uppercase keys
                        $keyMap = [
                            'var_id' => 'VAR_ID',
                            'var_capacity' => 'VAR_CAPACITY',
                            'var_srp_price' => 'VAR_SRP_PRICE',
                            'var_price_free_install' => 'VAR_PRICE_FREE_INSTALL',
                            'var_price_with_install' => 'VAR_PRICE_WITH_INSTALL',
                            'var_power_consumption' => 'VAR_POWER_CONSUMPTION'
                        ];
                        
                        foreach ($keyMap as $lowerKey => $upperKey) {
                            if (isset($variant[$lowerKey])) {
                                $normalizedVariant[$upperKey] = $variant[$lowerKey];
                            } elseif (isset($variant[$upperKey])) {
                                $normalizedVariant[$upperKey] = $variant[$upperKey];
                            }
                        }
                        
                        $this->productVariantModel->createVariant($normalizedVariant);
                    }
                }
            }
            
            // Commit the transaction
            $this->productModel->commit();
            
            $this->jsonSuccess(['product_id' => $id], 'Product updated successfully');
            
        } catch (\Exception $e) {
            $this->productModel->rollback();
            error_log("Error updating product: " . $e->getMessage() . "\n" . $e->getTraceAsString());
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
        $summary = $this->productModel->getProductSummary();
        $this->jsonSuccess($summary);
    }
    
    /**
     * Handle product booking creation
     */
    public function createProductBooking()
    {
        // Get the booking data from the request
        $data = $this->getJsonInput();
        if (empty($data)) {
            $data = $_POST; // Try to get from regular form data if JSON is empty
        }
        
        // Get the current user ID from the session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->jsonError('You must be logged in to place a booking', 401);
            return;
        }
        
        // Check if we have the necessary booking data
        if (empty($data['PB_VARIANT_ID']) || 
            empty($data['PB_QUANTITY']) || 
            empty($data['PB_UNIT_PRICE']) ||
            empty($data['PB_PREFERRED_DATE']) ||
            empty($data['PB_PREFERRED_TIME']) ||
            empty($data['PB_ADDRESS'])) {
            $this->jsonError('Missing required booking information', 400);
            return;
        }
        
        // Verify that the variant exists
        $variant = $this->productVariantModel->getVariantById($data['PB_VARIANT_ID']);
        if (!$variant) {
            $this->jsonError('Selected product variant not found', 404);
            return;
        }
        
        // Create a complete booking data structure
        $bookingData = [
            'PB_CUSTOMER_ID' => $userId,
            'PB_VARIANT_ID' => $data['PB_VARIANT_ID'],
            'PB_QUANTITY' => $data['PB_QUANTITY'],
            'PB_UNIT_PRICE' => $data['PB_UNIT_PRICE'],
            'PB_STATUS' => 'pending',
            'PB_PREFERRED_DATE' => $data['PB_PREFERRED_DATE'],
            'PB_PREFERRED_TIME' => $data['PB_PREFERRED_TIME'],
            'PB_ADDRESS' => $data['PB_ADDRESS']
        ];
        
        // Create the booking in the database
        $bookingId = $this->productBookingModel->createBooking($bookingData);
        
        if (!$bookingId) {
            $this->jsonError('Failed to create booking. Please try again.', 500);
            return;
        }
        
        // Return success response with the booking ID
        $this->jsonSuccess([
            'PB_ID' => $bookingId,
            'message' => 'Your booking has been received and is being processed.'
        ], 'Booking created successfully');
    }

    /**
     * Get all product bookings for the current user
     */
    public function getUserProductBookings()
    {
        // Get the current user ID from the session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->jsonError('You must be logged in to view your bookings', 401);
            return;
        }
        
        // Get all bookings for this customer
        $bookings = $this->productBookingModel->getBookingsByCustomerId($userId);
        
        // Return the bookings as JSON
        $this->jsonSuccess($bookings);
    }
    
    /**
     * Get details for a specific product booking
     */
    public function getUserProductBookingDetails($id)
    {
        // Get the current user ID from the session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            $this->jsonError('You must be logged in to view booking details', 401);
            return;
        }
        
        // Get the booking details
        $booking = $this->productBookingModel->getBookingById($id);
        
        // Check if the booking exists and belongs to this user
        if (!$booking) {
            $this->jsonError('Booking not found', 404);
            return;
        }
        
        // Check if the booking belongs to the current user
        $customerId = $booking['pb_customer_id'] ?? $booking['PB_CUSTOMER_ID'] ?? null;
        if (!$customerId || $customerId != $userId) {
            $this->jsonError('You do not have permission to view this booking', 403);
            return;
        }
        
        // Get technicians assigned to this booking
        $booking['technicians'] = $this->productBookingModel->getAssignedTechnicians($id);
        
        $this->jsonSuccess($booking);
    }

    /**
     * Get all product bookings for admin
     */
    public function getAdminProductBookings()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Unauthorized access', 401);
            return;
        }
        
        // Get filter parameters
        $filters = [];
        
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        
        if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
            $filters['product_id'] = $_GET['product_id'];
        }
        
        if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
            $filters['date_range'] = $_GET['date_range'];
        }
        
        if (isset($_GET['technician_id']) && !empty($_GET['technician_id'])) {
            $filters['technician_id'] = $_GET['technician_id'];
        }
        
        if (isset($_GET['has_technician'])) {
            $filters['has_technician'] = filter_var($_GET['has_technician'], FILTER_VALIDATE_BOOLEAN);
        }
        
        // Get all bookings with filters
        $bookings = $this->productBookingModel->getFilteredBookings($filters);
        
        // Enhance the response with additional information
        if ($bookings) {
            foreach ($bookings as &$booking) {
                // Add customer information
                if (isset($booking['pb_customer_id']) && !empty($booking['pb_customer_id'])) {
                    $customer = $this->getUserInfo($booking['pb_customer_id']);
                    if ($customer) {
                        $booking['customer_email'] = $customer['UA_EMAIL'] ?? '';
                        $booking['customer_phone'] = $customer['UA_PHONE_NUMBER'] ?? '';
                        $booking['customer_profile_url'] = $customer['UA_PROFILE_URL'] ?? '';
                    }
                } else if (isset($booking['PB_CUSTOMER_ID']) && !empty($booking['PB_CUSTOMER_ID'])) {
                    $customer = $this->getUserInfo($booking['PB_CUSTOMER_ID']);
                    if ($customer) {
                        $booking['customer_email'] = $customer['UA_EMAIL'] ?? '';
                        $booking['customer_phone'] = $customer['UA_PHONE_NUMBER'] ?? '';
                        $booking['customer_profile_url'] = $customer['UA_PROFILE_URL'] ?? '';
                    }
                } else {
                    $booking['customer_email'] = '';
                    $booking['customer_phone'] = '';
                    $booking['customer_profile_url'] = '';
                }
                
                // Get assigned technicians for each booking
                $booking['technicians'] = $this->productBookingModel->getAssignedTechnicians($booking['pb_id'] ?? $booking['PB_ID'] ?? null);
                
                // Add profile images to technicians
                foreach ($booking['technicians'] as &$tech) {
                    $techInfo = $this->getUserInfo($tech['id']);
                    if ($techInfo) {
                        $tech['profile_url'] = $techInfo['UA_PROFILE_URL'] ?? '';
                        $tech['email'] = $techInfo['UA_EMAIL'] ?? '';
                        $tech['phone'] = $techInfo['UA_PHONE_NUMBER'] ?? '';
                    }
                }
            }
        }
        
        // Return the bookings as JSON
        $this->jsonSuccess($bookings);
    }
    
    /**
     * Get details for a specific product booking (admin)
     */
    public function getAdminProductBookingDetails($id)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Unauthorized access', 401);
            return;
        }
        
        // Get the booking details
        $booking = $this->productBookingModel->getBookingById($id);
        
        if (!$booking) {
            $this->jsonError('Booking not found', 404);
            return;
        }
        
        // Add customer information
        if (isset($booking['pb_customer_id']) && !empty($booking['pb_customer_id'])) {
            $customer = $this->getUserInfo($booking['pb_customer_id']);
            if ($customer) {
                $booking['customer_email'] = $customer['UA_EMAIL'] ?? '';
                $booking['customer_phone'] = $customer['UA_PHONE_NUMBER'] ?? '';
                $booking['customer_profile_url'] = $customer['UA_PROFILE_URL'] ?? '';
            }
        } else if (isset($booking['PB_CUSTOMER_ID']) && !empty($booking['PB_CUSTOMER_ID'])) {
            $customer = $this->getUserInfo($booking['PB_CUSTOMER_ID']);
            if ($customer) {
                $booking['customer_email'] = $customer['UA_EMAIL'] ?? '';
                $booking['customer_phone'] = $customer['UA_PHONE_NUMBER'] ?? '';
                $booking['customer_profile_url'] = $customer['UA_PROFILE_URL'] ?? '';
            }
        } else {
            $booking['customer_email'] = '';
            $booking['customer_phone'] = '';
            $booking['customer_profile_url'] = '';
        }
        
        // Get assigned technicians for this booking
        $booking['technicians'] = $this->productBookingModel->getAssignedTechnicians($id);
        
        // Add profile images to technicians
        foreach ($booking['technicians'] as &$tech) {
            $techInfo = $this->getUserInfo($tech['id']);
            if ($techInfo) {
                $tech['profile_url'] = $techInfo['UA_PROFILE_URL'] ?? '';
                $tech['email'] = $techInfo['UA_EMAIL'] ?? '';
                $tech['phone'] = $techInfo['UA_PHONE_NUMBER'] ?? '';
            }
        }
        
        // Return the booking details as JSON
        $this->jsonSuccess($booking);
    }
    
    /**
     * Update a product booking (admin)
     */
    public function updateProductBooking()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Unauthorized access', 401);
            return;
        }
        
        // Get the booking data from the request
        $data = $this->getJsonInput();
        
        if (empty($data) || empty($data['bookingId'])) {
            $this->jsonError('Missing required booking information', 400);
            return;
        }
        
        $bookingId = $data['bookingId'];
        
        // Check if booking exists
        $booking = $this->productBookingModel->getBookingById($bookingId);
        if (!$booking) {
            $this->jsonError('Booking not found', 404);
            return;
        }
        
        // Prepare update data
        $updateData = [];
        
        if (!empty($data['status'])) {
            $updateData['PB_STATUS'] = $data['status'];
        }
        
        if (!empty($data['preferredDate'])) {
            $updateData['PB_PREFERRED_DATE'] = $data['preferredDate'];
        }
        
        if (!empty($data['preferredTime'])) {
            $updateData['PB_PREFERRED_TIME'] = $data['preferredTime'];
        }
        
        // Update the booking
        $this->productBookingModel->updateBooking($bookingId, $updateData);
        
        // Update technician assignments if provided
        if (!empty($data['technicians'])) {
            // Remove current assignments
            $this->productBookingModel->removeAllTechnicians($bookingId);
            
            // Add new assignments
            foreach ($data['technicians'] as $tech) {
                $this->productBookingModel->assignTechnician($bookingId, $tech['id'], $tech['notes'] ?? '');
            }
        }
        
        $this->jsonSuccess(null, 'Booking updated successfully');
    }
    
    /**
     * Delete a product booking (admin)
     */
    public function deleteProductBooking($id)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Unauthorized access', 401);
            return;
        }
        
        // Check if booking exists
        $booking = $this->productBookingModel->getBookingById($id);
        if (!$booking) {
            $this->jsonError('Booking not found', 404);
            return;
        }
        
        // Delete the booking
        $result = $this->productBookingModel->deleteBooking($id);
        
        if ($result) {
            $this->jsonSuccess(null, 'Booking deleted successfully');
        } else {
            $this->jsonError('Failed to delete booking', 500);
        }
    }

    /**
     * Check if current user is an admin
     */
    private function isAdmin()
    {
        // Get the current user role from the session
        $userRole = $_SESSION['user_role'] ?? null;
        
        // Check if user is an admin
        return $userRole === 'admin';
    }

    /**
     * Get user account information
     */
    private function getUserInfo($userId) 
    {
        $sql = "SELECT 
                UA_ID,
                UA_PROFILE_URL,
                UA_FIRST_NAME,
                UA_LAST_NAME,
                UA_EMAIL,
                UA_PHONE_NUMBER
            FROM USER_ACCOUNT 
            WHERE UA_ID = :userId";
            
        // Use pdo instead of db
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getUserInfo: " . $e->getMessage());
            return null;
        }
    }
} 