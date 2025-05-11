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
        
        // Initialize modal elements references
        this.modal = {
            element: document.getElementById(this.config.modalId),
            image: document.getElementById('modal-product-image'),
            name: document.getElementById('modal-product-name'),
            variantSelect: document.getElementById('modal-variant-select'),
            price: document.getElementById('modal-product-price'),
            code: document.getElementById('modal-product-code'),
            availabilityStatus: document.getElementById('modal-availability-status'),
            quantity: document.getElementById('modal-quantity'),
            orderId: document.getElementById('modal-order-id'),
            orderDate: document.getElementById('modal-order-date'),
            status: document.getElementById('modal-status'),
            totalAmount: document.getElementById('modal-total-amount'),
            features: document.getElementById('modal-features'),
            specifications: document.getElementById('modal-specifications'),
            confirmButton: document.getElementById('confirm-order')
        };
        
        // Container for product cards
        this.container = document.querySelector(this.config.containerSelector);
        
        // Store all products for filtering
        this.allProducts = [];
        
        // Pagination state
        this.currentPage = 1;
        this.itemsPerPage = this.config.itemsPerPage;
        
        // Initialize modal controls and order confirmation
        this.initModalControls();
        
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
        // Quantity increase/decrease
        document.getElementById('increase-quantity').addEventListener('click', () => {
            const quantity = parseInt(this.modal.quantity.value, 10);
            const selectedVariant = this.currentProduct.variants.find(
                v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
            );
            if (selectedVariant && quantity < this.getAvailableQuantity(selectedVariant)) {
                this.modal.quantity.value = quantity + 1;
                this.updateTotalAmount();
            }
        });
        
        document.getElementById('decrease-quantity').addEventListener('click', () => {
            const quantity = parseInt(this.modal.quantity.value, 10);
            if (quantity > 1) {
                this.modal.quantity.value = quantity - 1;
                this.updateTotalAmount();
            }
        });
        
        // Variant selection change
        if (this.modal.variantSelect) {
            this.modal.variantSelect.addEventListener('change', () => {
                this.updateModalPriceAndAvailability();
                this.updateTotalAmount();
            });
        }
        
        // Add event listener to all "Order Now" buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-details')) {
                const productId = e.target.getAttribute('data-product-id');
                this.openProductModal(productId);
            }
        });
        
        // Add event listener for confirm order button
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
        // Get filter form and search input
        this.filterForm = document.getElementById(this.config.filterFormId);
        this.searchInput = document.getElementById(this.config.searchInputId);
        
        // Add event listeners for filter changes
        if (this.filterForm) {
            this.filterForm.addEventListener('change', () => this.applyFilters());
            this.filterForm.addEventListener('reset', () => {
                setTimeout(() => this.applyFilters(), 10);
            });
        }
        
        // Add event listener for search input
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
        if (!this.allProducts.length) return;
        
        let filteredProducts = [...this.allProducts];
        
        // Apply category filter if exists
        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (categoryFilter && categoryFilter.value) {
            filteredProducts = filteredProducts.filter(product => 
                product.category === categoryFilter.value
            );
        }
        
        // Apply price range filter if exists
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
        
        // Apply availability status filter
        const availabilityFilter = this.filterForm?.querySelector('[name="availability-status"]');
        if (availabilityFilter && availabilityFilter.value !== '') {
            filteredProducts = filteredProducts.filter(product => 
                product.PROD_AVAILABILITY_STATUS === availabilityFilter.value
            );
        }
        
        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredProducts = filteredProducts.filter(product => 
                product.PROD_NAME.toLowerCase().includes(searchTerm) || 
                (product.PROD_DESCRIPTION && product.PROD_DESCRIPTION.toLowerCase().includes(searchTerm))
            );
        }
        
        // Reset to first page when filters change
        this.currentPage = 1;
        
        // Render filtered products with pagination
        this.renderProducts(filteredProducts);
        
        // Update results count if element exists
        const resultsCountElement = document.getElementById('results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${filteredProducts.length} products found`;
        }
    }
    
    /**
     * Update total amount based on quantity and selected variant
     */
    updateTotalAmount() {
        const quantity = parseInt(this.modal.quantity.value, 10);
        const selectedVariant = this.currentProduct.variants.find(
            v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
        );
        const price = selectedVariant ? parseFloat(selectedVariant.VAR_SRP_PRICE) : 0;
        const total = price * quantity;
        this.modal.totalAmount.textContent = `$${total.toLocaleString()}`;
    }
    
    /**
     * Update modal price and availability based on selected variant
     */
    updateModalPriceAndAvailability() {
        const selectedVariant = this.currentProduct.variants.find(
            v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
        );
        if (selectedVariant) {
            this.modal.price.textContent = `$${selectedVariant.VAR_SRP_PRICE}`;
            this.modal.availabilityStatus.textContent = 
                this.currentProduct.PROD_AVAILABILITY_STATUS === 'Available' 
                ? `Available (${this.getAvailableQuantity(selectedVariant)} units)` 
                : this.currentProduct.PROD_AVAILABILITY_STATUS;
        }
    }
    
    /**
     * Fetch products with variants from API and render them
     */
    async fetchAndRenderProducts() {
        try {
            const response = await axios.get(this.config.productsEndpoint);
            const products = response.data;
            
            if (Array.isArray(products) && products.length > 0) {
                // Store all products for filtering
                this.allProducts = products;
                
                // Populate category filter if it exists
                this.populateCategoryFilter(products);
                
                // Render first page of products
                this.renderProducts(products);
            } else {
                console.error('No products found or invalid data format');
                this.container.innerHTML = '<div class="col-12"><p class="text-center">No products available.</p></div>';
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching products:', error);
            this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load products. Please try again later.</p></div>';
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
        return category
            .split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
    
    /**
     * Render product cards with pagination
     */
    renderProducts(products) {
        if (products.length === 0) {
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No products match your filters. Try different criteria.</p></div>';
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
        
        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
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
                    <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
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
        const totalPages = Math.ceil(this.allProducts.length / this.itemsPerPage);
        
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
        
        this.applyFilters();
    }
    
    /**
     * Open product modal with details
     */
    async openProductModal(productId) {
        try {
            const response = await axios.get(`${this.config.productsEndpoint}/${productId}`);
            const product = response.data;
            
            this.currentProduct = product;
            this.populateModal(product);
            
            const modalElement = document.getElementById(this.config.modalId);
            const bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();
        } catch (error) {
            console.error('Error fetching product details:', error);
            alert('Failed to load product details. Please try again.');
        }
    }
    
    /**
     * Populate modal with product details
     */
    populateModal(product) {
        this.modal.quantity.value = 1;
        
        this.modal.image.src = product.PROD_IMAGE;
        this.modal.image.alt = product.PROD_NAME;
        this.modal.name.textContent = product.PROD_NAME;
        this.modal.code.textContent = `PROD-${product.PROD_ID}`;
        
        // Populate variant selector
        let variantOptions = '';
        product.variants.forEach(variant => {
            variantOptions += `<option value="${variant.VAR_ID}">${variant.VAR_CAPACITY} - $${variant.VAR_SRP_PRICE}</option>`;
        });
        this.modal.variantSelect.innerHTML = variantOptions;
        
        // Set initial price and availability
        this.updateModalPriceAndAvailability();
        
        // Order details
        this.modal.orderId.textContent = `PO-${new Date().getFullYear()}-${String(product.PROD_ID).padStart(4, '0')}`;
        this.modal.orderDate.textContent = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        this.modal.status.textContent = 'Pending';
        this.updateTotalAmount();
        
        // Features
        let featuresHTML = '';
        if (product.features && Array.isArray(product.features)) {
            product.features.forEach(feature => {
                featuresHTML += `<li>• ${feature.FEATURE_NAME}</li>`;
            });
        } else {
            featuresHTML = '<li>No features available</li>';
        }
        this.modal.features.innerHTML = featuresHTML;
        
        // Specifications
        let specsHTML = '';
        if (product.specifications && Array.isArray(product.specifications)) {
            product.specifications.forEach(spec => {
                specsHTML += `<li>• ${spec.SPEC_NAME}: ${spec.SPEC_VALUE}</li>`;
            });
        } else {
            specsHTML = `
                <li>• Energy Rating: 5 Star</li>
                <li>• Cooling Capacity: 12,000 BTU</li>
                <li>• Smart Features: WiFi Control</li>
                <li>• Warranty: 5 Years</li>
            `;
        }
        this.modal.specifications.innerHTML = specsHTML;
    }
    
    /**
     * Get available quantity from inventory
     */
    getAvailableQuantity(variant) {
        // TODO: Implement actual inventory check
        return 100; // Placeholder
    }
    
    /**
     * Handle order confirmation and send to backend
     */
    async confirmOrder() {
        if (!this.currentProduct) {
            alert('No product selected. Please try again.');
            return;
        }
        
        const customerId = this.getCustomerId();
        if (!customerId) {
            alert('You must be logged in to place an order. Please log in and try again.');
            return;
        }
        
        const selectedVariant = this.currentProduct.variants.find(
            v => v.VAR_ID === parseInt(this.modal.variantSelect.value)
        );
        
        // Collect order data
        const orderData = {
            PO_CUSTOMER_ID: customerId,
            PO_VARIANT_ID: selectedVariant.VAR_ID,
            PO_QUANTITY: parseInt(this.modal.quantity.value, 10),
            PO_UNIT_PRICE: parseFloat(selectedVariant.VAR_SRP_PRICE),
            PO_STATUS: 'pending',
            PO_ORDER_DATE: new Date().toISOString()
        };
        
        try {
            const response = await axios.post(this.config.orderEndpoint, orderData);
            
            alert('Order placed successfully! Order ID: ' + response.data.PO_ID);
            
            const modalElement = document.getElementById(this.config.modalId);
            const bsModal = bootstrap.Modal.getInstance(modalElement);
            bsModal.hide();
            
            this.fetchAndRenderProducts();
        } catch (error) {
            console.error('Error placing order:', error);
            alert('Failed to place order. Please try again.');
        }
    }
    
    /**
     * Get customer ID from embedded JavaScript variable
     */
    getCustomerId() {
        return window.currentUserId ? parseInt(window.currentUserId, 10) : null;
    }
}