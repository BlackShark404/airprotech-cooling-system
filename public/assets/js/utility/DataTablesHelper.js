/**
 * DataTablesHelper - A jQuery DataTables enhancement library
 * 
 * A standardized helper for DataTables that provides:
 * - Simplified initialization with sensible defaults
 * - Badge support for status columns
 * - Toast notifications
 * - Action buttons with callbacks
 * - CRUD operations (add/edit/delete)
 * - Filter management
 * 
 * Requires: jQuery, DataTables, Bootstrap 5
 * 
 * @version 1.0.0
 */
;(function($, window, document, undefined) {
  'use strict';

  /**
   * DataTablesHelper constructor
   * @param {string} selector - CSS selector for the table
   * @param {Object} options - Configuration options
   */
  function DataTablesHelper(selector, options) {
    // Store reference to the element
    this.$table = $(selector);
    if (!this.$table.length) {
      console.error('DataTablesHelper: Table not found with selector', selector);
      return;
    }
    
    // Default options
    const defaultOptions = {
      // Table configuration
      columns: [],
      ajaxUrl: '',
      responsive: true,
      processing: true,
      serverSide: false,
      paging: true,
      searching: true,
      ordering: true,
      info: true,
      
      // Export buttons - disabled by default
      buttons: [],
      
      // Callback functions
      onView: null,
      onEdit: null,
      onDelete: null,
      onInitComplete: null,
      
      // Custom buttons
      customButtons: {},
      
      // Toast configuration
      toast: {
        enabled: true,
        position: 'bottom-right',
        autoClose: 4000,
        hideProgressBar: false,
        closeOnClick: true,
        pauseOnHover: true,
        enableIcons: true
      },
      
      // Action button labels
      actionLabels: {
        view: 'View',
        edit: 'Edit',
        delete: 'Delete'
      },
      
      // Action button classes
      actionClasses: {
        view: 'btn btn-info btn-sm',
        edit: 'btn btn-warning btn-sm',
        delete: 'btn btn-danger btn-sm'
      },
      
      // Confirmation texts
      confirmTexts: {
        deleteTitle: 'Confirm Delete',
        deleteMessage: 'Are you sure you want to delete this record?',
        deleteConfirm: 'Delete',
        deleteCancel: 'Cancel'
      }
    };
    
    // Merge options
    this.options = $.extend(true, {}, defaultOptions, options);
    
    // Initialize properties
    this.dataTable = null;
    this.data = [];

    // Initialize toast container
    if (this.options.toast.enabled) {
      this._initToastContainer();
    }
    
    // Initialize the DataTable
    this.init();
    
    // Return this instance
    return this;
  }
  
  /**
   * Initialize the DataTable
   */
  DataTablesHelper.prototype.init = function() {
    const self = this;
    
    // Process columns to add badge rendering
    const processedColumns = this._processColumns();
    
    // Add action column if any callback is provided
    if (this.options.onView || this.options.onEdit || this.options.onDelete) {
      processedColumns.push(this._createActionsColumn());
    }
    
    // Prepare DataTables configuration
    const dtConfig = {
      columns: processedColumns,
      responsive: this.options.responsive,
      processing: this.options.processing,
      serverSide: this.options.serverSide,
      paging: this.options.paging,
      searching: this.options.searching,
      ordering: this.options.ordering,
      info: this.options.info,
      
      // Move search and pagination to right
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-end"f>>' +
           '<"row"<"col-sm-12"tr>>' +
           '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 text-end"p>>',
      
      language: {
        searchPlaceholder: "Search records",
        emptyTable: "No data available"
      },
      initComplete: function(settings, json) {
        if (typeof self.options.onInitComplete === 'function') {
          self.options.onInitComplete.call(self, settings, json);
        }
      }
    };
    
    // Add buttons if specified and not empty
    if (this.options.buttons && this.options.buttons.length) {
      dtConfig.dom = 'B' + dtConfig.dom;
      dtConfig.buttons = [
        ...this.options.buttons,
        ...Object.values(this.options.customButtons)
      ];
    }
    
    // Add AJAX data source if specified
    if (this.options.ajaxUrl) {
      dtConfig.ajax = {
        url: this.options.ajaxUrl,
        // Handle response data
        dataSrc: (json) => {
          try {
            // Validate response format
            if (json === null || json === undefined) {
              this._handleAjaxError('Empty response received from server.');
              return [];
            }
            
            // Store data based on response format
            if (Array.isArray(json)) {
              // Direct array format
              this.data = json;
            } else if (json.data && Array.isArray(json.data)) {
              // DataTables standard { data: [...] } format
              this.data = json.data;
            } else if (typeof json === 'object') {
              // Try to extract data from other properties if available
              const possibleDataProps = ['records', 'items', 'results', 'rows'];
              for (const prop of possibleDataProps) {
                if (json[prop] && Array.isArray(json[prop])) {
                  this.data = json[prop];
                  break;
                }
              }
              
              // If no array data found, but it's an object, warn but try to use it
              if (!Array.isArray(this.data)) {
                console.warn('Response is not an array or doesn\'t contain a data array. Using response as is.');
                this.data = json;
              }
            } else {
              this._handleAjaxError('Invalid response format: Expected JSON array or object.');
              return [];
            }
            
            return this.data;
          } catch (error) {
            this._handleAjaxError(`Error processing server response: ${error.message}`);
            return [];
          }
        },
        
        // Add explicit error handling
        error: (xhr, error, thrown) => {
          let errorMessage = 'Error loading data from server';
          
          // Try to parse error response if it exists
          if (xhr.responseText) {
            try {
              const errorData = JSON.parse(xhr.responseText);
              if (errorData.message || errorData.error) {
                errorMessage = errorData.message || errorData.error;
              }
            } catch (e) {
              // If response isn't valid JSON, use status text
              errorMessage = xhr.statusText || error || 'Unknown error';
            }
          }
          
          this._handleAjaxError(errorMessage);
        }
      };
    }
    
    // Initialize DataTable
    this.dataTable = this.$table.DataTable(dtConfig);
    
    // Attach event listeners
    this._attachEventListeners();
    
    // Return this instance for chaining
    return this;
  };
  
  /**
   * Process columns and add badge rendering
   * @private
   */
  DataTablesHelper.prototype._processColumns = function() {
    return this.options.columns.map(column => {
      // If column has badge configuration, add render function
      if (column.badge) {
        const newColumn = { ...column };
        
        newColumn.render = (data, type, row) => {
          // For sorting and filtering, use the raw data
          if (type === 'sort' || type === 'filter') {
            return data;
          }
          
          // For display, generate badge HTML
          return this._renderBadge(data, column.badge);
        };
        
        return newColumn;
      }
      
      // Return original column if no badge
      return column;
    });
  };
  
  /**
   * Create actions column for view/edit/delete buttons
   * @private
   */
  DataTablesHelper.prototype._createActionsColumn = function() {
    const self = this;
    
    return {
      data: null,
      title: 'Actions',
      orderable: false,
      className: 'dt-actions-column',
      render: function(data, type, row) {
        let html = '<div class="d-flex gap-1">';
        
        if (self.options.onView) {
          html += `<button class="dt-view-btn ${self.options.actionClasses.view}" 
                          data-id="${row.id || row.ID || 0}">
                    ${self.options.actionLabels.view}
                  </button>`;
        }
        
        if (self.options.onEdit) {
          html += `<button class="dt-edit-btn ${self.options.actionClasses.edit}" 
                          data-id="${row.id || row.ID || 0}">
                    ${self.options.actionLabels.edit}
                  </button>`;
        }
        
        if (self.options.onDelete) {
          html += `<button class="dt-delete-btn ${self.options.actionClasses.delete}" 
                          data-id="${row.id || row.ID || 0}">
                    ${self.options.actionLabels.delete}
                  </button>`;
        }
        
        html += '</div>';
        return html;
      }
    };
  };
  
  /**
   * Attach event listeners for action buttons
   * @private
   */
  DataTablesHelper.prototype._attachEventListeners = function() {
    const self = this;
    
    // View button click handler
    if (this.options.onView) {
      this.$table.on('click', '.dt-view-btn', function(e) {
        const id = $(this).data('id');
        const rowData = self._findRowById(id);
        
        if (rowData) {
          self.options.onView.call(self, rowData, id);
          
          if (self.options.toast.enabled) {
            self.showToast('info', 'View Record', `Viewing record #${id}`);
          }
        }
      });
    }
    
    // Edit button click handler
    if (this.options.onEdit) {
      this.$table.on('click', '.dt-edit-btn', function(e) {
        const id = $(this).data('id');
        const rowData = self._findRowById(id);
        
        if (rowData) {
          self.options.onEdit.call(self, rowData, id);
          
          if (self.options.toast.enabled) {
            self.showToast('warning', 'Edit Record', `Editing record #${id}`);
          }
        }
      });
    }
    
    // Delete button click handler
    if (this.options.onDelete) {
      this.$table.on('click', '.dt-delete-btn', function(e) {
        const id = $(this).data('id');
        const rowData = self._findRowById(id);
        
        if (rowData) {
          self._showDeleteConfirmation(rowData, id);
        }
      });
    }
  };
  
  /**
   * Find a row by ID in the current data
   * @param {number|string} id - ID of the row to find
   * @private
   */
  DataTablesHelper.prototype._findRowById = function(id) {
    return this.data.find(row => {
      const rowId = row.id || row.ID;
      return rowId == id; // Use loose equality to handle string/number differences
    });
  };
  
  /**
   * Display delete confirmation dialog
   * @param {Object} rowData - Data for the row to delete
   * @param {number|string} id - ID of the row
   * @private
   */
  DataTablesHelper.prototype._showDeleteConfirmation = function(rowData, id) {
    const self = this;
    const modalId = 'dtHelperDeleteModal';
    
    // Remove existing modal if present
    $(`#${modalId}`).remove();
    
    // Create Bootstrap modal for confirmation
    const $modal = $(`
      <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">${this.options.confirmTexts.deleteTitle}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              ${this.options.confirmTexts.deleteMessage}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                ${this.options.confirmTexts.deleteCancel}
              </button>
              <button type="button" class="btn btn-danger" id="dtHelperConfirmDelete">
                ${this.options.confirmTexts.deleteConfirm}
              </button>
            </div>
          </div>
        </div>
      </div>
    `).appendTo('body');
    
    // Create Bootstrap modal instance
    const modal = new bootstrap.Modal($modal);
    
    // Confirm button click handler
    $modal.find('#dtHelperConfirmDelete').on('click', function() {
      // Call delete callback
      self.options.onDelete.call(self, rowData, id);
      
      // Close modal
      modal.hide();
      
      // Show toast if enabled
      if (self.options.toast.enabled) {
        self.showToast('error', 'Delete Record', `Record #${id} deleted`);
      }
    });
    
    // Show modal
    modal.show();
  };
  
  /**
   * Initialize toast notification container
   * @private
   */
  DataTablesHelper.prototype._initToastContainer = function() {
    // Check if container already exists
    if ($('#dtHelperToastContainer').length) {
      return;
    }
    
    // Add toast styles
    $('<style>')
      .html(`
        #dtHelperToastContainer {
          position: fixed;
          z-index: 9999;
          padding: 15px;
          pointer-events: none;
        }
        #dtHelperToastContainer.top-right {
          top: 15px;
          right: 15px;
        }
        #dtHelperToastContainer.top-left {
          top: 15px;
          left: 15px;
        }
        #dtHelperToastContainer.bottom-right {
          bottom: 15px;
          right: 15px;
        }
        #dtHelperToastContainer.bottom-left {
          bottom: 15px;
          left: 15px;
        }
        .dt-toast {
          position: relative;
          max-width: 350px;
          margin-bottom: 10px;
          border-radius: 5px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
          color: white;
          padding: 15px 20px;
          overflow: hidden;
          display: flex;
          align-items: center;
          pointer-events: auto;
          opacity: 0;
          transform: translateY(-20px);
          transition: all 0.3s ease-in-out;
        }
        .dt-toast.show {
          opacity: 1;
          transform: translateY(0);
        }
        .dt-toast-icon {
          margin-right: 12px;
          font-size: 20px;
        }
        .dt-toast-content {
          flex: 1;
        }
        .dt-toast-title {
          font-weight: bold;
          margin-bottom: 5px;
        }
        .dt-toast-message {
          font-size: 14px;
        }
        .dt-toast-close {
          margin-left: 10px;
          cursor: pointer;
          opacity: 0.7;
          transition: opacity 0.2s;
          font-size: 18px;
          background: none;
          border: none;
          color: white;
        }
        .dt-toast-close:hover {
          opacity: 1;
        }
        .dt-toast-success { background-color: #4caf50; }
        .dt-toast-error { background-color: #f44336; }
        .dt-toast-warning { background-color: #ff9800; }
        .dt-toast-info { background-color: #2196f3; }
        .dt-toast-progress {
          position: absolute;
          bottom: 0;
          left: 0;
          height: 3px;
          width: 100%;
          background-color: rgba(255, 255, 255, 0.3);
        }
        .dt-toast-progress-bar {
          height: 100%;
          width: 100%;
          background-color: rgba(255, 255, 255, 0.5);
          transition: width linear;
        }
        /* Custom styles for right-aligned search and pagination */
        .dataTables_filter {
          float: right !important;
          text-align: right !important;
        }
        .dataTables_filter label {
          display: flex;
          justify-content: flex-end;
          align-items: center;
        }
        .dataTables_filter input {
          margin-left: 8px;
          width: auto;
        }
        .dataTables_paginate {
          float: right !important;
        }
      `)
      .appendTo('head');
    
    // Create toast container
    const position = this.options.toast.position || 'bottom-right';
    $('<div>', {
      id: 'dtHelperToastContainer',
      class: position
    }).appendTo('body');
  };
  
  /**
   * Render a badge for a value
   * @param {*} value - Value to display in badge
   * @param {Object} config - Badge configuration
   * @private
   */
  DataTablesHelper.prototype._renderBadge = function(value, config) {
    // Default badge configuration
    const defaultConfig = {
      type: 'primary',
      pill: false,
      size: '',
      prefix: '',
      suffix: '',
      customClass: '',
      valueMap: null
    };
    
    // Merge with provided config
    const badgeConfig = $.extend({}, defaultConfig, config);
    
    // Check if we have a value mapping
    let displayValue = value;
    let badgeType = badgeConfig.type;
    
    if (badgeConfig.valueMap && badgeConfig.valueMap[value] !== undefined) {
      const mapping = badgeConfig.valueMap[value];
      
      // Handle object mapping (with custom color and display text)
      if (typeof mapping === 'object') {
        displayValue = mapping.display || value;
        badgeType = mapping.type || badgeType;
      } 
      // Handle string mapping (just display text)
      else if (typeof mapping === 'string') {
        displayValue = mapping;
      }
    }
    
    // Build badge classes
    let badgeClasses = `badge bg-${badgeType}`;
    if (badgeConfig.pill) badgeClasses += ' rounded-pill';
    if (badgeConfig.size) badgeClasses += ` badge-${badgeConfig.size}`;
    if (badgeConfig.customClass) badgeClasses += ` ${badgeConfig.customClass}`;
    
    // Create badge HTML
    return `<span class="${badgeClasses}">${badgeConfig.prefix}${displayValue}${badgeConfig.suffix}</span>`;
  };
  
  /**
   * Show a toast notification
   * @param {string} type - Toast type: success, error, warning, info
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {Object} options - Additional options
   */
  DataTablesHelper.prototype.showToast = function(type, title, message, options) {
    if (!this.options.toast.enabled) {
      return null;
    }
    
    const toastOptions = $.extend({}, this.options.toast, options);
    const toastId = `dt-toast-${Date.now()}`;
    
    // Get icon based on type
    let icon = '';
    switch (type) {
      case 'success':
        icon = toastOptions.enableIcons ? '<i class="fas fa-check-circle"></i>' : '✓';
        break;
      case 'error':
        icon = toastOptions.enableIcons ? '<i class="fas fa-times-circle"></i>' : '✗';
        break;
      case 'warning':
        icon = toastOptions.enableIcons ? '<i class="fas fa-exclamation-triangle"></i>' : '⚠';
        break;
      case 'info':
        icon = toastOptions.enableIcons ? '<i class="fas fa-info-circle"></i>' : 'ℹ';
        break;
      default:
        icon = toastOptions.enableIcons ? '<i class="fas fa-bell"></i>' : '🔔';
    }
    
    // Create toast HTML
    const $toast = $(`
      <div id="${toastId}" class="dt-toast dt-toast-${type}">
        <div class="dt-toast-icon">${icon}</div>
        <div class="dt-toast-content">
          <div class="dt-toast-title">${title}</div>
          <div class="dt-toast-message">${message}</div>
        </div>
        <button class="dt-toast-close">&times;</button>
        ${!toastOptions.hideProgressBar ? 
          '<div class="dt-toast-progress"><div class="dt-toast-progress-bar"></div></div>' : ''}
      </div>
    `);
    
    // Append to container
    $('#dtHelperToastContainer').append($toast);
    
    // Show toast with animation
    setTimeout(() => {
      $toast.addClass('show');
      
      // Set progress bar animation if enabled
      if (!toastOptions.hideProgressBar && toastOptions.autoClose) {
        $toast.find('.dt-toast-progress-bar').css({
          'width': '0%',
          'transition': `width ${toastOptions.autoClose}ms linear`
        });
      }
      
      // Auto close if enabled
      if (toastOptions.autoClose) {
        this._setToastTimeout($toast, toastOptions.autoClose);
      }
    }, 10);
    
    // Handle toast close button
    $toast.find('.dt-toast-close').on('click', () => {
      this._closeToast($toast);
    });
    
    // Handle pause on hover
    if (toastOptions.pauseOnHover && toastOptions.autoClose) {
      $toast.data('remainingTime', toastOptions.autoClose);
      
      $toast.on('mouseenter', () => {
        clearTimeout($toast.data('timeoutId'));
        
        // Pause progress bar
        if (!toastOptions.hideProgressBar) {
          const $progressBar = $toast.find('.dt-toast-progress-bar');
          const width = $progressBar.width() / $progressBar.parent().width() * 100;
          $progressBar.css({
            'width': `${width}%`,
            'transition': 'none'
          });
        }
      });
      
      $toast.on('mouseleave', () => {
        const remainingTime = $toast.data('remainingTime');
        
        // Resume progress bar
        if (!toastOptions.hideProgressBar) {
          $toast.find('.dt-toast-progress-bar').css({
            'width': '0%',
            'transition': `width ${remainingTime}ms linear`
          });
        }
        
        this._setToastTimeout($toast, remainingTime);
      });
    }
    
    return $toast;
  };
  
  /**
   * Set toast timeout with tracking
   * @param {jQuery} $toast - Toast element
   * @param {number} timeout - Timeout duration in ms
   * @private
   */
  DataTablesHelper.prototype._setToastTimeout = function($toast, timeout) {
    const startTime = Date.now();
    
    const timeoutId = setTimeout(() => {
      this._closeToast($toast);
    }, timeout);
    
    $toast.data('timeoutId', timeoutId);
    $toast.data('startTime', startTime);
  };
  
  /**
   * Close a toast notification
   * @param {jQuery} $toast - Toast element to close
   * @private
   */
  DataTablesHelper.prototype._closeToast = function($toast) {
    // Clear any existing timeout
    clearTimeout($toast.data('timeoutId'));
    
    // Remove show class to trigger fade out animation
    $toast.removeClass('show');
    
    // Remove the element after animation completes
    setTimeout(() => {
      $toast.remove();
    }, 300);
  };
  
  /**
   * Handle Ajax error and display notification
   * @param {string} message - Error message
   * @private
   */
  DataTablesHelper.prototype._handleAjaxError = function(message) {
    console.error('DataTablesHelper Ajax Error:', message);
    
    // Show error toast if enabled
    if (this.options.toast.enabled) {
      this.showErrorToast('Data Loading Error', message);
    }
    
    // Add error message to table if it exists
    if (this.$table.length) {
      // Find or create error display element
      let $errorDisplay = this.$table.siblings('.dt-error-display');
      if (!$errorDisplay.length) {
        $errorDisplay = $('<div class="dt-error-display alert alert-danger mt-3"></div>');
        this.$table.after($errorDisplay);
      }
      
      // Show error message
      $errorDisplay.html(`<strong>Error:</strong> ${message}`);
      setTimeout(() => {
        $errorDisplay.fadeOut(500, function() {
          $(this).remove();
        });
      }, 10000); // Remove after 10 seconds
    }
  };
  
  /**
   * Shorthand methods for different toast types
   */
  DataTablesHelper.prototype.showSuccessToast = function(title, message, options) {
    return this.showToast('success', title, message, options);
  };
  
  DataTablesHelper.prototype.showErrorToast = function(title, message, options) {
    return this.showToast('error', title, message, options);
  };
  
  DataTablesHelper.prototype.showWarningToast = function(title, message, options) {
    return this.showToast('warning', title, message, options);
  };
  
  DataTablesHelper.prototype.showInfoToast = function(title, message, options) {
    return this.showToast('info', title, message, options);
  };
  
  /**
   * Refresh the DataTable
   * @param {Array} [newData] - New data to set (optional)
   * @param {function} [callback] - Callback function after reload completes
   */
  DataTablesHelper.prototype.refresh = function(newData, callback) {
    try {
      if (Array.isArray(newData)) {
        this.data = newData;
        this.dataTable.clear().rows.add(newData).draw();
        if (typeof callback === 'function') callback(true);
      } else {
        this.dataTable.ajax.reload((json) => {
          if (typeof callback === 'function') callback(json);
        }, false); // false to maintain current paging
      }
    } catch (error) {
      console.error('Error refreshing table:', error);
      this._handleAjaxError(`Failed to refresh data: ${error.message}`);
      if (typeof callback === 'function') callback(false);
    }
    
    return this;
  };
  
  /**
   * Add a row to the DataTable
   * @param {Object} rowData - Row data to add
   */
  DataTablesHelper.prototype.addRow = function(rowData) {
    this.data.push(rowData);
    this.dataTable.row.add(rowData).draw();
    
    if (this.options.toast.enabled) {
      this.showSuccessToast('Record Added', `New record has been added`);
    }
    
    return this;
  };
  
  /**
   * Update a row in the DataTable
   * @param {number|string} id - ID of the row to update
   * @param {Object} newData - New data to set
   */
  DataTablesHelper.prototype.updateRow = function(id, newData) {
    // Find row index in data
    const index = this.data.findIndex(row => {
      const rowId = row.id || row.ID;
      return rowId == id;
    });
    
    if (index !== -1) {
      // Update data array
      this.data[index] = { ...this.data[index], ...newData };
      
      // Update DataTable
      this.dataTable.row(function(idx, data) {
        const rowId = data.id || data.ID;
        return rowId == id;
      }).data(this.data[index]).draw();
      
      if (this.options.toast.enabled) {
        this.showSuccessToast('Record Updated', `Record #${id} has been updated`);
      }
    } else if (this.options.toast.enabled) {
      this.showErrorToast('Update Error', `Record #${id} not found`);
    }
    
    return this;
  };
  
  /**
   * Delete a row from the DataTable
   * @param {number|string} id - ID of the row to delete
   */
  DataTablesHelper.prototype.deleteRow = function(id) {
    // Find row index in data
    const index = this.data.findIndex(row => {
      const rowId = row.id || row.ID;
      return rowId == id;
    });
    
    if (index !== -1) {
      // Remove from data array
      this.data.splice(index, 1);
      
      // Remove from DataTable
      this.dataTable.row(function(idx, data) {
        const rowId = data.id || data.ID;
        return rowId == id;
      }).remove().draw();
      
      if (this.options.toast.enabled) {
        this.showInfoToast('Record Deleted', `Record #${id} has been deleted`);
      }
    } else if (this.options.toast.enabled) {
      this.showErrorToast('Delete Error', `Record #${id} not found`);
    }
    
    return this;
  };
  
  /**
   * Apply filters to the DataTable
   * @param {Object} filters - Filter criteria (key-value pairs)
   */
  DataTablesHelper.prototype.applyFilters = function(filters) {
    // Remove existing custom filters
    $.fn.dataTable.ext.search.pop();
    
    // Add custom filter function if filters provided
    if (filters && Object.keys(filters).length > 0) {
      $.fn.dataTable.ext.search.push((settings, data, dataIndex, rowData) => {
        // Check each filter criteria
        for (const [key, value] of Object.entries(filters)) {
          if (rowData[key] !== value) {
            return false;
          }
        }
        return true;
      });
      
      if (this.options.toast.enabled) {
        this.showInfoToast('Filters Applied', `Table data has been filtered`);
      }
    } else if (this.options.toast.enabled) {
      this.showInfoToast('Filters Cleared', 'All filters have been removed');
    }
    
    // Redraw the table
    this.dataTable.draw();
    return this;
  };
  
  /**
   * Get selected rows
   * @returns {Array} Array of selected row data
   */
  DataTablesHelper.prototype.getSelectedRows = function() {
    return this.dataTable.rows({ selected: true }).data().toArray();
  };
  
  /**
   * Get all data
   * @returns {Array} Array of all row data
   */
  DataTablesHelper.prototype.getData = function() {
    return [...this.data];
  };
  
  /**
   * Get DataTable instance
   * @returns {Object} DataTable instance
   */
  DataTablesHelper.prototype.getTable = function() {
    return this.dataTable;
  };
  
  /**
   * Destroy the DataTable and clean up
   */
  DataTablesHelper.prototype.destroy = function() {
    // Destroy DataTable
    if (this.dataTable) {
      this.dataTable.destroy();
    }
    
    // Remove event listeners
    this.$table.off('click', '.dt-view-btn');
    this.$table.off('click', '.dt-edit-btn');
    this.$table.off('click', '.dt-delete-btn');
    
    // Clear data
    this.data = [];
    this.dataTable = null;
  };
  
  // Register as jQuery plugin
  $.fn.dataTablesHelper = function(options) {
    return this.each(function() {
      if (!$.data(this, 'dataTablesHelper')) {
        $.data(this, 'dataTablesHelper', new DataTablesHelper(this, options));
      }
    });
  };
  
  // Expose constructor
  window.DataTablesHelper = DataTablesHelper;
  
})(jQuery, window, document);