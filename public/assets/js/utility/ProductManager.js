/**
 * ProductManager Class
 * Handles creating product cards, managing the product details modal,
 * filtering/searching products, client-side pagination, and order confirmation
 */
class ProductManager {
    constructor(options = {}) {
        // Default configuration with pagination and order endpoint
        this.config = {
            productsEndpoint: '/api/products',
            containerSelector: '#products-container',
            modalId: 'productDetailModal',
            filterFormId: 'product-filters',
            searchInputId: 'product-search',
            cardTemplate: this.getDefaultCardTemplate(),
            itemsPerPage: 9,
            paginationContainerSelector: '#pagination-container',
            orderEndpoint: '/api/product-bookings',
            ...options
        };

        // Container for product cards
        this.container = document.querySelector(this.config.containerSelector);
        if (!this.container) {
            console.error(`ProductManager: Products container with selector "${this.config.containerSelector}" not found.`);
            return; // Critical element missing
        }

        // Initialize modal elements once on class creation
        this.initModalElements();

        // Store all products for filtering
        this.allProducts = [];

        // Pagination state
        this.currentPage = 1;
        this.itemsPerPage = this.config.itemsPerPage;

        // Initialize filter and search
        this.initFilterAndSearch();
    }

    /**
     * Default card template showing primary variant price
     */
    getDefaultCardTemplate() {
        return (product) => {
            // Convert relative paths to absolute paths if needed
            let imagePath = product.PROD_IMAGE || product.prod_image || '';
            if (imagePath && !imagePath.startsWith('http') && !imagePath.startsWith('/uploads/')) {
                imagePath = '/' + imagePath;
            }

            const productId = product.PROD_ID || product.prod_id;
            const productName = product.PROD_NAME || product.prod_name || 'Unnamed Product';
            const productDesc = product.PROD_DESCRIPTION || product.prod_description || '';

            // Filter variants with inventory > 0
            const variantsWithStock = product.variants?.filter(v => (v.INVENTORY_QUANTITY || 0) > 0) || [];

            // Get the primary variant price if available
            let priceDisplay = '';
            if (variantsWithStock.length > 0) {
                const primaryVariant = variantsWithStock[0];
                const price = primaryVariant.VAR_SRP_PRICE || primaryVariant.var_srp_price || '0.00';
                priceDisplay = `<div class="product-price">₱${price}</div>`;

                // If there are multiple variants, show a range
                if (variantsWithStock.length > 1) {
                    let minPrice = Number.MAX_VALUE;
                    let maxPrice = 0;

                    variantsWithStock.forEach(variant => {
                        const varPrice = parseFloat(variant.VAR_SRP_PRICE || variant.var_srp_price || 0);
                        minPrice = Math.min(minPrice, varPrice);
                        maxPrice = Math.max(maxPrice, varPrice);
                    });

                    if (minPrice !== maxPrice) {
                        priceDisplay = `<div class="product-price">₱${minPrice.toFixed(2)} - ₱${maxPrice.toFixed(2)}</div>`;
                    }
                }
            }

            // Show the capacity variants available with inventory quantity
            let variantInfo = '';
            if (variantsWithStock.length > 0) {
                const capacities = variantsWithStock.map(v => {
                    const capacity = v.VAR_CAPACITY || v.var_capacity;
                    const quantity = v.INVENTORY_QUANTITY || 0;
                    return `<span class="variant-badge" title="${quantity} units in stock">${capacity} <span class="badge bg-success">${quantity}</span></span>`;
                }).join(' ');

                if (capacities) {
                    variantInfo = `
                        <div class="product-variants">
                            <small class="text-muted d-block mb-1">Available Variants (Stock):</small>
                            ${capacities}
                        </div>
                    `;
                }
            }

            return `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card" data-product-id="${productId}" data-category="${product.category || ''}">
                        <div class="product-img-container">
                            <img src="${imagePath}" alt="${productName}" class="product-img">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">${productName}</h3>
                            <p class="product-desc">${productDesc.substring(0, 70)}${productDesc.length > 70 ? '...' : ''}</p>
                            ${priceDisplay}
                            ${variantInfo}
                            <div class="d-flex justify-content-end align-items-center mt-3">
                                <button class="btn btn-book-now view-details" data-product-id="${productId}">Book Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        };
    }

    /**
     * Initialize modal elements once on class creation
     */
    initModalElements() {
        this.modal = {
            element: document.getElementById('productDetailModal'),
            title: document.getElementById('productDetailModalLabel'),
            productImage: document.getElementById('modal-product-image'),
            productName: document.getElementById('modal-product-name'),
            productCode: document.getElementById('modal-product-code'),
            price: document.getElementById('modal-product-price'),
            availabilityStatus: document.getElementById('modal-availability-status'),
            variantSelect: document.getElementById('modal-variant-select'),
            quantity: document.getElementById('modal-quantity'),
            features: document.getElementById('modal-features'),
            specifications: document.getElementById('modal-specifications'),
            preferredDate: document.getElementById('modal-preferred-date'),
            preferredTime: document.getElementById('modal-preferred-time'),
            address: document.getElementById('modal-address'),
            optionalNotes: document.getElementById('modal-description'), // Note: This is the description field in the UI
            totalAmount: document.getElementById('modal-total-amount'),
            submitBtn: document.getElementById('confirm-order') // Using the actual ID from the HTML
        };

        // Add event to confirm button if it exists
        if (this.modal.submitBtn) {
            this.modal.submitBtn.addEventListener('click', () => this.confirmOrder());
            console.log('Initialized confirm order button');
        } else {
            console.error('Could not find confirm order button');
        }

        // Initialize quantity control buttons
        this.initModalControls();

        // Add event listener for variant selection changes
        if (this.modal.variantSelect) {
            this.modal.variantSelect.addEventListener('change', () => {
                this.updateModalPriceAndAvailability();
                this.updateTotalAmount();
            });
        }
    }

    /**
     * Initialize controls within the modal
     */
    initModalControls() {
        const increaseQtyBtn = document.getElementById('increase-quantity');
        const decreaseQtyBtn = document.getElementById('decrease-quantity');

        if (increaseQtyBtn && this.modal.quantity) {
            increaseQtyBtn.addEventListener('click', () => {
                if (!this.currentProduct || !this.modal.variantSelect || !this.modal.quantity) return;

                // Get current quantity and selected variant
                const quantity = parseInt(this.modal.quantity.value, 10) || 1;
                const selectedVariantId = parseInt(this.modal.variantSelect.value);

                // Find the selected variant 
                const selectedVariant = this.currentProduct.variants.find(v => {
                    const id = parseInt(v.VAR_ID || v.var_id);
                    return id === selectedVariantId;
                });

                // Get available quantity from inventory
                const availableQuantity = this.getAvailableQuantity(selectedVariant);
                console.log('Increasing quantity: Current =', quantity, 'Available =', availableQuantity);

                // Only increase if we haven't hit the limit
                if (selectedVariant && quantity < availableQuantity) {
                    this.modal.quantity.value = quantity + 1;

                    // Update total amount
                    this.updateTotalAmount();

                    // Update button states
                    decreaseQtyBtn.disabled = false;
                    increaseQtyBtn.disabled = (quantity + 1) >= availableQuantity;
                }
            });
        }

        if (decreaseQtyBtn && this.modal.quantity) {
            decreaseQtyBtn.addEventListener('click', () => {
                if (!this.modal.quantity) return;

                // Get current quantity
                const quantity = parseInt(this.modal.quantity.value, 10) || 1;
                console.log('Decreasing quantity: Current =', quantity);

                // Only decrease if we're above 1
                if (quantity > 1) {
                    this.modal.quantity.value = quantity - 1;

                    // Update total amount
                    this.updateTotalAmount();

                    // Update button states
                    decreaseQtyBtn.disabled = (quantity - 1) <= 1;

                    // Re-enable increase button since we're decreasing
                    if (increaseQtyBtn) {
                        const selectedVariantId = parseInt(this.modal.variantSelect.value);
                        const selectedVariant = this.currentProduct.variants.find(v => {
                            const id = parseInt(v.VAR_ID || v.var_id);
                            return id === selectedVariantId;
                        });
                        const availableQuantity = this.getAvailableQuantity(selectedVariant);

                        increaseQtyBtn.disabled = (quantity - 1) >= availableQuantity;
                    }
                }
            });
        }

        // Add an input event listener to the quantity input for direct changes
        if (this.modal.quantity) {
            this.modal.quantity.addEventListener('input', () => {
                let quantity = parseInt(this.modal.quantity.value, 10);

                // Get selected variant and its available quantity
                const selectedVariantId = parseInt(this.modal.variantSelect.value);
                const selectedVariant = this.currentProduct.variants.find(v => {
                    const id = parseInt(v.VAR_ID || v.var_id);
                    return id === selectedVariantId;
                });
                const availableQuantity = this.getAvailableQuantity(selectedVariant);

                // Enforce minimum value of 1
                if (isNaN(quantity) || quantity < 1) {
                    quantity = 1;
                    this.modal.quantity.value = quantity;
                }

                // Enforce maximum based on inventory
                if (quantity > availableQuantity) {
                    quantity = availableQuantity;
                    this.modal.quantity.value = quantity;
                }

                // Update button states
                if (increaseQtyBtn) increaseQtyBtn.disabled = quantity >= availableQuantity;
                if (decreaseQtyBtn) decreaseQtyBtn.disabled = quantity <= 1;

                // Update total amount
                this.updateTotalAmount();
            });
        }

        // Add event listener to all "Order Now" buttons (delegated to document)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-details')) {
                if (!this.modal.element) {
                    console.error("ProductManager: Modal element not found. Cannot open product details.");
                    alert("Sorry, the product details view is currently unavailable.");
                    return;
                }
                const productId = e.target.getAttribute('data-product-id');
                this.openProductModal(productId);
            }
        });
    }

    /**
     * Initialize filter and search functionality
     */
    initFilterAndSearch() {
        this.filterForm = document.getElementById(this.config.filterFormId);
        this.searchInput = document.getElementById(this.config.searchInputId);

        if (this.filterForm) {
            // Handle form submission
            this.filterForm.addEventListener('submit', (e) => {
                e.preventDefault(); // Prevent page reload
                this.applyFilters();
            });

            // Optional: Handle input changes if you want live filtering
            this.filterForm.addEventListener('change', () => this.applyFilters());

            // Handle form reset
            this.filterForm.addEventListener('reset', () => {
                setTimeout(() => this.applyFilters(), 10); // Allow form to reset before applying
            });
        }

        if (this.searchInput) {
            let searchTimeout;
            this.searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => this.applyFilters(), 300);
            });
        }
    }

    /**
     * Apply all active filters and search
     */
    applyFilters() {
        if (!this.allProducts.length && this.container) { // Check if container exists
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No products loaded to filter.</p></div>';
            this.renderPagination(0);
            return;
        }
        if (!this.allProducts.length) return;

        let filteredProducts = [...this.allProducts];

        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (categoryFilter && categoryFilter.value) {
            filteredProducts = filteredProducts.filter(product =>
                product.category === categoryFilter.value
            );
        }

        const minPriceFilter = this.filterForm?.querySelector('[name="min-price"]');
        const maxPriceFilter = this.filterForm?.querySelector('[name="max-price"]');

        if (minPriceFilter && minPriceFilter.value !== '') {
            const minPrice = parseFloat(minPriceFilter.value);
            filteredProducts = filteredProducts.filter(product => {
                // Check if variants exist before filtering
                return product.variants && Array.isArray(product.variants) &&
                    product.variants.some(variant => {
                        const price = parseFloat(variant.VAR_SRP_PRICE || variant.var_srp_price || 0);
                        return price >= minPrice;
                    });
            });
        }

        if (maxPriceFilter && maxPriceFilter.value !== '') {
            const maxPrice = parseFloat(maxPriceFilter.value);
            filteredProducts = filteredProducts.filter(product => {
                // Check if variants exist before filtering
                return product.variants && Array.isArray(product.variants) &&
                    product.variants.some(variant => {
                        const price = parseFloat(variant.VAR_SRP_PRICE || variant.var_srp_price || 0);
                        return price <= maxPrice;
                    });
            });
        }

        const availabilityFilter = this.filterForm?.querySelector('[name="availability-status"]');
        if (availabilityFilter && availabilityFilter.value !== '') {
            filteredProducts = filteredProducts.filter(product => {
                const status = product.PROD_AVAILABILITY_STATUS || product.prod_availability_status;
                return status === availabilityFilter.value;
            });
        }

        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredProducts = filteredProducts.filter(product => {
                const name = (product.PROD_NAME || product.prod_name || '').toLowerCase();
                const desc = (product.PROD_DESCRIPTION || product.prod_description || '').toLowerCase();
                return name.includes(searchTerm) || desc.includes(searchTerm);
            });
        }

        this.currentPage = 1;
        this.renderProducts(filteredProducts);

        const resultsCountElement = document.getElementById('results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${filteredProducts.length} products found`;
        }
    }

    /**
     * Update total amount in the modal
     */
    updateTotalAmount() {
        if (!this.currentProduct || !this.modal.variantSelect || !this.modal.quantity || !this.modal.totalAmount) {
            return;
        }

        const selectedVariantId = parseInt(this.modal.variantSelect.value);
        const quantity = parseInt(this.modal.quantity.value) || 1;

        // Find the selected variant
        const selectedVariant = this.currentProduct.variants.find(v => {
            const id = v.VAR_ID || v.var_id;
            return id === selectedVariantId;
        });

        if (selectedVariant) {
            const price = parseFloat(selectedVariant.VAR_SRP_PRICE || selectedVariant.var_srp_price || 0);
            const availableQuantity = this.getAvailableQuantity(selectedVariant);

            // Ensure quantity doesn't exceed available inventory
            const validQuantity = Math.min(quantity, availableQuantity);
            if (validQuantity !== quantity && this.modal.quantity) {
                this.modal.quantity.value = validQuantity;
            }

            const totalAmount = price * validQuantity;
            this.modal.totalAmount.textContent = `₱${totalAmount.toFixed(2)}`;

            // Disable increase button if at inventory limit
            const increaseQtyBtn = document.getElementById('increase-quantity');
            if (increaseQtyBtn) {
                increaseQtyBtn.disabled = validQuantity >= availableQuantity;
            }
        } else {
            this.modal.totalAmount.textContent = '₱0.00';
        }
    }

    /**
     * Update modal price and availability based on selected variant
     */
    updateModalPriceAndAvailability() {
        if (!this.currentProduct || !this.modal.variantSelect || !this.modal.price || !this.modal.availabilityStatus) {
            console.log('Missing elements for updating modal price and availability');
            return;
        }

        // Handle the case where there are no variants available
        if (this.modal.variantSelect.options.length === 0 || this.modal.variantSelect.value === '') {
            console.log('No variants available or selected');
            this.modal.price.textContent = '₱N/A';
            this.modal.availabilityStatus.textContent = 'Out of Stock';
            this.modal.availabilityStatus.className = 'text-danger fw-medium';

            // Update badge color - find it by going up one level to parent and then find badge
            const parent = this.modal.availabilityStatus.parentNode;
            const badge = parent.querySelector('.badge');
            if (badge) {
                badge.className = 'badge rounded-pill bg-danger-subtle text-danger me-2';
                const icon = badge.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-times-circle';
                }
            }

            // Disable confirm button
            if (this.modal.submitBtn) {
                this.modal.submitBtn.disabled = true;
                this.modal.submitBtn.title = "No variants available in stock";
            }

            // Disable quantity controls
            const increaseQtyBtn = document.getElementById('increase-quantity');
            const decreaseQtyBtn = document.getElementById('decrease-quantity');
            if (increaseQtyBtn) increaseQtyBtn.disabled = true;
            if (decreaseQtyBtn) decreaseQtyBtn.disabled = true;

            return;
        }

        const selectedVariantId = parseInt(this.modal.variantSelect.value);
        console.log('Selected variant ID:', selectedVariantId);

        const selectedVariant = this.currentProduct.variants.find(v => {
            const id = parseInt(v.VAR_ID || v.var_id);
            return id === selectedVariantId;
        });

        console.log('Selected variant:', selectedVariant);

        if (selectedVariant) {
            const price = selectedVariant.VAR_SRP_PRICE || selectedVariant.var_srp_price || '0.00';
            this.modal.price.textContent = `₱${price}`;

            // Check inventory quantity for this variant
            const availableQuantity = this.getAvailableQuantity(selectedVariant);
            console.log('Available quantity:', availableQuantity);

            // Update availability status based on inventory quantity
            let productStatus = 'Out of Stock';
            let statusClass = 'text-danger';
            let badgeClass = 'bg-danger-subtle text-danger';
            let iconClass = 'fas fa-times-circle';

            if (availableQuantity > 10) {
                productStatus = `In Stock (${availableQuantity})`;
                statusClass = 'text-success';
                badgeClass = 'bg-success-subtle text-success';
                iconClass = 'fas fa-check-circle';
            } else if (availableQuantity > 0) {
                productStatus = `Limited Stock (${availableQuantity})`;
                statusClass = 'text-warning';
                badgeClass = 'bg-warning-subtle text-warning';
                iconClass = 'fas fa-exclamation-circle';
            }

            // Update the display
            this.modal.availabilityStatus.textContent = productStatus;
            this.modal.availabilityStatus.className = `fw-medium ${statusClass}`;

            // Update badge color - find it by going up one level to parent and then find badge
            const parent = this.modal.availabilityStatus.parentNode;
            const badge = parent.querySelector('.badge');
            if (badge) {
                badge.className = `badge rounded-pill ${badgeClass} me-2`;
                const icon = badge.querySelector('i');
                if (icon) {
                    icon.className = iconClass;
                }
            }

            // Reset quantity to 1 or available quantity if less than 1
            if (this.modal.quantity) {
                const maxQuantity = Math.max(1, Math.min(availableQuantity, 10)); // Limit to 10 for UI purposes
                this.modal.quantity.value = Math.min(1, maxQuantity);

                // Enable/disable quantity controls based on stock
                const increaseQtyBtn = document.getElementById('increase-quantity');
                const decreaseQtyBtn = document.getElementById('decrease-quantity');

                if (increaseQtyBtn) {
                    increaseQtyBtn.disabled = availableQuantity <= 1;
                }

                if (decreaseQtyBtn) {
                    decreaseQtyBtn.disabled = true; // Start with 1
                }

                // Set max attribute on quantity input to inventory limit
                this.modal.quantity.setAttribute('max', availableQuantity);

                // Disable the confirm button if out of stock
                if (this.modal.submitBtn) {
                    this.modal.submitBtn.disabled = availableQuantity <= 0;
                    if (availableQuantity <= 0) {
                        this.modal.submitBtn.title = "This product is out of stock";
                        this.modal.submitBtn.classList.add('btn-secondary');
                        this.modal.submitBtn.classList.remove('btn-primary');

                        // Update icon
                        const icon = this.modal.submitBtn.querySelector('i');
                        if (icon) {
                            icon.className = 'fas fa-times-circle me-2';
                        }
                    } else {
                        this.modal.submitBtn.title = "";
                        this.modal.submitBtn.classList.add('btn-primary');
                        this.modal.submitBtn.classList.remove('btn-secondary');

                        // Update icon
                        const icon = this.modal.submitBtn.querySelector('i');
                        if (icon) {
                            icon.className = 'fas fa-check-circle me-2';
                        }
                    }
                }
            }
        } else {
            console.log('No matching variant found for ID:', selectedVariantId);
            this.modal.price.textContent = '₱N/A';
            this.modal.availabilityStatus.textContent = 'N/A';
            this.modal.availabilityStatus.className = 'fw-medium text-muted';

            // Update badge color - find it by going up one level to parent and then find badge
            const parent = this.modal.availabilityStatus.parentNode;
            const badge = parent.querySelector('.badge');
            if (badge) {
                badge.className = 'badge rounded-pill bg-secondary-subtle text-secondary me-2';
                const icon = badge.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-question-circle';
                }
            }

            if (this.modal.submitBtn) {
                this.modal.submitBtn.disabled = true;
                this.modal.submitBtn.title = "Please select a variant";
            }
        }
    }

    /**
     * Fetch products with variants from API and render them
     */
    async fetchAndRenderProducts() {
        if (typeof axios === 'undefined') {
            console.error('ProductManager: axios is not available. Cannot fetch products.');
            if (this.container) this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">A critical library (axios) is missing. Products cannot be loaded.</p></div>';
            return;
        }
        try {
            const response = await axios.get(this.config.productsEndpoint);
            console.log('API Response:', response.data);

            // Check for success response structure with data field
            if (response.data && response.data.success && Array.isArray(response.data.data)) {
                const allProducts = response.data.data;

                if (allProducts.length > 0) {
                    // Filter to only products with variants that have inventory
                    const productsWithInventory = allProducts.filter(product => {
                        // Check if product has variants with inventory
                        return product.variants && Array.isArray(product.variants) &&
                            product.variants.some(variant => {
                                const inventory = variant.INVENTORY_QUANTITY || 0;
                                return inventory > 0;
                            });
                    });

                    this.allProducts = productsWithInventory;
                    this.populateCategoryFilter(productsWithInventory);
                    this.renderProducts(productsWithInventory);

                    console.log(`Filtered ${productsWithInventory.length} products with inventory out of ${allProducts.length} total products`);
                } else {
                    console.warn('No products found in API response.');
                    if (this.container) this.container.innerHTML = '<div class="col-12"><p class="text-center">No products available at the moment.</p></div>';
                    this.renderPagination(0);
                }
            } else {
                console.warn('Invalid API response format:', response.data);
                if (this.container) this.container.innerHTML = '<div class="col-12"><p class="text-center">No products available at the moment.</p></div>';
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching products:', error);
            if (this.container) this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load products. Please try again later.</p></div>';
            this.renderPagination(0);
        }
    }

    /**
     * Populate category filter dropdown with unique categories from products
     */
    populateCategoryFilter(products) {
        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (!categoryFilter) return;

        const categories = new Set();
        products.forEach(product => {
            if (product.category) {
                categories.add(product.category);
            }
        });

        let options = '<option value="">All Categories</option>';
        categories.forEach(category => {
            options += `<option value="${category}">${this.formatCategoryName(category)}</option>`;
        });

        categoryFilter.innerHTML = options;
    }

    /**
     * Format category name for display
     */
    formatCategoryName(category) {
        if (typeof category !== 'string') return '';
        return category
            .split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    /**
     * Render product cards with pagination
     */
    renderProducts(products) {
        if (!this.container) return; // Should have been caught in constructor, but good practice

        if (!products || products.length === 0) {
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No products match your filters or none are available.</p></div>';
            this.renderPagination(0);
            return;
        }

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const paginatedProducts = products.slice(startIndex, endIndex);

        let html = '';
        paginatedProducts.forEach(product => {
            html += this.config.cardTemplate(product);
        });

        this.container.innerHTML = html;
        this.renderPagination(products.length);
    }

    /**
     * Render pagination controls
     */
    renderPagination(totalItems) {
        const paginationContainer = document.querySelector(this.config.paginationContainerSelector);
        if (!paginationContainer) return;

        if (totalItems === 0) {
            paginationContainer.innerHTML = ''; // Clear pagination if no items
            return;
        }

        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
        if (totalPages <= 1) { // No pagination needed for 0 or 1 page
            paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <nav aria-label="Product pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="prev">Previous</a>
                    </li>
        `;

        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${this.currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        paginationHTML += `
                    <li class="page-item ${this.currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="next">Next</a>
                    </li>
                </ul>
            </nav>
        `;

        paginationContainer.innerHTML = paginationHTML;
        this.initPaginationControls();
    }

    /**
     * Initialize pagination controls
     */
    initPaginationControls() {
        const paginationLinks = document.querySelectorAll(`${this.config.paginationContainerSelector} .page-link`);
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageAction = e.target.getAttribute('data-page');
                this.handlePageChange(pageAction);
            });
        });
    }

    /**
     * Handle page change
     */
    handlePageChange(pageAction) {
        const totalPages = Math.ceil(this.getFilteredProductsCount() / this.itemsPerPage);

        if (pageAction === 'prev' && this.currentPage > 1) {
            this.currentPage--;
        } else if (pageAction === 'next' && this.currentPage < totalPages) {
            this.currentPage++;
        } else if (!isNaN(pageAction)) {
            const pageNum = parseInt(pageAction);
            if (pageNum >= 1 && pageNum <= totalPages) {
                this.currentPage = pageNum;
            }
        }

        // Apply the same filtering logic as in applyFilters and getFilteredProductsCount,
        // but without resetting the current page
        let filteredProducts = [...this.allProducts];

        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (categoryFilter && categoryFilter.value) {
            filteredProducts = filteredProducts.filter(p => p.category === categoryFilter.value);
        }

        const minPriceFilter = this.filterForm?.querySelector('[name="min-price"]');
        if (minPriceFilter && minPriceFilter.value !== '') {
            const minPrice = parseFloat(minPriceFilter.value);
            filteredProducts = filteredProducts.filter(p => {
                return p.variants && Array.isArray(p.variants) &&
                    p.variants.some(v => {
                        const price = parseFloat(v.VAR_SRP_PRICE || v.var_srp_price || 0);
                        return price >= minPrice;
                    });
            });
        }

        const maxPriceFilter = this.filterForm?.querySelector('[name="max-price"]');
        if (maxPriceFilter && maxPriceFilter.value !== '') {
            const maxPrice = parseFloat(maxPriceFilter.value);
            filteredProducts = filteredProducts.filter(p => {
                return p.variants && Array.isArray(p.variants) &&
                    p.variants.some(v => {
                        const price = parseFloat(v.VAR_SRP_PRICE || v.var_srp_price || 0);
                        return price <= maxPrice;
                    });
            });
        }

        const availabilityFilter = this.filterForm?.querySelector('[name="availability-status"]');
        if (availabilityFilter && availabilityFilter.value !== '') {
            filteredProducts = filteredProducts.filter(p => {
                const status = p.PROD_AVAILABILITY_STATUS || p.prod_availability_status;
                return status === availabilityFilter.value;
            });
        }

        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredProducts = filteredProducts.filter(p => {
                const name = (p.PROD_NAME || p.prod_name || '').toLowerCase();
                const desc = (p.PROD_DESCRIPTION || p.prod_description || '').toLowerCase();
                return name.includes(searchTerm) || desc.includes(searchTerm);
            });
        }

        this.renderProducts(filteredProducts); // Render with the new page
    }

    /**
     * Helper to get count of currently filtered products (before pagination)
     */
    getFilteredProductsCount() {
        let filtered = [...this.allProducts];
        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (categoryFilter && categoryFilter.value) {
            filtered = filtered.filter(p => p.category === categoryFilter.value);
        }
        const minPriceFilter = this.filterForm?.querySelector('[name="min-price"]');
        if (minPriceFilter && minPriceFilter.value !== '') {
            const minPrice = parseFloat(minPriceFilter.value);
            filtered = filtered.filter(p => {
                return p.variants && Array.isArray(p.variants) &&
                    p.variants.some(v => {
                        const price = parseFloat(v.VAR_SRP_PRICE || v.var_srp_price || 0);
                        return price >= minPrice;
                    });
            });
        }
        const maxPriceFilter = this.filterForm?.querySelector('[name="max-price"]');
        if (maxPriceFilter && maxPriceFilter.value !== '') {
            const maxPrice = parseFloat(maxPriceFilter.value);
            filtered = filtered.filter(p => {
                return p.variants && Array.isArray(p.variants) &&
                    p.variants.some(v => {
                        const price = parseFloat(v.VAR_SRP_PRICE || v.var_srp_price || 0);
                        return price <= maxPrice;
                    });
            });
        }
        const availabilityFilter = this.filterForm?.querySelector('[name="availability-status"]');
        if (availabilityFilter && availabilityFilter.value !== '') {
            filtered = filtered.filter(p => {
                const status = p.PROD_AVAILABILITY_STATUS || p.prod_availability_status;
                return status === availabilityFilter.value;
            });
        }
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filtered = filtered.filter(p => {
                const name = (p.PROD_NAME || p.prod_name || '').toLowerCase();
                const desc = (p.PROD_DESCRIPTION || p.prod_description || '').toLowerCase();
                return name.includes(searchTerm) || desc.includes(searchTerm);
            });
        }
        return filtered.length;
    }


    /**
     * Open product modal with details
     */
    async openProductModal(productId) {
        if (!productId) {
            console.error('ProductManager: No product ID provided to openProductModal');
            alert('Sorry, cannot load product details without a product ID.');
            return;
        }

        if (typeof axios === 'undefined' || typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
            console.error('ProductManager: Critical library (axios or Bootstrap) not available.');
            alert('Sorry, cannot load product details at the moment.');
            return;
        }

        try {
            const response = await axios.get(`${this.config.productsEndpoint}/${productId}`);

            // Check for success response structure with data field
            if (response.data && response.data.success && response.data.data) {
                const product = response.data.data;

                this.currentProduct = product;
                this.populateModal(product);

                if (this.modal.element) {
                    const bsModal = new bootstrap.Modal(this.modal.element);
                    bsModal.show();
                } else {
                    console.error("ProductManager: Modal element is not defined, cannot show modal.");
                }
            } else {
                console.error(`Product details not found for ID: ${productId}`);
                alert('Product details could not be loaded.');
            }
        } catch (error) {
            console.error('Error fetching product details:', error);
            alert('Failed to load product details. Please try again.');
        }
    }

    /**
     * Populate the modal with product details
     */
    populateModal(product) {
        if (!product || !this.modal.element) return;

        console.log('Populating modal with product:', product);
        this.currentProduct = product;

        // Reset form values
        if (this.modal.variantSelect) this.modal.variantSelect.innerHTML = '';
        if (this.modal.quantity) this.modal.quantity.value = '1';
        if (this.modal.features) this.modal.features.innerHTML = '';
        if (this.modal.specifications) this.modal.specifications.innerHTML = '';
        if (this.modal.preferredDate) this.modal.preferredDate.value = '';
        if (this.modal.preferredTime) this.modal.preferredTime.value = '';
        if (this.modal.address) this.modal.address.value = '';
        if (this.modal.optionalNotes) this.modal.optionalNotes.value = '';

        // Set min date for the preferred date to today
        if (this.modal.preferredDate) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            this.modal.preferredDate.min = `${yyyy}-${mm}-${dd}`;
        }

        // Populate basic product details
        if (this.modal.productImage) {
            let imagePath = product.PROD_IMAGE || product.prod_image || '';
            if (imagePath && !imagePath.startsWith('http') && !imagePath.startsWith('/uploads/')) {
                imagePath = '/' + imagePath;
            }
            this.modal.productImage.src = imagePath;
        }

        if (this.modal.productName) {
            this.modal.productName.textContent = product.PROD_NAME || product.prod_name || 'Unnamed Product';
        }

        if (this.modal.productCode) {
            this.modal.productCode.textContent = `${product.PROD_ID || product.prod_id || 'N/A'}`;
        }

        // Make sure variants array exists
        if (!product.variants || !Array.isArray(product.variants)) {
            console.error('Product variants missing or not an array:', product);
            product.variants = [];
        }

        console.log('Product variants:', product.variants);

        // Filter variants with inventory > 0 for the dropdown
        const variantsWithStock = product.variants.filter(variant => {
            const inventory = variant.INVENTORY_QUANTITY || 0;
            return inventory > 0;
        });

        console.log('Variants with stock:', variantsWithStock);

        // Populate variants in dropdown
        if (this.modal.variantSelect) {
            if (!variantsWithStock || variantsWithStock.length === 0) {
                this.modal.variantSelect.innerHTML = '<option value="">No variants available in stock</option>';
                console.log('No variants with stock found');
            } else {
                variantsWithStock.forEach(variant => {
                    const capacity = variant.VAR_CAPACITY || variant.var_capacity || 'Unknown';
                    const price = variant.VAR_SRP_PRICE || variant.var_srp_price || '0.00';
                    const inventory = variant.INVENTORY_QUANTITY || 0;
                    const variantId = variant.VAR_ID || variant.var_id;

                    const option = document.createElement('option');
                    option.value = variantId;
                    option.textContent = `${capacity} - ₱${price} (${inventory} in stock)`;

                    this.modal.variantSelect.appendChild(option);
                });

                // Select the first option to trigger price update
                if (this.modal.variantSelect.options.length > 0) {
                    this.modal.variantSelect.selectedIndex = 0;
                    console.log('Selected first variant option');
                }
            }
        }

        // Update price and availability based on selected variant
        this.updateModalPriceAndAvailability();

        // Populate product features
        if (this.modal.features && product.features && Array.isArray(product.features)) {
            if (product.features.length === 0) {
                this.modal.features.innerHTML = '<li class="list-group-item border-0 text-muted">No features available</li>';
            } else {
                let featuresHTML = '';
                product.features.forEach(feature => {
                    const featureName = feature.FEATURE_NAME || feature.feature_name || '';
                    if (featureName) {
                        featuresHTML += `
                            <li class="list-group-item border-0 py-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>${featureName}</span>
                                </div>
                            </li>
                        `;
                    }
                });
                this.modal.features.innerHTML = featuresHTML || '<li class="list-group-item border-0 text-muted">No features available</li>';
            }
        }

        // Populate product specifications
        if (this.modal.specifications && product.specs && Array.isArray(product.specs)) {
            if (product.specs.length === 0) {
                this.modal.specifications.innerHTML = '<li class="list-group-item border-0 text-muted">No specifications available</li>';
            } else {
                let specsHTML = '';
                product.specs.forEach(spec => {
                    const specName = spec.SPEC_NAME || spec.spec_name || '';
                    const specValue = spec.SPEC_VALUE || spec.spec_value || '';
                    if (specName && specValue) {
                        specsHTML += `
                            <li class="list-group-item border-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium">${specName}</span>
                                    <span class="badge bg-light text-dark">${specValue}</span>
                                </div>
                            </li>
                        `;
                    }
                });
                this.modal.specifications.innerHTML = specsHTML || '<li class="list-group-item border-0 text-muted">No specifications available</li>';
            }
        }

        // Reset quantity controls
        const increaseQtyBtn = document.getElementById('increase-quantity');
        const decreaseQtyBtn = document.getElementById('decrease-quantity');

        if (increaseQtyBtn) {
            increaseQtyBtn.disabled = false;
        }

        if (decreaseQtyBtn) {
            decreaseQtyBtn.disabled = true; // Start with 1, so can't decrease
        }

        // Update total amount
        this.updateTotalAmount();

        // Check if product has variants in stock to enable/disable the order button
        const hasVariantsWithStock = variantsWithStock.length > 0;

        if (this.modal.submitBtn) {
            this.modal.submitBtn.disabled = !hasVariantsWithStock;

            if (!hasVariantsWithStock) {
                this.modal.submitBtn.title = "No variants available in stock";
                this.modal.submitBtn.classList.add('btn-secondary');
                this.modal.submitBtn.classList.remove('btn-primary');
                // Update the icon to show out of stock
                const icon = this.modal.submitBtn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-times-circle me-2';
                }
            } else {
                this.modal.submitBtn.title = "";
                this.modal.submitBtn.classList.add('btn-primary');
                this.modal.submitBtn.classList.remove('btn-secondary');
                // Update the icon to show check
                const icon = this.modal.submitBtn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-check-circle me-2';
                }
            }
        }

        // Make sure we start at the booking tab
        const bookingTab = document.getElementById('booking-tab');
        if (bookingTab) {
            const tabInstance = new bootstrap.Tab(bookingTab);
            tabInstance.show();
        }
    }

    /**
     * Get available quantity from inventory
     */
    getAvailableQuantity(variant) {
        console.log('Getting available quantity for variant:', variant);

        // If we have INVENTORY_QUANTITY from the API, use it (comes from the inventory)
        if (variant && variant.INVENTORY_QUANTITY !== undefined) {
            console.log('Using INVENTORY_QUANTITY:', variant.INVENTORY_QUANTITY);
            return parseInt(variant.INVENTORY_QUANTITY);
        }

        // Legacy fallback: check individual inventory items
        if (this.currentProduct && this.currentProduct.inventory && Array.isArray(this.currentProduct.inventory)) {
            const variantId = variant?.VAR_ID || variant?.var_id;
            if (!variantId) {
                console.log('No variant ID found, returning 0');
                return 0;
            }

            let total = 0;
            this.currentProduct.inventory.forEach(inv => {
                const invVariantId = inv.VAR_ID || inv.var_id;
                if (invVariantId === variantId) {
                    const quantity = parseInt(inv.QUANTITY || inv.quantity || 0);
                    total += quantity;
                }
            });

            console.log('Calculated total from inventory:', total);
            return total;
        }

        // Absolute fallback
        console.log('No inventory data found, returning 0');
        return 0;
    }

    /**
     * Handle order confirmation
     */
    async confirmOrder() {
        if (!this.currentProduct || !this.modal.element) {
            console.error('No product selected or modal not initialized');
            return;
        }

        // Get selected variant
        const selectedVariantId = parseInt(this.modal.variantSelect?.value);
        if (!selectedVariantId) {
            alert('Please select a product variant');
            return;
        }

        console.log('Confirming order for variant:', selectedVariantId);

        // Get selected variant details
        const selectedVariant = this.currentProduct.variants.find(v => {
            const id = v.VAR_ID || v.var_id;
            return id === selectedVariantId;
        });

        if (!selectedVariant) {
            console.error('Selected variant not found in current product variants');
            alert('Error: Could not find selected variant');
            return;
        }

        // Get quantity
        const quantity = parseInt(this.modal.quantity.value) || 1;
        if (quantity <= 0) {
            alert('Please enter a valid quantity');
            return;
        }

        const availableQuantity = this.getAvailableQuantity(selectedVariant);
        if (quantity > availableQuantity) {
            alert(`Sorry, only ${availableQuantity} units available for this variant.`);
            return;
        }

        // Get other form values
        const preferredDate = this.modal.preferredDate?.value || '';
        const preferredTime = this.modal.preferredTime?.value || '';
        const address = this.modal.address?.value || '';
        const optionalNotes = this.modal.optionalNotes?.value || '';

        // Validate required fields
        if (!preferredDate) {
            alert('Please select a preferred date');
            return;
        }
        if (!preferredTime) {
            alert('Please select a preferred time');
            return;
        }
        if (!address) {
            alert('Please enter an address for delivery');
            return;
        }

        // Get price from selected variant
        const unitPrice = parseFloat(selectedVariant.VAR_SRP_PRICE || selectedVariant.var_srp_price || 0);

        try {
            console.log('Submitting booking with data:', {
                PB_VARIANT_ID: selectedVariantId,
                PB_QUANTITY: quantity,
                PB_UNIT_PRICE: unitPrice,
                PB_PREFERRED_DATE: preferredDate,
                PB_PREFERRED_TIME: preferredTime,
                PB_ADDRESS: address,
                PB_DESCRIPTION: optionalNotes
            });
            console.log('Using order endpoint:', this.config.orderEndpoint);

            // Show loading state
            this.modal.submitBtn.disabled = true;
            this.modal.submitBtn.innerHTML = 'Processing...';

            const response = await axios.post(this.config.orderEndpoint, {
                PB_VARIANT_ID: selectedVariantId,
                PB_QUANTITY: quantity,
                PB_UNIT_PRICE: unitPrice,
                PB_PREFERRED_DATE: preferredDate,
                PB_PREFERRED_TIME: preferredTime,
                PB_ADDRESS: address,
                PB_DESCRIPTION: optionalNotes
            });

            console.log('Booking response:', response.data);

            if (response.data && response.data.success) {
                // Close the modal
                this.closeModal();

                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: response.data.message || 'Your booking has been submitted successfully.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    // Refresh the page to update product availability
                    window.location.reload();
                });
            } else {
                const errorMessage = (response.data && response.data.message)
                    ? response.data.message
                    : 'An unknown error occurred. Please try again.';

                console.error('Error in booking submission:', errorMessage, response.data);

                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error'
                });

                // Reset submit button
                this.modal.submitBtn.disabled = false;
                this.modal.submitBtn.innerHTML = 'Confirm';
            }
        } catch (error) {
            console.error('Exception in booking submission:', error);

            // Log detailed error information
            if (error.response) {
                // The request was made and the server responded with a status code
                // that falls out of the range of 2xx
                console.error('Server response error:', {
                    data: error.response.data,
                    status: error.response.status,
                    headers: error.response.headers
                });
            } else if (error.request) {
                // The request was made but no response was received
                console.error('No response received:', error.request);
            } else {
                // Something happened in setting up the request that triggered an Error
                console.error('Request setup error:', error.message);
            }

            // Create a user-friendly error message
            let errorMessage = 'An error occurred. Please try again.';

            if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            } else if (error.message && (error.message.includes('Network Error') || error.message.includes('timeout'))) {
                errorMessage = 'Network connection issue. Please check your internet connection and try again.';
            }

            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error'
            });

            // Reset submit button
            this.modal.submitBtn.disabled = false;
            this.modal.submitBtn.innerHTML = 'Confirm';
        }
    }

    /**
     * Close the product modal
     */
    closeModal() {
        if (!this.modal.element) return;

        const bsModal = bootstrap.Modal.getInstance(this.modal.element);
        if (bsModal) {
            bsModal.hide();
            console.log('Modal closed');
        } else {
            console.warn('Could not get Modal instance');
        }
    }
}