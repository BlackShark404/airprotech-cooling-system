/**
 * OrdersManager Class
 * Handles creating product order cards, managing the details modal,
 * filtering/searching orders, and client-side pagination
 */
class OrdersManager {
    constructor(options = {}) {
        // Default configuration
        this.config = {
            ordersEndpoint: '/api/product-orders',
            containerSelector: '#orders',
            modalId: 'orderDetailModal',
            filterFormId: 'order-filters',
            searchInputId: 'order-search',
            dateFilterId: 'order-date-filter',
            statusFilterId: 'order-status-filter',
            cardTemplate: this.getDefaultCardTemplate(),
            itemsPerPage: 10,
            paginationContainerSelector: '#orders-pagination-container',
            ...options
        };

        // Initialize modal elements references
        this.modal = {
            element: null, // Will be initialized in init()
            orderId: null,
            productName: null,
            productImage: null,
            variant: null,
            quantity: null,
            unitPrice: null,
            totalAmount: null,
            status: null,
            orderDate: null,
            paidDate: null
        };

        // Container for order cards
        this.container = null;

        // Store all orders for filtering
        this.allOrders = [];
        this.filteredOrders = [];

        // Pagination state
        this.currentPage = 1;
        this.itemsPerPage = this.config.itemsPerPage;
        
        // Bootstrap modal instance
        this.bsModal = null;
    }

    /**
     * Initialize the OrdersManager
     */
    init() {
        // Get container element
        this.container = document.querySelector(this.config.containerSelector);
        if (!this.container) {
            console.error(`Container element not found: ${this.config.containerSelector}`);
            return;
        }

        // Initialize modal elements
        this.modal.element = document.getElementById(this.config.modalId);
        
        if (this.modal.element) {
            this.modal.orderId = document.getElementById('modal-order-id');
            this.modal.productName = document.getElementById('modal-product-name');
            this.modal.productImage = document.getElementById('modal-product-image');
            this.modal.variant = document.getElementById('modal-variant');
            this.modal.quantity = document.getElementById('modal-quantity');
            this.modal.unitPrice = document.getElementById('modal-unit-price');
            this.modal.totalAmount = document.getElementById('modal-total-amount');
            this.modal.status = document.getElementById('modal-status');
            this.modal.orderDate = document.getElementById('modal-order-date');
            this.modal.paidDate = document.getElementById('modal-paid-date');
            
            // Create Bootstrap modal instance
            this.bsModal = new bootstrap.Modal(this.modal.element);
        } else {
            console.warn(`Modal element not found: ${this.config.modalId}`);
        }

        // Initialize modal controls
        this.initModalControls();

        // Initialize filter and search
        this.initFilterAndSearch();
        
        // Fetch and render orders
        this.fetchAndRenderOrders();
    }

    /**
     * Default card template for product orders
     */
    getDefaultCardTemplate() {
        return (order) => `
            <div class="booking-item card shadow-sm mb-3">
                <div class="card-body d-flex align-items-center p-4">
                    <img src="${order.PROD_IMAGE}" alt="${order.PROD_NAME}" class="me-4" width="100" height="100">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">ORD-${order.PO_ID} <span class="text-muted">${new Date(order.PO_ORDER_DATE).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span></p>
                                <h5 class="fw-bold mb-1">${order.PROD_NAME}</h5>
                                <p class="text-muted mb-0">Model: ${order.VAR_CAPACITY || 'N/A'}</p>
                                <p class="fw-bold text-dark mb-0">$${order.PO_TOTAL_AMOUNT.toLocaleString()}</p>
                            </div>
                            <div class="text-end">
                                <p class="text-muted mb-1">Ordered on: ${new Date(order.PO_ORDER_DATE).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <span class="badge bg-${this.getStatusBadgeClass(order.PO_STATUS)}-subtle text-${this.getStatusBadgeClass(order.PO_STATUS)}">${order.PO_STATUS.charAt(0).toUpperCase() + order.PO_STATUS.slice(1)}</span>
                                <div class="mt-2">
                                    <button class="btn btn-danger view-details" data-order-id="${order.PO_ID}">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Get badge class based on status
     */
    getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'pending': return 'warning';
            case 'confirmed':
            case 'completed': return 'success';
            case 'cancelled': return 'danger';
            default: return 'secondary';
        }
    }

    /**
     * Initialize controls within the modal
     */
    initModalControls() {
        // Add event listener to all "View Details" buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-details')) {
                const orderId = e.target.getAttribute('data-order-id');
                this.openOrderModal(orderId);
            }
        });
    }

    /**
     * Initialize filter and search functionality
     */
    initFilterAndSearch() {
        // Get filter form, search input, and filter selects
        this.filterForm = document.getElementById(this.config.filterFormId);
        this.searchInput = document.getElementById(this.config.searchInputId);
        this.dateFilter = document.getElementById(this.config.dateFilterId);
        this.statusFilter = document.getElementById(this.config.statusFilterId);

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
        if (!this.allOrders.length) return;

        this.filteredOrders = [...this.allOrders];

        // Apply date range filter
        if (this.dateFilter && this.dateFilter.value) {
            const now = new Date();
            let startDate;
            
            switch (this.dateFilter.value) {
                case 'Last 30 days':
                    startDate = new Date();
                    startDate.setDate(now.getDate() - 30);
                    break;
                case 'Last 60 days':
                    startDate = new Date();
                    startDate.setDate(now.getDate() - 60);
                    break;
                case 'Last 90 days':
                    startDate = new Date();
                    startDate.setDate(now.getDate() - 90);
                    break;
                case 'All time':
                default:
                    startDate = null;
            }
            
            if (startDate) {
                this.filteredOrders = this.filteredOrders.filter(order =>
                    new Date(order.PO_ORDER_DATE) >= startDate
                );
            }
        }

        // Apply status filter
        if (this.statusFilter && this.statusFilter.value && this.statusFilter.value !== 'All Status') {
            this.filteredOrders = this.filteredOrders.filter(order =>
                order.PO_STATUS.toLowerCase() === this.statusFilter.value.toLowerCase()
            );
        }

        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            this.filteredOrders = this.filteredOrders.filter(order =>
                order.PROD_NAME.toLowerCase().includes(searchTerm) ||
                (order.VAR_CAPACITY && order.VAR_CAPACITY.toLowerCase().includes(searchTerm)) ||
                `ORD-${order.PO_ID}`.toLowerCase().includes(searchTerm)
            );
        }

        // Reset to first page when filters change
        this.currentPage = 1;

        // Render filtered orders with pagination
        this.renderOrders(this.filteredOrders);

        // Update results count if element exists
        const resultsCountElement = document.getElementById('order-results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${this.filteredOrders.length} orders found`;
        }
    }

    /**
     * Fetch orders from API and render them
     */
    async fetchAndRenderOrders() {
        try {
            const response = await axios.get(this.config.ordersEndpoint);
            const orders = response.data;

            if (Array.isArray(orders) && orders.length > 0) {
                // Store all orders for filtering
                this.allOrders = orders;
                this.filteredOrders = [...orders];

                // Render first page of orders
                this.renderOrders(this.filteredOrders);
            } else {
                console.error('No orders found or invalid data format');
                if (this.container) {
                    this.container.innerHTML = '<div class="col-12"><p class="text-center">No orders available.</p></div>';
                }
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
            if (this.container) {
                this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load orders. Please try again later.</p></div>';
            }
            this.renderPagination(0);
        }
    }

    /**
     * Render order cards with pagination
     */
    renderOrders(orders) {
        if (!this.container) return;
        
        if (orders.length === 0) {
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No orders match your filters. Try different criteria.</p></div>';
            this.renderPagination(0);
            return;
        }

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const paginatedOrders = orders.slice(startIndex, endIndex);

        let html = '';
        paginatedOrders.forEach(order => {
            html += this.config.cardTemplate(order);
        });

        this.container.innerHTML = html;

        this.renderPagination(orders.length);
    }

    /**
     * Render pagination controls
     */
    renderPagination(totalItems) {
        const paginationContainer = document.querySelector(this.config.paginationContainerSelector);
        if (!paginationContainer) return;

        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
        
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = `
            <nav aria-label="Order pagination">
                <ul class="pagination">
                    <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="prev"><i class="fas fa-chevron-left"></i></a>
                    </li>
        `;

        // Calculate visible page range
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = startPage + maxVisiblePages - 1;
        
        if (endPage > totalPages) {
            endPage = totalPages;
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        // Add first page and ellipsis if needed
        if (startPage > 1) {
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
            `;
            
            if (startPage > 2) {
                paginationHTML += `
                    <li class="page-item disabled">
                        <a class="page-link" href="#">...</a>
                    </li>
                `;
            }
        }

        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${this.currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Add last page and ellipsis if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `
                    <li class="page-item disabled">
                        <a class="page-link" href="#">...</a>
                    </li>
                `;
            }
            
            paginationHTML += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `;
        }

        paginationHTML += `
                    <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="next"><i class="fas fa-chevron-right"></i></a>
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
        const paginationContainer = document.querySelector(this.config.paginationContainerSelector);
        if (!paginationContainer) return;
        
        const paginationLinks = paginationContainer.querySelectorAll('.page-link');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Get the data-page attribute from the clicked element or its parent
                let pageAction;
                
                if (e.target.hasAttribute('data-page')) {
                    pageAction = e.target.getAttribute('data-page');
                } else if (e.target.parentElement.hasAttribute('data-page')) {
                    pageAction = e.target.parentElement.getAttribute('data-page');
                }
                
                if (pageAction) {
                    this.handlePageChange(pageAction);
                }
            });
        });
    }

    /**
     * Handle page change
     */
    handlePageChange(pageAction) {
        const totalPages = Math.ceil(this.filteredOrders.length / this.itemsPerPage);

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

        this.renderOrders(this.filteredOrders);
    }

    /**
     * Open order modal with details
     */
    async openOrderModal(orderId) {
        try {
            const response = await axios.get(`${this.config.ordersEndpoint}/${orderId}`);
            const order = response.data;

            this.currentOrder = order;
            this.populateModal(order);

            if (this.bsModal) {
                this.bsModal.show();
            } else if (this.modal.element) {
                // Fallback if bsModal wasn't initialized
                this.bsModal = new bootstrap.Modal(this.modal.element);
                this.bsModal.show();
            } else {
                console.error('Modal element not found');
            }
        } catch (error) {
            console.error('Error fetching order details:', error);
            alert('Failed to load order details. Please try again.');
        }
    }

    /**
     * Populate modal with order details
     */
    populateModal(order) {
        if (!order) return;
        
        // Check if modal elements exist before updating them
        if (this.modal.orderId) this.modal.orderId.textContent = `ORD-${order.PO_ID}`;
        if (this.modal.productName) this.modal.productName.textContent = order.PROD_NAME;
        
        if (this.modal.productImage) {
            this.modal.productImage.src = order.PROD_IMAGE;
            this.modal.productImage.alt = order.PROD_NAME;
        }
        
        if (this.modal.variant) this.modal.variant.textContent = order.VAR_CAPACITY || 'N/A';
        if (this.modal.quantity) this.modal.quantity.textContent = order.PO_QUANTITY;
        if (this.modal.unitPrice) this.modal.unitPrice.textContent = `$${order.PO_UNIT_PRICE.toLocaleString()}`;
        if (this.modal.totalAmount) this.modal.totalAmount.textContent = `$${order.PO_TOTAL_AMOUNT.toLocaleString()}`;
        
        if (this.modal.status) {
            this.modal.status.textContent = order.PO_STATUS.charAt(0).toUpperCase() + order.PO_STATUS.slice(1);
            
            // Add appropriate status class
            this.modal.status.className = ''; // Clear existing classes
            this.modal.status.classList.add(`text-${this.getStatusBadgeClass(order.PO_STATUS)}`);
        }
        
        if (this.modal.orderDate) {
            this.modal.orderDate.textContent = new Date(order.PO_ORDER_DATE).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
        
        if (this.modal.paidDate) {
            this.modal.paidDate.textContent = order.PO_PAID_DATE ? new Date(order.PO_PAID_DATE).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) : 'Not Paid';
        }
    }
}