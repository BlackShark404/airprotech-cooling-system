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

        // Initialize modal elements references
        this.modal = { element: document.getElementById(this.config.modalId) };
        if (this.modal.element) {
            this.modal.image = document.getElementById('modal-product-image');
            this.modal.name = document.getElementById('modal-product-name');
            this.modal.variantSelect = document.getElementById('modal-variant-select');
            this.modal.price = document.getElementById('modal-product-price');
            this.modal.code = document.getElementById('modal-product-code');
            this.modal.availabilityStatus = document.getElementById('modal-availability-status');
            this.modal.quantity = document.getElementById('modal-quantity');
            this.modal.orderId = document.getElementById('modal-order-id');
            this.modal.orderDate = document.getElementById('modal-order-date');
            this.modal.status = document.getElementById('modal-status');
            this.modal.totalAmount = document.getElementById('modal-total-amount');
            this.modal.features = document.getElementById('modal-features');
            this.modal.specifications = document.getElementById('modal-specifications');
            this.modal.confirmButton = document.getElementById('confirm-order');
            this.modal.preferredDate = document.getElementById('modal-preferred-date');
            this.modal.preferredTime = document.getElementById('modal-preferred-time');
            this.modal.address = document.getElementById('modal-address');
            this.modal.description = document.getElementById('modal-description');
        } else {
            console.warn(`ProductManager: Modal with ID "${this.config.modalId}" not found. Modal functionality will be disabled.`);
        }

        // Store all products for filtering
        this.allProducts = [];

        // Pagination state
        this.currentPage = 1;
        this.itemsPerPage = this.config.itemsPerPage;

        // Initialize modal controls and order confirmation if modal exists
        if (this.modal.element) {
            this.initModalControls();
        }

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
     * Initialize controls within the modal
     */
    initModalControls() {
        const increaseQtyBtn = document.getElementById('increase-quantity');
        const decreaseQtyBtn = document.getElementById('decrease-quantity');

        if (increaseQtyBtn && this.modal.quantity) {
            increaseQtyBtn.addEventListener('click', () => {
                if (!this.currentProduct || !this.modal.variantSelect || !this.modal.quantity) return;
                const quantity = parseInt(this.modal.quantity.value, 10);
                const selectedVariant = this.currentProduct.variants.find(
                    v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
                );
                if (selectedVariant && quantity < this.getAvailableQuantity(selectedVariant)) {
                    this.modal.quantity.value = quantity + 1;
                    this.updateTotalAmount();
                }
            });
        }

        if (decreaseQtyBtn && this.modal.quantity) {
            decreaseQtyBtn.addEventListener('click', () => {
                if (!this.modal.quantity) return;
                const quantity = parseInt(this.modal.quantity.value, 10);
                if (quantity > 1) {
                    this.modal.quantity.value = quantity - 1;
                    this.updateTotalAmount();
                }
            });
        }

        if (this.modal.variantSelect) {
            this.modal.variantSelect.addEventListener('change', () => {
                this.updateModalPriceAndAvailability();
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

        if (this.modal.confirmButton) {
            this.modal.confirmButton.addEventListener('click', () => {
                this.confirmOrder();
            });
        }
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
            this.modal.availabilityStatus.className = 'fw-bold text-danger';

            // Disable confirm button
            if (this.modal.confirmButton) {
                this.modal.confirmButton.disabled = true;
                this.modal.confirmButton.title = "No variants available in stock";
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

            if (availableQuantity > 10) {
                productStatus = `In Stock (${availableQuantity})`;
                statusClass = 'text-success';
            } else if (availableQuantity > 0) {
                productStatus = `Limited Stock (${availableQuantity})`;
                statusClass = 'text-warning';
            }

            // Update the display
            this.modal.availabilityStatus.textContent = productStatus;
            this.modal.availabilityStatus.className = `fw-bold ${statusClass}`;

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
                if (this.modal.confirmButton) {
                    this.modal.confirmButton.disabled = availableQuantity <= 0;
                    if (availableQuantity <= 0) {
                        this.modal.confirmButton.title = "This product is out of stock";
                    } else {
                        this.modal.confirmButton.title = "";
                    }
                }
            }
        } else {
            console.log('No matching variant found for ID:', selectedVariantId);
            this.modal.price.textContent = '₱N/A';
            this.modal.availabilityStatus.textContent = 'N/A';
            this.modal.availabilityStatus.className = 'fw-bold text-muted';

            if (this.modal.confirmButton) {
                this.modal.confirmButton.disabled = true;
                this.modal.confirmButton.title = "Please select a variant";
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

        console.log('Populating modal with product:', product); // Debug: Log the entire product
        this.currentProduct = product;

        // Reset form values
        if (this.modal.variantSelect) this.modal.variantSelect.innerHTML = '';
        if (this.modal.quantity) this.modal.quantity.value = '1';
        if (this.modal.features) this.modal.features.innerHTML = '';
        if (this.modal.specifications) this.modal.specifications.innerHTML = '';
        if (this.modal.preferredDate) this.modal.preferredDate.value = '';
        if (this.modal.preferredTime) this.modal.preferredTime.value = '';
        if (this.modal.address) this.modal.address.value = '';
        if (this.modal.description) this.modal.description.value = '';

        // Set min date for the preferred date to today
        if (this.modal.preferredDate) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            this.modal.preferredDate.min = `${yyyy}-${mm}-${dd}`;
        }

        // Populate basic product details
        if (this.modal.image) {
            let imagePath = product.PROD_IMAGE || product.prod_image || '';
            if (imagePath && !imagePath.startsWith('http') && !imagePath.startsWith('/uploads/')) {
                imagePath = '/' + imagePath;
            }
            this.modal.image.src = imagePath;
        }

        if (this.modal.name) {
            this.modal.name.textContent = product.PROD_NAME || product.prod_name || 'Unnamed Product';
        }

        if (this.modal.code) {
            this.modal.code.textContent = `Product ID: ${product.PROD_ID || product.prod_id || 'N/A'}`;
        }

        // Make sure variants array exists
        if (!product.variants || !Array.isArray(product.variants)) {
            console.error('Product variants missing or not an array:', product);
            product.variants = [];
        }

        console.log('Product variants:', product.variants); // Debug: Log the variants

        // Filter variants with inventory > 0 for the dropdown
        const variantsWithStock = product.variants.filter(variant => {
            // Log each variant inventory for debugging
            console.log('Variant:', variant, 'Inventory:', variant.INVENTORY_QUANTITY);

            const inventory = variant.INVENTORY_QUANTITY || 0;
            return inventory > 0;
        });

        console.log('Variants with stock:', variantsWithStock); // Debug: Log filtered variants

        // Populate variants in dropdown
        if (this.modal.variantSelect) {
            if (!variantsWithStock || variantsWithStock.length === 0) {
                this.modal.variantSelect.innerHTML = '<option value="">No variants available in stock</option>';
                console.log('No variants with stock found'); // Debug
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
                    console.log('Selected first variant option'); // Debug
                }
            }
        }

        // Update price and availability based on selected variant
        this.updateModalPriceAndAvailability();

        // Populate product features
        if (this.modal.features && product.features && Array.isArray(product.features)) {
            if (product.features.length === 0) {
                this.modal.features.innerHTML = '<li class="text-muted">No features available</li>';
            } else {
                let featuresHTML = '';
                product.features.forEach(feature => {
                    const featureName = feature.FEATURE_NAME || feature.feature_name || '';
                    if (featureName) {
                        featuresHTML += `<li><i class="fas fa-check-circle text-success me-2"></i>${featureName}</li>`;
                    }
                });
                this.modal.features.innerHTML = featuresHTML || '<li class="text-muted">No features available</li>';
            }
        }

        // Populate product specifications
        if (this.modal.specifications && product.specs && Array.isArray(product.specs)) {
            if (product.specs.length === 0) {
                this.modal.specifications.innerHTML = '<li class="text-muted">No specifications available</li>';
            } else {
                let specsHTML = '';
                product.specs.forEach(spec => {
                    const specName = spec.SPEC_NAME || spec.spec_name || '';
                    const specValue = spec.SPEC_VALUE || spec.spec_value || '';
                    if (specName && specValue) {
                        specsHTML += `
                            <li class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>${specName}:</strong>
                                    <span class="ms-3 text-end">${specValue}</span>
                                </div>
                            </li>
                        `;
                    }
                });
                this.modal.specifications.innerHTML = specsHTML || '<li class="text-muted">No specifications available</li>';
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

        // Set order ID and date (hidden until checkout)
        if (this.modal.orderId) {
            this.modal.orderId.textContent = 'To be generated';
        }

        if (this.modal.orderDate) {
            this.modal.orderDate.textContent = new Date().toLocaleDateString();
        }

        if (this.modal.status) {
            this.modal.status.textContent = 'Pending';
            this.modal.status.className = 'text-primary fw-bold';
        }

        // Update total amount
        this.updateTotalAmount();

        // Check if product has variants in stock to enable/disable the order button
        const hasVariantsWithStock = variantsWithStock.length > 0;

        if (this.modal.confirmButton) {
            this.modal.confirmButton.disabled = !hasVariantsWithStock;
            this.modal.confirmButton.classList.add('btn-lg');
            this.modal.confirmButton.classList.add('px-4');

            if (!hasVariantsWithStock) {
                this.modal.confirmButton.title = "No variants available in stock";
                this.modal.confirmButton.classList.add('btn-secondary');
                this.modal.confirmButton.classList.remove('btn-primary');
            } else {
                this.modal.confirmButton.title = "";
                this.modal.confirmButton.classList.add('btn-primary');
                this.modal.confirmButton.classList.remove('btn-secondary');
            }
        }

        // Style quantity controls
        const quantityControls = this.modal.quantity?.parentElement;
        if (quantityControls) {
            quantityControls.classList.add('input-group');
            quantityControls.classList.add('mb-3');

            const decreaseBtn = document.getElementById('decrease-quantity');
            const increaseBtn = document.getElementById('increase-quantity');

            if (decreaseBtn) {
                decreaseBtn.classList.add('btn');
                decreaseBtn.classList.add('btn-outline-secondary');
            }

            if (increaseBtn) {
                increaseBtn.classList.add('btn');
                increaseBtn.classList.add('btn-outline-secondary');
            }

            if (this.modal.quantity) {
                this.modal.quantity.classList.add('text-center');
            }

            // Find the label for the quantity input and style it
            const quantityLabel = quantityControls.parentElement?.querySelector('label');
            if (quantityLabel) {
                quantityLabel.classList.add('fw-bold');
                quantityLabel.classList.add('mb-2');
            }
        }

        // Style the total amount
        if (this.modal.totalAmount) {
            const totalAmountContainer = this.modal.totalAmount.parentElement;
            if (totalAmountContainer) {
                totalAmountContainer.classList.add('bg-light');
                totalAmountContainer.classList.add('p-3');
                totalAmountContainer.classList.add('rounded');
                totalAmountContainer.classList.add('mb-3');
                totalAmountContainer.classList.add('border');

                // Find the label for the total amount and style it
                const totalLabel = totalAmountContainer.querySelector('label, span:not(#modal-total-amount)');
                if (totalLabel) {
                    totalLabel.classList.add('fw-bold');
                }

                this.modal.totalAmount.classList.add('fw-bold');
                this.modal.totalAmount.classList.add('fs-4');
                this.modal.totalAmount.classList.add('text-primary');
            }
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
     * Handle order confirmation and send to backend
     */
    async confirmOrder() {
        if (typeof axios === 'undefined') {
            console.error('ProductManager: axios is not available. Cannot place order.');
            alert('Order placement service is currently unavailable.');
            return;
        }
        if (!this.currentProduct) {
            alert('No product selected. Please try again.');
            return;
        }

        if (!this.modal.variantSelect || !this.modal.quantity) {
            alert('Modal elements for order are missing. Cannot proceed.');
            return;
        }

        if (!this.modal.preferredDate || !this.modal.preferredTime || !this.modal.address) {
            alert('Please fill in all required booking information: preferred date, time, and address.');
            return;
        }

        if (!this.modal.preferredDate.value) {
            alert('Please select a preferred date for your booking.');
            this.modal.preferredDate.focus();
            return;
        }

        if (!this.modal.preferredTime.value) {
            alert('Please select a preferred time for your booking.');
            this.modal.preferredTime.focus();
            return;
        }

        if (!this.modal.address.value.trim()) {
            alert('Please provide your address for delivery/installation.');
            this.modal.address.focus();
            return;
        }

        const variantId = parseInt(this.modal.variantSelect.value);
        if (isNaN(variantId)) {
            alert('Please select a valid product variant.');
            this.modal.variantSelect.focus();
            return;
        }

        // Get the original variant object from the product data
        const variants = this.currentProduct.variants || [];
        const selectedVariant = variants.find(v => {
            const id = v.VAR_ID || v.var_id;
            return id === variantId;
        });

        if (!selectedVariant) {
            alert('Invalid product variant selected. Please select a valid option.');
            return;
        }

        const orderData = {
            PB_VARIANT_ID: variantId,
            PB_QUANTITY: parseInt(this.modal.quantity.value, 10),
            PB_UNIT_PRICE: parseFloat(selectedVariant.VAR_SRP_PRICE || selectedVariant.var_srp_price),
            PB_STATUS: 'pending',
            PB_ORDER_DATE: new Date().toISOString(),
            PB_PREFERRED_DATE: this.modal.preferredDate.value,
            PB_PREFERRED_TIME: this.modal.preferredTime.value,
            PB_ADDRESS: this.modal.address.value.trim(),
            PB_DESCRIPTION: this.modal.description ? this.modal.description.value.trim() : ''
        };

        try {
            if (this.modal.confirmButton) {
                this.modal.confirmButton.disabled = true;
                this.modal.confirmButton.textContent = 'Placing Order...';
            }

            console.log('Sending order data:', orderData);
            const response = await axios.post(this.config.orderEndpoint, orderData);
            console.log('Order response:', response.data);

            // Check for success response structure with data field
            if (response.data && response.data.success) {
                const orderData = response.data.data;
                const orderId = orderData && orderData.PB_ID ? orderData.PB_ID : 'N/A';
                alert('Booking placed successfully! Booking ID: ' + orderId);
            } else {
                alert('Booking received but no confirmation details were returned.');
            }

            if (this.modal.element) {
                const bsModal = bootstrap.Modal.getInstance(this.modal.element);
                if (bsModal) {
                    bsModal.hide();
                }
            }

            this.fetchAndRenderProducts();
        } catch (error) {
            console.error('Error placing booking:', error.response ? error.response.data : error.message);
            let errorMessage = 'Failed to place booking. Please try again.';
            if (error.response && error.response.status === 401) {
                errorMessage = 'You must be logged in to place a booking. Please log in and try again.';
            } else if (error.response && error.response.data && error.response.data.message) {
                errorMessage = `Failed to place booking. ${error.response.data.message}`;
            }
            alert(errorMessage);
        } finally {
            if (this.modal.confirmButton) {
                this.modal.confirmButton.disabled = false;
                this.modal.confirmButton.textContent = 'Confirm Booking';
            }
        }
    }
}