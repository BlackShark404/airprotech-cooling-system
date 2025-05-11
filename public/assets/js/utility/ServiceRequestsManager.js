/**
 * ServiceRequestsManager Class
 * Handles creating service request cards, managing the details modal,
 * filtering/searching service requests, and client-side pagination
 */
class ServiceRequestsManager {
    constructor(options = {}) {
        // Default configuration
        this.config = {
            serviceRequestsEndpoint: '/api/service-bookings',
            containerSelector: '#services',
            modalId: 'serviceRequestDetailModal',
            filterFormId: 'service-request-filters',
            searchInputId: 'service-request-search',
            dateFilterId: 'date-filter',
            statusFilterId: 'status-filter',
            cardTemplate: this.getDefaultCardTemplate(),
            itemsPerPage: 10,
            paginationContainerSelector: '#services-pagination-container',
            ...options
        };

        // Initialize modal elements references
        this.modal = {
            element: document.getElementById(this.config.modalId),
            serviceId: document.getElementById('modal-service-id'),
            serviceName: document.getElementById('modal-service-name'),
            serviceDescription: document.getElementById('modal-service-description'),
            requestedDate: document.getElementById('modal-requested-date'),
            requestedTime: document.getElementById('modal-requested-time'),
            address: document.getElementById('modal-address'),
            status: document.getElementById('modal-status'),
            estimatedCost: document.getElementById('modal-estimated-cost'),
            priority: document.getElementById('modal-priority'),
            notes: document.getElementById('modal-notes')
        };

        // Container for service request cards
        this.container = document.querySelector(this.config.containerSelector);

        // Store all service requests for filtering
        this.allServiceRequests = [];

        // Pagination state
        this.currentPage = 1;
        this.itemsPerPage = this.config.itemsPerPage;

        // Initialize modal controls
        this.initModalControls();

        // Initialize filter and search
        this.initFilterAndSearch();
    }

    /**
     * Map service type codes to Font Awesome icons
     */
    getServiceIcon(serviceTypeCode) {
        const iconMap = {
            'checkup-repair': 'fas fa-tools fa-lg',
            'installation': 'fas fa-plug fa-lg',
            'ducting': 'fas fa-wind fa-lg',
            'cleaning-pms': 'fas fa-broom fa-lg',
            'survey-estimation': 'fas fa-search fa-lg',
            'project-quotations': 'fas fa-file-invoice-dollar fa-lg'
        };
        return iconMap[serviceTypeCode] || 'fas fa-cog fa-lg'; // Fallback icon
    }

    /**
     * Default card template for service requests
     */
    getDefaultCardTemplate() {
        return (service) => `
            <div class="booking-item card shadow-sm mb-3">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="service-icon me-4">
                        <i class="${this.getServiceIcon(service.ST_CODE)}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">SRV-${service.SB_ID} <span class="text-muted">${new Date(service.SB_CREATED_AT).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span></p>
                                <h5 class="fw-bold mb-1">${service.ST_NAME}</h5>
                                <p class="text-muted mb-0">Service: ${service.ST_DESCRIPTION || 'N/A'}</p>
                                <p class="fw-bold text-dark mb-0">$${service.SB_ESTIMATED_COST || service.ST_PRICE_BASE || 'TBD'}</p>
                            </div>
                            <div class="text-end">
                                <p class="text-muted mb-1">Requested on: ${new Date(service.SB_CREATED_AT).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                <span class="badge bg-${this.getStatusBadgeClass(service.SB_STATUS)}-subtle text-${this.getStatusBadgeClass(service.SB_STATUS)}">${service.SB_STATUS.charAt(0).toUpperCase() + service.SB_STATUS.slice(1)}</span>
                                <div class="mt-2">
                                    <button class="btn btn-danger view-details" data-service-id="${service.SB_ID}">View Details</button>
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
            case 'in-progress':
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
                const serviceId = e.target.getAttribute('data-service-id');
                this.openServiceRequestModal(serviceId);
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
        if (!this.allServiceRequests.length) return;

        let filteredServiceRequests = [...this.allServiceRequests];

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
                filteredServiceRequests = filteredServiceRequests.filter(service =>
                    new Date(service.SB_CREATED_AT) >= startDate
                );
            }
        }

        // Apply status filter
        if (this.statusFilter && this.statusFilter.value && this.statusFilter.value !== 'All Status') {
            filteredServiceRequests = filteredServiceRequests.filter(service =>
                service.SB_STATUS.toLowerCase() === this.statusFilter.value.toLowerCase()
            );
        }

        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredServiceRequests = filteredServiceRequests.filter(service =>
                service.ST_NAME.toLowerCase().includes(searchTerm) ||
                (service.ST_DESCRIPTION && service.ST_DESCRIPTION.toLowerCase().includes(searchTerm)) ||
                `SRV-${service.SB_ID}`.toLowerCase().includes(searchTerm)
            );
        }

        // Reset to first page when filters change
        this.currentPage = 1;

        // Render filtered service requests with pagination
        this.renderServiceRequests(filteredServiceRequests);

        // Update results count if element exists
        const resultsCountElement = document.getElementById('service-results-count');
        if (resultsCountElement) {
            resultsCountElement.textContent = `${filteredServiceRequests.length} service requests found`;
        }
    }

    /**
     * Fetch service requests from API and render them
     */
    async fetchAndRenderServiceRequests() {
        try {
            const response = await axios.get(this.config.serviceRequestsEndpoint);
            const serviceRequests = response.data;

            if (Array.isArray(serviceRequests) && serviceRequests.length > 0) {
                // Store all service requests for filtering
                this.allServiceRequests = serviceRequests;

                // Render first page of service requests
                this.renderServiceRequests(serviceRequests);
            } else {
                console.error('No service requests found or invalid data format');
                this.container.innerHTML = '<div class="col-12"><p class="text-center">No service requests available.</p></div>';
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching service requests:', error);
            this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load service requests. Please try again later.</p></div>';
            this.renderPagination(0);
        }
    }

    /**
     * Render service request cards with pagination
     */
    renderServiceRequests(serviceRequests) {
        if (serviceRequests.length === 0) {
            this.container.innerHTML = '<div class="col-12"><p class="text-center">No service requests match your filters. Try different criteria.</p></div>';
            this.renderPagination(0);
            return;
        }

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const paginatedServiceRequests = serviceRequests.slice(startIndex, endIndex);

        let html = '';
        paginatedServiceRequests.forEach(service => {
            html += this.config.cardTemplate(service);
        });

        this.container.innerHTML = html;

        this.renderPagination(serviceRequests.length);
    }

    /**
     * Render pagination controls
     */
    renderPagination(totalItems) {
        const paginationContainer = document.querySelector(this.config.paginationContainerSelector);
        if (!paginationContainer) return;

        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
        let paginationHTML = `
            <nav aria-label="Service request pagination">
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
        const totalPages = Math.ceil(this.allServiceRequests.length / this.itemsPerPage);

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
     * Open service request modal with details
     */
    async openServiceRequestModal(serviceId) {
        try {
            const response = await axios.get(`${this.config.serviceRequestsEndpoint}/${serviceId}`);
            const service = response.data;

            this.currentServiceRequest = service;
            this.populateModal(service);

            const modalElement = document.getElementById(this.config.modalId);
            const bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();
        } catch (error) {
            console.error('Error fetching service request details:', error);
            alert('Failed to load service request details. Please try again.');
        }
    }

    /**
     * Populate modal with service request details
     */
    populateModal(service) {
        this.modal.serviceId.textContent = `SRV-${service.SB_ID}`;
        this.modal.serviceName.textContent = service.ST_NAME;
        this.modal.serviceDescription.textContent = service.ST_DESCRIPTION || 'No description available';
        this.modal.requestedDate.textContent = new Date(service.SB_REQUESTED_DATE).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        this.modal.requestedTime.textContent = service.SB_REQUESTED_TIME || 'N/A';
        this.modal.address.textContent = service.SB_ADDRESS || 'N/A';
        this.modal.status.textContent = service.SB_STATUS.charAt(0).toUpperCase() + service.SB_STATUS.slice(1);
        this.modal.estimatedCost.textContent = service.SB_ESTIMATED_COST ? `$${service.SB_ESTIMATED_COST}` : `$${service.ST_PRICE_BASE || 'TBD'}`;
        this.modal.priority.textContent = service.SB_PRIORITY.charAt(0).toUpperCase() + service.SB_PRIORITY.slice(1);
        this.modal.notes.textContent = service.SB_DESCRIPTION || 'No additional notes';
    }
}