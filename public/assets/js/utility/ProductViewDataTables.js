/**
 * ProductViewDataTables.js
 * Handles the DataTables initialization and functionality for the products view
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable for products view
    const productsTable = $('#productsTable').DataTable({
        processing: true,
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        language: {
            emptyTable: "No products available",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "Showing 0 to 0 of 0 products",
            lengthMenu: "Show _MENU_ products per page",
            search: "Search products:",
            zeroRecords: "No matching products found"
        },
        columns: [
            { data: 'image', title: 'Image', orderable: false },
            { data: 'name', title: 'Name' },
            { data: 'description', title: 'Description' },
            { data: 'variants', title: 'Variants' },
            { data: 'stock', title: 'Total Stock' },
            { data: 'status', title: 'Status' },
            { data: 'actions', title: 'Actions', orderable: false }
        ],
        order: [[1, 'asc']], // Sort by name by default
        responsive: true,
        autoWidth: false
    });

    // Load products data
    function loadProductsData() {
        $.ajax({
            url: '/api/products',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    populateProductsTable(response.data);
                } else {
                    showErrorToast(response.message || 'Failed to load products data');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading products data: ' + error);
            }
        });
    }

    // Populate products table with data
    function populateProductsTable(data) {
        productsTable.clear();

        data.forEach(function (product) {
            let features = [];
            if (product.features && Array.isArray(product.features)) {
                features = product.features;
            }

            let specs = {};
            if (product.specifications && typeof product.specifications === 'object') {
                specs = product.specifications;
            }

            // Check for stock information
            let totalStock = 0;
            if (product.total_stock) {
                totalStock = product.total_stock;
            }

            // Format variants information
            let variantsHtml = '<div class="small text-muted">No variants</div>';
            let variantCount = 0;

            if (product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
                variantCount = product.variants.length;
                variantsHtml = `<span class="badge bg-primary">${variantCount} ${variantCount === 1 ? 'variant' : 'variants'}</span>`;
            }

            // Determine status class
            const statusClass = getProductStatusClass(product.PROD_AVAILABILITY_STATUS);

            productsTable.row.add({
                'image': `<img src="${product.PROD_IMAGE || '/assets/img/no-image.png'}" width="50" height="50" style="object-fit: cover; border-radius: 4px;">`,
                'name': product.PROD_NAME,
                'description': truncateText(product.PROD_DESCRIPTION || 'No description available', 100),
                'variants': variantsHtml,
                'stock': totalStock,
                'status': `<span class="badge ${statusClass}">${product.PROD_AVAILABILITY_STATUS}</span>`,
                'actions': `<div class="action-buttons">
                    <button class="btn btn-sm table-action-btn view-btn" data-id="${product.PROD_ID}" title="View Product">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn edit-btn" data-id="${product.PROD_ID}" title="Edit Product">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn delete-btn" data-id="${product.PROD_ID}" title="Delete Product">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-sm table-action-btn manage-btn" data-id="${product.PROD_ID}" title="Manage Inventory">
                        <i class="bi bi-boxes"></i>
                    </button>
                </div>`
            });
        });

        productsTable.draw();

        // After populating, attach event handlers to action buttons
        attachProductActionHandlers();
    }

    // Attach event handlers to product action buttons
    function attachProductActionHandlers() {
        // View button handler
        $('#productsTable').on('click', '.view-btn', function () {
            const id = $(this).data('id');
            viewProduct(id);
        });

        // Edit button handler
        $('#productsTable').on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            editProduct(id);
        });

        // Delete button handler
        $('#productsTable').on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            confirmDeleteProduct(id);
        });

        // Manage inventory button handler
        $('#productsTable').on('click', '.manage-btn', function () {
            const id = $(this).data('id');
            openManageInventoryModal(id);
        });
    }

    // View product details
    function viewProduct(id) {
        $.ajax({
            url: `/api/products/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const product = response.data;
                    populateProductDetailsModal(product);
                    $('#productDetailsModal').modal('show');
                } else {
                    showErrorToast(response.message || 'Failed to load product details');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading product details: ' + error);
            }
        });
    }

    // Populate product details modal
    function populateProductDetailsModal(product) {
        // Set basic product information
        $('#productDetailName').text(product.PROD_NAME);
        $('#productDetailDescription').text(product.PROD_DESCRIPTION || 'No description available');
        $('#productDetailId').text(`Product ID: ${product.PROD_ID}`);
        $('#productDetailImage').attr('src', product.PROD_IMAGE || '/assets/img/no-image.png');

        // Set product status
        const statusClass = getProductStatusClass(product.PROD_AVAILABILITY_STATUS);
        $('#productDetailStatus').html(`<span class="badge ${statusClass}">${product.PROD_AVAILABILITY_STATUS}</span>`);

        // Set product features
        const featuresContainer = $('#productDetailFeatures');
        featuresContainer.empty();

        if (product.features && Array.isArray(product.features) && product.features.length > 0) {
            product.features.forEach(function (feature) {
                featuresContainer.append(`<li>${feature}</li>`);
            });
        } else {
            featuresContainer.append('<li class="text-muted">No features specified</li>');
        }

        // Set product specifications
        const specsContainer = $('#productDetailSpecs');
        specsContainer.empty();

        if (product.specifications && Object.keys(product.specifications).length > 0) {
            for (const [key, value] of Object.entries(product.specifications)) {
                specsContainer.append(`<tr><td>${key}</td><td>${value}</td></tr>`);
            }
        } else {
            specsContainer.append('<tr><td colspan="2" class="text-center text-muted">No specifications available</td></tr>');
        }

        // Set product variants
        const variantsContainer = $('#productDetailVariants');
        variantsContainer.empty();

        if (product.variants && product.variants.length > 0) {
            product.variants.forEach(function (variant) {
                variantsContainer.append(`
                    <tr>
                        <td>${variant.VAR_CAPACITY || 'N/A'}</td>
                        <td>${variant.VAR_POWER_CONSUMPTION || 'N/A'}</td>
                        <td>${formatCurrency(variant.VAR_SRP_PRICE)}</td>
                        <td>${formatCurrency(variant.VAR_PRICE_FREE_INSTALL)}</td>
                        <td>${formatCurrency(variant.VAR_PRICE_WITH_INSTALL)}</td>
                    </tr>
                `);
            });
        } else {
            variantsContainer.append('<tr><td colspan="5" class="text-center text-muted">No variants available</td></tr>');
        }

        // Set inventory summary
        const inventoryContainer = $('#productDetailInventory');
        inventoryContainer.empty();

        // This would normally come from an API call to get inventory details
        // For now, just display a placeholder
        inventoryContainer.append(`
            <div class="col-md-4 mb-3">
                <div class="inventory-detail-card">
                    <h6>Regular Stock</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">${product.total_stock || 0}</span>
                        <span class="badge bg-primary">Total</span>
                    </div>
                </div>
            </div>
        `);

        // Set the product ID for the edit button
        $('#editProductBtn').data('id', product.PROD_ID);
    }

    // Edit product
    function editProduct(id) {
        // Implementation of editing product
        // This would typically involve loading the product data into a form for editing
        showInfoToast('Editing product with ID: ' + id);
    }

    // Confirm delete product
    function confirmDeleteProduct(id) {
        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            deleteProduct(id);
        }
    }

    // Delete product
    function deleteProduct(id) {
        $.ajax({
            url: `/api/products/delete/${id}`,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccessToast(response.message || 'Product deleted successfully');
                    loadProductsData(); // Refresh table data
                } else {
                    showErrorToast(response.message || 'Failed to delete product');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error deleting product: ' + error);
            }
        });
    }

    // Open the manage inventory modal for a product
    function openManageInventoryModal(productId) {
        // Set hidden fields in the form
        $('#moveStockProductId').val(productId);
        $('#addStockProductId').val(productId);

        // Load product variants
        loadProductVariants(productId);

        // Load current stock information
        loadCurrentStock(productId);

        // Show the modal
        $('#manageInventoryModal').modal('show');
    }

    // Load product variants for the manage inventory modal
    function loadProductVariants(productId) {
        $.ajax({
            url: `/api/products/${productId}/variants`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const variants = response.data;

                    // Populate variant dropdowns
                    const addVariantSelect = $('#addVariantSelect');
                    const moveVariantSelect = $('#moveVariantSelect');

                    addVariantSelect.empty();
                    moveVariantSelect.empty();

                    variants.forEach(function (variant) {
                        const option = `<option value="${variant.VAR_ID}">${variant.VAR_CAPACITY}</option>`;
                        addVariantSelect.append(option);
                        moveVariantSelect.append(option);
                    });
                } else {
                    showErrorToast(response.message || 'Failed to load product variants');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading product variants: ' + error);
            }
        });
    }

    // Load current stock information for the manage inventory modal
    function loadCurrentStock(productId) {
        $.ajax({
            url: `/api/inventory/product/${productId}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const inventory = response.data;
                    const currentStockTable = $('#currentStockTable');

                    currentStockTable.empty();

                    if (inventory.length === 0) {
                        currentStockTable.append('<tr><td colspan="5" class="text-center">No stock data available</td></tr>');
                    } else {
                        inventory.forEach(function (item) {
                            const row = `<tr>
                                <td>${item.VAR_CAPACITY || 'Standard'}</td>
                                <td>${item.WHOUSE_NAME}</td>
                                <td><span class="badge inventory-${item.INVE_TYPE.toLowerCase()}">${item.INVE_TYPE}</span></td>
                                <td>${item.QUANTITY}</td>
                                <td>${formatDate(item.LAST_UPDATED)}</td>
                            </tr>`;
                            currentStockTable.append(row);
                        });
                    }
                } else {
                    showErrorToast(response.message || 'Failed to load current stock data');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading current stock data: ' + error);
            }
        });
    }

    // Search products
    $('#productsSearch').on('keyup', function () {
        productsTable.search($(this).val()).draw();
    });

    // Helper function to truncate text
    function truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + '...';
    }

    // Helper function to get product status class
    function getProductStatusClass(status) {
        if (!status) return 'bg-secondary';

        switch (status) {
            case 'Available':
                return 'bg-success';
            case 'Out of Stock':
                return 'bg-danger';
            case 'Discontinued':
                return 'bg-secondary';
            default:
                return 'bg-info';
        }
    }

    // Helper function to format currency
    function formatCurrency(amount) {
        if (!amount) return 'N/A';
        return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';

        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Add event listener for save product button
    $('#saveProductBtn').on('click', function () {
        saveProduct();
    });

    // Save product function
    function saveProduct() {
        const formData = new FormData(document.getElementById('productForm'));
        const productData = {};

        // Convert form data to JSON object
        for (const [key, value] of formData.entries()) {
            if (key.includes('[') && key.includes(']')) {
                // Handle array inputs
                const matches = key.match(/([^\[]+)\[([^\]]+)\](?:\[([^\]]+)\])?/);
                if (matches) {
                    const mainKey = matches[1];
                    const subKey = matches[2];

                    if (!productData[mainKey]) {
                        productData[mainKey] = [];
                    }

                    // Check if it's an array of objects or simple array
                    if (matches[3]) {
                        const thirdKey = matches[3];

                        // Make sure the index exists
                        if (!productData[mainKey][subKey]) {
                            productData[mainKey][subKey] = {};
                        }

                        productData[mainKey][subKey][thirdKey] = value;
                    } else {
                        // Simple array
                        if (!productData[mainKey][subKey]) {
                            productData[mainKey][subKey] = value;
                        } else {
                            productData[mainKey][subKey] = value;
                        }
                    }
                }
            } else {
                // Handle regular inputs
                productData[key] = value;
            }
        }

        // Process product image
        const fileInput = document.getElementById('productImage');
        if (fileInput.files.length > 0) {
            // In a real implementation, this would handle file uploads
            // For this example, just set a placeholder
            productData.PROD_IMAGE = '/assets/img/sample-product.jpg';
        } else {
            productData.PROD_IMAGE = '/assets/img/no-image.png';
        }

        // All field names are now already using uppercase naming consistent with the backend
        console.log('Sending product data:', productData);

        // Send data to server
        $.ajax({
            url: '/api/products',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(productData),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccessToast(response.message || 'Product saved successfully');
                    $('#addProductModal').modal('hide');
                    loadProductsData(); // Refresh table data
                } else {
                    showErrorToast(response.message || 'Failed to save product');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error saving product:', xhr.responseText);
                showErrorToast('Error saving product: ' + error);
            }
        });
    }

    // Add event listener for edit product button in the details modal
    $('#editProductBtn').on('click', function () {
        const productId = $(this).data('id');
        editProduct(productId);
    });

    // Initial load of products data
    loadProductsData();
});
