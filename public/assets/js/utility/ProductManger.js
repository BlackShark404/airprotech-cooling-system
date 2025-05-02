/**
 * ProductManager Class
 * Handles creating product cards, managing the product details modal,
 * and filtering/searching products
 */
class ProductManager {
    constructor(options = {}) {
        // Default configuration
        this.config = {
            productsEndpoint: '/api/products',
            containerSelector: '#products-container',
            modalId: 'productDetailModal',
            filterFormId: 'product-filters',
            searchInputId: 'product-search',
            cardTemplate: this.getDefaultCardTemplate(),
            ...options
        };
        
        // Initialize modal elements references
        this.modal = {
            element: document.getElementById(this.config.modalId),
            image: document.getElementById('modal-product-image'),
            title: document.getElementById('modal-product-title'),
            price: document.getElementById('modal-product-price'),
            code: document.getElementById('modal-product-code'),
            stockStatus: document.getElementById('modal-stock-status'),
            stockQuantity: document.getElementById('modal-stock-quantity'),
            quantity: document.getElementById('modal-quantity'),
            orderId: document.getElementById('modal-order-id'),
            orderDate: document.getElementById('modal-order-date'),
            status: document.getElementById('modal-status'),
            totalAmount: document.getElementById('modal-total-amount'),
            specifications: document.getElementById('modal-specifications')
        };
        
        // Container for product cards
        this.container = document.querySelector(this.config.containerSelector);
        
        // Store all products for filtering
        this.allProducts = [];
        
        // Initialize modal quantity controls
        this.initModalControls();
        
        // Initialize filter and search
        this.initFilterAndSearch();
    }
    
    /**
     * Default card template if none provided
     */
    getDefaultCardTemplate() {
        return (product) => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="product-card" data-product-id="${product.id}" data-category="${product.category || ''}">
                    <div class="product-img-container">
                        <img src="${product.image}" alt="${product.title}" class="product-img">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.title}</h3>
                        <p class="product-desc">${product.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$${product.price}</span>
                            <button class="btn btn-book-now view-details" data-product-id="${product.id}">Book Now</button>
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
            const available = parseInt(this.currentProduct.stock, 10);
            if (quantity < available) {
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
        
        // Add event listener to all "Book Now" buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-details')) {
                const productId = e.target.getAttribute('data-product-id');
                this.openProductModal(productId);
            }
        });
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
                setTimeout(() => this.applyFilters(), 10); // Small delay to ensure form reset completes
            });
        }
        
        // Add event listener for search input
        if (this.searchInput) {
            // Debounce search to improve performance
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
                parseFloat(product.price) >= minPrice
            );
        }
        
        if (maxPriceFilter && maxPriceFilter.value !== '') {
            const maxPrice = parseFloat(maxPriceFilter.value);
            filteredProducts = filteredProducts.filter(product => 
                parseFloat(product.price) <= maxPrice
            );
        }
        
        // Apply stock status filter if exists
        const stockFilter = this.filterForm?.querySelector('[name="stock-status"]');
        if (stockFilter && stockFilter.value !== '') {
            const inStock = stockFilter.value === 'in-stock';
            filteredProducts = filteredProducts.filter(product => 
                product.inStock === inStock
            );
        }
        
        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredProducts = filteredProducts.filter(product => 
                product.title.toLowerCase().includes(searchTerm) || 
                product.description.toLowerCase().includes(searchTerm) ||
                (product.code && product.code.toLowerCase().includes(searchTerm))
            );
        }
        
        // Render filtered products
        this.renderProducts(filteredProducts);
        
        // Update results count if element exists
        const resultsCountElement = document.getElementById('results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${filteredProducts.length} products found`;
        }
    }
    
    /**
     * Update total amount based on quantity
     */
    updateTotalAmount() {
        const quantity = parseInt(this.modal.quantity.value, 10);
        const price = parseFloat(this.currentProduct.price);
        const total = price * quantity;
        this.modal.totalAmount.textContent = `$${total.toLocaleString()}`;
    }
    
    /**
     * Fetch products from API and render them
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
                
                this.renderProducts(products);
            } else {
                console.error('No products found or invalid data format');
                this.container.innerHTML = '<div class="col-12"><p class="text-center">No products available.</p></div>';
            }
        } catch (error) {
            console.error('Error fetching products:', error);
            this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load products. Please try again later.</p></div>';
        }
    }
    
    /**
     * Populate category filter dropdown with unique categories from products
     * @param {Array} products - Array of product objects
     */
    populateCategoryFilter(products) {
        const categoryFilter = this.filterForm?.querySelector('[name="category"]');
        if (!categoryFilter) return;
        
        const categories = new Set();
        
        // Extract unique categories
        products.forEach(product => {
            if (product.category) {
                categories.add(product.category);
            }
        });
        
        // Create option elements for each category
        let options = '<option value="">All Categories</option>';
        categories.forEach(category => {
            options += `<option value="${category}">${this.formatCategoryName(category)}</option>`;
        });
        
        categoryFilter.innerHTML = options;
    }
    
    /**
     * Format category name for display (capitalize, replace dashes with spaces)
     * @param {string} category - Category name to format
     * @returns {string} - Formatted category name
     */
    formatCategoryName(category) {
        return category
            .split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
    
    /**
     * Render product cards in the container
     * @param {Array} products - Array of product objects
     */
    renderProducts(products) {
        if (products.length === 0) {
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No products match your filters. Try different criteria.</p></div>';
            return;
        }
        
        let html = '';
        
        products.forEach(product => {
            html += this.config.cardTemplate(product);
        });
        
        this.container.innerHTML = html;
    }
    
    /**
     * Open product modal with details
     * @param {string|number} productId - ID of the product to show details for
     */
    async openProductModal(productId) {
        try {
            const response = await axios.get(`${this.config.productsEndpoint}/${productId}`);
            const product = response.data;
            
            this.currentProduct = product;
            this.populateModal(product);
            
            // Show modal using Bootstrap's modal API
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
     * @param {Object} product - Product data object
     */
    populateModal(product) {
        // Reset quantity to 1
        this.modal.quantity.value = 1;
        
        // Set basic product info
        this.modal.image.src = product.image;
        this.modal.image.alt = product.title;
        this.modal.title.textContent = product.title;
        this.modal.price.textContent = `$${product.price}`;
        this.modal.code.textContent = product.code || `SI-${product.id}`;
        
        // Stock status
        this.modal.stockStatus.textContent = product.inStock ? 'In Stock' : 'Out of Stock';
        this.modal.stockQuantity.textContent = product.inStock ? `(${product.stock} units available)` : '';
        
        // Order details
        this.modal.orderId.textContent = product.orderId || `ORD-${new Date().getFullYear()}-${String(product.id).padStart(4, '0')}`;
        this.modal.orderDate.textContent = product.orderDate || new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        this.modal.status.textContent = product.status || 'New';
        this.modal.totalAmount.textContent = `$${product.price}`;
        
        // Specifications
        let specsHTML = '';
        if (product.specifications && Array.isArray(product.specifications)) {
            product.specifications.forEach(spec => {
                specsHTML += `<li>• ${spec}</li>`;
            });
        } else if (product.specifications && typeof product.specifications === 'object') {
            // Handle specifications as an object
            for (const [key, value] of Object.entries(product.specifications)) {
                specsHTML += `<li>• ${key}: ${value}</li>`;
            }
        } else {
            // Default specifications based on image
            specsHTML = `
                <li>• Energy Rating: 5 Star</li>
                <li>• Cooling Capacity: 12,000 BTU</li>
                <li>• Smart Features: WiFi Control</li>
                <li>• Warranty: 5 Years</li>
            `;
        }
        this.modal.specifications.innerHTML = specsHTML;
    }
}