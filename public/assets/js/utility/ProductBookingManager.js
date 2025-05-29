/**
 * OrdersManager Class
 * Handles creating product booking cards, managing the details modal,
 * filtering/searching bookings, and client-side pagination
 */
class OrdersManager {
    constructor(options = {}) {
        // Default configuration
        this.config = {
            ordersEndpoint: '/api/product-bookings',
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
            preferredDate: null,
            preferredTime: null,
            address: null
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
            this.modal.preferredDate = document.getElementById('modal-preferred-date');
            this.modal.preferredTime = document.getElementById('modal-preferred-time');
            this.modal.address = document.getElementById('modal-address');

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
     * Default card template for product bookings
     */
    getDefaultCardTemplate() {
        return (order) => {
            // Handle both upper and lowercase field names from API
            const id = order.PB_ID || order.pb_id;
            const orderDate = order.PB_ORDER_DATE || order.pb_order_date;
            const productName = order.PROD_NAME || order.prod_name || 'Unknown Product';
            const productImage = order.PROD_IMAGE || order.prod_image || '/assets/images/product-placeholder.jpg';
            const variantCapacity = order.VAR_CAPACITY || order.var_capacity || 'N/A';
            const totalAmount = order.PB_TOTAL_AMOUNT || order.pb_total_amount || 0;
            const status = order.PB_STATUS || order.pb_status || 'pending';
            const preferredDate = order.PB_PREFERRED_DATE || order.pb_preferred_date;

            return `
                <div class="booking-item card shadow-sm mb-3">
                    <div class="card-body d-flex align-items-center p-4">
                        <img src="${productImage}" alt="${productName}" class="me-4 rounded" width="100" height="100">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">PB-${id} <span class="text-muted">${new Date(orderDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span></p>
                                    <h5 class="fw-bold mb-1">${productName}</h5>
                                    <p class="text-muted mb-0">Model: ${variantCapacity}</p>
                                    <p class="fw-bold text-dark mb-0">$${parseFloat(totalAmount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                                </div>
                                <div class="text-end">
                                    <p class="text-muted mb-1">Preferred Date: ${new Date(preferredDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                    <span class="badge bg-${this.getStatusBadgeClass(status)}-subtle text-${this.getStatusBadgeClass(status)}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                                    <div class="mt-2">
                                        <button class="btn btn-primary view-details" data-order-id="${id}">View Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        };
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
                this.filteredOrders = this.filteredOrders.filter(order => {
                    const dateField = order.PB_ORDER_DATE || order.pb_order_date;
                    return new Date(dateField) >= startDate;
                });
            }
        }

        // Apply status filter
        if (this.statusFilter && this.statusFilter.value && this.statusFilter.value !== 'All Status') {
            this.filteredOrders = this.filteredOrders.filter(order => {
                const status = order.PB_STATUS || order.pb_status || '';
                return status.toLowerCase() === this.statusFilter.value.toLowerCase();
            });
        }

        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            this.filteredOrders = this.filteredOrders.filter(order => {
                const productName = order.PROD_NAME || order.prod_name || '';
                const variantCapacity = order.VAR_CAPACITY || order.var_capacity || '';
                const id = order.PB_ID || order.pb_id || '';

                return productName.toLowerCase().includes(searchTerm) ||
                    variantCapacity.toLowerCase().includes(searchTerm) ||
                    `PB-${id}`.toLowerCase().includes(searchTerm);
            });
        }

        // Reset to first page when filters change
        this.currentPage = 1;

        // Render filtered orders with pagination
        this.renderOrders(this.filteredOrders);

        // Update results count if element exists
        const resultsCountElement = document.getElementById('order-results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${this.filteredOrders.length} bookings found`;
        }
    }

    /**
     * Fetch orders from API and render them
     */
    async fetchAndRenderOrders() {
        try {
            const response = await axios.get(this.config.ordersEndpoint);

            // Check for success response structure with data field
            let orders = [];
            if (response.data && response.data.success && Array.isArray(response.data.data)) {
                orders = response.data.data;
            } else if (Array.isArray(response.data)) {
                orders = response.data;
            }

            if (orders.length > 0) {
                // Store all orders for filtering
                this.allOrders = orders;
                this.filteredOrders = [...orders];

                // Render first page of orders
                this.renderOrders(this.filteredOrders);
            } else {
                console.warn('No bookings found or invalid data format');
                if (this.container) {
                    this.container.innerHTML = '<div class="col-12"><p class="text-center">No bookings available.</p></div>';
                }
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching bookings:', error);
            if (this.container) {
                this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load bookings. Please try again later.</p></div>';
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
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No bookings match your filters. Try different criteria.</p></div>';
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

            // Handle different API response formats
            let order;
            if (response.data && response.data.success && response.data.data) {
                order = response.data.data;
            } else {
                order = response.data;
            }

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
            console.error('Error fetching booking details:', error);
            alert('Failed to load booking details. Please try again.');
        }
    }

    /**
     * Populate modal with order details
     */
    populateModal(order) {
        if (!order) return;

        // Normalize field names (handle both upper and lowercase)
        const orderData = {
            id: order.PB_ID || order.pb_id,
            variantId: order.PB_VARIANT_ID || order.pb_variant_id,
            quantity: order.PB_QUANTITY || order.pb_quantity,
            unitPrice: order.PB_UNIT_PRICE || order.pb_unit_price,
            totalAmount: order.PB_TOTAL_AMOUNT || order.pb_total_amount,
            status: order.PB_STATUS || order.pb_status,
            orderDate: order.PB_ORDER_DATE || order.pb_order_date,
            preferredDate: order.PB_PREFERRED_DATE || order.pb_preferred_date,
            preferredTime: order.PB_PREFERRED_TIME || order.pb_preferred_time,
            address: order.PB_ADDRESS || order.pb_address,
            productName: order.PROD_NAME || order.prod_name,
            productImage: order.PROD_IMAGE || order.prod_image,
            variantCapacity: order.VAR_CAPACITY || order.var_capacity
        };

        // Apply styling to modal elements
        if (this.modal.element) {
            // Style the modal dialog
            const modalDialog = this.modal.element.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.classList.add('modal-lg');
                modalDialog.classList.add('modal-dialog-centered');
            }

            // Style the modal header
            const modalHeader = this.modal.element.querySelector('.modal-header');
            if (modalHeader) {
                modalHeader.classList.add('bg-light');
                modalHeader.classList.add('border-0');
            }

            // Style the modal body
            const modalBody = this.modal.element.querySelector('.modal-body');
            if (modalBody) {
                modalBody.classList.add('p-4');
            }
        }

        // Check if modal elements exist before updating them
        if (this.modal.orderId) {
            this.modal.orderId.textContent = `PB-${orderData.id}`;
            this.modal.orderId.classList.add('fw-bold');
        }

        if (this.modal.productName) {
            this.modal.productName.textContent = orderData.productName || 'N/A';
            this.modal.productName.classList.add('fs-4');
            this.modal.productName.classList.add('fw-bold');
            this.modal.productName.classList.add('text-primary');
        }

        if (this.modal.productImage) {
            this.modal.productImage.src = orderData.productImage || '';
            this.modal.productImage.alt = orderData.productName || 'Product Image';
            this.modal.productImage.classList.add('img-fluid');
            this.modal.productImage.classList.add('rounded');
            this.modal.productImage.classList.add('shadow-sm');
        }

        if (this.modal.variant) {
            this.modal.variant.textContent = orderData.variantCapacity || 'N/A';
            this.modal.variant.classList.add('badge');
            this.modal.variant.classList.add('bg-secondary');
            this.modal.variant.classList.add('rounded-pill');
            this.modal.variant.classList.add('px-3');
        }

        if (this.modal.quantity) {
            this.modal.quantity.textContent = orderData.quantity;
            this.modal.quantity.classList.add('fw-bold');
        }

        if (this.modal.unitPrice) {
            this.modal.unitPrice.textContent = `$${parseFloat(orderData.unitPrice).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            this.modal.unitPrice.classList.add('fw-bold');
        }

        if (this.modal.totalAmount) {
            this.modal.totalAmount.textContent = `$${parseFloat(orderData.totalAmount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            this.modal.totalAmount.classList.add('fs-4');
            this.modal.totalAmount.classList.add('fw-bold');
            this.modal.totalAmount.classList.add('text-primary');
        }

        if (this.modal.status) {
            const statusText = orderData.status.charAt(0).toUpperCase() + orderData.status.slice(1);
            this.modal.status.textContent = statusText;

            // Add appropriate status class
            this.modal.status.className = ''; // Clear existing classes
            this.modal.status.classList.add('badge');

            const statusClass = this.getStatusBadgeClass(orderData.status);
            this.modal.status.classList.add(`bg-${statusClass}`);
            this.modal.status.classList.add('rounded-pill');
            this.modal.status.classList.add('px-3');
            this.modal.status.classList.add('py-2');
        }

        if (this.modal.orderDate) {
            this.modal.orderDate.textContent = new Date(orderData.orderDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            this.modal.orderDate.classList.add('text-muted');
        }

        if (this.modal.preferredDate) {
            this.modal.preferredDate.textContent = new Date(orderData.preferredDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            this.modal.preferredDate.classList.add('fw-bold');
        }

        if (this.modal.preferredTime) {
            // Format time from HH:MM:SS to HH:MM AM/PM
            let timeText = 'N/A';
            if (orderData.preferredTime) {
                const timeParts = orderData.preferredTime.split(':');
                if (timeParts.length >= 2) {
                    const hours = parseInt(timeParts[0]);
                    const minutes = timeParts[1];
                    const period = hours >= 12 ? 'PM' : 'AM';
                    const displayHours = hours % 12 || 12;
                    timeText = `${displayHours}:${minutes} ${period}`;
                }
            }
            this.modal.preferredTime.textContent = timeText;
            this.modal.preferredTime.classList.add('fw-bold');
        }

        if (this.modal.address) {
            this.modal.address.textContent = orderData.address || 'N/A';
            this.modal.address.classList.add('text-muted');
        }
    }
}