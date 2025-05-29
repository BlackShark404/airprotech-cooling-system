/**
 * DataTable5 Class
 *
 * Creates an interactive data table with client-side rendering, pagination,
 * search, sorting, filtering, and modal support for actions (View, Edit, Delete, Custom).
 * Relies on a server-side endpoint (PHP) for data fetching and manipulation.
 *
 * @version 1.0.0
 * @author Your Name / Company
 */
class DataTable5 {
    /**
     * Initializes the DataTable.
     * @param {string} targetSelector - CSS selector for the container element where the table will be rendered.
     * @param {object} config - Configuration object for the table.
     * @param {string} config.ajaxUrl - The base URL for API calls (data fetching, updates, etc.).
     * @param {string} config.idKey - The name of the unique identifier field in the data objects (e.g., 'id').
     * @param {Array<object>} config.columns - Array defining table columns.
     *   @param {string} column.data - The key in the data object for this column. Supports dot notation for nested objects (e.g., 'user.name').
     *   @param {string} column.title - The display title for the column header.
     *   @param {boolean} [column.sortable=true] - Whether the column can be sorted.
     *   @param {boolean} [column.searchable=true] - Whether this column should be included in the general search.
     *   @param {function} [column.render] - Optional function to customize cell rendering. Receives (data, type, row, meta). `type` is 'display'. `row` is the full row data object. `meta` contains {row_index, col_index}. Should return HTML string or Node.
     * @param {Array<object>} [config.actions=[]] - Array defining row actions.
     *   @param {string} action.type - Type of action ('view', 'edit', 'delete', 'custom').
     *   @param {string} action.label - Text/HTML for the action button/link.
     *   @param {string} action.className - CSS class for the action button/link.
     *   @param {string} [action.modalId] - (Required for view, edit, custom if using modals) The ID of the Bootstrap modal element.
     *   @param {string} [action.customEvent] - (Required for 'custom' type) The name of a custom event to dispatch on the table container when the action is clicked. Event detail will contain { action, rowId, rowData }.
     *   @param {function} [action.onClick] - (Alternative to modal/customEvent) A function to call directly when the action is clicked. Receives (rowId, rowData, event).
     * @param {Array<object>} [config.filters=[]] - Array defining filter controls to be added above the table.
     *   @param {string} filter.id - Unique ID for the filter control (used as element ID).
     *   @param {string} filter.label - Display label for the filter.
     *   @param {string} filter.type - Type of HTML input ('text', 'select', 'date', etc.).
     *   @param {string} filter.paramName - The query parameter name to send to the backend (e.g., 'status', 'role').
     *   @param {Array<object>} [filter.options] - (Required for 'select') Array of options {value: '', text: ''}.
     *   @param {string} [filter.placeholder] - Placeholder text for input fields.
     *   @param {string} [filter.defaultValue] - Default value for the filter.
     * @param {object} [config.options={}] - General table options.
     *   @param {number} [options.pageSize=10] - Number of rows per page.
     *   @param {boolean} [options.showSearch=true] - Whether to show the global search input.
     *   @param {string} [options.searchPlaceholder='Search...'] - Placeholder for search input.
     *   @param {number} [options.searchDelay=300] - Debounce delay (ms) for search input.
     *   @param {boolean} [options.showFilters=true] - Whether to show the defined filters area.
     *   @param {boolean} [options.showActions=true] - Whether to show the actions column if actions are defined.
     *   @param {string} [options.actionsColumnTitle='Actions'] - Title for the actions column header.
     *   @param {object} [options.messages] - Text messages for localization/customization.
     *     @param {string} [messages.processing='Processing...']
     *     @param {string} [messages.emptyTable='No data available in table']
     *     @param {string} [messages.info='Showing _START_ to _END_ of _TOTAL_ entries']
     *     @param {string} [messages.infoEmpty='Showing 0 to 0 of 0 entries']
     *     @param {string} [messages.infoFiltered='(filtered from _MAX_ total entries)'] - Note: _MAX_ requires server support. We use _TOTAL_ for simplicity here.
     *     @param {string} [messages.confirmDeleteTitle='Confirm Deletion']
     *     @param {string} [messages.confirmDeleteBody='Are you sure you want to delete this record?']
     *     @param {string} [messages.deleteSuccess='Record deleted successfully.']
     *     @param {string} [messages.deleteError='Error deleting record.']
     *     @param {string} [messages.saveSuccess='Record saved successfully.']
     *     @param {string} [messages.saveError='Error saving record.']
     *     @param {string} [messages.fetchError='Error fetching data.']
     * @param {object} [config.modalTemplates={}] - Optional: Allows providing custom functions to populate modals instead of relying solely on form field names matching data keys.
     *   @param {function} [modalTemplates.populateView] - Function(modalElement, rowData) to populate the view modal.
     *   @param {function} [modalTemplates.populateEdit] - Function(modalElement, rowData) to populate the edit modal form.
     *   @param {function} [modalTemplates.getEditData] - Function(modalElement) to retrieve data from the edit form, should return an object.
     * @param {object} [config.fetchOptions={}] - Optional: Custom options for the fetch API (e.g., headers, credentials).
     */
    constructor(targetSelector, config) {
        this.container = document.querySelector(targetSelector);
        if (!this.container) {
            console.error(`DataTable5 Error: Target container "${targetSelector}" not found.`);
            return;
        }

        // --- Default Configuration ---
        const defaults = {
            idKey: 'id',
            columns: [],
            actions: [],
            filters: [],
            options: {
                pageSize: 10,
                showSearch: true,
                searchPlaceholder: 'Search...',
                searchDelay: 300,
                showFilters: true,
                showActions: true,
                actionsColumnTitle: 'Actions',
                messages: {
                    processing: 'Processing...',
                    emptyTable: 'No data available in table',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '', // '(filtered from _MAX_ total entries)' - Requires backend support for total unfiltered count
                    confirmDeleteTitle: 'Confirm Deletion',
                    confirmDeleteBody: 'Are you sure you want to delete this record?',
                    deleteSuccess: 'Record deleted successfully.',
                    deleteError: 'Error deleting record.',
                    saveSuccess: 'Record saved successfully.',
                    saveError: 'Error saving record.',
                    fetchError: 'Error fetching data.',
                }
            },
            modalTemplates: {
                populateView: null, // (modalElement, rowData)
                populateEdit: null, // (modalElement, rowData)
                getEditData: null,  // (modalElement) => dataObject
            },
            fetchOptions: {} // Custom fetch options
        };

        // --- Merge Config ---
        this.config = this._deepMerge(defaults, config);
        this.config.options.showFilters = this.config.options.showFilters && this.config.filters.length > 0;
        this.config.options.showActions = this.config.options.showActions && this.config.actions.length > 0;

        // --- Internal State ---
        this.currentPage = 1;
        this.searchTerm = '';
        this.sortBy = null;
        this.sortDir = 'asc'; // 'asc' or 'desc'
        this.currentFilters = {}; // { paramName: value }
        this.totalRecords = 0;
        this.currentData = []; // Data for the current page
        this.isLoading = false;
        this.searchTimeout = null;
        this.abortController = null; // For aborting pending fetch requests

        // --- Initial Setup ---
        this._initializeFilters();
        this._renderSkeleton();
        this.fetchData();
        this._bindGlobalEvents(); // Event delegation for table body
    }

    // =========================================================================
    // Public API Methods
    // =========================================================================

    /**
     * Refreshes the table data, optionally staying on the current page.
     * @param {boolean} [stayOnPage=false] - If true, stays on the current page after refresh. Otherwise goes to page 1.
     */
    refresh(stayOnPage = false) {
        if (!stayOnPage) {
            this.currentPage = 1;
        }
        this.fetchData();
    }

    /**
     * Destroys the DataTable instance, removing elements and event listeners.
     */
    destroy() {
        if (this.searchTimeout) clearTimeout(this.searchTimeout);
        if (this.abortController) this.abortController.abort();

        // Remove event listeners (more robustly if not using delegation)
        this.container.removeEventListener('click', this._handleTableClick);
        this.container.removeEventListener('input', this._handleInput);
        this.container.removeEventListener('change', this._handleChange);

        // Clear container
        this.container.innerHTML = '';
        // Dereference elements to help garbage collection (optional)
        this.table = null;
        this.thead = null;
        this.tbody = null;
        this.tfoot = null;
        this.container = null;
        // Potentially remove references to modal instances if created dynamically
    }


    // =========================================================================
    // Core Rendering Methods
    // =========================================================================

    /** Renders the basic structure (skeleton) of the table and controls. */
    _renderSkeleton() {
        this.container.innerHTML = `
            <div class="datatable5-wrapper">
                <div class="datatable5-controls-top">
                    ${this.config.options.showFilters ? this._renderFiltersArea() : ''}
                    ${this.config.options.showSearch ? this._renderSearchArea() : ''}
                </div>
                <div class="datatable5-table-container">
                    <table class="datatable5-table table table-striped table-bordered" role="grid">
                         <thead></thead>
                         <tbody></tbody>
                         <tfoot></tfoot>
                    </table>
                </div>
                 <div class="datatable5-info" role="status" aria-live="polite"></div>
                 <div class="datatable5-pagination"></div>
                 <div class="datatable5-loading" style="display: none;">
                     <div class="spinner-border text-primary" role="status">
                         <span class="visually-hidden">${this.config.options.messages.processing}</span>
                     </div>
                 </div>
                 <div class="datatable5-error" style="display: none; color: red;"></div>
            </div>
        `;

        // Cache references to key elements
        this.table = this.container.querySelector('.datatable5-table');
        this.thead = this.table.querySelector('thead');
        this.tbody = this.table.querySelector('tbody');
        this.tfoot = this.table.querySelector('tfoot'); // Although we render pagination separately
        this.infoContainer = this.container.querySelector('.datatable5-info');
        this.paginationContainer = this.container.querySelector('.datatable5-pagination');
        this.loadingIndicator = this.container.querySelector('.datatable5-loading');
        this.errorContainer = this.container.querySelector('.datatable5-error');
        this.searchBox = this.container.querySelector('.datatable5-search-input'); // Might be null if disabled
        this.filtersContainer = this.container.querySelector('.datatable5-filters-area'); // Might be null
    }

    /** Renders the filter controls area */
    _renderFiltersArea() {
        if (!this.config.filters || this.config.filters.length === 0) return '';

        const filterControls = this.config.filters.map(filter => {
            let controlHtml = '';
            const currentValue = this.currentFilters[filter.paramName] || filter.defaultValue || '';
            const commonAttrs = `id="${filter.id}" name="${filter.paramName}" class="form-control form-control-sm datatable5-filter-control" data-param-name="${filter.paramName}"`;

            switch (filter.type) {
                case 'select':
                    const optionsHtml = filter.options.map(opt =>
                        `<option value="${opt.value}" ${opt.value == currentValue ? 'selected' : ''}>${opt.text}</option>`
                    ).join('');
                    controlHtml = `<select ${commonAttrs}>${optionsHtml}</select>`;
                    break;
                case 'date':
                    controlHtml = `<input type="date" ${commonAttrs} value="${currentValue}" />`;
                    break;
                case 'number':
                     controlHtml = `<input type="number" ${commonAttrs} value="${currentValue}" placeholder="${filter.placeholder || ''}" />`;
                    break;
                case 'text':
                default:
                    controlHtml = `<input type="text" ${commonAttrs} value="${currentValue}" placeholder="${filter.placeholder || filter.label}" />`;
                    break;
            }

            return `
                <div class="datatable5-filter-item mb-2 me-2">
                    <label for="${filter.id}" class="form-label me-1">${filter.label}:</label>
                    ${controlHtml}
                </div>
            `;
        }).join('');

        return `
            <div class="datatable5-filters-area d-flex flex-wrap align-items-center">
                ${filterControls}
                <button class="btn btn-sm btn-secondary datatable5-filter-reset ms-2 mb-2">Reset Filters</button>
            </div>`;
    }

    /** Renders the search input area */
    _renderSearchArea() {
        return `
            <div class="datatable5-search-area ms-auto">
                 <label for="${this.container.id}-search" class="form-label me-1 visually-hidden">Search:</label>
                 <input type="search" id="${this.container.id}-search" class="form-control form-control-sm datatable5-search-input" placeholder="${this.config.options.searchPlaceholder}">
            </div>`;
    }


    /** Renders the table header (thead). */
    _renderThead() {
        const headerCells = this.config.columns.map((col, index) => {
            let sortClass = '';
            let sortIcon = ''; // Use FontAwesome or Bootstrap Icons
            let ariaSort = 'none';
            if (col.sortable !== false) {
                sortClass = 'sorting';
                if (this.sortBy === col.data) {
                    sortClass = this.sortDir === 'asc' ? 'sorting_asc' : 'sorting_desc';
                    ariaSort = this.sortDir === 'asc' ? 'ascending' : 'descending';
                    sortIcon = this.sortDir === 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; // Example using Bootstrap Icons
                } else {
                     sortIcon = '<i class="bi bi-arrow-down-up"></i>'; // Example default sort icon
                }
            }
            return `<th scope="col" class="${sortClass}" data-col-index="${index}" data-col-data="${col.data}" ${col.sortable !== false ? 'tabindex="0" role="button" aria-label="'+col.title+': activate to sort column '+(ariaSort === 'ascending' ? 'descending' : 'ascending')+'"' : ''} aria-sort="${ariaSort}">
                        ${col.title} ${sortIcon}
                    </th>`;
        }).join('');

        let actionsHeader = '';
        if (this.config.options.showActions) {
            actionsHeader = `<th scope="col" class="actions-column">${this.config.options.actionsColumnTitle}</th>`;
        }

        this.thead.innerHTML = `<tr>${headerCells}${actionsHeader}</tr>`;
    }

    /** Renders the table body (tbody). */
    _renderTbody() {
        this._showLoading();
        this.tbody.innerHTML = ''; // Clear previous content

        if (this.currentData.length === 0) {
            const colCount = this.config.columns.length + (this.config.options.showActions ? 1 : 0);
            this.tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">${this.config.options.messages.emptyTable}</td></tr>`;
            this._hideLoading();
            return;
        }

        const fragment = document.createDocumentFragment();
        this.currentData.forEach((row, rowIndex) => {
            const tr = document.createElement('tr');
            tr.setAttribute('role', 'row');
            const rowId = this._getNestedValue(row, this.config.idKey);
            if (rowId !== null && rowId !== undefined) {
                tr.dataset.rowId = rowId;
            } else {
                console.warn(`DataTable5 Warning: Row ID key "${this.config.idKey}" not found or is null/undefined in row data:`, row);
            }


            // Render Data Cells
            this.config.columns.forEach((col, colIndex) => {
                const cell = tr.insertCell();
                cell.setAttribute('role', 'cell');
                const cellData = this._getNestedValue(row, col.data);
                if (col.render && typeof col.render === 'function') {
                    const renderedContent = col.render(cellData, 'display', row, { row_index: rowIndex, col_index: colIndex });
                    if (renderedContent instanceof Node) {
                        cell.appendChild(renderedContent);
                    } else {
                        cell.innerHTML = renderedContent;
                    }
                } else {
                    cell.textContent = cellData !== null && cellData !== undefined ? cellData : '';
                }
            });

            // Render Actions Cell
            if (this.config.options.showActions) {
                const actionsCell = tr.insertCell();
                actionsCell.classList.add('actions-column');
                actionsCell.setAttribute('role', 'cell');
                this.config.actions.forEach(action => {
                    const button = document.createElement('button'); // Could be 'a' as well
                    button.innerHTML = action.label;
                    button.className = `btn btn-sm datatable5-action ${action.className || ''}`;
                    button.dataset.actionType = action.type;
                    button.dataset.rowId = rowId; // Ensure rowId is available
                    if (action.modalId) {
                        button.dataset.bsToggle = 'modal';
                        button.dataset.bsTarget = `#${action.modalId}`;
                        // Store necessary info for modal population
                        button.dataset.action = JSON.stringify(action);
                    } else if (action.customEvent) {
                         button.dataset.customEvent = action.customEvent;
                         button.dataset.action = JSON.stringify(action); // Include action config in event
                    } else if (action.onClick) {
                         // We handle this via event delegation later using action.type
                    }
                    actionsCell.appendChild(button);
                     actionsCell.appendChild(document.createTextNode(' ')); // Add space between buttons
                });
            }

            fragment.appendChild(tr);
        });

        this.tbody.appendChild(fragment);
        this._hideLoading();
    }

    /** Renders the pagination controls. */
    _renderPagination() {
        this.paginationContainer.innerHTML = '';
        if (this.totalRecords <= this.config.options.pageSize) {
            return; // No pagination needed if fits on one page
        }

        const totalPages = Math.ceil(this.totalRecords / this.config.options.pageSize);
        const currentPage = this.currentPage;

        let paginationHtml = '<ul class="pagination pagination-sm justify-content-end">'; // Bootstrap pagination classes

        // Previous Button
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                               <a class="page-link datatable5-page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                                   <span aria-hidden="true">«</span>
                               </a>
                           </li>`;

        // Page Number Buttons (simplified logic for brevity, could be more complex)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
             paginationHtml += `<li class="page-item"><a class="page-link datatable5-page-link" href="#" data-page="1">1</a></li>`;
             if (startPage > 2) {
                 paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
             }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                   <a class="page-link datatable5-page-link" href="#" data-page="${i}">${i}</a>
                               </li>`;
        }

         if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHtml += `<li class="page-item"><a class="page-link datatable5-page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }


        // Next Button
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                               <a class="page-link datatable5-page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                                   <span aria-hidden="true">»</span>
                               </a>
                           </li>`;

        paginationHtml += '</ul>';
        this.paginationContainer.innerHTML = paginationHtml;
    }

    /** Renders the information text (Showing X to Y of Z entries). */
    _renderInfo() {
        let infoText = this.config.options.messages.infoEmpty;
        if (this.totalRecords > 0) {
            const start = (this.currentPage - 1) * this.config.options.pageSize + 1;
            const end = Math.min(start + this.config.options.pageSize - 1, this.totalRecords);
            infoText = this.config.options.messages.info
                .replace('_START_', start)
                .replace('_END_', end)
                .replace('_TOTAL_', this.totalRecords);
            // Add filtered message if applicable (requires backend support for _MAX_)
             // if (this.totalRecords < totalUnfilteredRecords) {
             //     infoText += ' ' + this.config.options.messages.infoFiltered.replace('_MAX_', totalUnfilteredRecords);
             // }
        }
        this.infoContainer.textContent = infoText;
    }

    // =========================================================================
    // Data Fetching & Handling
    // =========================================================================

    /** Fetches data from the server based on the current state. */
    async fetchData() {
        if (this.isLoading) {
            // Optionally abort previous request if a new one starts quickly
             if (this.abortController) {
                 this.abortController.abort();
                 console.log("Aborted previous fetch request.");
             }
            // return; // Or queue? For simplicity, we'll abort.
        }
        this.isLoading = true;
        this._showLoading();
        this._hideError();
        this.abortController = new AbortController(); // Create a new controller for this request
        const { signal } = this.abortController;


        const params = new URLSearchParams({
            page: this.currentPage,
            limit: this.config.options.pageSize,
        });

        if (this.searchTerm) {
            params.set('search', this.searchTerm);
            // Add searchable columns if needed by backend
             this.config.columns.forEach(col => {
                 if (col.searchable !== false) {
                     // Example: params.append('search_in[]', col.data); // Depends on backend API
                 }
             });
        }
        if (this.sortBy) {
            params.set('sort_by', this.sortBy);
            params.set('sort_dir', this.sortDir);
        }

        // Add active filters
        for (const key in this.currentFilters) {
             if (this.currentFilters[key] !== null && this.currentFilters[key] !== '') {
                params.set(key, this.currentFilters[key]);
             }
        }


        const fetchUrl = `${this.config.ajaxUrl}?${params.toString()}`;
        const fetchOptions = {
            method: 'GET', // Default, override via config.fetchOptions if needed
            signal: signal, // Pass the signal to fetch
            ...this.config.fetchOptions, // Merge custom fetch options
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest', // Common practice for AJAX
                 ...(this.config.fetchOptions.headers || {}) // Merge custom headers
            }
        };

        try {
            const response = await fetch(fetchUrl, fetchOptions);

            if (!response.ok) {
                let errorMsg = `HTTP error! Status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMsg = errorData.message || errorMsg;
                } catch(e) { /* Ignore if response is not JSON */ }
                throw new Error(errorMsg);
            }

            const result = await response.json();

            if (result && typeof result === 'object' && Array.isArray(result.data) && typeof result.totalRecords === 'number') {
                 this.currentData = result.data;
                 this.totalRecords = result.totalRecords;

                 // --- Re-render components ---
                 this._renderThead(); // Update sort indicators
                 this._renderTbody();
                 this._renderPagination();
                 this._renderInfo();
            } else {
                console.error("DataTable5 Error: Invalid data format received from server.", result);
                throw new Error("Invalid data format received.");
            }

        } catch (error) {
            if (error.name === 'AbortError') {
                console.log("Fetch aborted."); // Don't show error for aborts
            } else {
                console.error("DataTable5 Fetch Error:", error);
                this._showError(`${this.config.options.messages.fetchError}: ${error.message}`);
                 // Optionally clear table on fetch error
                 this.currentData = [];
                 this.totalRecords = 0;
                 this._renderTbody(); // Show empty message
                 this._renderPagination();
                 this._renderInfo();
            }
        } finally {
            this.isLoading = false;
            this._hideLoading();
            this.abortController = null; // Clear the controller
        }
    }

    /** Fetches data for a single record, typically for View/Edit modals. */
    async _fetchSingleRecord(recordId) {
        if (!recordId) return null;
        this._showLoading(true); // Show a smaller indicator or on modal
         this._hideError();

        const fetchUrl = `${this.config.ajaxUrl}/${recordId}`; // Assuming RESTful URL for single record
        const fetchOptions = {
            method: 'GET',
            ...this.config.fetchOptions,
             headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                 ...(this.config.fetchOptions.headers || {})
            }
        };

        try {
            const response = await fetch(fetchUrl, fetchOptions);
             if (!response.ok) {
                 let errorMsg = `HTTP error! Status: ${response.status}`;
                 try { const errorData = await response.json(); errorMsg = errorData.message || errorMsg; } catch(e) {}
                 throw new Error(errorMsg);
             }
             const result = await response.json();
             if (result && result.success && result.data) {
                 return result.data;
             } else {
                  throw new Error(result.message || "Failed to fetch record details.");
             }
        } catch (error) {
            console.error("DataTable5 Fetch Single Record Error:", error);
             this._showError(`${this.config.options.messages.fetchError}: ${error.message}`); // Or show in modal
            return null; // Indicate failure
        } finally {
             this._hideLoading(true);
        }
    }


    // =========================================================================
    // Event Handling
    // =========================================================================

    /** Binds event listeners using delegation on the container. */
    _bindGlobalEvents() {
        // Use event delegation on the container for efficiency
        this.container.addEventListener('click', this._handleTableClick.bind(this));
        this.container.addEventListener('input', this._handleInput.bind(this));
        this.container.addEventListener('change', this._handleChange.bind(this)); // For select dropdowns
    }

    /** Handles click events within the table container. */
    _handleTableClick(event) {
        const target = event.target;
        const linkTarget = target.closest('.datatable5-page-link');
        const sortTarget = target.closest('th[data-col-data]');
        const actionTarget = target.closest('.datatable5-action');
        const filterResetTarget = target.closest('.datatable5-filter-reset');

        if (linkTarget && !linkTarget.closest('.disabled')) {
            event.preventDefault();
            const page = parseInt(linkTarget.dataset.page, 10);
            if (!isNaN(page) && page !== this.currentPage) {
                this.currentPage = page;
                this.fetchData();
            }
        }
        else if (sortTarget && sortTarget.classList.contains('sorting') || sortTarget.classList.contains('sorting_asc') || sortTarget.classList.contains('sorting_desc')) {
            event.preventDefault();
            const columnData = sortTarget.dataset.colData;
            if (this.sortBy === columnData) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = columnData;
                this.sortDir = 'asc';
            }
            this.currentPage = 1; // Reset to first page on sort
            this.fetchData();
        }
        else if (actionTarget) {
            event.preventDefault();
            const rowId = actionTarget.dataset.rowId;
            const actionType = actionTarget.dataset.actionType;
             const rowElement = actionTarget.closest('tr');
             const rowData = this.currentData.find(row => String(this._getNestedValue(row, this.config.idKey)) === String(rowId)); // Find data for this row

            if (rowId === undefined || rowData === undefined) {
                 console.error("DataTable5 Error: Could not find row ID or data for action.", actionTarget);
                 return;
            }

            // Try specific onClick first
            const actionConfig = this.config.actions.find(a => a.type === actionType && a.label === actionTarget.innerHTML); // Find config
            if (actionConfig && typeof actionConfig.onClick === 'function') {
                actionConfig.onClick(rowId, rowData, event);
                return; // Stop further processing if onClick handled it
            }

            // Handle standard actions
            switch (actionType) {
                case 'view':
                    this._handleViewAction(rowId, rowData, actionTarget);
                    break;
                case 'edit':
                    this._handleEditAction(rowId, rowData, actionTarget);
                    break;
                case 'delete':
                    this._handleDeleteAction(rowId, rowData);
                    break;
                case 'custom':
                    this._handleCustomAction(rowId, rowData, actionTarget);
                    break;
                default:
                    console.warn(`DataTable5 Warning: Unhandled action type "${actionType}"`);
            }
        }
         else if (filterResetTarget) {
             event.preventDefault();
             this._resetFilters();
         }
    }

     /** Handles input events (primarily for search). */
    _handleInput(event) {
         const target = event.target;
         if (target.classList.contains('datatable5-search-input')) {
             // Debounce search
             if (this.searchTimeout) clearTimeout(this.searchTimeout);
             this.searchTimeout = setTimeout(() => {
                 this.searchTerm = target.value.trim();
                 this.currentPage = 1; // Reset to first page on search
                 this.fetchData();
             }, this.config.options.searchDelay);
         }
         // Handle input filters if needed (e.g., real-time filtering on text input)
         // else if (target.classList.contains('datatable5-filter-control') && target.type === 'text') {
         //     // Optional: Add debounce for text filters too
         //     const paramName = target.dataset.paramName;
         //     this.currentFilters[paramName] = target.value;
         //     this.currentPage = 1;
         //     this.fetchData();
         // }
     }

    /** Handles change events (primarily for select/date filters). */
     _handleChange(event) {
         const target = event.target;
         if (target.classList.contains('datatable5-filter-control')) {
             const paramName = target.dataset.paramName;
             this.currentFilters[paramName] = target.value;
             this.currentPage = 1; // Reset page on filter change
             this.fetchData();
         }
     }


    // =========================================================================
    // Action Handlers (Modals & Logic)
    // =========================================================================

     /** Handles the 'view' action, typically opening a modal. */
    async _handleViewAction(rowId, rowData, actionElement) {
        const modalId = actionElement.dataset.bsTarget?.substring(1); // Get modal ID from button
        if (!modalId) {
            console.error("DataTable5 Error: View action requires a modalId defined in config or data-bs-target attribute.");
            return;
        }
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`DataTable5 Error: Modal element with ID "${modalId}" not found.`);
            return;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement); // Use Bootstrap's Modal JS

        // Show loading state in modal?
        this._clearModal(modalElement); // Clear previous content

        // Fetch full details if needed, or use existing rowData
        // For view, often rowData is enough, but fetch if more details needed
        // const fullData = await this._fetchSingleRecord(rowId); // Uncomment if needed
        const dataToDisplay = rowData; // Or fullData if fetched

        if (dataToDisplay) {
             // Populate Modal - Prioritize custom populator
            if (typeof this.config.modalTemplates.populateView === 'function') {
                this.config.modalTemplates.populateView(modalElement, dataToDisplay);
            } else {
                this._populateModalGeneric(modalElement, dataToDisplay, 'view');
            }
            modal.show();
        } else {
            // Handle error if data fetch failed (error shown by _fetchSingleRecord)
            // Optionally show error message within the modal space
             this._showError("Could not load record details.", modalElement.querySelector('.modal-body')); // Example
        }
    }

    /** Handles the 'edit' action, populating an edit modal form. */
    async _handleEditAction(rowId, rowData, actionElement) {
        const modalId = actionElement.dataset.bsTarget?.substring(1);
        if (!modalId) {
             console.error("DataTable5 Error: Edit action requires a modalId defined in config or data-bs-target attribute.");
             return;
         }
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`DataTable5 Error: Modal element with ID "${modalId}" not found.`);
            return;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const form = modalElement.querySelector('form');
        if (!form) {
            console.error(`DataTable5 Error: Edit modal #${modalId} does not contain a <form> element.`);
            return;
        }

        this._clearModal(modalElement, true); // Clear previous content/errors, reset form

        // Fetch full details (usually required for edit forms)
        const fullData = await this._fetchSingleRecord(rowId);

        if (fullData) {
            // Populate Modal Form - Prioritize custom populator
            if (typeof this.config.modalTemplates.populateEdit === 'function') {
                this.config.modalTemplates.populateEdit(modalElement, fullData);
            } else {
                this._populateModalGeneric(modalElement, fullData, 'edit');
            }

            // Store the ID in the form (e.g., hidden input) or on the form element itself
            form.dataset.recordId = rowId; // Store ID for submission handler

            // Remove previous submit listener (if any) and add new one
            form.removeEventListener('submit', this._handleEditFormSubmit); // Avoid multiple listeners
            form.addEventListener('submit', this._handleEditFormSubmit.bind(this, modal, form, rowId));

            modal.show();
        } else {
            // Error handled by _fetchSingleRecord
             this._showError("Could not load record details for editing.", modalElement.querySelector('.modal-body')); // Example
        }
    }

     /** Handles the submission of the edit form modal. */
    async _handleEditFormSubmit(modalInstance, formElement, recordId, event) {
        event.preventDefault();
        this._showLoading(true, formElement); // Show loading indicator within modal/form
        this._hideError(formElement); // Hide previous errors in form


        let formDataObject;
        // Get data - Prioritize custom getter
        if (typeof this.config.modalTemplates.getEditData === 'function') {
            formDataObject = this.config.modalTemplates.getEditData(formElement);
        } else {
            formDataObject = this._getFormDataGeneric(formElement);
        }

        if (!formDataObject) {
             console.error("DataTable5 Error: Could not retrieve data from edit form.");
              this._showError("Error reading form data.", formElement);
              this._hideLoading(true, formElement);
             return;
        }

         // Add/Ensure ID is included if backend needs it in the body
         // formDataObject[this.config.idKey] = recordId; // Adjust if needed

        const fetchUrl = `${this.config.ajaxUrl}/${recordId}`; // Assuming RESTful PUT/POST for update
        const fetchOptions = {
            method: 'POST', // Or 'PUT', depending on your backend API
            ...this.config.fetchOptions,
            headers: {
                'Content-Type': 'application/json', // Common for sending JSON
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                // Add CSRF token header if needed
                 ...(this.config.fetchOptions.headers || {})
            },
            body: JSON.stringify(formDataObject) // Send data as JSON string
        };


        try {
            const response = await fetch(fetchUrl, fetchOptions);
             const result = await response.json(); // Assume backend always returns JSON

            if (!response.ok || !result.success) {
                 throw new Error(result.message || `HTTP error! Status: ${response.status}`);
            }

            // Success
             this._showToast(this.config.options.messages.saveSuccess); // Use a toast library or simple alert
             modalInstance.hide();
             this.refresh(true); // Refresh table, stay on current page

        } catch (error) {
            console.error("DataTable5 Save Error:", error);
             this._showError(`${this.config.options.messages.saveError}: ${error.message}`, formElement.querySelector('.modal-footer') || formElement); // Show error in modal footer or form
        } finally {
            this._hideLoading(true, formElement);
        }
    }


    /** Handles the 'delete' action, usually showing a confirmation modal. */
    _handleDeleteAction(rowId, rowData) {
        // Use a dedicated confirmation modal or a generic one
        // For simplicity, using window.confirm, but Bootstrap modal is better UX

        if (confirm(`${this.config.options.messages.confirmDeleteBody}\n\nID: ${rowId}`)) { // Replace with a proper modal
            this._executeDelete(rowId);
        }

        // --- Example using a Bootstrap Confirmation Modal (Requires HTML for this modal) ---
        /*
        const modalId = 'deleteConfirmModal'; // ID of your confirmation modal
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`DataTable5 Error: Delete confirmation modal #${modalId} not found.`);
            alert(this.config.options.messages.confirmDeleteBody); // Fallback
             if (confirm("Confirm deletion?")) this._executeDelete(rowId);
            return;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const confirmBtn = modalElement.querySelector('.btn-danger'); // Button to confirm deletion
        const bodyElement = modalElement.querySelector('.modal-body');

        if (bodyElement) bodyElement.textContent = this.config.options.messages.confirmDeleteBody + ` (ID: ${rowId})`; // Customize message

        // Add one-time listener for the confirm button
        const confirmHandler = async () => {
            confirmBtn.removeEventListener('click', confirmHandler); // Remove listener
            modal.hide(); // Hide modal first
            await this._executeDelete(rowId);
        };
        confirmBtn.addEventListener('click', confirmHandler, { once: true });

        modal.show();
        */
    }

    /** Executes the actual delete request to the server. */
    async _executeDelete(recordId) {
        this._showLoading(); // Show global loading indicator

        const fetchUrl = `${this.config.ajaxUrl}/${recordId}`; // Assuming RESTful DELETE
        const fetchOptions = {
            method: 'DELETE',
             ...this.config.fetchOptions,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                 // Add CSRF token header if needed
                ...(this.config.fetchOptions.headers || {})
            }
        };

        try {
            const response = await fetch(fetchUrl, fetchOptions);
             const result = await response.json(); // Assume backend always returns JSON

             if (!response.ok || !result.success) {
                throw new Error(result.message || `HTTP error! Status: ${response.status}`);
            }

            // Success
            this._showToast(this.config.options.messages.deleteSuccess); // Use a toast library or simple alert
             // Refresh: Go to page 1 if current page might become empty, otherwise stay
             const recordsOnCurrentPage = this.currentData.length;
             const newTotalRecords = this.totalRecords - 1;
             const newTotalPages = Math.ceil(newTotalRecords / this.config.options.pageSize);
             const stayOnPage = this.currentPage <= newTotalPages || newTotalPages === 0;

             // A simpler logic: If only 1 record was on the current page (and it's not page 1), go back one page.
             let goToPage1 = false;
             if (recordsOnCurrentPage === 1 && this.currentPage > 1) {
                 this.currentPage = Math.max(1, this.currentPage - 1); // Go back one page
             }

             this.refresh(stayOnPage && !goToPage1); // Refresh table data


        } catch (error) {
            console.error("DataTable5 Delete Error:", error);
             this._showError(`${this.config.options.messages.deleteError}: ${error.message}`);
        } finally {
            this._hideLoading();
        }
    }

    /** Handles 'custom' actions, dispatching an event or calling onClick. */
    _handleCustomAction(rowId, rowData, actionElement) {
        const customEventName = actionElement.dataset.customEvent;
        const actionConfigJson = actionElement.dataset.action;
        const actionConfig = actionConfigJson ? JSON.parse(actionConfigJson) : {};

        if (customEventName) {
             // Dispatch a custom event on the container element
             const event = new CustomEvent(customEventName, {
                 bubbles: true, // Allow event to bubble up
                 cancelable: true,
                 detail: {
                     action: actionConfig, // Pass the specific action config
                     rowId: rowId,
                     rowData: rowData,
                     sourceElement: actionElement
                 }
             });
             this.container.dispatchEvent(event);
             console.log(`DataTable5: Dispatched custom event "${customEventName}"`);
        } else {
             console.warn("DataTable5 Warning: Custom action clicked, but no 'customEvent' or 'onClick' defined for:", actionElement);
             // Potentially try modal logic if bs-target is set as a fallback?
             const modalId = actionElement.dataset.bsTarget?.substring(1);
             if (modalId) {
                 console.log("DataTable5: Custom action has bs-target, attempting to open modal:", modalId);
                 const modalElement = document.getElementById(modalId);
                 if (modalElement) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    // You might need to populate this modal based on rowData here
                    // This requires a convention or specific setup for custom modals
                    this._clearModal(modalElement);
                     // Maybe populate with basic info?
                    const title = modalElement.querySelector('.modal-title');
                    if (title) title.textContent = `Details for ID: ${rowId}`;
                     const body = modalElement.querySelector('.modal-body');
                     if (body) body.innerHTML = `<pre>${JSON.stringify(rowData, null, 2)}</pre>`;

                    modal.show();
                 } else {
                    console.error(`DataTable5 Error: Modal element with ID "${modalId}" for custom action not found.`);
                 }
             }
        }
    }

    // =========================================================================
    // Modal Utility Methods
    // =========================================================================

     /** Clears content and resets forms within a modal. */
    _clearModal(modalElement, isEditModal = false) {
        if (!modalElement) return;

        // Clear general display areas (e.g., for view modals)
        const displayAreas = modalElement.querySelectorAll('[data-dt5-display]');
        displayAreas.forEach(el => el.innerHTML = ''); // Clear elements marked for display

        // Reset form fields if it's an edit modal or contains a form
        const form = modalElement.querySelector('form');
        if (form && isEditModal) {
            form.reset(); // Reset form fields to default values
            form.removeAttribute('data-record-id'); // Remove stored record ID
             // Clear validation states (if using Bootstrap validation classes)
            form.classList.remove('was-validated');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // Clear any specific error messages within the modal
        this._hideError(modalElement);
    }

    /** Populates modal content generically based on data attributes or field names. */
    _populateModalGeneric(modalElement, data, type = 'view') {
        if (!modalElement || !data) return;

        if (type === 'view') {
            // Populate elements with 'data-dt5-display' attribute matching data keys
            const displayElements = modalElement.querySelectorAll('[data-dt5-display]');
            displayElements.forEach(el => {
                const key = el.dataset.dt5Display;
                const value = this._getNestedValue(data, key);
                el.textContent = value !== null && value !== undefined ? value : ''; // Or use innerHTML if needed
            });
        } else if (type === 'edit') {
            // Populate form fields where 'name' attribute matches data keys
             const form = modalElement.querySelector('form');
             if (!form) return;
            const formElements = form.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                const name = element.name;
                if (name) {
                    const value = this._getNestedValue(data, name);
                    if (value !== null && value !== undefined) {
                        if (element.type === 'checkbox') {
                            element.checked = !!value; // Set checked state based on truthiness
                        } else if (element.type === 'radio') {
                            if (element.value == value) { // Use == for loose comparison with string value
                                element.checked = true;
                            }
                        } else {
                            element.value = value;
                        }
                    } else {
                        // Handle null/undefined if needed (e.g., clear the field)
                         if (element.type !== 'checkbox' && element.type !== 'radio') {
                            element.value = '';
                         } else {
                             element.checked = false;
                         }
                    }
                }
            }
        }
    }

     /** Retrieves form data generically as an object. */
    _getFormDataGeneric(formElement) {
        if (!formElement) return null;
        const formData = new FormData(formElement);
        const dataObject = {};
        formData.forEach((value, key) => {
            // Handle potential duplicate keys (e.g., checkboxes group) - create array
             if (dataObject.hasOwnProperty(key)) {
                if (!Array.isArray(dataObject[key])) {
                     dataObject[key] = [dataObject[key]]; // Convert to array
                 }
                 dataObject[key].push(value);
            } else {
                dataObject[key] = value;
            }
        });
        return dataObject;
    }

    // =========================================================================
    // UI State & Helper Methods
    // =========================================================================

    /** Shows the loading indicator. */
    _showLoading(isModal = false, targetElement = null) {
         if (isModal) {
             // Show loading indicator within a specific modal or form
             const modalLoading = targetElement?.querySelector('.datatable5-modal-loading') || document.createElement('div');
             modalLoading.className = 'datatable5-modal-loading text-center p-2';
             modalLoading.innerHTML = `<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`;
             if (targetElement && !targetElement.querySelector('.datatable5-modal-loading')) {
                 // Append strategically (e.g., in modal footer or near submit button)
                  const footer = targetElement.querySelector('.modal-footer');
                  if (footer) footer.prepend(modalLoading);
                  else targetElement.appendChild(modalLoading);
             }
             // Disable submit button in modal?
             const submitButton = targetElement?.querySelector('button[type="submit"]');
             if(submitButton) submitButton.disabled = true;

         } else if (this.loadingIndicator) {
             this.loadingIndicator.style.display = 'flex'; // Use flex for centering spinner
             // Optionally add overlay to table
             this.table?.classList.add('loading-overlay');
         }
     }

     /** Hides the loading indicator. */
    _hideLoading(isModal = false, targetElement = null) {
         if (isModal) {
              const modalLoading = targetElement?.querySelector('.datatable5-modal-loading');
              modalLoading?.remove();
              // Re-enable submit button
              const submitButton = targetElement?.querySelector('button[type="submit"]');
             if(submitButton) submitButton.disabled = false;
         } else if (this.loadingIndicator) {
             this.loadingIndicator.style.display = 'none';
             this.table?.classList.remove('loading-overlay');
         }
     }

    /** Shows an error message. */
    _showError(message, targetContainer = this.errorContainer) {
        if (targetContainer) {
            targetContainer.textContent = message;
            targetContainer.style.display = 'block';
        } else {
             // Fallback or dedicated modal error display?
             console.error("DataTable5 Error:", message);
             // alert(message); // Simple fallback
        }
    }

    /** Hides the error message. */
    _hideError(targetContainer = this.errorContainer) {
        if (targetContainer) {
            targetContainer.textContent = '';
            targetContainer.style.display = 'none';
        }
         // Also hide modal-specific errors if applicable
         const modalErrors = this.container.querySelectorAll('.datatable5-modal-error'); // Add this class to modal error divs
         modalErrors.forEach(el => { el.textContent = ''; el.style.display = 'none'; });
    }

     /** Shows a temporary success/info message (Toast). Requires a Toast library/CSS. */
     _showToast(message, type = 'success') {
         // Basic implementation using alert, replace with a real toast component
         console.log(`DataTable5 Toast (${type}):`, message);
         alert(message);

         // --- Example using Bootstrap 5 Toasts (requires HTML structure and JS init) ---
         /*
         const toastPlacement = document.getElementById('toastPlacement'); // Container for toasts
         if (toastPlacement) {
             const toastId = `toast-${Date.now()}`;
             const toastHtml = `
                 <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                     <div class="d-flex">
                         <div class="toast-body">${message}</div>
                         <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                     </div>
                 </div>`;
             toastPlacement.insertAdjacentHTML('beforeend', toastHtml);
             const toastElement = document.getElementById(toastId);
             const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
             toast.show();
             toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove()); // Cleanup
         } else {
              alert(message); // Fallback
         }
         */
     }

    /** Safely gets a value from an object using dot notation string. */
    _getNestedValue(obj, path) {
        if (!path) return obj;
        const keys = path.split('.');
        let result = obj;
        for (const key of keys) {
            if (result === null || result === undefined) return undefined;
            result = result[key];
        }
        return result;
    }

    /** Deep merges two objects (useful for config). */
    _deepMerge(target, source) {
        const isObject = (obj) => obj && typeof obj === 'object' && !Array.isArray(obj);
        let output = { ...target };
        if (isObject(target) && isObject(source)) {
            Object.keys(source).forEach(key => {
                if (isObject(source[key])) {
                    if (!(key in target)) {
                        output[key] = source[key];
                    } else {
                        output[key] = this._deepMerge(target[key], source[key]);
                    }
                } else {
                    output[key] = source[key];
                }
            });
        }
        return output;
    }

     /** Sets up initial filter state */
     _initializeFilters() {
         this.config.filters.forEach(filter => {
             if (filter.defaultValue !== undefined) {
                 this.currentFilters[filter.paramName] = filter.defaultValue;
             }
         });
     }

     /** Resets all filters and refreshes the table */
     _resetFilters() {
         this.currentFilters = {};
         this._initializeFilters(); // Re-apply defaults

         // Clear filter UI controls
         this.container.querySelectorAll('.datatable5-filter-control').forEach(control => {
             const paramName = control.dataset.paramName;
             const filterConfig = this.config.filters.find(f => f.paramName === paramName);
             const defaultValue = filterConfig?.defaultValue || '';
             if (control.type === 'checkbox' || control.type === 'radio') {
                 control.checked = control.value == defaultValue; // Check if value matches default
             } else {
                  control.value = defaultValue;
             }
         });

         this.currentPage = 1;
         this.fetchData();
     }
}
