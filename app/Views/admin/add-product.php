<?php
$title = 'Add Product - AirProtect';
$activeTab = 'add_product';

// Add any additional styles specific to this page
$additionalStyles = <<<HTML
<style>
    .form-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 24px;
    }
    
    .form-section {
        margin-bottom: 30px;
    }
    
    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .form-label {
        font-weight: 500;
        color: #343a40;
    }
    
    .required-field::after {
        content: " *";
        color: #ff3b30;
    }
    
    .image-upload-container {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .image-upload-container:hover {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.03);
    }
    
    .image-upload-icon {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .image-upload-text {
        color: #6c757d;
        font-size: 14px;
    }
    
    .image-format-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .btn-action {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
    }
    
    .btn-save {
        background-color: #007bff;
        color: white;
    }
    
    .btn-cancel {
        border: 1px solid #dee2e6;
        color: #6c757d;
    }
</style>
HTML;

// Start output buffering for content
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Page Heading with Back Button -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <a href="<?= base_url('/admin/inventory') ?>" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center mb-2">
                <i class="bi bi-arrow-left me-1"></i> Back to Inventory
            </a>
            <h1 class="h3 mb-0">Add Product</h1>
            <p class="text-muted">Add new AC product or accessory to inventory</p>
        </div>
    </div>

    <!-- Product Form -->
    <div class="form-container">
        <form id="addProductForm" method="post" action="<?= base_url('/admin/save-product') ?>" enctype="multipart/form-data">
            
            <!-- Basic Information Section -->
            <div class="form-section">
                <h2 class="form-section-title">Basic Information</h2>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="productName" class="form-label required-field">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter product name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="skuCode" class="form-label required-field">SKU/Product Code</label>
                        <input type="text" class="form-control" id="skuCode" name="skuCode" placeholder="Enter SKU" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="category" class="form-label required-field">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="" selected disabled>Select category</option>
                            <option value="smart_inverter">Smart Inverter AC</option>
                            <option value="split_system">Split System Classic</option>
                            <option value="portable">Portable AC Unit</option>
                            <option value="parts">AC Parts</option>
                            <option value="tools">Tools</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brand" class="form-label required-field">Brand</label>
                        <select class="form-select" id="brand" name="brand" required>
                            <option value="" selected disabled>Select brand</option>
                            <option value="airprotect">AirProtect</option>
                            <option value="daikin">Daikin</option>
                            <option value="carrier">Carrier</option>
                            <option value="lennox">Lennox</option>
                            <option value="trane">Trane</option>
                            <option value="mitsubishi">Mitsubishi</option>
                            <option value="lg">LG</option>
                            <option value="samsung">Samsung</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter product description"></textarea>
                </div>
            </div>
            
            <!-- Pricing & Inventory Section -->
            <div class="form-section">
                <h2 class="form-section-title">Pricing & Inventory</h2>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="purchasePrice" class="form-label required-field">Purchase Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="purchasePrice" name="purchasePrice" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="sellingPrice" class="form-label required-field">Selling Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="sellingPrice" name="sellingPrice" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="currentStock" class="form-label required-field">Current Stock</label>
                        <input type="number" class="form-control" id="currentStock" name="currentStock" placeholder="Enter quantity" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lowStockThreshold" class="form-label">Low Stock Alert Threshold</label>
                        <input type="number" class="form-control" id="lowStockThreshold" name="lowStockThreshold" placeholder="Enter threshold" min="0">
                    </div>
                </div>
            </div>
            
            <!-- Technical Specifications Section -->
            <div class="form-section">
                <h2 class="form-section-title">Technical Specifications</h2>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="coolingCapacity" class="form-label">Cooling Capacity (BTU)</label>
                        <input type="number" class="form-control" id="coolingCapacity" name="coolingCapacity" placeholder="Enter BTU">
                    </div>
                    <div class="col-md-6">
                        <label for="energyEfficiency" class="form-label">Energy Efficiency Rating</label>
                        <input type="text" class="form-control" id="energyEfficiency" name="energyEfficiency" placeholder="Enter EER">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="inventoryType" class="form-label">Inventory Type</label>
                        <select class="form-select" id="inventoryType" name="inventoryType">
                            <option value="" selected disabled>Select type</option>
                            <option value="warehouse_a">Warehouse A</option>
                            <option value="warehouse_b">Warehouse B</option>
                            <option value="showroom">Showroom</option>
                            <option value="service_vehicle">Service Vehicle</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="warrantyPeriod" class="form-label">Warranty Period</label>
                        <input type="text" class="form-control" id="warrantyPeriod" name="warrantyPeriod" placeholder="Enter warranty period">
                    </div>
                </div>
            </div>
            
            <!-- Product Images Section -->
            <div class="form-section">
                <h2 class="form-section-title">Product Images</h2>
                
                <div class="image-upload-container" id="imageUploadArea">
                    <input type="file" id="productImages" name="productImages[]" multiple style="display: none;" accept="image/jpeg, image/png">
                    <div class="image-upload-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <div class="image-upload-text">Drag and drop images here or click to upload</div>
                    <div class="image-format-text">Supported formats: JPG, PNG. Max file size: 5MB</div>
                </div>
                
                <div id="imagePreviewContainer" class="row mt-3">
                    <!-- Image previews will be displayed here -->
                </div>
            </div>
            
            <!-- Form Buttons -->
            <div class="d-flex justify-content-end mt-4">
                <a href="<?= base_url('/admin/inventory') ?>" class="btn btn-cancel me-2">Cancel</a>
                <button type="submit" class="btn btn-save">Save Product</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional scripts specific to this page
$additionalScripts = <<<HTML
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload functionality
        const imageUploadArea = document.getElementById('imageUploadArea');
        const productImagesInput = document.getElementById('productImages');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        
        imageUploadArea.addEventListener('click', function() {
            productImagesInput.click();
        });
        
        imageUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            imageUploadArea.classList.add('border-primary');
        });
        
        imageUploadArea.addEventListener('dragleave', function() {
            imageUploadArea.classList.remove('border-primary');
        });
        
        imageUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            imageUploadArea.classList.remove('border-primary');
            
            if (e.dataTransfer.files.length) {
                productImagesInput.files = e.dataTransfer.files;
                displayImagePreviews(e.dataTransfer.files);
            }
        });
        
        productImagesInput.addEventListener('change', function() {
            displayImagePreviews(this.files);
        });
        
        function displayImagePreviews(files) {
            imagePreviewContainer.innerHTML = '';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Validate file type and size
                const validTypes = ['image/jpeg', 'image/png'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!validTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG and PNG are allowed.');
                    continue;
                }
                
                if (file.size > maxSize) {
                    alert('File size exceeds 5MB limit.');
                    continue;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-6 mb-3';
                    
                    const card = document.createElement('div');
                    card.className = 'card h-100';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'card-img-top';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    
                    const cardBody = document.createElement('div');
                    cardBody.className = 'card-body p-2';
                    
                    const fileName = document.createElement('p');
                    fileName.className = 'card-text small text-truncate mb-0';
                    fileName.textContent = file.name;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-outline-danger w-100 mt-2';
                    removeBtn.textContent = 'Remove';
                    removeBtn.onclick = function() {
                        col.remove();
                    };
                    
                    cardBody.appendChild(fileName);
                    cardBody.appendChild(removeBtn);
                    card.appendChild(img);
                    card.appendChild(cardBody);
                    col.appendChild(card);
                    imagePreviewContainer.appendChild(col);
                };
                
                reader.readAsDataURL(file);
            }
        }
        
        // Form validation
        const addProductForm = document.getElementById('addProductForm');
        
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Perform validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // If form is valid, submit
            if (isValid) {
                // In a real application, this would submit the form
                alert('Product saved successfully!');
                window.location.href = '<?= base_url('/admin/inventory') ?>';
            }
        });
    });
</script>
HTML;

// Include the base template
include __DIR__ . '/../includes/admin/base.php';
?>