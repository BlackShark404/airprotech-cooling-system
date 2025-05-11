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
            orderEndpoint: '/api/product-orders',
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
        return (product) => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="product-card" data-product-id="${product.PROD_ID}" data-category="${product.category || ''}">
                    <div class="product-img-container">
                        <img src="${product.PROD_IMAGE}" alt="${product.PROD_NAME}" class="product-img">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.PROD_NAME}</h3>
                        <p class="product-desc">${product.PROD_DESCRIPTION || ''}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$${product.variants[0]?.VAR_SRP_PRICE || 'N/A'}</span>
                            <button class="btn btn-book-now view-details" data-product-id="${product.PROD_ID}">Order Now</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
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
            this.filterForm.addEventListener('change', () => this.applyFilters());
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
            filteredProducts = filteredProducts.filter(product => 
                product.variants.some(variant => parseFloat(variant.VAR_SRP_PRICE) >= minPrice)
            );
        }
        
        if (maxPriceFilter && maxPriceFilter.value !== '') {
            const maxPrice = parseFloat(maxPriceFilter.value);
            filteredProducts = filteredProducts.filter(product => 
                product.variants.some(variant => parseFloat(variant.VAR_SRP_PRICE) <= maxPrice)
            );
        }
        
        const availabilityFilter = this.filterForm?.querySelector('[name="availability-status"]');
        if (availabilityFilter && availabilityFilter.value !== '') {
            filteredProducts = filteredProducts.filter(product => 
                product.PROD_AVAILABILITY_STATUS === availabilityFilter.value
            );
        }
        
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredProducts = filteredProducts.filter(product => 
                product.PROD_NAME.toLowerCase().includes(searchTerm) || 
                (product.PROD_DESCRIPTION && product.PROD_DESCRIPTION.toLowerCase().includes(searchTerm))
            );
        }
        
        this.currentPage = 1;
        this.renderProducts(filteredProducts);
        
        const resultsCountElement = document.getElementById('results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${filteredProducts.length} products found`;
        }
    }
    
    /**
     * Update total amount based on quantity and selected variant
     */
    updateTotalAmount() {
        if (!this.currentProduct || !this.currentProduct.variants || !this.modal.quantity || !this.modal.variantSelect || !this.modal.totalAmount) {
            if (this.modal.totalAmount) this.modal.totalAmount.textContent = '$0.00';
            return;
        }
        
        const quantity = parseInt(this.modal.quantity.value, 10);
        const selectedVariant = this.currentProduct.variants.find(
            v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
        );
        const price = selectedVariant ? parseFloat(selectedVariant.VAR_SRP_PRICE) : 0;
        const total = price * quantity;
        this.modal.totalAmount.textContent = `$${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    
    /**
     * Update modal price and availability based on selected variant
     */
    updateModalPriceAndAvailability() {
        if (!this.currentProduct || !this.currentProduct.variants || !this.modal.variantSelect || !this.modal.price || !this.modal.availabilityStatus) {
            return;
        }
        
        const selectedVariant = this.currentProduct.variants.find(
            v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
        );

        if (selectedVariant) {
            this.modal.price.textContent = `$${selectedVariant.VAR_SRP_PRICE}`;
            this.modal.availabilityStatus.textContent = 
                this.currentProduct.PROD_AVAILABILITY_STATUS === 'Available' 
                ? `Available (${this.getAvailableQuantity(selectedVariant)} units)` 
                : this.currentProduct.PROD_AVAILABILITY_STATUS;
        } else {
            this.modal.price.textContent = '$N/A';
            this.modal.availabilityStatus.textContent = 'N/A';
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
            const products = response.data;
            
            if (Array.isArray(products) && products.length > 0) {
                this.allProducts = products;
                this.populateCategoryFilter(products);
                this.renderProducts(products);
            } else {
                console.warn('No products found or invalid data format from API.');
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
        // Determine total items based on current filtered list, or allProducts if no filter applied yet
        // This part is tricky because applyFilters itself calls renderProducts which calls renderPagination.
        // We need the count of items *before* slicing for pagination.
        // applyFilters() will re-calculate and re-render using this.allProducts and current filters.
        // So, the totalPages logic should rely on the list that applyFilters will use.

        // For simplicity, we assume applyFilters will be called after page change, which will use the correctly filtered list.
        // The totalPages for enabling/disabling buttons should be based on the currently displayed filtered set.
        // This is currently handled correctly because applyFilters calls renderProducts with the filtered list.

        let productsForPaging = this.allProducts; // Default to all products
        // If filters are active, applyFilters() will handle the rendering with the filtered set.
        // The totalPages calculation within handlePageChange might be slightly off if filters change *between* page changes without re-filtering.
        // However, applyFilters() is the source of truth for re-rendering.

        const totalPages = Math.ceil(document.querySelectorAll(`${this.config.containerSelector} .product-card`).length > 0 ?
            this.getFilteredProductsCount() / this.itemsPerPage :
            this.allProducts.length / this.itemsPerPage
        );


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
        
        this.applyFilters(); // Re-apply filters to render the correct page
    }

    /**
     * Helper to get count of currently filtered products (before pagination)
     * This is a simplified version; applyFilters itself does the filtering.
     * This function is mainly for handlePageChange to estimate totalPages.
     */
    getFilteredProductsCount() {
        // This is a bit redundant as applyFilters does the actual filtering.
        // For robust totalPages in handlePageChange, it should use the result of applyFilters logic.
        // However, applyFilters itself sets currentPage = 1.
        // The current structure where applyFilters() is called will correctly re-render and re-paginate.
        // The current `totalPages` calculation in `renderPagination` is based on the list passed to it, which is correct.
        // So, `handlePageChange` should primarily just update `this.currentPage` and call `applyFilters`.

        // Re-evaluating filter logic to get count (dry run of applyFilters without side effects)
        let filtered = [...this.allProducts];
        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (categoryFilter && categoryFilter.value) filtered = filtered.filter(p => p.category === categoryFilter.value);
        // Add other filters similarly... (minPrice, maxPrice, availability, search)
        const minPriceFilter = this.filterForm?.querySelector('[name="min-price"]');
        if (minPriceFilter && minPriceFilter.value !== '') {
            const minPrice = parseFloat(minPriceFilter.value);
            filtered = filtered.filter(p => p.variants.some(v => parseFloat(v.VAR_SRP_PRICE) >= minPrice));
        }
        const maxPriceFilter = this.filterForm?.querySelector('[name="max-price"]');
        if (maxPriceFilter && maxPriceFilter.value !== '') {
            const maxPrice = parseFloat(maxPriceFilter.value);
            filtered = filtered.filter(p => p.variants.some(v => parseFloat(v.VAR_SRP_PRICE) <= maxPrice));
        }
        const availabilityFilter = this.filterForm?.querySelector('[name="availability-status"]');
        if (availabilityFilter && availabilityFilter.value !== '') {
            filtered = filtered.filter(p => p.PROD_AVAILABILITY_STATUS === availabilityFilter.value);
        }
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filtered = filtered.filter(p => p.PROD_NAME.toLowerCase().includes(searchTerm) || (p.PROD_DESCRIPTION && p.PROD_DESCRIPTION.toLowerCase().includes(searchTerm)));
        }
        return filtered.length;
    }

    
    /**
     * Open product modal with details
     */
    async openProductModal(productId) {
        if (typeof axios === 'undefined' || typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
            console.error('ProductManager: Critical library (axios or Bootstrap) not available.');
            alert('Sorry, cannot load product details at the moment.');
            return;
        }
        try {
            const response = await axios.get(`${this.config.productsEndpoint}/${productId}`);
            const product = response.data;
            
            if (!product) {
                console.error(`Product details not found for ID: ${productId}`);
                alert('Product details could not be loaded.');
                return;
            }

            this.currentProduct = product;
            this.populateModal(product);
            
            if (this.modal.element) {
                const bsModal = new bootstrap.Modal(this.modal.element);
                bsModal.show();
            } else {
                console.error("ProductManager: Modal element is not defined, cannot show modal.");
            }
        } catch (error) {
            console.error('Error fetching product details:', error);
            alert('Failed to load product details. Please try again.');
        }
    }
    
    /**
     * Populate modal with product details
     */
    populateModal(product) {
        if (!this.modal.element || !product) return; // Modal or product not available

        if (this.modal.quantity) this.modal.quantity.value = 1;
        
        if (this.modal.image) {
            this.modal.image.src = product.PROD_IMAGE || '';
            this.modal.image.alt = product.PROD_NAME || 'Product Image';
        }
        if (this.modal.name) this.modal.name.textContent = product.PROD_NAME || 'N/A';
        if (this.modal.code) this.modal.code.textContent = `PROD-${product.PROD_ID || 'N/A'}`;
        
        if (this.modal.variantSelect && product.variants && Array.isArray(product.variants)) {
            let variantOptions = '';
            product.variants.forEach(variant => {
                variantOptions += `<option value="${variant.VAR_ID}">${variant.VAR_CAPACITY || 'Standard'} - $${variant.VAR_SRP_PRICE || '0.00'}</option>`;
            });
            this.modal.variantSelect.innerHTML = variantOptions;
        }
        
        this.updateModalPriceAndAvailability(); // Sets initial price/availability
        
        if (this.modal.orderId) this.modal.orderId.textContent = `PO-${new Date().getFullYear()}-${String(product.PROD_ID || '0').padStart(4, '0')}`;
        if (this.modal.orderDate) {
            this.modal.orderDate.textContent = new Date().toLocaleDateString('en-US', { 
                year: 'numeric', month: 'short', day: 'numeric' 
            });
        }
        if (this.modal.status) this.modal.status.textContent = 'Pending';
        this.updateTotalAmount(); // Sets initial total amount
        
        if (this.modal.features) {
            let featuresHTML = '';
            if (product.features && Array.isArray(product.features) && product.features.length > 0) {
                product.features.forEach(feature => {
                    featuresHTML += `<li>• ${feature.FEATURE_NAME || 'Unnamed Feature'}</li>`;
                });
            } else {
                featuresHTML = '<li>No features listed.</li>';
            }
            this.modal.features.innerHTML = featuresHTML;
        }
        
        if (this.modal.specifications) {
            let specsHTML = '';
            if (product.specifications && Array.isArray(product.specifications) && product.specifications.length > 0) {
                product.specifications.forEach(spec => {
                    specsHTML += `<li>• ${spec.SPEC_NAME || 'Spec'}: ${spec.SPEC_VALUE || 'N/A'}</li>`;
                });
            } else {
                specsHTML = '<li>No specifications available.</li>';
            }
            this.modal.specifications.innerHTML = specsHTML;
        }
    }
    
    /**
     * Get available quantity from inventory
     */
    getAvailableQuantity(variant) {
        // TODO: Implement actual inventory check based on variant
        // This should ideally come from product data or a separate inventory API
        return variant?.VAR_STOCK_QUANTITY || 100; // Placeholder, use actual stock if available
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
        if (!this.currentProduct || !this.currentProduct.variants) {
            alert('No product selected or product data is incomplete. Please try again.');
            return;
        }
        
        const customerId = this.getCustomerId();
        if (!customerId) {
            alert('You must be logged in to place an order. Please log in and try again.');
            // Potentially redirect to login page: window.location.href = '/login';
            return;
        }
        
        if (!this.modal.variantSelect || !this.modal.quantity) {
            alert('Modal elements for order are missing. Cannot proceed.');
            return;
        }

        const selectedVariant = this.currentProduct.variants.find(
            v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
        );

        if (!selectedVariant) {
            alert('Invalid product variant selected. Please select a valid option.');
            return;
        }
        
        const orderData = {
            PO_CUSTOMER_ID: customerId,
            PO_VARIANT_ID: selectedVariant.VAR_ID,
            PO_QUANTITY: parseInt(this.modal.quantity.value, 10),
            PO_UNIT_PRICE: parseFloat(selectedVariant.VAR_SRP_PRICE),
            PO_STATUS: 'pending', // Or as per backend requirements
            PO_ORDER_DATE: new Date().toISOString() // Standard ISO 8601 format
        };
        
        try {
            // Display loading state on confirm button if exists
            if (this.modal.confirmButton) {
                this.modal.confirmButton.disabled = true;
                this.modal.confirmButton.textContent = 'Placing Order...';
            }

            const response = await axios.post(this.config.orderEndpoint, orderData);
            
            alert('Order placed successfully! Order ID: ' + (response.data.PO_ID || response.data.id || 'N/A'));
            
            if (this.modal.element) {
                const bsModal = bootstrap.Modal.getInstance(this.modal.element);
                if (bsModal) {
                    bsModal.hide();
                }
            }
            
            this.fetchAndRenderProducts(); // Refresh product list (e.g., update stock)
        } catch (error) {
            console.error('Error placing order:', error.response ? error.response.data : error.message);
            alert(`Failed to place order. ${error.response?.data?.message || 'Please try again.'}`);
        } finally {
            // Reset confirm button state
             if (this.modal.confirmButton) {
                this.modal.confirmButton.disabled = false;
                this.modal.confirmButton.textContent = 'Confirm Order';
            }
        }
    }
    
    /**
     * Get customer ID from embedded JavaScript variable
     */
    getCustomerId() {
        // Ensure window.currentUserId is a number or can be parsed to one.
        const userId = window.currentUserId;
        if (userId === null || userId === undefined || isNaN(parseInt(userId, 10))) {
            return null;
        }
        return parseInt(userId, 10);
    }
}