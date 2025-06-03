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
                imagePath = '/uploads/' + imagePath;
            }

            const productId = product.PROD_ID || product.prod_id;
            const productName = product.PROD_NAME || product.prod_name || 'Unnamed Product';
            const productDesc = product.PROD_DESCRIPTION || product.prod_description || '';

            return `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card" data-product-id="${productId}" data-category="${product.category || ''}">
                        <div class="product-img-container">
                            <img src="${imagePath}" alt="${productName}" class="product-img">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">${productName}</h3>
                            <p class="product-desc">${productDesc}</p>
                            <div class="d-flex justify-content-end align-items-center">
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
     * Update total amount based on quantity and selected variant
     */
    updateTotalAmount() {
        if (!this.currentProduct || !this.modal.quantity || !this.modal.variantSelect || !this.modal.totalAmount) {
            if (this.modal.totalAmount) this.modal.totalAmount.textContent = '₱0.00';
            return;
        }

        const quantity = parseInt(this.modal.quantity.value, 10);
        const variantId = parseInt(this.modal.variantSelect.value);

        // Get variants from the current product and normalize them
        const variants = this.currentProduct.variants || [];
        const normalizedVariants = variants.map(variant => ({
            id: variant.VAR_ID || variant.var_id,
            price: variant.VAR_SRP_PRICE || variant.var_srp_price || '0.00'
        }));

        // Find the selected variant
        const selectedVariant = normalizedVariants.find(v => v.id === variantId);
        const price = selectedVariant ? parseFloat(selectedVariant.price) : 0;
        const total = price * quantity;

        this.modal.totalAmount.textContent = `₱${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    /**
     * Update modal price and availability based on selected variant
     */
    updateModalPriceAndAvailability() {
        if (!this.currentProduct || !this.modal.variantSelect || !this.modal.price || !this.modal.availabilityStatus) {
            return;
        }

        const variantId = parseInt(this.modal.variantSelect.value);

        // Get variants from the current product and normalize them
        const variants = this.currentProduct.variants || [];
        const normalizedVariants = variants.map(variant => ({
            id: variant.VAR_ID || variant.var_id,
            price: variant.VAR_SRP_PRICE || variant.var_srp_price || '0.00'
        }));

        // Find the selected variant
        const selectedVariant = normalizedVariants.find(v => v.id === variantId);

        if (selectedVariant) {
            this.modal.price.textContent = `₱${selectedVariant.price}`;

            // Get product status
            const productStatus = this.currentProduct.PROD_AVAILABILITY_STATUS ||
                this.currentProduct.prod_availability_status ||
                'Unknown';

            this.modal.availabilityStatus.textContent =
                productStatus === 'Available'
                    ? `Available (${this.getAvailableQuantity(selectedVariant)} units)`
                    : productStatus;
        } else {
            this.modal.price.textContent = '₱N/A';
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
            console.log('API Response:', response.data);

            // Check for success response structure with data field
            if (response.data && response.data.success && Array.isArray(response.data.data)) {
                const products = response.data.data;

                if (products.length > 0) {
                    this.allProducts = products;
                    this.populateCategoryFilter(products);
                    this.renderProducts(products);
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
     * Populate modal with product details
     */
    populateModal(product) {
        if (!this.modal.element || !product) return; // Modal or product not available

        // Normalize product data - handle both uppercase and lowercase field names
        const productData = {
            id: product.PROD_ID || product.prod_id,
            name: product.PROD_NAME || product.prod_name,
            description: product.PROD_DESCRIPTION || product.prod_description,
            image: product.PROD_IMAGE || product.prod_image,
            status: product.PROD_AVAILABILITY_STATUS || product.prod_availability_status
        };

        // Convert relative paths to absolute paths if needed
        let imagePath = productData.image || '';
        if (imagePath && !imagePath.startsWith('http') && !imagePath.startsWith('/uploads/')) {
            imagePath = '/uploads/' + imagePath;
        }

        // Add custom styling to modal elements
        if (this.modal.element) {
            // Get the modal body and ensure it has the right styling
            const modalBody = this.modal.element.querySelector('.modal-body');
            if (modalBody) {
                modalBody.classList.add('p-4');
            }

            // Add a fade-in animation to the modal
            this.modal.element.classList.add('fade');

            // Style the modal dialog for better aesthetics
            const modalDialog = this.modal.element.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.classList.add('modal-lg');
                modalDialog.classList.add('modal-dialog-centered');
            }

            // Add a modern header style
            const modalHeader = this.modal.element.querySelector('.modal-header');
            if (modalHeader) {
                modalHeader.classList.add('bg-light');
                modalHeader.classList.add('border-0');
                modalHeader.classList.add('py-3');
            }

            // Style the footer
            const modalFooter = this.modal.element.querySelector('.modal-footer');
            if (modalFooter) {
                modalFooter.classList.add('border-0');
                modalFooter.classList.add('pt-0');
                modalFooter.classList.add('pb-4');
                modalFooter.classList.add('px-4');
                modalFooter.classList.add('bg-white');
            }
        }

        if (this.modal.quantity) this.modal.quantity.value = 1;

        // Enhance image display with responsive class
        if (this.modal.image) {
            this.modal.image.src = imagePath;
            this.modal.image.alt = productData.name || 'Product Image';
            this.modal.image.classList.add('img-fluid');
            this.modal.image.classList.add('rounded');
            this.modal.image.classList.add('shadow-sm');

            // Find the parent container and add styling
            const imageContainer = this.modal.image.parentElement;
            if (imageContainer) {
                imageContainer.classList.add('text-center');
                imageContainer.classList.add('mb-4');
            }
        }

        // Enhance product name styling
        if (this.modal.name) {
            this.modal.name.textContent = productData.name || 'N/A';
            this.modal.name.classList.add('fw-bold');
            this.modal.name.classList.add('text-primary');
            this.modal.name.classList.add('mb-3');
        }

        if (this.modal.code) {
            this.modal.code.textContent = `PROD-${productData.id || 'N/A'}`;
            this.modal.code.classList.add('text-muted');
            this.modal.code.classList.add('small');
        }

        // Normalize variants data (handle both upper and lowercase field names)
        const variants = product.variants || [];
        const normalizedVariants = variants.map(variant => ({
            id: variant.VAR_ID || variant.var_id,
            capacity: variant.VAR_CAPACITY || variant.var_capacity || 'Standard',
            price: variant.VAR_SRP_PRICE || variant.var_srp_price || '0.00',
            freeInstallPrice: variant.VAR_PRICE_FREE_INSTALL || variant.var_price_free_install,
            withInstallPrice: variant.VAR_PRICE_WITH_INSTALL || variant.var_price_with_install,
            powerConsumption: variant.VAR_POWER_CONSUMPTION || variant.var_power_consumption
        }));

        const hasVariants = normalizedVariants.length > 0;

        // Enhance variant select styling
        if (this.modal.variantSelect) {
            if (hasVariants) {
                let variantOptions = '';
                normalizedVariants.forEach(variant => {
                    variantOptions += `<option value="${variant.id}">${variant.capacity} - ₱${variant.price}</option>`;
                });
                this.modal.variantSelect.innerHTML = variantOptions;
                this.modal.variantSelect.disabled = false;
                this.modal.variantSelect.classList.add('form-select');
                this.modal.variantSelect.classList.add('border-primary');

                // Find the label for the variant select and style it
                const variantLabel = this.modal.variantSelect.parentElement?.querySelector('label');
                if (variantLabel) {
                    variantLabel.classList.add('fw-bold');
                    variantLabel.classList.add('mb-2');
                }
            } else {
                // No variants available
                this.modal.variantSelect.innerHTML = '<option value="">No variants available</option>';
                this.modal.variantSelect.disabled = true;
                this.modal.variantSelect.classList.add('form-select');
                this.modal.variantSelect.classList.add('bg-light');
            }
        }

        // Update availability status with badge styling
        if (this.modal.availabilityStatus) {
            this.modal.availabilityStatus.textContent = productData.status || 'N/A';

            // Remove any previous badge classes
            this.modal.availabilityStatus.className = '';

            // Add badge styling based on availability
            this.modal.availabilityStatus.classList.add('badge');
            if (productData.status === 'Available') {
                this.modal.availabilityStatus.classList.add('bg-success');
            } else if (productData.status === 'Out of Stock') {
                this.modal.availabilityStatus.classList.add('bg-danger');
            } else {
                this.modal.availabilityStatus.classList.add('bg-secondary');
            }
            this.modal.availabilityStatus.classList.add('rounded-pill');
            this.modal.availabilityStatus.classList.add('px-3');
            this.modal.availabilityStatus.classList.add('py-2');
        }

        // Update price with attractive styling
        if (this.modal.price) {
            if (hasVariants) {
                this.modal.price.textContent = `₱${normalizedVariants[0].price}`;
                this.modal.price.classList.add('fs-4');
                this.modal.price.classList.add('fw-bold');
                this.modal.price.classList.add('text-primary');
            } else {
                this.modal.price.textContent = 'Price not available';
                this.modal.price.classList.add('text-muted');
                this.modal.price.classList.add('fst-italic');
            }
        }

        if (this.modal.orderId) {
            this.modal.orderId.textContent = `PB-${new Date().getFullYear()}-${String(productData.id || '0').padStart(4, '0')}`;
            this.modal.orderId.classList.add('text-muted');
        }

        if (this.modal.orderDate) {
            this.modal.orderDate.textContent = new Date().toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            this.modal.orderDate.classList.add('text-muted');
        }

        if (this.modal.status) {
            this.modal.status.textContent = 'Pending';
            this.modal.status.classList.add('badge');
            this.modal.status.classList.add('bg-warning');
            this.modal.status.classList.add('text-dark');
        }

        this.updateTotalAmount(); // Sets initial total amount

        // Style the date and time inputs
        if (this.modal.preferredDate) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            this.modal.preferredDate.value = tomorrow.toISOString().split('T')[0];
            this.modal.preferredDate.classList.add('form-control');
            this.modal.preferredDate.classList.add('mb-3');

            // Find the label for the date input and style it
            const dateLabel = this.modal.preferredDate.parentElement?.querySelector('label');
            if (dateLabel) {
                dateLabel.classList.add('fw-bold');
                dateLabel.classList.add('mb-2');
            }
        }

        if (this.modal.preferredTime) {
            this.modal.preferredTime.value = '09:00';
            this.modal.preferredTime.classList.add('form-control');
            this.modal.preferredTime.classList.add('mb-3');

            // Find the label for the time input and style it
            const timeLabel = this.modal.preferredTime.parentElement?.querySelector('label');
            if (timeLabel) {
                timeLabel.classList.add('fw-bold');
                timeLabel.classList.add('mb-2');
            }
        }

        if (this.modal.address) {
            this.modal.address.value = '';
            this.modal.address.classList.add('form-control');
            this.modal.address.classList.add('mb-3');

            // Find the label for the address input and style it
            const addressLabel = this.modal.address.parentElement?.querySelector('label');
            if (addressLabel) {
                addressLabel.classList.add('fw-bold');
                addressLabel.classList.add('mb-2');
            }
        }

        // Style the features list
        if (this.modal.features) {
            let featuresHTML = '';
            // Normalize features array handling both upper and lowercase field names
            const features = product.features || product.FEATURES || [];

            if (features.length > 0) {
                featuresHTML = '<div class="list-group list-group-flush">';
                features.forEach(feature => {
                    const featureName = feature.FEATURE_NAME || feature.feature_name || 'Unnamed Feature';
                    featuresHTML += `<div class="list-group-item border-0 ps-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i> ${featureName}
                    </div>`;
                });
                featuresHTML += '</div>';
            } else {
                featuresHTML = '<p class="text-muted fst-italic">No features listed.</p>';
            }
            this.modal.features.innerHTML = featuresHTML;

            // Find the features section title and style it
            const featuresTitle = this.modal.features.parentElement?.querySelector('h5, h4, h3');
            if (featuresTitle) {
                featuresTitle.classList.add('fw-bold');
                featuresTitle.classList.add('mb-3');
                featuresTitle.classList.add('border-bottom');
                featuresTitle.classList.add('pb-2');
            }
        }

        // Style the specifications list
        if (this.modal.specifications) {
            let specsHTML = '';
            // Normalize specs array handling both upper and lowercase field names
            const specs = product.specs || product.SPECS || [];

            if (specs.length > 0) {
                specsHTML = '<div class="table-responsive"><table class="table table-sm table-hover">';
                specsHTML += '<tbody>';
                specs.forEach(spec => {
                    const specName = spec.SPEC_NAME || spec.spec_name || 'Spec';
                    const specValue = spec.SPEC_VALUE || spec.spec_value || 'N/A';
                    specsHTML += `<tr>
                        <td class="fw-bold text-nowrap">${specName}</td>
                        <td>${specValue}</td>
                    </tr>`;
                });
                specsHTML += '</tbody></table></div>';
            } else {
                specsHTML = '<p class="text-muted fst-italic">No specifications available.</p>';
            }
            this.modal.specifications.innerHTML = specsHTML;

            // Find the specifications section title and style it
            const specsTitle = this.modal.specifications.parentElement?.querySelector('h5, h4, h3');
            if (specsTitle) {
                specsTitle.classList.add('fw-bold');
                specsTitle.classList.add('mb-3');
                specsTitle.classList.add('border-bottom');
                specsTitle.classList.add('pb-2');
            }
        }

        // Style the confirm button
        if (this.modal.confirmButton) {
            this.modal.confirmButton.disabled = !hasVariants;
            this.modal.confirmButton.classList.add('btn-lg');
            this.modal.confirmButton.classList.add('px-4');

            if (!hasVariants) {
                this.modal.confirmButton.title = "Product variants are not available for ordering";
                this.modal.confirmButton.classList.add('btn-secondary');
            } else {
                this.modal.confirmButton.title = "";
                this.modal.confirmButton.classList.add('btn-primary');
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
        // If we have inventory data from the API, use it
        if (this.currentProduct && this.currentProduct.inventory && Array.isArray(this.currentProduct.inventory)) {
            // Calculate total quantity across all warehouses for this product
            return this.currentProduct.inventory.reduce((total, inv) => {
                return total + (parseInt(inv.quantity) || 0);
            }, 0);
        }

        // Fallback: Use variant stock quantity if available, or default to 100
        return variant?.VAR_STOCK_QUANTITY || variant?.var_stock_quantity || 100;
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
            PB_ADDRESS: this.modal.address.value.trim()
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