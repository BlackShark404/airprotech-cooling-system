/**
 * inventory.js
 * Main JavaScript file for the inventory management page
 */

document.addEventListener('DOMContentLoaded', function () {
    // Load inventory statistics
    loadInventoryStats();

    // Load warehouses for dropdown
    loadWarehousesForProductForm();

    // View selector functionality
    const viewButtons = document.querySelectorAll('.view-selector .btn');
    const viewCards = document.querySelectorAll('.view-card');

    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Update active button
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Show selected view, hide others
            const selectedView = this.getAttribute('data-view');
            viewCards.forEach(card => {
                if (card.id === selectedView + 'View') {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });
        });
    });

    // Add variant button click handler
    document.querySelector('.add-variant-btn').addEventListener('click', function () {
        addVariantForm();
    });

    // Add feature button click handler
    document.querySelector('.add-feature-btn').addEventListener('click', function () {
        addFeatureInput();
    });

    // Add specification button click handler
    document.querySelector('.add-spec-btn').addEventListener('click', function () {
        addSpecInput();
    });

    // Remove feature button handler
    document.querySelector('.features-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-feature') ||
            e.target.closest('.remove-feature')) {
            const button = e.target.closest('.remove-feature');
            if (button) {
                const inputGroup = button.closest('.input-group');
                if (inputGroup) {
                    inputGroup.remove();
                }
            }
        }
    });

    // Remove spec button handler
    document.querySelector('.specs-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-spec') ||
            e.target.closest('.remove-spec')) {
            const button = e.target.closest('.remove-spec');
            if (button) {
                const row = button.closest('.row');
                if (row) {
                    row.remove();
                }
            }
        }
    });

    // Tab change handler for add product modal
    $('#productTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).attr('id');

        // If moving to the inventory tab, update variants in the inventory container
        if (targetTab === 'product-inventory-tab') {
            updateVariantsInventoryContainer();
        }
    });

    // Load inventory statistics
    function loadInventoryStats() {
        $.ajax({
            url: '/api/inventory/stats',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    updateStatsDisplay(response.data);
                } else {
                    showErrorToast(response.message || 'Failed to load inventory statistics');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading inventory statistics: ' + error);
            }
        });
    }

    // Update stats display with data
    function updateStatsDisplay(data) {
        $('#totalProducts').text(data.productCount || 0);
        $('#totalVariants').text(data.variantCount || 0);
        $('#totalWarehouses').text(data.warehouseCount || 0);
        $('#lowStockItems').text(data.lowStockCount || 0);
    }

    // Add a new variant form
    function addVariantForm() {
        const variantsContainer = document.querySelector('.variants-container');
        const variantForms = variantsContainer.querySelectorAll('.variant-form');
        const index = variantForms.length;

        const newVariantForm = document.createElement('div');
        newVariantForm.className = 'variant-form mb-3 p-3 border rounded';
        newVariantForm.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Variant ${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-variant">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Capacity</label>
                    <input type="text" class="form-control" name="variants[${index}][var_capacity]" placeholder="e.g., 0.8HP (20)">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Power Consumption</label>
                    <input type="text" class="form-control" name="variants[${index}][var_power_consumption]" placeholder="e.g., CSPF (4.60)">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">SRP Price</label>
                    <input type="number" class="form-control" name="variants[${index}][var_srp_price]" placeholder="Standard price" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price (Free Install)</label>
                    <input type="number" class="form-control" name="variants[${index}][var_price_free_install]" placeholder="Optional" step="0.01">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price (With Install)</label>
                    <input type="number" class="form-control" name="variants[${index}][var_price_with_install]" placeholder="Optional" step="0.01">
                </div>
            </div>
        `;

        variantsContainer.insertBefore(newVariantForm, document.querySelector('.add-variant-btn'));

        // Add event listener for remove button
        newVariantForm.querySelector('.remove-variant').addEventListener('click', function () {
            newVariantForm.remove();
            updateVariantIndices();
            updateVariantsInventoryContainer();
        });
    }

    // Update variant form indices after removal
    function updateVariantIndices() {
        const variantForms = document.querySelectorAll('.variant-form');

        variantForms.forEach((form, index) => {
            const title = form.querySelector('h6');
            if (title) {
                title.textContent = `Variant ${index + 1}`;
            }

            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/variants\[\d+\]/, `variants[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }

    // Add a new feature input
    function addFeatureInput() {
        const featuresContainer = document.querySelector('.features-container');
        const featureInputs = featuresContainer.querySelectorAll('.input-group');
        const index = featureInputs.length;

        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-2';
        inputGroup.innerHTML = `
            <input type="text" class="form-control" name="features[${index}]" placeholder="Enter feature">
            <button class="btn btn-outline-danger remove-feature" type="button">
                <i class="bi bi-x"></i>
            </button>
        `;

        featuresContainer.appendChild(inputGroup);
    }

    // Add a new specification input
    function addSpecInput() {
        const specsContainer = document.querySelector('.specs-container');
        const specRows = specsContainer.querySelectorAll('.row');
        const index = specRows.length;

        const row = document.createElement('div');
        row.className = 'row mb-2';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" class="form-control" name="specs[${index}][spec_name]" placeholder="Spec name">
            </div>
            <div class="col-5">
                <input type="text" class="form-control" name="specs[${index}][spec_value]" placeholder="Spec value">
            </div>
            <div class="col-2">
                <button class="btn btn-outline-danger w-100 remove-spec" type="button">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;

        specsContainer.appendChild(row);
    }

    // Update variants in the inventory container
    function updateVariantsInventoryContainer() {
        const variantForms = document.querySelectorAll('.variant-form');
        const variantsInventoryContainer = document.querySelector('.variants-inventory-container');

        // Clear the container
        variantsInventoryContainer.innerHTML = '';

        if (variantForms.length === 0) {
            variantsInventoryContainer.innerHTML = `
                <div class="alert alert-secondary">
                    Please add variants in the Variants tab first.
                </div>
            `;
            return;
        }

        // Create inventory inputs for each variant
        variantForms.forEach((form, index) => {
            const capacity = form.querySelector('input[name^="variants"][name$="[var_capacity]"]').value || `Variant ${index + 1}`;

            const inventoryItem = document.createElement('div');
            inventoryItem.className = 'mb-3 p-3 border rounded';
            inventoryItem.innerHTML = `
                <h6>${capacity}</h6>
                <div class="mb-3">
                    <label class="form-label">Initial Quantity</label>
                    <input type="number" class="form-control" name="inventory[${index}][quantity]" min="0" value="0">
                </div>
            `;

            variantsInventoryContainer.appendChild(inventoryItem);
        });
    }

    // Load warehouses for the product form dropdown
    function loadWarehousesForProductForm() {
        $.ajax({
            url: '/api/warehouses',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const warehouses = response.data;
                    console.log('Loaded warehouses for product form:', warehouses);

                    // Populate warehouse dropdowns
                    const warehouseSelect = $('#warehouseSelect'); // For product form

                    warehouseSelect.empty();
                    warehouseSelect.append('<option value="">Select warehouse</option>');

                    if (warehouses && warehouses.length > 0) {
                        warehouses.forEach(function (warehouse) {
                            // Handle both uppercase and lowercase property names
                            const id = warehouse.WHOUSE_ID || warehouse.whouse_id || '';
                            const name = warehouse.WHOUSE_NAME || warehouse.whouse_name || 'Unnamed Warehouse';
                            const location = warehouse.WHOUSE_LOCATION || warehouse.whouse_location || 'No location';

                            const option = `<option value="${id}">${name} (${location})</option>`;
                            warehouseSelect.append(option);
                        });
                    }
                } else {
                    showErrorToast(response.message || 'Failed to load warehouses');
                }
            },
            error: function (xhr, status, error) {
                showErrorToast('Error loading warehouses: ' + error);
                console.error('Error loading warehouses for product form:', xhr.responseText);
            }
        });
    }
});
