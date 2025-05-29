/**
 * CardManager - A class for managing dynamic card display with search, filters, and pagination
 * Handles communication with backend PHP using Axios
 */
class CardManager {
  /**
   * Create a new CardManager instance
   * @param {Object} config - Configuration object
   * @param {string} config.cardsContainer - Selector for the container that will hold the cards
   * @param {string} config.searchInput - Selector for the search input element
   * @param {Object} config.filters - Object containing filter elements selectors
   * @param {string} config.apiEndpoint - Backend API endpoint for data
   * @param {boolean} config.pagination - Whether to enable pagination
   * @param {number} config.perPage - Number of items per page (if pagination enabled)
   * @param {string} config.paginationContainer - Selector for pagination container (if pagination enabled)
   * @param {Object} config.modalConfig - Configuration for modals (if needed)
   * @param {Function} config.cardTemplate - Function that returns HTML for a single card
   */
  constructor(config) {
    // Store configuration
    this.config = {
      cardsContainer: null,
      searchInput: null,
      filters: {},
      apiEndpoint: '',
      pagination: false,
      perPage: 10,
      paginationContainer: null,
      modalConfig: {},
      cardTemplate: null,
      customActions: {}, // Custom action handlers for buttons within cards
      ...config
    };

    // Internal state
    this.state = {
      cards: [],
      filteredCards: [],
      currentPage: 1,
      totalPages: 1,
      searchTerm: '',
      activeFilters: {},
      isLoading: false,
      error: null
    };

    // Initialize the card manager
    this.init();
  }

  /**
   * Initialize the card manager
   */
  init() {
    // Get DOM elements
    this.cardsContainer = document.querySelector(this.config.cardsContainer);
    this.searchInput = document.querySelector(this.config.searchInput);
    
    // Set up event listeners for search
    if (this.searchInput) {
      this.searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
    }
    
    // Set up filters
    this.initFilters();
    
    // Set up pagination if enabled
    if (this.config.pagination) {
      this.paginationContainer = document.querySelector(this.config.paginationContainer);
      this.initPagination();
    }
    
    // Set up modals if configured
    if (this.config.modalConfig) {
      this.initModals();
    }
    
    // Initial data fetch
    this.fetchCards();
  }

  /**
   * Initialize filter elements and event listeners
   */
  initFilters() {
    this.filterElements = {};
    
    // Loop through the filter config and set up event listeners
    for (const [filterName, selector] of Object.entries(this.config.filters)) {
      const element = document.querySelector(selector);
      if (element) {
        this.filterElements[filterName] = element;
        
        // Different handling based on element type
        if (element.tagName === 'SELECT') {
          element.addEventListener('change', () => this.applyFilters());
        } else if (element.type === 'checkbox' || element.type === 'radio') {
          element.addEventListener('change', () => this.applyFilters());
        } else {
          // For other inputs like text, use debounce
          element.addEventListener('input', this.debounce(() => this.applyFilters(), 300));
        }
      }
    }
  }

  /**
   * Initialize pagination elements and controls
   */
  initPagination() {
    if (!this.paginationContainer) return;
    
    // Clear existing pagination
    this.paginationContainer.innerHTML = '';
    this.renderPagination();
  }

  /**
   * Initialize modal functionality
   */
  initModals() {
    // If using a library like Bootstrap, this would configure their modal system
    // For a custom implementation, we'd set up listeners on modal triggers
    
    if (this.config.modalConfig.cancelSelector) {
      document.querySelectorAll(this.config.modalConfig.cancelSelector).forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          this.closeModal(e.target.closest('.modal'));
        });
      });
    }
  }

  /**
   * Fetch cards data from the backend
   * @param {Object} params - Additional parameters to send with the request
   * @returns {Promise} - Promise that resolves when data is fetched
   */
  async fetchCards(params = {}) {
    try {
      this.setState({ isLoading: true, error: null });
      this.renderLoadingState();
      
      // Combine any passed params with current state for filtering/search
      const requestParams = {
        ...params,
        search: this.state.searchTerm,
        ...this.state.activeFilters,
        page: this.state.currentPage,
        perPage: this.config.perPage
      };
      
      // Make the API request using Axios
      const response = await axios.get(this.config.apiEndpoint, { params: requestParams });
      
      if (response.data && response.data.success) {
        // Update state with the fetched cards
        this.setState({
          cards: response.data.cards || [],
          filteredCards: response.data.cards || [],
          totalPages: response.data.totalPages || 1
        });
        
        // Render the updated cards
        this.renderCards();
        
        if (this.config.pagination) {
          this.renderPagination();
        }
      } else {
        throw new Error(response.data.message || 'Failed to fetch cards');
      }
    } catch (error) {
      this.setState({ error: error.message || 'An error occurred while fetching data' });
      this.renderErrorState();
    } finally {
      this.setState({ isLoading: false });
    }
  }

  /**
   * Handle search input changes
   * @param {Event} event - The input event
   */
  handleSearch(event) {
    const searchTerm = event.target.value.trim().toLowerCase();
    this.setState({ searchTerm, currentPage: 1 });
    
    // For server-side search, fetch new results
    this.fetchCards();
    
    // For client-side search (if needed)
    // this.filterCards();
  }

  /**
   * Apply all active filters
   */
  applyFilters() {
    const activeFilters = {};
    
    // Gather values from filter elements
    for (const [filterName, element] of Object.entries(this.filterElements)) {
      if (element.tagName === 'SELECT') {
        if (element.value) {
          activeFilters[filterName] = element.value;
        }
      } else if (element.type === 'checkbox') {
        activeFilters[filterName] = element.checked;
      } else if (element.type === 'radio') {
        if (element.checked) {
          activeFilters[filterName] = element.value;
        }
      } else {
        // Text inputs or other types
        if (element.value.trim()) {
          activeFilters[filterName] = element.value.trim();
        }
      }
    }
    
    this.setState({ activeFilters, currentPage: 1 });
    this.fetchCards();
  }

  /**
   * Filter cards client-side (optional, can be used instead of server-side filtering)
   */
  filterCards() {
    const { searchTerm, activeFilters, cards } = this.state;
    
    let filteredCards = [...cards];
    
    // Apply search filter if there's a search term
    if (searchTerm) {
      filteredCards = filteredCards.filter(card => {
        // Search through card properties (customize as needed)
        return (
          (card.title && card.title.toLowerCase().includes(searchTerm)) ||
          (card.description && card.description.toLowerCase().includes(searchTerm))
        );
      });
    }
    
    // Apply active filters
    Object.entries(activeFilters).forEach(([filterName, filterValue]) => {
      if (filterValue !== undefined && filterValue !== null && filterValue !== '') {
        filteredCards = filteredCards.filter(card => {
          // Different filter types
          if (typeof filterValue === 'boolean') {
            return card[filterName] === filterValue;
          } else if (Array.isArray(card[filterName])) {
            return card[filterName].includes(filterValue);
          } else {
            return card[filterName] == filterValue; // Use loose equality for type conversion
          }
        });
      }
    });
    
    this.setState({ filteredCards, totalPages: Math.ceil(filteredCards.length / this.config.perPage) });
    this.renderCards();
    
    if (this.config.pagination) {
      this.renderPagination();
    }
  }

  /**
   * Render the filtered cards to the container
   */
  renderCards() {
    if (!this.cardsContainer) return;
    
    this.cardsContainer.innerHTML = '';
    
    const { filteredCards, currentPage } = this.state;
    const { perPage, pagination, cardTemplate } = this.config;
    
    let cardsToRender = filteredCards;
    
    // Apply pagination if enabled
    if (pagination) {
      const startIndex = (currentPage - 1) * perPage;
      const endIndex = startIndex + perPage;
      cardsToRender = filteredCards.slice(startIndex, endIndex);
    }
    
    // No cards to display
    if (cardsToRender.length === 0) {
      this.cardsContainer.innerHTML = '<div class="no-results">No cards found. Try adjusting your search or filters.</div>';
      return;
    }
    
    // Render each card
    cardsToRender.forEach(card => {
      const cardElement = document.createElement('div');
      cardElement.classList.add('card');
      
      // Use the card template function if provided, otherwise use a default
      if (typeof cardTemplate === 'function') {
        cardElement.innerHTML = cardTemplate(card);
      } else {
        cardElement.innerHTML = `
          <h3>${card.title || 'Untitled'}</h3>
          <p>${card.description || ''}</p>
        `;
      }
      
      // Add event listeners for card interactions
      this.addCardEventListeners(cardElement, card);
      
      this.cardsContainer.appendChild(cardElement);
    });
  }

  /**
   * Add event listeners to card elements
   * @param {HTMLElement} cardElement - The card DOM element
   * @param {Object} cardData - The card data
   */
  addCardEventListeners(cardElement, cardData) {
    // Make the whole card clickable to open the main modal
    if (this.config.modalConfig && this.config.modalConfig.enabled) {
      cardElement.addEventListener('click', (e) => {
        // Only open the main modal if we didn't click on a button or link with its own handler
        if (!e.target.closest('button, a, .custom-action')) {
          this.openModal(cardData);
        }
      });
      
      // Add cursor pointer to indicate clickable
      cardElement.style.cursor = 'pointer';
    }
    
    // Handle specific action buttons within the card
    const customButtons = cardElement.querySelectorAll('.custom-action-btn');
    customButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent the card click from triggering
        
        // Get the action type from data attribute
        const actionType = button.dataset.action;
        
        if (actionType && this.config.customActions && typeof this.config.customActions[actionType] === 'function') {
          // Call the appropriate custom action handler
          this.config.customActions[actionType](cardData, e);
        }
      });
    });
    
    // View button still works independently
    const viewButton = cardElement.querySelector('.card-view-btn');
    if (viewButton) {
      viewButton.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent the card click from triggering
        this.openModal(cardData);
      });
    }
  }

  /**
   * Render pagination controls
   */
  renderPagination() {
    if (!this.paginationContainer || !this.config.pagination) return;
    
    const { currentPage, totalPages } = this.state;
    
    this.paginationContainer.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    const paginationElement = document.createElement('div');
    paginationElement.classList.add('pagination');
    
    // Previous button
    const prevButton = document.createElement('button');
    prevButton.innerHTML = '&laquo; Previous';
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener('click', () => this.goToPage(currentPage - 1));
    paginationElement.appendChild(prevButton);
    
    // Page numbers
    const maxPages = 5; // Maximum number of page buttons to show
    const startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
    const endPage = Math.min(totalPages, startPage + maxPages - 1);
    
    for (let i = startPage; i <= endPage; i++) {
      const pageButton = document.createElement('button');
      pageButton.textContent = i;
      pageButton.classList.toggle('active', i === currentPage);
      pageButton.addEventListener('click', () => this.goToPage(i));
      paginationElement.appendChild(pageButton);
    }
    
    // Next button
    const nextButton = document.createElement('button');
    nextButton.innerHTML = 'Next &raquo;';
    nextButton.disabled = currentPage === totalPages;
    nextButton.addEventListener('click', () => this.goToPage(currentPage + 1));
    paginationElement.appendChild(nextButton);
    
    this.paginationContainer.appendChild(paginationElement);
  }

  /**
   * Navigate to a specific page
   * @param {number} page - The page number to go to
   */
  goToPage(page) {
    if (page < 1 || page > this.state.totalPages) return;
    
    this.setState({ currentPage: page });
    
    // For server-side pagination, fetch new data
    this.fetchCards();
    
    // For client-side pagination (if needed)
    // this.renderCards();
    // this.renderPagination();
  }

  /**
   * Open a modal with card details
   * @param {Object} cardData - The card data to display in the modal
   */
  openModal(cardData) {
    if (!this.config.modalConfig.template) return;
    
    // Create modal element
    const modalElement = document.createElement('div');
    modalElement.classList.add('modal');
    modalElement.innerHTML = this.config.modalConfig.template(cardData);
    
    // Add close button event listener
    const closeButtons = modalElement.querySelectorAll('.modal-close, .modal-cancel');
    closeButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        this.closeModal(modalElement);
      });
    });
    
    // Add action button event listener (if specified in config)
    const actionButtons = modalElement.querySelectorAll('.modal-action');
    actionButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        
        // If there's a callback function for this action, execute it
        if (typeof this.config.modalConfig.onAction === 'function') {
          this.config.modalConfig.onAction(cardData, modalElement);
        }
        
        // If the action should close the modal, do so
        if (this.config.modalConfig.closeOnAction) {
          this.closeModal(modalElement);
        }
      });
    });
    
    // Add to document
    document.body.appendChild(modalElement);
    
    // Show modal (with slight delay for animation)
    setTimeout(() => {
      modalElement.classList.add('active');
    }, 10);
    
    // Close modal when clicking outside
    modalElement.addEventListener('click', (e) => {
      if (e.target === modalElement) {
        this.closeModal(modalElement);
      }
    });
    
    // Add escape key listener
    const escHandler = (e) => {
      if (e.key === 'Escape') {
        this.closeModal(modalElement);
        document.removeEventListener('keydown', escHandler);
      }
    };
    document.addEventListener('keydown', escHandler);
  }

  /**
   * Close a modal
   * @param {HTMLElement} modalElement - The modal element to close
   */
  closeModal(modalElement) {
    if (!modalElement) return;
    
    modalElement.classList.remove('active');
    
    // Remove after animation finishes
    setTimeout(() => {
      if (modalElement.parentNode) {
        modalElement.parentNode.removeChild(modalElement);
      }
    }, 300); // Match this to your CSS transition time
  }

  /**
   * Render loading state when fetching data
   */
  renderLoadingState() {
    if (!this.cardsContainer) return;
    
    // Add a loading indicator or spinner
    this.cardsContainer.innerHTML = '<div class="loading-spinner">Loading...</div>';
  }

  /**
   * Render error state if an error occurs
   */
  renderErrorState() {
    if (!this.cardsContainer) return;
    
    this.cardsContainer.innerHTML = `
      <div class="error-message">
        <p>${this.state.error || 'An error occurred.'}</p>
        <button class="retry-button">Retry</button>
      </div>
    `;
    
    // Add retry button functionality
    this.cardsContainer.querySelector('.retry-button')?.addEventListener('click', () => {
      this.fetchCards();
    });
  }

  /**
   * Update the internal state
   * @param {Object} newState - The state changes to apply
   */
  setState(newState) {
    this.state = { ...this.state, ...newState };
  }

  /**
   * Create a debounced function to limit rapid firing of an event
   * @param {Function} func - The function to debounce
   * @param {number} delay - The delay in milliseconds
   * @returns {Function} - The debounced function
   */
  debounce(func, delay) {
    let timeoutId;
    return function(...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        func.apply(this, args);
      }, delay);
    };
  }

  /**
   * Send a POST request to the server
   * @param {string} endpoint - The API endpoint
   * @param {Object} data - The data to send
   * @returns {Promise} - Promise that resolves with the response
   */
  async postData(endpoint, data) {
    try {
      const response = await axios.post(endpoint, data);
      return response.data;
    } catch (error) {
      console.error('Error posting data:', error);
      throw error;
    }
  }
}

// Usage example:
/*
const cardManager = new CardManager({
  cardsContainer: '#cards-container',
  searchInput: '#search-input',
  filters: {
    category: '#category-filter',
    price: '#price-filter',
    inStock: '#in-stock-filter'
  },
  apiEndpoint: '/api/cards.php',
  pagination: true,
  perPage: 12,
  paginationContainer: '#pagination-container',
  modalConfig: {
    enabled: true,
    closeOnAction: false, // Set to true if you want modal to close after action button is clicked
    template: (card) => `
      <div class="modal-content">
        <button class="modal-close">&times;</button>
        <h2>${card.title}</h2>
        <div class="modal-body">
          <img src="${card.image}" alt="${card.title}" class="modal-image">
          <div class="modal-details">
            <p class="price"><strong>Price:</strong> ${card.price.toFixed(2)}</p>
            <p class="description">${card.fullDescription || card.description}</p>
            ${card.specs ? `<div class="specs">${card.specs}</div>` : ''}
          </div>
        </div>
        <div class="modal-footer">
          <button class="modal-cancel">Close</button>
          <button class="modal-action">Add to Cart</button>
        </div>
      </div>
    `,
    onAction: (cardData, modalElement) => {
      // Example function that would be executed when the action button is clicked
      console.log('Action button clicked for:', cardData);
      
      // You could call an API here to perform the action
      // Example: this.postData('/api/cart.php', { action: 'add', id: cardData.id });
      
      // Optionally show a success message in the modal
      const messageElement = document.createElement('div');
      messageElement.classList.add('modal-message', 'success');
      messageElement.textContent = 'Item added to cart!';
      
      // Find the footer and insert before it
      const footer = modalElement.querySelector('.modal-footer');
      footer.parentNode.insertBefore(messageElement, footer);
      
      // Automatically remove the message after a few seconds
      setTimeout(() => {
        messageElement.remove();
      }, 3000);
    }
  },
  cardTemplate: (card) => `
    <div class="card-inner">
      <img src="${card.image}" alt="${card.title}">
      <h3>${card.title}</h3>
      <p class="price">${card.price.toFixed(2)}</p>
      <p class="description">${card.description}</p>
      <button class="card-view-btn">View Details</button>
    </div>
  `
});
*/