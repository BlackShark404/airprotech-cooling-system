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
            element: document.getElementById(this.config.modalId),
            orderId: document.getElementById('modal-order-id'),
            productName: document.getElementById('modal-product-name'),
            productImage: document.getElementById('modal-product-image'),
            variant: document.getElementById('modal-variant'),
            quantity: document.getElementById('modal-quantity'),
            unitPrice: document.getElementById('modal-unit-price'),
            totalAmount: document.getElementById('modal-total-amount'),
            status: document.getElementById('modal-status'),
            orderDate: document.getElementById('modal-order-date'),
            paidDate: document.getElementById('modal-paid-date')
        };

        // Container for order cards
        this.container = document.querySelector(this.config.containerSelector);

        // Store all orders for filtering
        this.allOrders = [];

        // Pagination state
        this.currentPage = 1;
        this.itemsPerPage = this.config.itemsPerPage;

        // Initialize modal controls
        this.initModalControls();

        // Initialize filter and search
        this.initFilterAndSearch();
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

        let filteredOrders = [...this.allOrders];

        // Apply date range filter
        if (this.dateFilter && this.dateFilter.value) {
            const now = new Date();
            let startDate;
            switch (this.dateFilter.value) {
                case 'Last 30 days':
                    startDate = new Date(now.setDate(now.getDate() - 30));
                    break;
                case 'Last 60 days':
                    startDate = new Date(now.setDate(now.getDate() - 60));
                    break;
                case 'Last 90 days':
                    startDate = new Date(now.setDate(now.getDate() - 90));
                    break;
                case 'All time':
                default:
                    startDate = null;
            }
            if (startDate) {
                filteredOrders = filteredOrders.filter(order =>
                    new Date(order.PO_ORDER_DATE) >= startDate
                );
            }
        }

        // Apply status filter
        if (this.statusFilter && this.statusFilter.value && this.statusFilter.value !== 'All Status') {
            filteredOrders = filteredOrders.filter(order =>
                order.PO_STATUS.toLowerCase() === this.statusFilter.value.toLowerCase()
            );
        }

        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredOrders = filteredOrders.filter(order =>
                order.PROD_NAME.toLowerCase().includes(searchTerm) ||
                (order.VAR_CAPACITY && order.VAR_CAPACITY.toLowerCase().includes(searchTerm)) ||
                `ORD-${order.PO_ID}`.toLowerCase().includes(searchTerm)
            );
        }

        // Reset to first page when filters change
        this.currentPage = 1;

        // Render filtered orders with pagination
        this.renderOrders(filteredOrders);

        // Update results count if element exists
        const resultsCountElement = document.getElementById('order-results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${filteredOrders.length} orders found`;
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

                // Render first page of orders
                this.renderOrders(orders);
            } else {
                console.error('No orders found or invalid data format');
                this.container.innerHTML = '<div class="col-12"><p class="text-center">No orders available.</p></div>';
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
            this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load orders. Please try again later.</p></div>';
            this.renderPagination(0);
        }
    }

    /**
     * Render order cards with pagination
     */
    renderOrders(orders) {
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
        let paginationHTML = `
            <nav aria-label="Order pagination">
                <ul class="pagination">
                    <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="prev"><i class="fas fa-chevron-left"></i></a>
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
        const paginationLinks = document.querySelectorAll(`${this.config.paginationContainerSelector} .page-link`);
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageAction = e.target.getAttribute('data-page') || e.target.parentElement.getAttribute('data-page');
                this.handlePageChange(pageAction);
            });
        });
    }

    /**
     * Handle page change
     */
    handlePageChange(pageAction) {
        const totalPages = Math.ceil(this.allOrders.length / this.itemsPerPage);

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
     * Open order modal with details
     */
    async openOrderModal(orderId) {
        try {
            const response = await axios.get(`${this.config.ordersEndpoint}/${orderId}`);
            const order = response.data;

            this.currentOrder = order;
            this.populateModal(order);

            const modalElement = document.getElementById(this.config.modalId);
            const bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();
        } catch (error) {
            console.error('Error fetching order details:', error);
            alert('Failed to load order details. Please try again.');
        }
    }

    /**
     * Populate modal with order details
     */
    populateModal(order) {
        this.modal.orderId.textContent = `ORD-${order.PO_ID}`;
        this.modal.productName.textContent = order.PROD_NAME;
        this.modal.productImage.src = order.PROD_IMAGE;
        this.modal.productImage.alt = order.PROD_NAME;
        this.modal.variant.textContent = order.VAR_CAPACITY || 'N/A';
        this.modal.quantity.textContent = order.PO_QUANTITY;
        this.modal.unitPrice.textContent = `$${order.PO_UNIT_PRICE.toLocaleString()}`;
        this.modal.totalAmount.textContent = `$${order.PO_TOTAL_AMOUNT.toLocaleString()}`;
        this.modal.status.textContent = order.PO_STATUS.charAt(0).toUpperCase() + order.PO_STATUS.slice(1);
        this.modal.orderDate.textContent = new Date(order.PO_ORDER_DATE).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        this.modal.paidDate.textContent = order.PO_PAID_DATE ? new Date(order.PO_PAID_DATE).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }) : 'Not Paid';
    }
}