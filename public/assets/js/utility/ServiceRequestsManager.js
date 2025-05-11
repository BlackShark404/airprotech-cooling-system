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
            itemsPerPage: 10,
            paginationContainerSelector: '#services-pagination-container',
            ...options
        };

        // Set card template after configuration is complete
        this.config.cardTemplate = this.config.cardTemplate || this.getDefaultCardTemplate();

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
        this.filteredServiceRequests = [];

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
        if (!status) return 'secondary';
        
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
                if (serviceId) {
                    this.openServiceRequestModal(serviceId);
                }
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
                filteredServiceRequests = filteredServiceRequests.filter(service =>
                    service.SB_CREATED_AT && new Date(service.SB_CREATED_AT) >= startDate
                );
            }
        }

        // Apply status filter
        if (this.statusFilter && this.statusFilter.value && this.statusFilter.value !== 'All Status') {
            filteredServiceRequests = filteredServiceRequests.filter(service =>
                service.SB_STATUS && service.SB_STATUS.toLowerCase() === this.statusFilter.value.toLowerCase()
            );
        }

        // Apply search filter
        if (this.searchInput && this.searchInput.value.trim() !== '') {
            const searchTerm = this.searchInput.value.trim().toLowerCase();
            filteredServiceRequests = filteredServiceRequests.filter(service =>
                (service.ST_NAME && service.ST_NAME.toLowerCase().includes(searchTerm)) ||
                (service.ST_DESCRIPTION && service.ST_DESCRIPTION.toLowerCase().includes(searchTerm)) ||
                (service.SB_ID && `SRV-${service.SB_ID}`.toLowerCase().includes(searchTerm))
            );
        }

        // Store the filtered results
        this.filteredServiceRequests = filteredServiceRequests;

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
                this.filteredServiceRequests = serviceRequests;

                // Render first page of service requests
                this.renderServiceRequests(serviceRequests);
            } else {
                console.error('No service requests found or invalid data format');
                if (this.container) {
                    this.container.innerHTML = '<div class="col-12"><p class="text-center">No service requests available.</p></div>';
                }
                this.renderPagination(0);
            }
        } catch (error) {
            console.error('Error fetching service requests:', error);
            if (this.container) {
                this.container.innerHTML = '<div class="col-12"><p class="text-center text-danger">Failed to load service requests. Please try again later.</p></div>';
            }
            this.renderPagination(0);
        }
    }

    /**
     * Render service request cards with pagination
     */
    renderServiceRequests(serviceRequests) {
        if (!this.container) {
            console.error('Service request container not found');
            return;
        }

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
        
        if (totalPages <= 0) {
            paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <nav aria-label="Service request pagination">
                <ul class="pagination">
                    <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="prev"><i class="fas fa-chevron-left"></i></a>
                    </li>
        `;

        const maxPageButtons = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxPageButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxPageButtons - 1);
        
        if (endPage - startPage + 1 < maxPageButtons) {
            startPage = Math.max(1, endPage - maxPageButtons + 1);
        }

        // First page button if not in view
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

        // Page buttons
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${this.currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Last page button if not in view
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
                
                // Find the data-page attribute on the clicked element or its parent
                let target = e.target;
                let pageAction = target.getAttribute('data-page');
                
                // If the target is an icon inside the link, get the parent's data-page
                if (!pageAction && target.tagName.toLowerCase() === 'i') {
                    pageAction = target.parentElement.getAttribute('data-page');
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
        const totalPages = Math.ceil(this.filteredServiceRequests.length / this.itemsPerPage);

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

        // Re-render the current filtered service requests with the new page
        this.renderServiceRequests(this.filteredServiceRequests);
        
        // Scroll to top of the container
        if (this.container) {
            this.container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /**
     * Open service request modal with details
     */
    async openServiceRequestModal(serviceId) {
        try {
            // Check if we already have this service request in our list
            let service = this.allServiceRequests.find(s => s.SB_ID == serviceId);
            
            // If not found in our list or we need fresh data, fetch it
            if (!service) {
                const response = await axios.get(`${this.config.serviceRequestsEndpoint}/${serviceId}`);
                service = response.data;
            }

            if (!service) {
                throw new Error('Service request not found');
            }

            this.currentServiceRequest = service;
            this.populateModal(service);

            // Check if the modal element exists
            if (!this.modal.element) {
                console.error('Modal element not found');
                return;
            }

            const bsModal = new bootstrap.Modal(this.modal.element);
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
        if (!service) return;
        
        // Safely set text content only if elements exist
        const safeSetText = (element, text) => {
            if (element) element.textContent = text;
        };
        
        safeSetText(this.modal.serviceId, `SRV-${service.SB_ID}`);
        safeSetText(this.modal.serviceName, service.ST_NAME || 'N/A');
        safeSetText(this.modal.serviceDescription, service.ST_DESCRIPTION || 'No description available');
        
        // Handle date formatting safely
        if (this.modal.requestedDate) {
            try {
                const date = service.SB_REQUESTED_DATE ? new Date(service.SB_REQUESTED_DATE) : null;
                this.modal.requestedDate.textContent = date ? date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) : 'N/A';
            } catch (e) {
                this.modal.requestedDate.textContent = 'Invalid Date';
            }
        }
        
        safeSetText(this.modal.requestedTime, service.SB_REQUESTED_TIME || 'N/A');
        safeSetText(this.modal.address, service.SB_ADDRESS || 'N/A');
        
        // Handle status formatting safely
        if (this.modal.status && service.SB_STATUS) {
            this.modal.status.textContent = service.SB_STATUS.charAt(0).toUpperCase() + service.SB_STATUS.slice(1);
        } else if (this.modal.status) {
            this.modal.status.textContent = 'N/A';
        }
        
        // Handle cost formatting safely
        if (this.modal.estimatedCost) {
            this.modal.estimatedCost.textContent = service.SB_ESTIMATED_COST ? 
                `$${service.SB_ESTIMATED_COST}` : 
                (service.ST_PRICE_BASE ? `$${service.ST_PRICE_BASE}` : 'TBD');
        }
        
        // Handle priority formatting safely
        if (this.modal.priority && service.SB_PRIORITY) {
            this.modal.priority.textContent = service.SB_PRIORITY.charAt(0).toUpperCase() + service.SB_PRIORITY.slice(1);
        } else if (this.modal.priority) {
            this.modal.priority.textContent = 'N/A';
        }
        
        safeSetText(this.modal.notes, service.SB_DESCRIPTION || 'No additional notes');
    }
}